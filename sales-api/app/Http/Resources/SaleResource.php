<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);

        return [
            'idx'                   => $this->resource->id,
            'version'               => '2.1.0',   // versión vigente del XSD
            'id' => 'comprobante',  // atributo requerido en el nodo <factura>
            'infoTributaria' => [
                'ambiente'          => '1',  // 1 = pruebas, 2 = producción
                'tipoEmision'       => '1',  // 1 = Normal (lo habitual)--- 2 = Indisponibilidad del sistema 
                'razonSocial'       => 'Joel Eduardo Luna Moya',
                'nombreComercial'   => 'Parabrisas Libertadores', // opcional pero recomendado
                'ruc'               => '1718251638001',
                'claveAcceso'       => $this->resource->clave_acceso, // 49 dígitos generados
                'codDoc'            => '01',   // 01 = Factura
                'estab'             => $this->resource->establecimiento,
                'ptoEmi'            => $this->resource->punto_emision,
                'secuencial'        => $this->resource->secuencial,
                'dirMatriz'         => 'Av. Los Libertadores Oe4-131 y pasaje Viracocha',
            ],
            'infoFactura' => [
                'fechaEmision'      => $this->resource->created_at->format('d/m/Y'),
                'dirEstablecimiento' => 'Av. Los Libertadores Oe4-131 y pasaje Viracocha',
                'contribuyenteEspecial' => '5368',          // opcional
                'obligadoContabilidad' => 'NO',             // obligatorio en muchos casos
                'tipoIdentificacionComprador' => '05',      // 05 = cédula, 04 = RUC
                'razonSocialComprador' => $this->resource->customer->name,
                'identificacionComprador' => $this->resource->customer->num_identificador,
                'totalSinImpuestos' => $this->resource->subtotal,
                'totalDescuento'    => $this->resource->discount,
                'totalConImpuestos' => [
                    [
                        'codigo'            => '2',  // IVA
                        'codigoPorcentaje'  => '4',  // 15%
                        'baseImponible'     => $this->resource->subtotal - $this->resource->discount,
                        'valor'             => $this->resource->iva,         // valor de iva
                    ]
                ],
                'propina'       => '0.00',
                'importeTotal'  => $this->resource->total,
                'moneda'        => 'DOLAR',
            ],
            'detalles' => $this->resource->details->map(function($details){
                return [
                    'codigoPrincipal'   => 'P001',
                    'descripcion'       => $details->product->name,
                    'cantidad'          => $details->quantity,
                    'precioUnitario'    => $details->price,
                    'descuento'         => $details->discount,
                    'precioTotalSinImpuesto' => $details->subtotal - $details->discount,
                    // 'impuestos' => [
                    //     [
                    //         'codigo'            => '2',  //  IVA
                    //         'codigoPorcentaje'  => '4',  //  4 -> 15%
                    //         'tarifa'            => '15',
                    //         'baseImponible'     => $details->subtotal - $details->discount,
                    //         'valor'             => round((($details->subtotal - $details->discount)*15)/100, 2) ,
                    //     ]
                    // ]
                ];
            }),
            'infoAdicional' => [
                [
                    'nombre' => 'email',
                    'valor' => 'cliente@correo.com'
                ]
            ],
            'customer'  => $this->resource->customer ? [
                            'id_customer'   => $this->resource->customer->id,
                            'name'     => $this->resource->customer->name,
                            'num_identificador' => $this->resource->customer->num_identificador,
                            'address' => $this->resource->customer->address,
                            'phone' => $this->resource->customer->phone,
            ]:NULL,
            // 'type_receivable'          => $this->resource->type_receivable,
            'form_pay'   => $this->resource->form_pay,
            'receivables' => $this->resource->receivables->map(function($receivables){
                return [
                    'id'  => $receivables->id,
                    'num_comprobante_abono'  => $receivables->num_comprobante_abono,
                    'num_comprobante_documento'  => $receivables->num_comprobante_documento,
                    'valor_abono'     => round($receivables->valor_abono, 2),
                    'observacion'     => $receivables->observacion,
                    'fecha' => $receivables->created_at
                ];
            }),
            'discount' => $this->resource->discount,
            'iva0' => $this->resource->iva0,
            'iva' => $this->resource->iva,
            'numero_factura' => $this->resource->numero_factura,
            // 'total_factura' => round($this->resource->total, 2),
            'total_abonos' => round($this->resource->receivables->sum('valor_abono'), 2),
            'cantidad_abonos' => $this->resource->receivables->count(),
            'saldo' => round($this->resource->total - $this->resource->receivables->sum('valor_abono'), 2),
            'estado_sri' => $this->resource->estado_sri,
            'error_no_autorizada' => $this->resource->error_no_autorizada
        ];

    }

}
