<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\ProductoTerminado;

class ProductoTerminadoActualizado implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * El producto terminado que ha sido actualizado.
     *
     * @var \App\Models\ProductoTerminado
     */
    public ProductoTerminado $producto;

    /**
     * El tipo de cambio realizado.
     *
     * @var string
     */
    public string $tipoCambio;

    /**
     * Create a new event instance.
     */
    public function __construct(ProductoTerminado $producto, string $tipoCambio = 'actualizada')
    {
        $this->producto = $producto;
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
            new PrivateChannel('productos'),
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'ProductoTerminadoActualizado';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'producto' => $this->producto->load('categoria'),
            'tipo_cambio' => $this->tipoCambio,
            'stock_actual' => $this->producto->stock_actual,
            'certificado_compostaje' => $this->producto->certificado_compostaje,
            'fecha_vencimiento' => $this->producto->fecha_vencimiento,
            'timestamp' => now()->toISOString(),
        ];
    }
}