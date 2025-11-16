<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Alerta;

class AlertaCreada implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * La alerta que ha sido creada.
     *
     * @var \App\Models\Alerta
     */
    public Alerta $alerta;

    /**
     * Create a new event instance.
     */
    public function __construct(Alerta $alerta)
    {
        $this->alerta = $alerta;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('alertas'),
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'AlertaCreada';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'alerta' => $this->alerta->load('destinatario'),
            'tipo' => $this->alerta->tipo_alerta,
            'urgencia' => $this->alerta->nivel_urgencia,
            'timestamp' => now()->toISOString(),
        ];
    }
}