<?php
 
 namespace App\Services;
 
use DOMDocument;
use DOMXPath;
use Exception;
use DateTime;
 
class SriFacturaService
{
    protected string $xsdPath;
 
    public function __construct()
    {
        $this->xsdPath = storage_path('app/schemas/factura_V2.1.0.xsd');
    }
 
    // =========================================================================
    // CLAVE DE ACCESO
    // =========================================================================
 
    public function generarClaveAcceso(
        string $fecha,
        string $tipoComprobante,
        string $ruc,
        string $ambiente,
        string $estab,
        string $pemis,
        string $secuencial,
        string $codigoNumerico,
        string $tipoEmision
    ): string {
        $fecha          = date('dmY', strtotime($fecha));
        $ruc            = str_pad($ruc,            13, '0', STR_PAD_LEFT);
        $estab          = str_pad($estab,           3, '0', STR_PAD_LEFT);
        $pemis          = str_pad($pemis,           3, '0', STR_PAD_LEFT);
        $secuencial     = str_pad($secuencial,      9, '0', STR_PAD_LEFT);
        $codigoNumerico = str_pad($codigoNumerico,  8, '0', STR_PAD_LEFT);
 
        $clave = $fecha . $tipoComprobante . $ruc . $ambiente . $estab . $pemis . $secuencial . $codigoNumerico . $tipoEmision;
 
        if (strlen($clave) !== 48) {
            throw new Exception("Clave de acceso debe tener 48 dígitos, tiene: " . strlen($clave));
        }
 
        return $clave . $this->calcularDigitoVerificador($clave);
    }
 
    public function calcularDigitoVerificador(string $clave48): int
    {
        $digitos         = array_map('intval', str_split($clave48));
        $multiplicadores = [2, 3, 4, 5, 6, 7];
        $digitos         = array_reverse($digitos);
        $suma            = 0;
 
        foreach ($digitos as $i => $d) {
            $suma += $d * $multiplicadores[$i % count($multiplicadores)];
        }
 
        $dv = 11 - ($suma % 11);
        if ($dv === 11) $dv = 0;
        if ($dv === 10) $dv = 1;
 
        return $dv;
    }
 
    // =========================================================================
    // GENERAR XML
    // =========================================================================
 
    public function generarXml(array $data): string
    {
        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->formatOutput = true;
 
        $root = $doc->createElement('factura');
        $root->setAttribute('id', 'comprobante');
        $root->setAttribute('version', $data['version'] ?? '2.1.0');
        $doc->appendChild($root);
 
        // infoTributaria
        $infoTrib = $doc->createElement('infoTributaria');
        foreach ($data['infoTributaria'] as $tag => $value) {
            $infoTrib->appendChild($doc->createElement($tag, htmlspecialchars((string)$value)));
        }
        $root->appendChild($infoTrib);
 
        // infoFactura
        $infoFactura = $doc->createElement('infoFactura');
        foreach ($data['infoFactura'] as $tag => $value) {
            if ($tag === 'totalConImpuestos' && is_array($value)) {
                $tc = $doc->createElement('totalConImpuestos');
                foreach ($value as $imp) {
                    $item = $doc->createElement('totalImpuesto');
                    foreach ($imp as $k => $v) {
                        $item->appendChild($doc->createElement($k, htmlspecialchars((string)$v)));
                    }
                    $tc->appendChild($item);
                }
                $infoFactura->appendChild($tc);
            } elseif ($tag === 'pagos' && is_array($value)) {
                $pagos = $doc->createElement('pagos');
                foreach ($value as $p) {
                    if (isset($p['pago']) && is_array($p['pago'])) {
                        $pago = $doc->createElement('pago');
                        foreach ($p['pago'] as $k => $v) {
                            $pago->appendChild($doc->createElement($k, htmlspecialchars((string)$v)));
                        }
                        $pagos->appendChild($pago);
                    }
                }
                $infoFactura->appendChild($pagos);
            } else {
                $infoFactura->appendChild($doc->createElement($tag, htmlspecialchars((string)$value)));
            }
        }
        $root->appendChild($infoFactura);
 
        // detalles
        if (!empty($data['detalles'])) {
            $detalles = $doc->createElement('detalles');
            foreach ($data['detalles'] as $detalle) {
                $detalleNode = $doc->createElement('detalle');
                foreach ($detalle as $tag => $value) {
                    if ($tag === 'impuestos' && is_array($value)) {
                        $impsNode = $doc->createElement('impuestos');
                        foreach ($value as $imp) {
                            $impNode = $doc->createElement('impuesto');
                            foreach ($imp as $k => $v) {
                                $impNode->appendChild($doc->createElement($k, htmlspecialchars((string)$v)));
                            }
                            $impsNode->appendChild($impNode);
                        }
                        $detalleNode->appendChild($impsNode);
                    } else {
                        $detalleNode->appendChild($doc->createElement($tag, htmlspecialchars((string)$value)));
                    }
                }
                $detalles->appendChild($detalleNode);
            }
            $root->appendChild($detalles);
        }
 
        // infoAdicional
        if (!empty($data['infoAdicional'])) {
            $infoAdicional = $doc->createElement('infoAdicional');
            foreach ($data['infoAdicional'] as $campo) {
                $campoAdicional = $doc->createElement('campoAdicional', htmlspecialchars($campo['valor']));
                $campoAdicional->setAttribute('nombre', $campo['nombre']);
                $infoAdicional->appendChild($campoAdicional);
            }
            $root->appendChild($infoAdicional);
        }
 
        return $doc->saveXML();
    }
 
    // =========================================================================
    // VALIDAR XSD
    // =========================================================================
 
    public function validarContraXsd(string $xml): array
    {
        $doc = new DOMDocument();
        $doc->loadXML($xml);
 
        libxml_use_internal_errors(true);
        $valid  = $doc->schemaValidate($this->xsdPath);
        $errors = libxml_get_errors();
        libxml_clear_errors();
 
        return ['valid' => $valid, 'errors' => $errors];
    }
 
    // =========================================================================
    // FIRMAR XML — XAdES-BES compatible con SRI Ecuador / UANATACA
    // =========================================================================
 
    public function firmarXml(string $xml, string $p12Path, string $password): string
    {
        $p12Data = file_get_contents($p12Path);
        if ($p12Data === false) {
            throw new Exception("No se pudo leer P12: $p12Path");
        }
        if (!openssl_pkcs12_read($p12Data, $certs, $password)) {
            throw new Exception("Error leyendo P12: " . openssl_error_string());
        }
 
        $privateKey = $certs['pkey'];
        $certPem    = $certs['cert'];
        $certBase64 = preg_replace('/-----[^-]+-----|[\r\n\s]/', '', $certPem);
 
        $certDer    = base64_decode($certBase64);
        $certDigest = base64_encode(hash('sha256', $certDer, true));
        $x509info   = openssl_x509_parse($certPem);
        $serialHex  = $x509info['serialNumberHex'] ?? dechex((int)($x509info['serialNumber'] ?? 0));
        $serialDec  = $this->hexToDec(ltrim($serialHex, '0x'));
        $issuerDN   = $this->buildIssuerDN($x509info['issuer'] ?? []);
 
        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->preserveWhiteSpace = true;
        $doc->formatOutput       = false;
        $doc->loadXML($xml);
        $root = $doc->documentElement;
        if (!$root->hasAttribute('id')) {
            $root->setAttribute('id', 'comprobante');
        }
 
        $uuid             = $this->generateUUID();
        $signatureId      = 'xmldsig-' . $uuid;
        $refDocId         = $signatureId . '-ref0';
        $signedPropsId    = $signatureId . '-signedprops';
        $signatureValueId = $signatureId . '-sigvalue';
 
        $now         = new DateTime();
        $signingTime = $now->format('Y-m-d\TH:i:s')
                     . '.' . str_pad($now->format('v'), 3, '0', STR_PAD_LEFT)
                     . $now->format('P');
 
        $docDigest = base64_encode(hash('sha256', $root->C14N(false, false), true));
 
        // Calcular digest de SignedProperties en documento temporal con misma jerarquía
        $spFragXml = '<?xml version="1.0" encoding="UTF-8"?>'
            . '<ds:Signature xmlns:ds="http://www.w3.org/2000/09/xmldsig#" Id="' . $signatureId . '">'
            .   '<ds:Object>'
            .     '<xades:QualifyingProperties xmlns:xades="http://uri.etsi.org/01903/v1.3.2#" xmlns:xades141="http://uri.etsi.org/01903/v1.4.1#" Target="#' . $signatureId . '">'
            .       '<xades:SignedProperties Id="' . $signedPropsId . '">'
            .         '<xades:SignedSignatureProperties>'
            .           '<xades:SigningTime>' . $signingTime . '</xades:SigningTime>'
            .           '<xades:SigningCertificate>'
            .             '<xades:Cert>'
            .               '<xades:CertDigest>'
            .                 '<ds:DigestMethod Algorithm="http://www.w3.org/2001/04/xmlenc#sha256"/>'
            .                 '<ds:DigestValue>' . $certDigest . '</ds:DigestValue>'
            .               '</xades:CertDigest>'
            .               '<xades:IssuerSerial>'
            .                 '<ds:X509IssuerName>' . htmlspecialchars($issuerDN, ENT_XML1) . '</ds:X509IssuerName>'
            .                 '<ds:X509SerialNumber>' . $serialDec . '</ds:X509SerialNumber>'
            .               '</xades:IssuerSerial>'
            .             '</xades:Cert>'
            .           '</xades:SigningCertificate>'
            .         '</xades:SignedSignatureProperties>'
            .         '<xades:SignedDataObjectProperties>'
            .           '<xades:DataObjectFormat ObjectReference="#' . $refDocId . '">'
            .             '<xades:Description>FIRMA DIGITAL SRI</xades:Description>'
            .             '<xades:MimeType>text/xml</xades:MimeType>'
            .             '<xades:Encoding>UTF-8</xades:Encoding>'
            .           '</xades:DataObjectFormat>'
            .         '</xades:SignedDataObjectProperties>'
            .       '</xades:SignedProperties>'
            .     '</xades:QualifyingProperties>'
            .   '</ds:Object>'
            . '</ds:Signature>';
 
        $spFragDoc = new DOMDocument('1.0', 'UTF-8');
        $spFragDoc->loadXML($spFragXml);
        $spXpath = new DOMXPath($spFragDoc);
        $spXpath->registerNamespace('xades', 'http://uri.etsi.org/01903/v1.3.2#');
        $spNode            = $spXpath->query('//xades:SignedProperties')->item(0);
        $signedPropsDigest = base64_encode(hash('sha256', $spNode->C14N(false, false), true));
 
        // Insertar Signature con PLACEHOLDER
        $sigXml = '<ds:Signature xmlns:ds="http://www.w3.org/2000/09/xmldsig#" Id="' . $signatureId . '">'
            .   '<ds:SignedInfo>'
            .     '<ds:CanonicalizationMethod Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315"/>'
            .     '<ds:SignatureMethod Algorithm="http://www.w3.org/2001/04/xmldsig-more#rsa-sha256"/>'
            .     '<ds:Reference Id="' . $refDocId . '" URI="#comprobante">'
            .       '<ds:Transforms><ds:Transform Algorithm="http://www.w3.org/2000/09/xmldsig#enveloped-signature"/></ds:Transforms>'
            .       '<ds:DigestMethod Algorithm="http://www.w3.org/2001/04/xmlenc#sha256"/>'
            .       '<ds:DigestValue>' . $docDigest . '</ds:DigestValue>'
            .     '</ds:Reference>'
            .     '<ds:Reference Type="http://uri.etsi.org/01903#SignedProperties" URI="#' . $signedPropsId . '">'
            .       '<ds:DigestMethod Algorithm="http://www.w3.org/2001/04/xmlenc#sha256"/>'
            .       '<ds:DigestValue>' . $signedPropsDigest . '</ds:DigestValue>'
            .     '</ds:Reference>'
            .   '</ds:SignedInfo>'
            .   '<ds:SignatureValue Id="' . $signatureValueId . '">PLACEHOLDER</ds:SignatureValue>'
            .   '<ds:KeyInfo><ds:X509Data><ds:X509Certificate>' . $certBase64 . '</ds:X509Certificate></ds:X509Data></ds:KeyInfo>'
            .   '<ds:Object>'
            .     '<xades:QualifyingProperties xmlns:xades="http://uri.etsi.org/01903/v1.3.2#" xmlns:xades141="http://uri.etsi.org/01903/v1.4.1#" Target="#' . $signatureId . '">'
            .       '<xades:SignedProperties Id="' . $signedPropsId . '">'
            .         '<xades:SignedSignatureProperties>'
            .           '<xades:SigningTime>' . $signingTime . '</xades:SigningTime>'
            .           '<xades:SigningCertificate>'
            .             '<xades:Cert>'
            .               '<xades:CertDigest>'
            .                 '<ds:DigestMethod Algorithm="http://www.w3.org/2001/04/xmlenc#sha256"/>'
            .                 '<ds:DigestValue>' . $certDigest . '</ds:DigestValue>'
            .               '</xades:CertDigest>'
            .               '<xades:IssuerSerial>'
            .                 '<ds:X509IssuerName>' . htmlspecialchars($issuerDN, ENT_XML1) . '</ds:X509IssuerName>'
            .                 '<ds:X509SerialNumber>' . $serialDec . '</ds:X509SerialNumber>'
            .               '</xades:IssuerSerial>'
            .             '</xades:Cert>'
            .           '</xades:SigningCertificate>'
            .         '</xades:SignedSignatureProperties>'
            .         '<xades:SignedDataObjectProperties>'
            .           '<xades:DataObjectFormat ObjectReference="#' . $refDocId . '">'
            .             '<xades:Description>FIRMA DIGITAL SRI</xades:Description>'
            .             '<xades:MimeType>text/xml</xades:MimeType>'
            .             '<xades:Encoding>UTF-8</xades:Encoding>'
            .           '</xades:DataObjectFormat>'
            .         '</xades:SignedDataObjectProperties>'
            .       '</xades:SignedProperties>'
            .     '</xades:QualifyingProperties>'
            .   '</ds:Object>'
            . '</ds:Signature>';
 
        $sigDoc = new DOMDocument('1.0', 'UTF-8');
        $sigDoc->loadXML($sigXml);
        $root->appendChild($doc->importNode($sigDoc->documentElement, true));
 
        // Canonicalizar SignedInfo DESDE el documento real para obtener namespaces correctos
        $xpath = new DOMXPath($doc);
        $xpath->registerNamespace('ds', 'http://www.w3.org/2000/09/xmldsig#');
        $signedInfoNode = $xpath->query('//ds:Signature[@Id="' . $signatureId . '"]/ds:SignedInfo')->item(0);
 
        if ($signedInfoNode === null) {
            throw new Exception("No se encontró ds:SignedInfo tras insertar la firma.");
        }
 
        $pkeyRes = openssl_pkey_get_private($privateKey);
        if ($pkeyRes === false) {
            throw new Exception("Error cargando clave privada: " . openssl_error_string());
        }
        if (!openssl_sign($signedInfoNode->C14N(false, false), $rawSig, $pkeyRes, OPENSSL_ALGO_SHA256)) {
            throw new Exception("Error al firmar: " . openssl_error_string());
        }
 
        $sigValueNode = $xpath->query(
            '//ds:Signature[@Id="' . $signatureId . '"]/ds:SignatureValue[@Id="' . $signatureValueId . '"]'
        )->item(0);
 
        if ($sigValueNode === null) {
            throw new Exception("No se encontró ds:SignatureValue.");
        }
 
        $sigValueNode->nodeValue = base64_encode($rawSig);
 
        return $doc->saveXML();
    }
 
    // =========================================================================
    // ENVIAR AL SRI (Recepción)
    // =========================================================================
 
    public function enviarComprobanteSri(string $xmlFirmado, string $ambiente = 'pruebas'): array
    {
        $url = $ambiente === 'produccion'
            ? 'https://cel.sri.gob.ec/comprobantes-electronicos-ws/RecepcionComprobantesOffline'
            : 'https://celcer.sri.gob.ec/comprobantes-electronicos-ws/RecepcionComprobantesOffline';
 
        $xmlBase64   = base64_encode($xmlFirmado);
        $soapRequest = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"
                  xmlns:ec="http://ec.gob.sri.ws.recepcion">
  <soapenv:Header/>
  <soapenv:Body>
    <ec:validarComprobante>
      <xml>' . $xmlBase64 . '</xml>
    </ec:validarComprobante>
  </soapenv:Body>
</soapenv:Envelope>';
 
        try {
            $client   = new \GuzzleHttp\Client();
            $response = $client->request('POST', $url, [
                'headers' => ['Content-Type' => 'text/xml; charset=utf-8', 'SOAPAction' => ''],
                'body'    => $soapRequest,
                'verify'  => false,
                'timeout' => 20,
                'curl'    => [CURLOPT_SSL_VERIFYPEER => false, CURLOPT_SSL_VERIFYHOST => false],
            ]);
            sleep(3);
 
            $body = (string) $response->getBody();
            $dom  = new DOMDocument();
            if (!@$dom->loadXML($body)) {
                return ['estado' => 'ERROR', 'mensaje' => 'Respuesta XML inválida', 'mensajes' => []];
            }
 
            $xpath      = new DOMXPath($dom);
            $estadoNode = $xpath->query('//RespuestaRecepcionComprobante/estado');
            $estado     = $estadoNode->length > 0 ? trim($estadoNode->item(0)->textContent) : 'DESCONOCIDO';
 
            $mensajes = [];
            foreach ($xpath->query('//RespuestaRecepcionComprobante/comprobantes/comprobante/mensajes/mensaje') as $msg) {
                $get        = fn($t) => trim($xpath->query($t, $msg)->item(0)?->textContent ?? '');
                $mensajes[] = [
                    'identificador'        => $get('identificador'),
                    'mensaje'              => $get('mensaje'),
                    'informacionAdicional' => $get('informacionAdicional'),
                    'tipo'                 => $get('tipo'),
                ];
            }
 
            return ['estado' => $estado, 'mensajes' => $mensajes];
 
        } catch (\Throwable $e) {
            return ['estado' => 'ERROR', 'mensaje' => $e->getMessage(), 'mensajes' => []];
        }
    }
 
    // =========================================================================
    // AUTORIZAR (Consulta)
    // =========================================================================
 
    public function autorizarSri(string $claveAcceso, string $ambiente = 'pruebas'): array
    {
        $url = $ambiente === 'produccion'
            ? 'https://cel.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantesOffline'
            : 'https://celcer.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantesOffline';
 
        $soapRequest = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"
                  xmlns:ec="http://ec.gob.sri.ws.autorizacion">
  <soapenv:Header/>
  <soapenv:Body>
    <ec:autorizacionComprobante>
      <claveAccesoComprobante>' . $claveAcceso . '</claveAccesoComprobante>
    </ec:autorizacionComprobante>
  </soapenv:Body>
</soapenv:Envelope>';
 
        try {
            $client   = new \GuzzleHttp\Client();
            $response = $client->request('POST', $url, [
                'headers' => ['Content-Type' => 'text/xml; charset=utf-8', 'SOAPAction' => ''],
                'body'    => $soapRequest,
                'verify'  => false,
                'timeout' => 15,
                'curl'    => [CURLOPT_SSL_VERIFYPEER => false, CURLOPT_SSL_VERIFYHOST => false],
            ]);
 
            $body = (string) $response->getBody();
            $dom  = new DOMDocument();
            if (!@$dom->loadXML($body)) {
                return ['estado' => 'ERROR', 'mensaje' => 'Respuesta XML inválida', 'mensajes' => [], 'xml' => null];
            }
 
            $xpath = new DOMXPath($dom);
 
            // SRI aún no tiene la clave registrada
            $numNode = $xpath->query('//numeroComprobantes');
            if ($numNode->length > 0 && trim($numNode->item(0)->textContent) === '0') {
                return ['estado' => 'EN_PROCESO', 'mensaje' => 'SRI aún no tiene registrada la clave (numeroComprobantes=0)', 'mensajes' => [], 'xml' => null];
            }
 
            $authNodes = $xpath->query('//autorizacion');
            if ($authNodes->length === 0) {
                return ['estado' => 'EN_PROCESO', 'mensaje' => 'No se encontró nodo <autorizacion>', 'mensajes' => [], 'xml' => null];
            }
 
            $auth     = $authNodes->item(0);
            $get      = fn($t) => trim($xpath->query($t, $auth)->item(0)?->textContent ?? '');
            $estado   = $get('estado');
            $mensajes = [];
 
            foreach ($xpath->query('mensajes/mensaje', $auth) as $msg) {
                $gs         = fn($t) => trim($xpath->query($t, $msg)->item(0)?->textContent ?? '');
                $mensajes[] = [
                    'identificador'        => $gs('identificador'),
                    'mensaje'              => $gs('mensaje'),
                    'informacionAdicional' => $gs('informacionAdicional'),
                    'tipo'                 => $gs('tipo'),
                ];
            }
 
            // textContent ya decodifica &lt;&gt; automáticamente
            $comprobanteNode = $xpath->query('comprobante', $auth)->item(0);
            $xmlComprobante  = $comprobanteNode ? trim($comprobanteNode->textContent) : null;
            if (empty($xmlComprobante)) $xmlComprobante = null;
 
            return [
                'estado'             => $estado,
                'numeroAutorizacion' => $get('numeroAutorizacion'),
                'fechaAutorizacion'  => $get('fechaAutorizacion'),
                'ambiente'           => $get('ambiente'),
                'mensajes'           => $mensajes,
                'xml'                => $xmlComprobante,
            ];
 
        } catch (\Throwable $e) {
            return ['estado' => 'ERROR', 'mensaje' => 'Excepción: ' . $e->getMessage(), 'mensajes' => [], 'xml' => null];
        }
    }
 
    // =========================================================================
    // HELPERS PRIVADOS
    // =========================================================================
 
    private function generateUUID(): string
    {
        $data    = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
 
    private function hexToDec(string $hex): string
    {
        $hex = ltrim(strtolower($hex), '0');
        if ($hex === '') return '0';
 
        if (function_exists('bcadd')) {
            $result = '0';
            foreach (str_split($hex) as $c) {
                $result = bcadd(bcmul($result, '16'), (string) hexdec($c));
            }
            return $result;
        }
        return base_convert($hex, 16, 10);
    }
 
    private function buildIssuerDN(array $issuer): string
    {
        $parts = [];
 
        if (!empty($issuer['organizationIdentifier'])) {
            $oi      = $issuer['organizationIdentifier'];
            $hex     = '0c' . str_pad(dechex(strlen($oi)), 2, '0', STR_PAD_LEFT) . bin2hex($oi);
            $parts[] = "2.5.4.97=#" . $hex;
        } elseif (!empty($issuer['O']) && stripos($issuer['O'], 'UANATACA') !== false) {
            $parts[] = "2.5.4.97=#0c0f56415445532d413636373231343939";
        }
 
        if (!empty($issuer['CN'])) $parts[] = "CN=" . $issuer['CN'];
        if (!empty($issuer['OU'])) $parts[] = "OU=" . $issuer['OU'];
        if (!empty($issuer['O']))  $parts[] = "O="  . $issuer['O'];
        if (!empty($issuer['L']))  $parts[] = "L="  . $issuer['L'];
        if (!empty($issuer['C']))  $parts[] = "C="  . $issuer['C'];
 
        return implode(',', $parts);
    }
}