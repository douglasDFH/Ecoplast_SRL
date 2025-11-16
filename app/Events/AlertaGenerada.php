<?php

namespace App\Events;

use App\Models\Alerta;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Evento: Alerta Generada
 * 
 * Se dispara cuando se crea una nueva alerta en el sistema.
 * Notifica en tiempo real a los usuarios conectados.
 */
class AlertaGenerada implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

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
            new Channel('alertas'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'alerta.generada';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->alerta->id,
            'tipo' => $this->alerta->tipo,
            'prioridad' => $this->alerta->prioridad,
            'titulo' => $this->alerta->titulo,
            'mensaje' => $this->alerta->mensaje,
            'estado' => $this->alerta->estado,
            'entidad_tipo' => $this->alerta->entidad_tipo,
            'entidad_id' => $this->alerta->entidad_id,
            'accion_recomendada' => $this->alerta->accion_recomendada,
            'usuario_id' => $this->alerta->usuario_id,
            'created_at' => $this->alerta->created_at,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
