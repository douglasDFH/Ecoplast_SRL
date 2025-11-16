<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductoTerminadoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre_producto' => $this->nombre_producto,
            'codigo_producto' => $this->codigo_producto,
            'unidad_venta' => $this->unidad_venta,
        ];
    }
}
