<?php

namespace App\Observers;

use App\Models\OrdenProduccion;
use App\Models\ProductoTerminado;
use App\Events\OrdenProduccionActualizada;
use App\Jobs\VerificarRetrasoOrdenProduccion;
use Carbon\Carbon;

class OrdenProduccionObserver
{
    /**
     * Handle the OrdenProduccion "created" event.
     */
    public function created(OrdenProduccion $ordenProduccion): void
    {
        // Transmitir evento de nueva orden creada
        broadcast(new OrdenProduccionActualizada($ordenProduccion, 'creada'))->toOthers();

        // Programar una verificación de retraso si la orden no empieza a tiempo
        // TODO: Descomentar cuando fecha_inicio_planificada esté disponible
        // $delay = $ordenProduccion->fecha_inicio_planificada->addHours(1);
        // VerificarRetrasoOrdenProduccion::dispatch($ordenProduccion)->delay($delay);
    }

    /**
     * Handle the OrdenProduccion "updated" event.
     */
    public function updated(OrdenProduccion $ordenProduccion): void
    {
        // Lógica cuando el estado de la orden cambia
        if ($ordenProduccion->isDirty('estado')) {
            $estadoAnterior = $ordenProduccion->getOriginal('estado');
            $estadoNuevo = $ordenProduccion->estado;

            // Si la orden se finaliza, actualizar el stock del producto
            if ($estadoNuevo === 'Finalizada' && $estadoAnterior !== 'Finalizada') {
                $this->actualizarStockProductoTerminado($ordenProduccion);
            }

            // Transmitir el cambio de estado a través de websockets
            broadcast(new OrdenProduccionActualizada($ordenProduccion, 'actualizada'))->toOthers();
        }
    }

    /**
     * Handle the OrdenProduccion "deleted" event.
     */
    public function deleted(OrdenProduccion $ordenProduccion): void
    {
        // Transmitir evento de orden eliminada
        broadcast(new OrdenProduccionActualizada($ordenProduccion, 'eliminada'))->toOthers();
    }

    /**
     * Actualiza el stock del producto terminado asociado a la orden.
     */
    protected function actualizarStockProductoTerminado(OrdenProduccion $ordenProduccion): void
    {
        $producto = $ordenProduccion->productoTerminado;
        if ($producto) {
            // Sumar la cantidad producida al stock actual
            // Se asume que la cantidad real producida se registra en algún lugar,
            // por ahora usaremos la cantidad planificada como ejemplo.
            // Lo ideal sería sumar el total de 'cantidad_producida' de los 'registrosProduccion'
            $cantidadProducida = $ordenProduccion->registrosProduccion()->sum('cantidad_producida');
            
            if ($cantidadProducida > 0) {
                $producto->increment('stock_actual', $cantidadProducida);
            }
        }
    }
}
