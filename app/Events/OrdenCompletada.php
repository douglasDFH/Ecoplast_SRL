<?php

namespace App\Events;

use App\Models\OrdenProduccion;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Evento: Orden Completada
 * 
 * Se dispara cuando una orden de producción se marca como completada.
 * Notifica a los dashboards y sistemas de planificación.
 */
class OrdenCompletada implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public OrdenProduccion $orden;

    /**
     * Create a new event instance.
     */
    public function __construct(OrdenProduccion $orden)
    {
        $this->orden = $orden;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('ordenes-produccion'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'orden.completada';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->orden->id,
            'numero_orden' => $this->orden->numero_orden,
            'producto' => $this->orden->productoTerminado->nombre_producto ?? null,
            'cantidad_planificada' => $this->orden->cantidad_planificada,
            'cantidad_producida' => $this->orden->cantidad_producida,
            'cantidad_conforme' => $this->orden->cantidad_conforme,
            'cantidad_defectuosa' => $this->orden->cantidad_defectuosa,
            'porcentaje_cumplimiento' => $this->orden->cantidad_planificada > 0
                ? round(($this->orden->cantidad_producida / $this->orden->cantidad_planificada) * 100, 2)
                : 0,
            'porcentaje_calidad' => $this->orden->cantidad_producida > 0
                ? round(($this->orden->cantidad_conforme / $this->orden->cantidad_producida) * 100, 2)
                : 0,
            'maquina' => $this->orden->maquina->nombre_maquina ?? null,
            'fecha_inicio' => $this->orden->fecha_inicio,
            'fecha_fin' => $this->orden->fecha_fin,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
