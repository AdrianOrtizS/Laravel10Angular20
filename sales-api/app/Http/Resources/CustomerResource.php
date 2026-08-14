<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
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
            'id'    => $this->resource->id,
            'name'  => $this->resource->name,
            'num_identificador' => $this->resource->num_identificador,
            'email' => $this->resource->email,
            'phone' => $this->resource->phone,
            'address' => $this->resource->address,
            'tipo_identificador' => $this->resource->tipo_identificador,
            'state' => $this->resource->state,
            'created_at' => $this->resource->created_at
        ];
    }
}
