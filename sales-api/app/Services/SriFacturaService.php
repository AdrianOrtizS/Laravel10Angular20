<?php

// Servicio Laravel para generar el XML de factura (SRI Ecuador) y validarlo contra XSD
// Requisitos (composer):
// composer require robrichards/xmlseclibs guzzlehttp/guzzle
// Extensiones PHP: xml, openssl

namespace App\Services;

use DOMDocument;
use Exception;
use RobRichards\XMLSecLibs\XMLSecurityDSig;
use RobRichards\XMLSecLibs\XMLSecurityKey;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
// use App\Services\SoapClient;

class SriFacturaService
{
    protected $xsdPath;

    public function __construct()
    {
        // Coloca aquí la ruta al XSD del SRI que vayas a usar (ej: resources/schemas/factura_v2.1.0.xsd)
        $this->xsdPath = storage_path('/app/schemas/factura_V1.1.0.xsd');
        // error_log($this->xsdPath);
    }

    public function generarClaveAcceso( 
                                        $fecha, 
                                        $tipoComprobante, 
                                        $ruc,
                                        $ambiente, 
                                        $estab,
                                        $pemis, 
                                        $secuencial, 
                                        $codigoNumerico,
                                        $tipoEmision 
         ) 
    {
        
        $fecha = date('dmY', strtotime($fecha)); // ddmmaaaa
        $ruc = str_pad($ruc, 13, '0', STR_PAD_LEFT);
        $estab = str_pad($estab, 3, '0', STR_PAD_LEFT);
        $pemis = str_pad($pemis, 3, '0', STR_PAD_LEFT);
        $secuencial = str_pad($secuencial, 9, '0', STR_PAD_LEFT);
        $codigoNumerico = str_pad($codigoNumerico, 8, '0', STR_PAD_LEFT);

        $clave = $fecha . $tipoComprobante . $ruc . $ambiente . $estab. $pemis . $secuencial . $codigoNumerico . $tipoEmision;
        if (strlen($clave) !== 48) {
            throw new \Exception("Clave de acceso incorrecta, debe tener 48 dígitos, actualmente tiene: " . strlen($clave));
        }

        $dv = $this->calcularDigitoVerificador($clave);
        $claveAcceso = $clave . $dv;

        $dvEnClave = substr($claveAcceso, -1); // último dígito de la clave

        if ($dv == $dvEnClave) {
            // echo "Clave válida. Dígito verificador correcto: $dv\n";
        } else {
            // echo "Clave inválida. DV esperado: $dv, DV en clave: $dvEnClave\n";
            // echo "Clave correcta debería ser: $clave$dv\n";
        }
        return $clave.$dv;
    }


    public function calcularDigitoVerificador($clave48)
    {
        // Convertir en array de dígitos
        $digitos = str_split($clave48);
        $digitos = array_map('intval', $digitos);

        // Multiplicadores de derecha a izquierda
        $multiplicadores = [2, 3, 4, 5, 6, 7];
        $digitos = array_reverse($digitos);

        $suma = 0;
        foreach ($digitos as $i => $d) {
            $suma += $d * $multiplicadores[$i % count($multiplicadores)];
        }

        $resto = $suma % 11;
        $dv = 11 - $resto;

        if ($dv == 11) $dv = 0;
        if ($dv == 10) $dv = 1;

        return $dv;
    }


    /**
     * Genera el XML según la estructura del SRI.
     * $data = arreglo con la información de la factura (infoTributaria, infoFactura, detalles, totales, etc.)
     */
    public function generarXml(array $data): string
    {
        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->formatOutput = true;

        // Root <factura id="comprobante" version="2.1.0">
        $root = $doc->createElement('factura');
        $root->setAttribute('id', 'comprobante');
        $root->setAttribute('version', $data['version'] ?? '2.1.0');
        $doc->appendChild($root);

        // infoTributaria
        $infoTrib = $doc->createElement('infoTributaria');
        foreach ($data['infoTributaria'] as $tag => $value) {
            $infoTrib->appendChild($doc->createElement($tag, htmlspecialchars($value)));
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
                    // cada item debe tener la llave 'pago' con sus campos
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
                                $impNode->appendChild($doc->createElement($k, htmlspecialchars($v)));
                            }
                            $impsNode->appendChild($impNode);
                        }
                        $detalleNode->appendChild($impsNode);
                    } else {
                        $detalleNode->appendChild($doc->createElement($tag, htmlspecialchars($value)));
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


    /**
     * Valida el XML generado contra el XSD del SRI
     */
    public function validarContraXsd(string $xml): array
    {
        $doc = new DOMDocument();
        $doc->loadXML($xml);
        
        libxml_use_internal_errors(true);

        $valid = $doc->schemaValidate($this->xsdPath);
        // Capturar errores generados
        $errors = libxml_get_errors();
        // Limpiar estado
        libxml_clear_errors();

        return [
            'valid' => $valid,
            'errors' => $errors,
        ];
    }

    /**
     * Firma el XML usando xmlseclibs (XMLDSig). Se requiere:
     * - archivo .p12 del certificado del contribuyente
     * - contraseña del .p12
     */
    public function firmarXml(string $xml, string $p12Path, string $p12Password): string
    {
        // Cargar XML
        $doc = new DOMDocument();
        $doc->loadXML($xml);

        // Extraer key y cert desde p12
        $pkcs12 = file_get_contents($p12Path);

        if ($pkcs12 === false) {
            // var_dump("No se pudo leer el archivo P12");
            throw new Exception('No se pudo leer el archivo P12');
        }
        if (!openssl_pkcs12_read($pkcs12, $certs, trim($p12Password))) {
            // var_dump("Error al leer P12 - contraseña incorrecta o archivo corrupto");
            throw new Exception('Error al leer P12 - contraseña incorrecta o archivo corrupto');
        }

        $privateKeyPem = $certs['pkey'];
        $certPem = $certs['cert'];
        
        // Crear objeto de firma
        $objDSig = new XMLSecurityDSig();
        $objDSig->setCanonicalMethod(XMLSecurityDSig::C14N);
        
        // Agregar referencia al documento entero
        $objDSig->addReference(
            $doc,
            XMLSecurityDSig::SHA1,
            array('http://www.w3.org/2000/09/xmldsig#enveloped-signature'),
            ['force_uri' => true]
        );

        // Crear key
        $objKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA1, array('type' => 'private'));
        $objKey->loadKey($privateKeyPem);

        // Firmar
        $objDSig->sign($objKey, $doc->documentElement);

        // Agregar certificado (Base64)
        $objDSig->add509Cert($certPem, true, false, array('subjectName' => true));

        // Retornar XML firmado
        return $doc->saveXML();
    }

    /**
     * Enviar XML firmado al WS del SRI (RecepcionComprobantesOffline)
     */
    public function enviarComprobanteSri($xmlFirmado, $ambiente = 'pruebas')
    {
        // Seleccionar endpoint según ambiente
        $wsdl = $ambiente === 'produccion'
            ? 'https://cel.sri.gob.ec/comprobantes-electronicos-ws/RecepcionComprobantesOffline?wsdl'
            : 'https://celcer.sri.gob.ec/comprobantes-electronicos-ws/RecepcionComprobantesOffline?wsdl';

        // Convertir XML a base64
        $xmlBase64 = base64_encode($xmlFirmado);

        // Construir cuerpo SOAP
        $soapRequest = <<<XML
                        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"
                                          xmlns:ec="http://ec.gob.sri.ws.recepcion">
                           <soapenv:Header/>
                           <soapenv:Body>
                              <ec:validarComprobante>
                                 <xml>{$xmlBase64}</xml>
                              </ec:validarComprobante>
                           </soapenv:Body>
                        </soapenv:Envelope>
                        XML;

        // Enviar usando Guzzle
        $client = new Client();

        $response = $client->request('POST', $wsdl, [
            'headers' => [
                'Content-Type' => 'text/xml; charset=UTF-8',
                'SOAPAction' => ''
            ],
            'body' => $soapRequest,
            'verify' => false // Deshabilitar verificación SSL si usas pruebas locales
        ]);

        $responseBody = (string)$response->getBody();

        try {
            $xmlResponse = simplexml_load_string($responseBody);
            $xmlResponse->registerXPathNamespace('soap', 'http://schemas.xmlsoap.org/soap/envelope/');
            $xmlResponse->registerXPathNamespace('ns2', 'http://ec.gob.sri.ws.recepcion');
            
            // Extraer información importante
            $estado = $xmlResponse->xpath('//soap:Body/ns2:validarComprobanteResponse/RespuestaRecepcionComprobante/estado');
            $comprobantes = $xmlResponse->xpath('//soap:Body/ns2:validarComprobanteResponse/RespuestaRecepcionComprobante/comprobantes');
            
            $resultado = [
                'estado'        =>  !empty($estado) ? (string)$estado[0] : 'DESCONOCIDO',
                'comprobantes'  =>  !empty($comprobantes) ? $comprobantes[0]->asXML() : null,
                'respuesta_completa' => $responseBody // Opcional: para debugging
            ];

            return $resultado;
                
        } catch (Exception $e) {
            return [
                'estado' => 'ERROR',
                'error' => $e->getMessage()
            ];
        }

        // return $response->getBody()->getContents();

    }



    public function autorizarSri(string $claveAcceso, $ambiente = 'pruebas'): array
    {
        $url = $ambiente === 'produccion'
            ? 'https://cel.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantesOffline'
            : 'https://celcer.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantesOffline';

        $soapRequest = <<<XML
                        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" 
                                          xmlns:ec="http://ec.gob.sri.ws.autorizacion">
                           <soapenv:Header/>
                           <soapenv:Body>
                              <ec:autorizacionComprobante>
                                 <claveAccesoComprobante>{$claveAcceso}</claveAccesoComprobante>
                              </ec:autorizacionComprobante>
                           </soapenv:Body>
                        </soapenv:Envelope>
                        XML;
        try{

            $client = new Client();
            
            $response = $client->request('POST', $url, [
                'headers' => [
                    'Content-Type' => 'text/xml; charset=utf-8',
                    'SOAPAction' => '',                    
                ],
                'body' => $soapRequest,
                'verify' => false,
                'timeout' => 15,
            ]);

            $statusCode = $response->getStatusCode();
            $body = (string) $response->getBody();

            if ($statusCode !== 200) {
                return [
                    'estado' => 'ERROR',
                    'mensaje' => 'Error en la conexión con el SRI. Código HTTP: ' . $statusCode,
                    'mensajes' => [],
                    'xml' => null,
                ];
            }

            $xml = @simplexml_load_string($body);

            if ($xml === false) {
                
                return [
                    'estado' => 'ERROR',
                    'mensaje' => 'Respuesta inválida del SRI (no es XML)',
                    'mensajes' => [],
                    'xml' => null,
                ];
            }


            $xml->registerXPathNamespace('ns', 'http://ec.gob.sri.ws.autorizacion');
            $autorizacion = $xml->xpath('//ns:autorizacion')[0] ?? null;


            if (!$autorizacion) {
                return [
                    'estado' => 'NO AUTORIZADO',
                    'mensaje' => 'No se encontró información de autorización en la respuesta del SRI.',
                    'mensajes' => [],
                    'xml' => null,
                ];
            }


            // Procesar mensajes
            $mensajes = [];
            if (isset($autorizacion->mensajes->mensaje)) {
                foreach ($autorizacion->mensajes->mensaje as $msg) {
                    $mensajes[] = [
                        'identificador' => (string)($msg->identificador ?? ''),
                        'mensaje' => (string)($msg->mensaje ?? ''),
                        'informacionAdicional' => (string)($msg->informacionAdicional ?? ''),
                        'tipo' => (string)($msg->tipo ?? ''),
                    ];
                }
            }

            return [
                'estado' => (string)($autorizacion->estado ?? 'DESCONOCIDO'),
                'fechaAutorizacion' => (string)($autorizacion->fechaAutorizacion ?? ''),
                'numeroAutorizacion' => (string)($autorizacion->numeroAutorizacion ?? ''),
                'mensajes' => $mensajes,
                'xml' => isset($autorizacion->comprobante) ? (string)$autorizacion->comprobante : null,
            ];

        } catch (Throwable $e) {
        
            return [
                'estado' => 'ERROR',
                'mensaje' => 'Excepción: ' . $e->getMessage(),
                'mensajes' => [],
                'xml' => null,
            ];
        }
    }


}

// USO: inyectar App\Services\SriFacturaService en un Controller y llamar generarXml, validarContraXsd, firmarXml y enviarAlSri
