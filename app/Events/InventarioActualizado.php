<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Insumo;

class InventarioActualizado implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * El insumo cuyo inventario ha sido actualizado.
     *
     * @var \App\Models\Insumo
     */
    public Insumo $insumo;

    /**
     * El tipo de cambio realizado.
     *
     * @var string
     */
    public string $tipoCambio;

    /**
     * Create a new event instance.
     */
    public function __construct(Insumo $insumo, string $tipoCambio = 'actualizada')
    {
        $this->insumo = $insumo;
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
            new PrivateChannel('inventario'),
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'InventarioActualizado';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'insumo' => $this->insumo->load('categoria'),
            'tipo_cambio' => $this->tipoCambio,
            'stock_actual' => $this->insumo->stock_actual,
            'stock_minimo' => $this->insumo->stock_minimo,
            'estado_stock' => $this->insumo->stock_actual <= $this->insumo->stock_minimo ? 'bajo' : 'normal',
            'timestamp' => now()->toISOString(),
        ];
    }
}