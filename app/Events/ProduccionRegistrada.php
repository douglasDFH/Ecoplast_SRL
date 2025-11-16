<?php

namespace App\Events;

use App\Models\RegistroProduccion;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Evento: Producción Registrada
 * 
 * Se dispara cuando se registra un nuevo evento de producción.
 * Transmite datos en tiempo real al dashboard.
 */
class ProduccionRegistrada implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public RegistroProduccion $registro;

    /**
     * Create a new event instance.
     */
    public function __construct(RegistroProduccion $registro)
    {
        $this->registro = $registro;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('produccion'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'produccion.registrada';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->registro->id,
            'orden_produccion_id' => $this->registro->orden_produccion_id,
            'numero_orden' => $this->registro->ordenProduccion->numero_orden ?? null,
            'producto' => $this->registro->ordenProduccion->productoTerminado->nombre_producto ?? null,
            'cantidad_producida' => $this->registro->cantidad_producida,
            'cantidad_conforme' => $this->registro->cantidad_conforme,
            'cantidad_defectuosa' => $this->registro->cantidad_defectuosa,
            'productividad_unidades_hora' => $this->registro->productividad_unidades_hora,
            'turno' => $this->registro->turno->nombre_turno ?? null,
            'operador' => $this->registro->operador->name ?? null,
            'fecha_hora_inicio' => $this->registro->fecha_hora_inicio,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
