<?php

namespace App\Events;

use App\Models\InspeccionCalidad;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Evento: Defecto Detectado
 * 
 * Se dispara cuando una inspección de calidad detecta defectos significativos.
 * Alerta a gerentes de calidad y producción.
 */
class DefectoDetectado implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public InspeccionCalidad $inspeccion;
    public int $cantidadDefectos;
    public float $porcentajeDefectos;

    /**
     * Create a new event instance.
     */
    public function __construct(InspeccionCalidad $inspeccion, int $cantidadDefectos, float $porcentajeDefectos)
    {
        $this->inspeccion = $inspeccion;
        $this->cantidadDefectos = $cantidadDefectos;
        $this->porcentajeDefectos = $porcentajeDefectos;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('calidad'),
            new Channel('alertas'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'defecto.detectado';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->inspeccion->id,
            'tipo_inspeccion' => $this->inspeccion->tipo_inspeccion,
            'resultado' => $this->inspeccion->resultado,
            'lote_codigo' => $this->inspeccion->loteProduccion->codigo_lote ?? null,
            'producto' => $this->inspeccion->loteProduccion->productoTerminado->nombre_producto ?? null,
            'cantidad_defectos' => $this->cantidadDefectos,
            'porcentaje_defectos' => $this->porcentajeDefectos,
            'defectos_encontrados' => $this->inspeccion->defectos_encontrados,
            'inspector' => $this->inspeccion->inspector->name ?? null,
            'fecha_inspeccion' => $this->inspeccion->fecha_inspeccion,
            'observaciones' => $this->inspeccion->observaciones,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
