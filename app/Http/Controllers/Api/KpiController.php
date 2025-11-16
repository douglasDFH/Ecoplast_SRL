<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Insumo;
use App\Models\ProductoTerminado;
use App\Models\Maquinaria;
use App\Models\OrdenProduccion;
use App\Models\RegistroProduccion;
use App\Models\LoteProduccion;
use App\Models\Mantenimiento;
use App\Models\InspeccionCalidad;
use App\Models\Alerta;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * API Controller para KPIs (Key Performance Indicators)
 * 
 * Calcula y proporciona indicadores clave de rendimiento para
 * producción, calidad, inventario, mantenimiento y eficiencia.
 */
class KpiController extends Controller
{
    /**
     * Dashboard general con todos los KPIs principales
     */
    public function dashboard(Request $request): JsonResponse
    {
        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth());
        $fechaFin = $request->get('fecha_fin', now()->endOfMonth());

        return response()->json([
            'periodo' => [
                'inicio' => $fechaInicio,
                'fin' => $fechaFin,
            ],
            'produccion' => $this->kpisProduccion($fechaInicio, $fechaFin),
            'calidad' => $this->kpisCalidad($fechaInicio, $fechaFin),
            'inventario' => $this->kpisInventario(),
            'mantenimiento' => $this->kpisMantenimiento($fechaInicio, $fechaFin),
            'alertas' => $this->kpisAlertas(),
        ]);
    }

    /**
     * KPIs de Producción
     */
    public function produccion(Request $request): JsonResponse
    {
        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth());
        $fechaFin = $request->get('fecha_fin', now()->endOfMonth());

        $kpis = $this->kpisProduccion($fechaInicio, $fechaFin);

        return response()->json([
            'periodo' => ['inicio' => $fechaInicio, 'fin' => $fechaFin],
            'kpis' => $kpis
        ]);
    }

    /**
     * KPIs de Calidad
     */
    public function calidad(Request $request): JsonResponse
    {
        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth());
        $fechaFin = $request->get('fecha_fin', now()->endOfMonth());

        $kpis = $this->kpisCalidad($fechaInicio, $fechaFin);

        return response()->json([
            'periodo' => ['inicio' => $fechaInicio, 'fin' => $fechaFin],
            'kpis' => $kpis
        ]);
    }

    /**
     * KPIs de Inventario
     */
    public function inventario(): JsonResponse
    {
        $kpis = $this->kpisInventario();

        return response()->json(['kpis' => $kpis]);
    }

    /**
     * KPIs de Mantenimiento
     */
    public function mantenimiento(Request $request): JsonResponse
    {
        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth());
        $fechaFin = $request->get('fecha_fin', now()->endOfMonth());

        $kpis = $this->kpisMantenimiento($fechaInicio, $fechaFin);

        return response()->json([
            'periodo' => ['inicio' => $fechaInicio, 'fin' => $fechaFin],
            'kpis' => $kpis
        ]);
    }

    /**
     * KPIs de Eficiencia de Maquinaria (OEE)
     */
    public function oee(Request $request): JsonResponse
    {
        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth());
        $fechaFin = $request->get('fecha_fin', now()->endOfMonth());

        $maquinas = Maquinaria::with(['ordenesProduccion' => function($query) use ($fechaInicio, $fechaFin) {
            $query->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin]);
        }])->get();

        $oeeData = $maquinas->map(function($maquina) use ($fechaInicio, $fechaFin) {
            // Calcular disponibilidad
            $tiempoDisponible = now()->diffInHours($fechaInicio);
            $tiempoMantenimiento = Mantenimiento::where('maquina_id', $maquina->id)
                ->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
                ->sum('duracion_real_horas');
            $disponibilidad = $tiempoDisponible > 0 
                ? (($tiempoDisponible - $tiempoMantenimiento) / $tiempoDisponible) * 100 
                : 0;

            // Calcular rendimiento y calidad desde registros de producción
            $registros = RegistroProduccion::whereHas('ordenProduccion', function($q) use ($maquina) {
                    $q->where('maquina_id', $maquina->id);
                })
                ->whereBetween('fecha_hora_inicio', [$fechaInicio, $fechaFin])
                ->get();

            $totalProducido = $registros->sum('cantidad_producida');
            $totalConforme = $registros->sum('cantidad_conforme');
            $calidad = $totalProducido > 0 ? ($totalConforme / $totalProducido) * 100 : 0;
            $rendimiento = $registros->avg('productividad_unidades_hora') ?? 0;

            // OEE = Disponibilidad × Rendimiento × Calidad
            $oee = ($disponibilidad / 100) * ($rendimiento / 100) * ($calidad / 100) * 100;

            return [
                'maquina_id' => $maquina->id,
                'codigo_maquina' => $maquina->codigo_maquina,
                'nombre_maquina' => $maquina->nombre_maquina,
                'disponibilidad' => round($disponibilidad, 2),
                'rendimiento' => round($rendimiento, 2),
                'calidad' => round($calidad, 2),
                'oee' => round($oee, 2),
            ];
        });

        return response()->json([
            'periodo' => ['inicio' => $fechaInicio, 'fin' => $fechaFin],
            'oee_promedio' => round($oeeData->avg('oee'), 2),
            'maquinas' => $oeeData
        ]);
    }

    // ==================== MÉTODOS PRIVADOS DE CÁLCULO ====================

    private function kpisProduccion($fechaInicio, $fechaFin): array
    {
        $ordenes = OrdenProduccion::whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])->get();
        $registros = RegistroProduccion::whereBetween('fecha_hora_inicio', [$fechaInicio, $fechaFin])->get();

        $totalProducido = $registros->sum('cantidad_producida');
        $totalConforme = $registros->sum('cantidad_conforme');
        $totalDefectuoso = $registros->sum('cantidad_defectuosa');

        return [
            'ordenes_totales' => $ordenes->count(),
            'ordenes_completadas' => $ordenes->where('estado', 'completada')->count(),
            'ordenes_en_proceso' => $ordenes->where('estado', 'en_proceso')->count(),
            'ordenes_atrasadas' => $ordenes->where('estado', 'en_proceso')
                ->where('fecha_programada', '<', now())->count(),
            'unidades_producidas' => $totalProducido,
            'unidades_conformes' => $totalConforme,
            'unidades_defectuosas' => $totalDefectuoso,
            'porcentaje_cumplimiento' => $ordenes->count() > 0
                ? round(($ordenes->where('estado', 'completada')->count() / $ordenes->count()) * 100, 2)
                : 0,
            'productividad_promedio' => round($registros->avg('productividad_unidades_hora'), 2),
            'merma_total_kg' => round($registros->sum('merma_kg'), 2),
            'tiempo_paro_total_minutos' => $registros->sum('tiempo_paro_minutos'),
        ];
    }

    private function kpisCalidad($fechaInicio, $fechaFin): array
    {
        $inspecciones = InspeccionCalidad::whereBetween('fecha_inspeccion', [$fechaInicio, $fechaFin])->get();
        $registros = RegistroProduccion::whereBetween('fecha_hora_inicio', [$fechaInicio, $fechaFin])->get();

        $totalProducido = $registros->sum('cantidad_producida');
        $totalDefectuoso = $registros->sum('cantidad_defectuosa');

        return [
            'inspecciones_totales' => $inspecciones->count(),
            'inspecciones_aprobadas' => $inspecciones->where('resultado', 'aprobado')->count(),
            'inspecciones_rechazadas' => $inspecciones->where('resultado', 'rechazado')->count(),
            'porcentaje_aprobacion' => $inspecciones->count() > 0
                ? round(($inspecciones->where('resultado', 'aprobado')->count() / $inspecciones->count()) * 100, 2)
                : 0,
            'porcentaje_rechazo' => $inspecciones->count() > 0
                ? round(($inspecciones->where('resultado', 'rechazado')->count() / $inspecciones->count()) * 100, 2)
                : 0,
            'tasa_defectos_ppm' => $totalProducido > 0
                ? round(($totalDefectuoso / $totalProducido) * 1000000, 0)
                : 0,
            'certificaciones_obtenidas' => $inspecciones->whereNotNull('certificacion_obtenida')->count(),
            'porcentaje_biodegradacion_promedio' => round($inspecciones->avg('porcentaje_biodegradado'), 2),
        ];
    }

    private function kpisInventario(): array
    {
        $insumos = Insumo::all();
        $productos = ProductoTerminado::all();
        $lotes = LoteProduccion::where('estado', 'disponible')->get();

        // Insumos bajo stock mínimo
        $insumosBajoMinimo = $insumos->filter(function($insumo) {
            return $insumo->stock_actual <= $insumo->stock_minimo;
        });

        // Lotes próximos a vencer (30 días)
        $lotesProximosVencer = $lotes->filter(function($lote) {
            return $lote->fecha_vencimiento && 
                   $lote->fecha_vencimiento->lte(now()->addDays(30)) &&
                   $lote->fecha_vencimiento->gte(now());
        });

        // Lotes vencidos
        $lotesVencidos = $lotes->filter(function($lote) {
            return $lote->fecha_vencimiento && $lote->fecha_vencimiento->lt(now());
        });

        return [
            'total_insumos' => $insumos->count(),
            'insumos_activos' => $insumos->where('activo', true)->count(),
            'insumos_bajo_minimo' => $insumosBajoMinimo->count(),
            'insumos_sin_stock' => $insumos->where('stock_actual', 0)->count(),
            'valor_inventario_insumos' => round($insumos->sum(function($i) {
                return $i->stock_actual * $i->precio_unitario;
            }), 2),
            'total_productos' => $productos->count(),
            'productos_activos' => $productos->where('activo', true)->count(),
            'total_lotes_disponibles' => $lotes->count(),
            'lotes_proximos_vencer' => $lotesProximosVencer->count(),
            'lotes_vencidos' => $lotesVencidos->count(),
            'rotacion_inventario' => 0, // Calcular con más datos históricos
        ];
    }

    private function kpisMantenimiento($fechaInicio, $fechaFin): array
    {
        $mantenimientos = Mantenimiento::whereBetween('fecha_programada', [$fechaInicio, $fechaFin])->get();
        $maquinas = Maquinaria::all();

        $atrasados = $mantenimientos->filter(function($m) {
            return $m->estado === 'programado' && $m->fecha_programada->lt(now());
        });

        return [
            'total_mantenimientos' => $mantenimientos->count(),
            'preventivos' => $mantenimientos->where('tipo', 'preventivo')->count(),
            'correctivos' => $mantenimientos->where('tipo', 'correctivo')->count(),
            'completados' => $mantenimientos->where('estado', 'completado')->count(),
            'atrasados' => $atrasados->count(),
            'porcentaje_cumplimiento' => $mantenimientos->count() > 0
                ? round(($mantenimientos->where('estado', 'completado')->count() / $mantenimientos->count()) * 100, 2)
                : 0,
            'costo_total' => round($mantenimientos->where('estado', 'completado')->sum('costo_real'), 2),
            'tiempo_total_horas' => round($mantenimientos->where('estado', 'completado')->sum('duracion_real_horas'), 2),
            'maquinas_operativas' => $maquinas->where('estado_actual', 'operativa')->count(),
            'maquinas_en_mantenimiento' => $maquinas->where('estado_actual', 'mantenimiento')->count(),
            'maquinas_paradas' => $maquinas->where('estado_actual', 'parada')->count(),
            'maquinas_averiadas' => $maquinas->where('estado_actual', 'averia')->count(),
        ];
    }

    private function kpisAlertas(): array
    {
        $alertasActivas = Alerta::where('estado', 'activa')->get();

        return [
            'total_activas' => $alertasActivas->count(),
            'no_leidas' => $alertasActivas->where('leida', false)->count(),
            'criticas' => $alertasActivas->where('prioridad', 'critica')->count(),
            'altas' => $alertasActivas->where('prioridad', 'alta')->count(),
            'medias' => $alertasActivas->where('prioridad', 'media')->count(),
            'stock' => $alertasActivas->whereIn('tipo', ['stock_minimo', 'stock_critico'])->count(),
            'vencimientos' => $alertasActivas->whereIn('tipo', ['vencimiento_proximo', 'vencimiento_pasado'])->count(),
            'calidad' => $alertasActivas->where('tipo', 'defecto_calidad')->count(),
            'mantenimiento' => $alertasActivas->whereIn('tipo', ['mantenimiento_pendiente', 'mantenimiento_atrasado'])->count(),
        ];
    }
}
