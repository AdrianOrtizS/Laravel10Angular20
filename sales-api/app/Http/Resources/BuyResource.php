<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BuyResource extends JsonResource
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
            'id'                => $this->resource->id,
            'numero_factura'    => $this->resource->numero_factura,
            'type_pay'          => $this->resource->type_pay,     
            'type_doc'          => $this->resource->type_doc,     
            'subtotal'          => $this->resource->subtotal,
            'fecha_ingreso'     => $this->resource->fecha_ingreso,
            'total'             => $this->resource->total,
            'iva'               => $this->resource->iva ? $this->resource->iva : 0,
            'iva0'              => $this->resource->iva0 ? $this->resource->iva0 : 0,
            'ice'               => $this->resource->ice ? $this->resource->ice : 0,
            'created_at'        => $this->resource->created_at->format('Y-m-d h:i A'),
            'state'             => $this->resource->state,
            'supplier'          => $this->resource->supplier ? [
                            'id_supplier'   => $this->resource->supplier->id,
                            'name'          => $this->resource->supplier->name,
                            'num_identificador' => $this->resource->supplier->num_identificador,
                            'address'       => $this->resource->supplier->address,
                            'phone'         => $this->resource->supplier->phone,
            ]:NULL,
            'details' => $this->resource->details->map(function($details){
                return [
                    'id'          => $details->id,
                    'product'     => [
                        'id_product'    => $details->product->id,
                        'name'          => $details->product->name,
                        'codigo'        => $details->product->cod_pro,
                        'tarifa_iva'    => $details->product->tarifa_iva,
                    ],
                    'quantity'  => $details->quantity,
                    'price'     => $details->price,
                    'subtotal'  => $details->subtotal,
                    'iva'       => $details->iva,
                    'ice'       => $details->ice,
                ];
            }),
            'pays' => $this->resource->pays->map(function($pays){
                return [
                    'id'    => $pays->id,
                    'num_comprobante_abono'  => $pays->num_comprobante_abono,
                    'valor_abono'   => round($pays->valor_abono, 2),
                    'imagen'        => $pays->imagen ? 
                        env('APP_URL').'storage/'.$pays->imagen
                    : NULL,
                    'fecha' => $pays->created_at
                ];
            }),
            'total_abonos'      => round($this->resource->pays->sum('valor_abono'), 2),
            'cantidad_abonos'   => $this->resource->pays->count(),
            'saldo'             => round($this->resource->total - $this->resource->pays->sum('valor_abono'), 2)
        ];
    }
}
