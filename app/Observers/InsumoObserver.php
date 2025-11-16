<?php

namespace App\Observers;

use App\Models\Insumo;
use App\Models\Alerta;

class InsumoObserver
{
    /**
     * Handle the Insumo "updated" event.
     */
    public function updated(Insumo $insumo): void
    {
        // Verificar si el stock ha cambiado y ha caído por debajo del mínimo
        if ($insumo->isDirty('stock_actual')) {
            if ($insumo->stock_actual < $insumo->stock_minimo) {
                $this->generarAlertaStockBajo($insumo);
            }
        }
    }

    /**
     * Genera una alerta de stock bajo para un insumo.
     */
    protected function generarAlertaStockBajo(Insumo $insumo): void
    {
        // Evitar duplicados: buscar si ya existe una alerta de stock bajo no leída para este insumo
        $alertaExistente = Alerta::where('relacion_tipo', Insumo::class)
            ->where('relacion_id', $insumo->id)
            ->where('tipo_alerta', 'Stock Bajo')
            ->where('leida', false)
            ->exists();

        if (!$alertaExistente) {
            Alerta::create([
                'tipo_alerta' => 'Stock Bajo',
                'mensaje' => "El stock del insumo '{$insumo->nombre}' (ID: {$insumo->id}) ha caído por debajo del mínimo. Stock actual: {$insumo->stock_actual}, Mínimo: {$insumo->stock_minimo}.",
                'nivel_urgencia' => 'Alto',
                'relacion_id' => $insumo->id,
                'relacion_tipo' => Insumo::class,
                // Opcional: Asignar a un usuario o rol específico, por ejemplo, 'Gerente' o 'Compras'
                // 'usuario_destino_id' => ... 
            ]);
        }
    }
}
