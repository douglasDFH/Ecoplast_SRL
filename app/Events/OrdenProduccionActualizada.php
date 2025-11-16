<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\OrdenProduccion;

class OrdenProduccionActualizada implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * La orden de producción que ha sido actualizada.
     *
     * @var \App\Models\OrdenProduccion
     */
    public OrdenProduccion $ordenProduccion;

    /**
     * El tipo de acción realizada ('creada', 'actualizada', 'eliminada').
     *
     * @var string
     */
    public string $accion;

    /**
     * Create a new event instance.
     */
    public function __construct(OrdenProduccion $ordenProduccion, string $accion)
    {
        $this->ordenProduccion = $ordenProduccion;
        $this->accion = $accion;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Se transmite en un canal privado para que solo los usuarios autenticados puedan escuchar.
        // El canal se nombra según la orden de producción específica.
        return [
            new PrivateChannel('ordenes-produccion'),
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'OrdenProduccionActualizada';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'accion' => $this->accion,
            'orden' => $this->ordenProduccion->load(['productoTerminado', 'maquina', 'operador', 'supervisor']),
        ];
    }
}
