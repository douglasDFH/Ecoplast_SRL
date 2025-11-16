<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Maquinaria;

class MaquinariaActualizada implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * La máquina que ha sido actualizada.
     *
     * @var \App\Models\Maquinaria
     */
    public Maquinaria $maquinaria;

    /**
     * El tipo de cambio realizado.
     *
     * @var string
     */
    public string $tipoCambio;

    /**
     * Create a new event instance.
     */
    public function __construct(Maquinaria $maquinaria, string $tipoCambio = 'actualizada')
    {
        $this->maquinaria = $maquinaria;
        $this->tipoCambio = $tipoCambio;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('maquinaria'),
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'MaquinariaActualizada';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'maquina' => $this->maquinaria->load('tipo'),
            'tipo_cambio' => $this->tipoCambio,
            'estado_actual' => $this->maquinaria->estado_actual,
            'oee_actual' => $this->maquinaria->calcularOEE(),
            'timestamp' => now()->toISOString(),
        ];
    }
}