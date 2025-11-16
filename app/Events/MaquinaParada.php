<?php

namespace App\Events;

use App\Models\Maquinaria;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Evento: Máquina Parada
 * 
 * Se dispara cuando una máquina cambia a estado parada o avería.
 * Alerta inmediata a mantenimiento y producción.
 */
class MaquinaParada implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Maquinaria $maquina;
    public string $motivo;
    public ?string $estadoAnterior;

    /**
     * Create a new event instance.
     */
    public function __construct(Maquinaria $maquina, string $motivo, ?string $estadoAnterior = null)
    {
        $this->maquina = $maquina;
        $this->motivo = $motivo;
        $this->estadoAnterior = $estadoAnterior;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('maquinaria'),
            new Channel('alertas'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'maquina.parada';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->maquina->id,
            'codigo_maquina' => $this->maquina->codigo_maquina,
            'nombre_maquina' => $this->maquina->nombre_maquina,
            'estado_actual' => $this->maquina->estado_actual,
            'estado_anterior' => $this->estadoAnterior,
            'motivo' => $this->motivo,
            'tipo_maquina' => $this->maquina->tipo->nombre_tipo ?? null,
            'ubicacion' => $this->maquina->ubicacion,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
