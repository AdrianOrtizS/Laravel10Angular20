<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SriFacturaService;

class FacturaController extends Controller
{
    protected $sri;

    public function __construct(SriFacturaService $sri)
    {
        $this->sri = $sri;
    }

    public function generar()
    {
        // Datos de prueba mínimos (ajusta a tu necesidad)
        $data = [
            'version' => '2.1.0', // versión vigente del XSD
            'id' => 'comprobante', // atributo requerido en el nodo <factura>
            'infoTributaria' => [
                'ambiente' => '1', // 1 = pruebas, 2 = producción
                'tipoEmision' => '1',
                'razonSocial' => 'Mi Empresa S.A.',
                'nombreComercial' => 'Mi Marca', // opcional pero recomendado
                'ruc' => '1790012345001',
                'claveAcceso' => '1234567890123456789012345678901234567890123456789', // 49 dígitos generados
                'codDoc' => '01', // 01 = Factura
                'estab' => '001',
                'ptoEmi' => '001',
                'secuencial' => '000000123',
                'dirMatriz' => 'Av. Principal 456',
            ],
            'infoFactura' => [
                'fechaEmision' => date('d/m/Y'),
                'dirEstablecimiento' => 'Av. Siempre Viva 123',
                'contribuyenteEspecial' => '5368', // opcional
                'obligadoContabilidad' => 'SI', // obligatorio en muchos casos
                'tipoIdentificacionComprador' => '05', // 05 = cédula, 04 = RUC
                'razonSocialComprador' => 'Juan Pérez',
                'identificacionComprador' => '0102030405',
                'totalSinImpuestos' => '100.00',
                'totalDescuento' => '0.00',
                'totalConImpuestos' => [
                    [
                        'codigo' => '2', // IVA
                        'codigoPorcentaje' => '2', // 12%
                        'baseImponible' => '100.00',
                        'valor' => '12.00',
                    ]
                ],
                'propina' => '0.00',
                'importeTotal' => '112.00',
                'moneda' => 'DOLAR',
            ],
            'detalles' => [
                [
                    'codigoPrincipal' => 'P001',
                    'descripcion' => 'Producto A',
                    'cantidad' => '2',
                    'precioUnitario' => '50.00',
                    'descuento' => '0.00',
                    'precioTotalSinImpuesto' => '100.00',
                    'impuestos' => [
                        [
                            'codigo' => '2', // IVA
                            'codigoPorcentaje' => '2',
                            'tarifa' => '12',
                            'baseImponible' => '100.00',
                            'valor' => '12.00',
                        ]
                    ],
                ],
                [
                    'codigoPrincipal' => 'P002',
                    'descripcion' => 'Producto B',
                    'cantidad' => '3',
                    'precioUnitario' => '20.00',
                    'descuento' => '0.00',
                    'precioTotalSinImpuesto' => '60.00',
                    'impuestos' => [
                        [
                            'codigo' => '2', // IVA
                            'codigoPorcentaje' => '2',
                            'tarifa' => '12',
                            'baseImponible' => '60.00',
                            'valor' => '7.50',
                        ]
                    ],
                ]
            ],
            'infoAdicional' => [
                [
                    'nombre' => 'email',
                    'valor' => 'cliente@correo.com'
                ]
            ]
        ];


        // 1) Generar XML
        $xml = $this->sri->generarXml($data);

        // 2) Validar contra XSD
        $resultado = $this->sri->validarContraXsd($xml);

        if (!$resultado['valid']) {
            return response()->json([
                'status' => 'error',
                'errors' => $resultado['errors']
            ]);
        }

        // 3) Firmar (requiere .p12 válido)
        // $xmlFirmado = $this->sri->firmarXml(
        //     $xml,
        //     storage_path('certificados/mi_certificado.p12'), // pon tu certificado
        //     'mi_clave' // pon tu clave
        // );

        // 4) Enviar al SRI (pruebas)
        // $respuesta = $this->sri->enviarAlSri($xmlFirmado, false);

        return response($xml, 200)
            ->header('Content-Type', 'application/xml');
    }

}
