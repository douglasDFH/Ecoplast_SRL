<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrdenProduccion;
use App\Models\RegistroProduccion;
use App\Models\Alerta;
use App\Models\Maquinaria;
use App\Models\Mantenimiento;
use App\Models\InspeccionCalidad;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * API Controller para Dashboard Principal
 * 
 * Proporciona datos consolidados para el dashboard principal:
 * resumen de producción, alertas, estado de máquinas, KPIs en tiempo real.
 */
class DashboardController extends Controller
{
    /**
     * Datos principales del dashboard
     */
    public function index(): JsonResponse
    {
        $hoy = now()->startOfDay();
        $inicioMes = now()->startOfMonth();

        return response()->json([
            'produccion_hoy' => $this->produccionHoy(),
            'alertas' => $this->resumenAlertas(),
            'maquinas' => $this->estadoMaquinas(),
            'calidad' => $this->calidadReciente(),
            'ordenes_activas' => $this->ordenesActivas(),
            'kpis_mes' => $this->kpisMesActual(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Producción del día actual
     */
    public function produccionHoy(): array
    {
        $registros = RegistroProduccion::whereDate('fecha_hora_inicio', today())->get();

        return [
            'unidades_producidas' => $registros->sum('cantidad_producida'),
            'unidades_conformes' => $registros->sum('cantidad_conforme'),
            'unidades_defectuosas' => $registros->sum('cantidad_defectuosa'),
            'porcentaje_calidad' => $registros->sum('cantidad_producida') > 0
                ? round(($registros->sum('cantidad_conforme') / $registros->sum('cantidad_producida')) * 100, 2)
                : 0,
            'productividad_promedio' => round($registros->avg('productividad_unidades_hora'), 2),
            'merma_kg' => round($registros->sum('merma_kg'), 2),
            'tiempo_paro_minutos' => $registros->sum('tiempo_paro_minutos'),
        ];
    }

    /**
     * Resumen de alertas activas
     */
    public function resumenAlertas(): array
    {
        $alertas = Alerta::where('estado', 'activa')->get();

        return [
            'total' => $alertas->count(),
            'no_leidas' => $alertas->where('leida', false)->count(),
            'criticas' => $alertas->where('prioridad', 'critica')->count(),
            'altas' => $alertas->where('prioridad', 'alta')->count(),
            'recientes' => Alerta::where('estado', 'activa')
                ->where('prioridad', 'critica')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(),
        ];
    }

    /**
     * Estado de las máquinas
     */
    public function estadoMaquinas(): array
    {
        $maquinas = Maquinaria::all();

        return [
            'total' => $maquinas->count(),
            'operativas' => $maquinas->where('estado_actual', 'operativa')->count(),
            'en_mantenimiento' => $maquinas->where('estado_actual', 'mantenimiento')->count(),
            'paradas' => $maquinas->where('estado_actual', 'parada')->count(),
            'averiadas' => $maquinas->where('estado_actual', 'averia')->count(),
            'detalle' => $maquinas->map(function($m) {
                return [
                    'id' => $m->id,
                    'codigo' => $m->codigo_maquina,
                    'nombre' => $m->nombre_maquina,
                    'estado' => $m->estado_actual,
                    'oee_estimado' => $m->calcularOEE(),
                ];
            }),
        ];
    }

    /**
     * Calidad reciente (últimas 24h)
     */
    public function calidadReciente(): array
    {
        $inspecciones = InspeccionCalidad::where('fecha_inspeccion', '>=', now()->subDay())->get();

        return [
            'total_inspecciones' => $inspecciones->count(),
            'aprobadas' => $inspecciones->where('resultado', 'aprobado')->count(),
            'rechazadas' => $inspecciones->where('resultado', 'rechazado')->count(),
            'porcentaje_aprobacion' => $inspecciones->count() > 0
                ? round(($inspecciones->where('resultado', 'aprobado')->count() / $inspecciones->count()) * 100, 2)
                : 0,
        ];
    }

    /**
     * Órdenes de producción activas
     */
    public function ordenesActivas(): array
    {
        $ordenes = OrdenProduccion::with(['productoTerminado', 'maquina'])
            ->whereIn('estado', ['pendiente', 'en_proceso'])
            ->orderBy('fecha_programada')
            ->get();

        return [
            'total' => $ordenes->count(),
            'pendientes' => $ordenes->where('estado', 'pendiente')->count(),
            'en_proceso' => $ordenes->where('estado', 'en_proceso')->count(),
            'atrasadas' => $ordenes->filter(function($o) {
                return $o->fecha_programada < now() && $o->estado !== 'completada';
            })->count(),
            'listado' => $ordenes->take(10)->map(function($o) {
                return [
                    'id' => $o->id,
                    'numero_orden' => $o->numero_orden,
                    'producto' => $o->productoTerminado->nombre_producto ?? null,
                    'maquina' => $o->maquina->nombre_maquina ?? null,
                    'cantidad_planificada' => $o->cantidad_planificada,
                    'cantidad_producida' => $o->cantidad_producida,
                    'porcentaje_avance' => $o->cantidad_planificada > 0
                        ? round(($o->cantidad_producida / $o->cantidad_planificada) * 100, 2)
                        : 0,
                    'estado' => $o->estado,
                    'prioridad' => $o->prioridad,
                    'fecha_programada' => $o->fecha_programada,
                ];
            }),
        ];
    }

    /**
     * KPIs del mes actual
     */
    public function kpisMesActual(): array
    {
        $inicioMes = now()->startOfMonth();
        $finMes = now()->endOfMonth();

        $ordenes = OrdenProduccion::whereBetween('fecha_inicio', [$inicioMes, $finMes])->get();
        $registros = RegistroProduccion::whereBetween('fecha_hora_inicio', [$inicioMes, $finMes])->get();
        $mantenimientos = Mantenimiento::whereBetween('fecha_programada', [$inicioMes, $finMes])->get();

        return [
            'ordenes_completadas' => $ordenes->where('estado', 'completada')->count(),
            'unidades_producidas' => $registros->sum('cantidad_producida'),
            'porcentaje_calidad' => $registros->sum('cantidad_producida') > 0
                ? round(($registros->sum('cantidad_conforme') / $registros->sum('cantidad_producida')) * 100, 2)
                : 0,
            'mantenimientos_completados' => $mantenimientos->where('estado', 'completado')->count(),
            'productividad_promedio' => round($registros->avg('productividad_unidades_hora'), 2),
        ];
    }

    /**
     * Gráfico de producción semanal
     */
    public function produccionSemanal(): JsonResponse
    {
        $datos = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $fecha = now()->subDays($i)->startOfDay();
            
            $registros = RegistroProduccion::whereDate('fecha_hora_inicio', $fecha)->get();
            
            $datos[] = [
                'fecha' => $fecha->format('Y-m-d'),
                'dia' => $fecha->locale('es')->format('l'),
                'producidas' => $registros->sum('cantidad_producida'),
                'conformes' => $registros->sum('cantidad_conforme'),
                'defectuosas' => $registros->sum('cantidad_defectuosa'),
            ];
        }

        return response()->json($datos);
    }

    /**
     * Gráfico de producción por turno (últimas 24h)
     */
    public function produccionPorTurno(): JsonResponse
    {
        $registros = RegistroProduccion::with('turno')
            ->where('fecha_hora_inicio', '>=', now()->subDay())
            ->get()
            ->groupBy('turno_id');

        $datos = $registros->map(function($grupo, $turnoId) {
            $turno = $grupo->first()->turno;
            return [
                'turno_id' => $turnoId,
                'turno_nombre' => $turno->nombre_turno ?? 'Sin turno',
                'producidas' => $grupo->sum('cantidad_producida'),
                'conformes' => $grupo->sum('cantidad_conforme'),
                'defectuosas' => $grupo->sum('cantidad_defectuosa'),
                'productividad' => round($grupo->avg('productividad_unidades_hora'), 2),
            ];
        })->values();

        return response()->json($datos);
    }

    /**
     * Mantenimientos próximos (7 días)
     */
    public function mantenimientosProximos(): JsonResponse
    {
        $mantenimientos = Mantenimiento::with('maquina', 'tecnico')
            ->where('estado', 'programado')
            ->whereBetween('fecha_programada', [now(), now()->addDays(7)])
            ->orderBy('fecha_programada')
            ->get();

        return response()->json($mantenimientos);
    }

    /**
     * Top 5 productos más producidos (mes actual)
     */
    public function topProductos(): JsonResponse
    {
        $inicioMes = now()->startOfMonth();

        $productos = OrdenProduccion::with('productoTerminado')
            ->where('fecha_inicio', '>=', $inicioMes)
            ->selectRaw('producto_id, SUM(cantidad_producida) as total_producido')
            ->groupBy('producto_id')
            ->orderByDesc('total_producido')
            ->limit(5)
            ->get();

        $datos = $productos->map(function($orden) {
            return [
                'producto_id' => $orden->producto_id,
                'nombre' => $orden->productoTerminado->nombre_producto ?? 'Desconocido',
                'codigo' => $orden->productoTerminado->codigo_producto ?? null,
                'total_producido' => $orden->total_producido,
            ];
        });

        return response()->json($datos);
    }
}
