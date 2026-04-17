<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'id' => $this->resource->id,
            'cod_pro' => $this->resource->cod_pro,
            'name' => $this->resource->name,
            'description' => $this->resource->description,
            'price' => $this->resource->price,
            'imagen' => $this->resource->imagen ? 
                        env('APP_URL').'storage/'.$this->resource->imagen
            : NULL,
            'state' => $this->resource->state,
            'created_at' => $this->resource->created_at->format('Y-m-d h:i:s'),
            'id_categorie' => $this->resource->id_categorie,
            'categorie' => $this->resource->categorie ? [
                'id' => $this->resource->categorie->id,
                'name' => $this->resource->categorie->name
            ]: NULL, 
            'stock' => $this->resource->stock.'', // Stock directo desde el join
            'stock_min' => $this->resource->stock_min.'',
            
            'tarifa_iva' => $this->resource->tarifa_iva

            // 'id_tarifa_iva' => $this->resource->id_tarifa_iva,
            // 'tarifa_iva' => $this->resource->tarifa_iva ? [
            //     'id' => $this->resource->tarifa_iva->id,
            //     'codigo' => $this->resource->tarifa_iva->codigo,
            //     'porcentaje' => $this->resource->tarifa_iva->porcentaje,
            // ]: NULL, 
        ];
    }
}
