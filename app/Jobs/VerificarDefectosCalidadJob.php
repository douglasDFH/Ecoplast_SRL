<?php

namespace App\Jobs;

use App\Models\InspeccionCalidad;
use App\Models\LoteProduccion;
use App\Models\Alerta;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

/**
 * Job: Verificar Defectos de Calidad
 * 
 * Analiza inspecciones de calidad para detectar patrones de defectos.
 */
class VerificarDefectosCalidadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Iniciando verificación de defectos de calidad');

        // 1. Inspecciones con alto porcentaje de defectos (últimas 24 horas)
        $this->verificarInspeccionesRecientes();

        // 2. Productos con tasa de rechazo alta (última semana)
        $this->verificarTasaRechazo();

        // 3. Lotes en cuarentena por mucho tiempo
        $this->verificarLotesCuarentena();

        Log::info('Verificación de defectos de calidad completada');
    }

    private function verificarInspeccionesRecientes(): void
    {
        $inspeccionesDefectuosas = InspeccionCalidad::where('created_at', '>=', now()->subDay())
            ->where('resultado', 'rechazado')
            ->with('lote.producto', 'inspector')
            ->get();

        foreach ($inspeccionesDefectuosas as $inspeccion) {
            $alertaExistente = Alerta::where('entidad_tipo', 'InspeccionCalidad')
                ->where('entidad_id', $inspeccion->id)
                ->where('tipo', 'calidad_defectos')
                ->where('estado', 'activa')
                ->exists();

            if ($alertaExistente) {
                continue;
            }

            // Decodificar defectos
            $defectos = json_decode($inspeccion->defectos_encontrados, true) ?? [];
            $defectosTexto = collect($defectos)->map(function ($defecto) {
                return "{$defecto['tipo']}: {$defecto['cantidad']} unidades ({$defecto['descripcion']})";
            })->join(', ');

            $prioridad = 'media';
            if ($inspeccion->cantidad_defectuosa >= $inspeccion->cantidad_inspeccionada * 0.2) {
                $prioridad = 'critica'; // >20% defectos
            } elseif ($inspeccion->cantidad_defectuosa >= $inspeccion->cantidad_inspeccionada * 0.1) {
                $prioridad = 'alta'; // >10% defectos
            }

            Alerta::create([
                'tipo' => 'calidad_defectos',
                'prioridad' => $prioridad,
                'titulo' => 'Lote rechazado: ' . ($inspeccion->lote->producto->nombre ?? 'Sin producto'),
                'mensaje' => sprintf(
                    'Lote %s rechazado con %.1f%% de defectos (%d/%d unidades). Defectos: %s',
                    $inspeccion->lote->codigo_lote ?? 'Sin código',
                    ($inspeccion->cantidad_defectuosa / $inspeccion->cantidad_inspeccionada) * 100,
                    $inspeccion->cantidad_defectuosa,
                    $inspeccion->cantidad_inspeccionada,
                    $defectosTexto ?: 'No especificados'
                ),
                'entidad_tipo' => 'InspeccionCalidad',
                'entidad_id' => $inspeccion->id,
                'accion_recomendada' => 'Revisar parámetros de producción, verificar calibración de máquinas, capacitar operadores.',
                'estado' => 'activa',
                'leida' => false,
            ]);
        }
    }

    private function verificarTasaRechazo(): void
    {
        $productos = DB::table('inspecciones_calidad as ic')
            ->join('lotes_produccion as lp', 'ic.lote_id', '=', 'lp.id')
            ->join('productos_terminados as pt', 'lp.producto_id', '=', 'pt.id')
            ->select(
                'pt.id as producto_id',
                'pt.nombre as producto_nombre',
                DB::raw('COUNT(*) as total_inspecciones'),
                DB::raw('SUM(CASE WHEN ic.resultado = "rechazado" THEN 1 ELSE 0 END) as rechazos'),
                DB::raw('(SUM(CASE WHEN ic.resultado = "rechazado" THEN 1 ELSE 0 END) * 100.0 / COUNT(*)) as tasa_rechazo')
            )
            ->where('ic.created_at', '>=', now()->subWeek())
            ->groupBy('pt.id', 'pt.nombre')
            ->having('tasa_rechazo', '>=', 15) // Tasa >= 15%
            ->get();

        foreach ($productos as $producto) {
            $alertaExistente = Alerta::where('entidad_tipo', 'Producto')
                ->where('entidad_id', $producto->producto_id)
                ->where('tipo', 'calidad_tasa_rechazo_alta')
                ->where('estado', 'activa')
                ->where('created_at', '>=', now()->subDay()) // Una alerta diaria
                ->exists();

            if ($alertaExistente) {
                continue;
            }

            Alerta::create([
                'tipo' => 'calidad_tasa_rechazo_alta',
                'prioridad' => $producto->tasa_rechazo >= 25 ? 'critica' : 'alta',
                'titulo' => 'Tasa de rechazo alta: ' . $producto->producto_nombre,
                'mensaje' => sprintf(
                    'El producto "%s" tiene una tasa de rechazo de %.1f%% en la última semana (%d rechazos de %d inspecciones)',
                    $producto->producto_nombre,
                    $producto->tasa_rechazo,
                    $producto->rechazos,
                    $producto->total_inspecciones
                ),
                'entidad_tipo' => 'Producto',
                'entidad_id' => $producto->producto_id,
                'accion_recomendada' => 'Auditoría completa del proceso de producción. Revisar formulación, parámetros de máquinas, capacitación del personal.',
                'estado' => 'activa',
                'leida' => false,
            ]);
        }
    }

    private function verificarLotesCuarentena(): void
    {
        $lotesCuarentena = LoteProduccion::where('estado', 'cuarentena')
            ->where('created_at', '<=', now()->subDays(7)) // Más de 7 días
            ->with('producto')
            ->get();

        foreach ($lotesCuarentena as $lote) {
            $alertaExistente = Alerta::where('entidad_tipo', 'LoteProduccion')
                ->where('entidad_id', $lote->id)
                ->where('tipo', 'lote_cuarentena_prolongada')
                ->where('estado', 'activa')
                ->exists();

            if ($alertaExistente) {
                continue;
            }

            $diasCuarentena = now()->diffInDays($lote->created_at);

            Alerta::create([
                'tipo' => 'lote_cuarentena_prolongada',
                'prioridad' => $diasCuarentena > 14 ? 'alta' : 'media',
                'titulo' => 'Lote en cuarentena prolongada: ' . ($lote->producto->nombre ?? 'Sin producto'),
                'mensaje' => sprintf(
                    'El lote %s de "%s" lleva %d días en cuarentena (cantidad: %.2f kg)',
                    $lote->codigo_lote ?? 'Sin código',
                    $lote->producto->nombre ?? 'Sin producto',
                    $diasCuarentena,
                    $lote->cantidad_kg
                ),
                'entidad_tipo' => 'LoteProduccion',
                'entidad_id' => $lote->id,
                'accion_recomendada' => 'Realizar inspección de calidad pendiente o aprobar/rechazar el lote.',
                'estado' => 'activa',
                'leida' => false,
            ]);
        }
    }
}
