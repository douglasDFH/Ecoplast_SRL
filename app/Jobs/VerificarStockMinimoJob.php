<?php

namespace App\Jobs;

use App\Models\Insumo;
use App\Models\Alerta;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job: Verificar Stock Mínimo de Insumos
 * 
 * Revisa todos los insumos activos y genera alertas
 * para aquellos que están en stock mínimo o crítico.
 */
class VerificarStockMinimoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Iniciando verificación de stock mínimo de insumos');

        $insumos = Insumo::where('activo', true)->get();
        $alertasGeneradas = 0;

        foreach ($insumos as $insumo) {
            // Stock crítico: menos del 50% del stock mínimo
            if ($insumo->stock_actual < ($insumo->stock_minimo * 0.5)) {
                $this->generarAlertaCritica($insumo);
                $alertasGeneradas++;
            }
            // Stock mínimo: entre 50% y 100% del stock mínimo
            elseif ($insumo->stock_actual <= $insumo->stock_minimo) {
                $this->generarAlertaBaja($insumo);
                $alertasGeneradas++;
            }
        }

        Log::info("Verificación de stock completada. Alertas generadas: {$alertasGeneradas}");
    }

    /**
     * Generar alerta crítica de stock
     */
    private function generarAlertaCritica(Insumo $insumo): void
    {
        // Verificar si ya existe alerta activa para este insumo
        $alertaExistente = Alerta::where('entidad_tipo', 'Insumo')
            ->where('entidad_id', $insumo->id)
            ->where('tipo', 'stock_critico')
            ->where('estado', 'activa')
            ->exists();

        if ($alertaExistente) {
            return; // No duplicar alertas
        }

        Alerta::create([
            'tipo' => 'stock_critico',
            'prioridad' => 'critica',
            'titulo' => 'Stock crítico: ' . $insumo->nombre_insumo,
            'mensaje' => sprintf(
                'El insumo "%s" (código: %s) tiene stock crítico. Stock actual: %.2f %s, Stock mínimo: %.2f %s. Se requiere compra urgente.',
                $insumo->nombre_insumo,
                $insumo->codigo_insumo,
                $insumo->stock_actual,
                $insumo->unidad_medida,
                $insumo->stock_minimo,
                $insumo->unidad_medida
            ),
            'entidad_tipo' => 'Insumo',
            'entidad_id' => $insumo->id,
            'accion_recomendada' => 'Generar orden de compra inmediata. Proveedor: ' . ($insumo->proveedor ?? 'No especificado'),
            'estado' => 'activa',
            'leida' => false,
        ]);

        Log::warning("Stock crítico detectado para insumo ID {$insumo->id}: {$insumo->nombre_insumo}");
    }

    /**
     * Generar alerta de stock bajo
     */
    private function generarAlertaBaja(Insumo $insumo): void
    {
        // Verificar si ya existe alerta activa
        $alertaExistente = Alerta::where('entidad_tipo', 'Insumo')
            ->where('entidad_id', $insumo->id)
            ->whereIn('tipo', ['stock_minimo', 'stock_critico'])
            ->where('estado', 'activa')
            ->exists();

        if ($alertaExistente) {
            return;
        }

        Alerta::create([
            'tipo' => 'stock_minimo',
            'prioridad' => 'alta',
            'titulo' => 'Stock mínimo: ' . $insumo->nombre_insumo,
            'mensaje' => sprintf(
                'El insumo "%s" (código: %s) alcanzó el stock mínimo. Stock actual: %.2f %s, Stock mínimo: %.2f %s.',
                $insumo->nombre_insumo,
                $insumo->codigo_insumo,
                $insumo->stock_actual,
                $insumo->unidad_medida,
                $insumo->stock_minimo,
                $insumo->unidad_medida
            ),
            'entidad_tipo' => 'Insumo',
            'entidad_id' => $insumo->id,
            'accion_recomendada' => 'Planificar compra en los próximos días. Proveedor: ' . ($insumo->proveedor ?? 'No especificado'),
            'estado' => 'activa',
            'leida' => false,
        ]);

        Log::info("Stock mínimo detectado para insumo ID {$insumo->id}: {$insumo->nombre_insumo}");
    }
}
