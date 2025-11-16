<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MaquinariaResource extends JsonResource
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
            'nombre_maquina' => $this->nombre_maquina,
            'codigo_maquina' => $this->codigo_maquina,
            'modelo' => $this->modelo,
            'estado_actual' => $this->estado_actual,
        ];
    }
}
