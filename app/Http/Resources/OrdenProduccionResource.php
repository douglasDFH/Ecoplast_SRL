<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrdenProduccionResource extends JsonResource
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
            'lote_produccion' => $this->lote_produccion,
            'cantidad_producir' => $this->cantidad_producir,
            'estado' => $this->estado,
            'prioridad' => $this->prioridad,
            'fecha_inicio_planificada' => $this->fecha_inicio_planificada->toIso8601String(),
            'fecha_fin_planificada' => $this->fecha_fin_planificada->toIso8601String(),
            'fecha_inicio_real' => $this->fecha_inicio_real?->toIso8601String(),
            'fecha_fin_real' => $this->fecha_fin_real?->toIso8601String(),
            'observaciones' => $this->observaciones,
            'producto_terminado' => new ProductoTerminadoResource($this->whenLoaded('productoTerminado')),
            'maquina' => new MaquinariaResource($this->whenLoaded('maquina')),
            'operador' => new UserResource($this->whenLoaded('operador')),
            'supervisor' => new UserResource($this->whenLoaded('supervisor')),
            'creado_por' => new UserResource($this->whenLoaded('creador')),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
