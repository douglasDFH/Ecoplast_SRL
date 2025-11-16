<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrdenProduccion;
use App\Models\RegistroProduccion;
use App\Models\Insumo;
use App\Models\ProductoTerminado;
use App\Models\LoteProduccion;
use App\Models\Mantenimiento;
use App\Models\InspeccionCalidad;
use App\Models\MovimientoInventarioInsumo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

/**
 * API Controller para Reportes del Sistema
 * 
 * Genera reportes consolidados de producción, calidad, inventario,
 * mantenimiento y trazabilidad de productos biodegradables.
 */
class ReporteController extends Controller
{
    /**
     * Reporte consolidado de producción
     */
    public function produccion(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'producto_id' => 'nullable|exists:productos,id',
            'maquina_id' => 'nullable|exists:maquinas,id',
            'turno_id' => 'nullable|exists:turnos,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $queryOrdenes = OrdenProduccion::with(['productoTerminado', 'maquina', 'turno'])
            ->whereBetween('fecha_inicio', [$request->fecha_inicio, $request->fecha_fin]);

        $queryRegistros = RegistroProduccion::with(['ordenProduccion.productoTerminado', 'turno'])
            ->whereBetween('fecha_hora_inicio', [$request->fecha_inicio, $request->fecha_fin]);

        if ($request->has('producto_id')) {
            $queryOrdenes->where('producto_id', $request->producto_id);
            $queryRegistros->whereHas('ordenProduccion', function($q) use ($request) {
                $q->where('producto_id', $request->producto_id);
            });
        }

        if ($request->has('maquina_id')) {
            $queryOrdenes->where('maquina_id', $request->maquina_id);
            $queryRegistros->whereHas('ordenProduccion', function($q) use ($request) {
                $q->where('maquina_id', $request->maquina_id);
            });
        }

        if ($request->has('turno_id')) {
            $queryOrdenes->where('turno_id', $request->turno_id);
            $queryRegistros->where('turno_id', $request->turno_id);
        }

        $ordenes = $queryOrdenes->get();
        $registros = $queryRegistros->get();

        $reporte = [
            'periodo' => [
                'inicio' => $request->fecha_inicio,
                'fin' => $request->fecha_fin,
            ],
            'resumen' => [
                'ordenes_totales' => $ordenes->count(),
                'ordenes_completadas' => $ordenes->where('estado', 'completada')->count(),
                'ordenes_en_proceso' => $ordenes->where('estado', 'en_proceso')->count(),
                'ordenes_canceladas' => $ordenes->where('estado', 'cancelada')->count(),
                'unidades_producidas' => $registros->sum('cantidad_producida'),
                'unidades_conformes' => $registros->sum('cantidad_conforme'),
                'unidades_defectuosas' => $registros->sum('cantidad_defectuosa'),
                'porcentaje_calidad' => $registros->sum('cantidad_producida') > 0
                    ? round(($registros->sum('cantidad_conforme') / $registros->sum('cantidad_producida')) * 100, 2)
                    : 0,
                'productividad_promedio' => round($registros->avg('productividad_unidades_hora'), 2),
                'merma_total_kg' => round($registros->sum('merma_kg'), 2),
                'tiempo_paro_total_horas' => round($registros->sum('tiempo_paro_minutos') / 60, 2),
            ],
            'por_producto' => $registros->groupBy(function($r) {
                return $r->ordenProduccion->productoTerminado->nombre_producto ?? 'Sin producto';
            })->map(function($grupo) {
                return [
                    'producidas' => $grupo->sum('cantidad_producida'),
                    'conformes' => $grupo->sum('cantidad_conforme'),
                    'defectuosas' => $grupo->sum('cantidad_defectuosa'),
                ];
            }),
            'por_turno' => $registros->groupBy(function($r) {
                return $r->turno->nombre_turno ?? 'Sin turno';
            })->map(function($grupo) {
                return [
                    'producidas' => $grupo->sum('cantidad_producida'),
                    'productividad' => round($grupo->avg('productividad_unidades_hora'), 2),
                ];
            }),
        ];

        return response()->json($reporte);
    }

    /**
     * Reporte de calidad
     */
    public function calidad(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'tipo_inspeccion' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $query = InspeccionCalidad::with(['loteProduccion.productoTerminado', 'inspector'])
            ->whereBetween('fecha_inspeccion', [$request->fecha_inicio, $request->fecha_fin]);

        if ($request->has('tipo_inspeccion')) {
            $query->where('tipo_inspeccion', $request->tipo_inspeccion);
        }

        $inspecciones = $query->get();
        $registros = RegistroProduccion::whereBetween('fecha_hora_inicio', [
            $request->fecha_inicio,
            $request->fecha_fin
        ])->get();

        $reporte = [
            'periodo' => [
                'inicio' => $request->fecha_inicio,
                'fin' => $request->fecha_fin,
            ],
            'inspecciones' => [
                'totales' => $inspecciones->count(),
                'aprobadas' => $inspecciones->where('resultado', 'aprobado')->count(),
                'rechazadas' => $inspecciones->where('resultado', 'rechazado')->count(),
                'condicionales' => $inspecciones->where('resultado', 'condicional')->count(),
                'porcentaje_aprobacion' => $inspecciones->count() > 0
                    ? round(($inspecciones->where('resultado', 'aprobado')->count() / $inspecciones->count()) * 100, 2)
                    : 0,
            ],
            'produccion' => [
                'total_producido' => $registros->sum('cantidad_producida'),
                'total_conforme' => $registros->sum('cantidad_conforme'),
                'total_defectuoso' => $registros->sum('cantidad_defectuosa'),
                'tasa_defectos_porcentaje' => $registros->sum('cantidad_producida') > 0
                    ? round(($registros->sum('cantidad_defectuosa') / $registros->sum('cantidad_producida')) * 100, 2)
                    : 0,
            ],
            'por_tipo_inspeccion' => $inspecciones->groupBy('tipo_inspeccion')->map(function($grupo) {
                return [
                    'total' => $grupo->count(),
                    'aprobadas' => $grupo->where('resultado', 'aprobado')->count(),
                    'rechazadas' => $grupo->where('resultado', 'rechazado')->count(),
                ];
            }),
            'certificaciones' => [
                'total_obtenidas' => $inspecciones->whereNotNull('certificacion_obtenida')->count(),
                'porcentaje_biodegradacion_promedio' => round($inspecciones->avg('porcentaje_biodegradado'), 2),
            ],
        ];

        return response()->json($reporte);
    }

    /**
     * Reporte de inventario de insumos
     */
    public function inventarioInsumos(Request $request): JsonResponse
    {
        $insumos = Insumo::with('categoria')->get();

        $reporte = [
            'fecha_reporte' => now()->toIso8601String(),
            'resumen' => [
                'total_insumos' => $insumos->count(),
                'activos' => $insumos->where('activo', true)->count(),
                'bajo_minimo' => $insumos->filter(function($i) {
                    return $i->stock_actual <= $i->stock_minimo;
                })->count(),
                'sin_stock' => $insumos->where('stock_actual', 0)->count(),
                'valor_total' => round($insumos->sum(function($i) {
                    return $i->stock_actual * $i->precio_unitario;
                }), 2),
            ],
            'alertas' => [
                'stock_critico' => $insumos->filter(function($i) {
                    return $i->stock_actual < ($i->stock_minimo * 0.5);
                })->map(function($i) {
                    return [
                        'codigo' => $i->codigo_insumo,
                        'nombre' => $i->nombre_insumo,
                        'stock_actual' => $i->stock_actual,
                        'stock_minimo' => $i->stock_minimo,
                        'unidad' => $i->unidad_medida,
                    ];
                })->values(),
            ],
            'por_categoria' => $insumos->groupBy(function($i) {
                return $i->categoria->nombre_categoria ?? 'Sin categoría';
            })->map(function($grupo) {
                return [
                    'cantidad_items' => $grupo->count(),
                    'valor_inventario' => round($grupo->sum(function($i) {
                        return $i->stock_actual * $i->precio_unitario;
                    }), 2),
                ];
            }),
        ];

        return response()->json($reporte);
    }

    /**
     * Reporte de inventario de productos terminados
     */
    public function inventarioProductos(Request $request): JsonResponse
    {
        $productos = ProductoTerminado::with('categoria')->get();
        $lotes = LoteProduccion::with('productoTerminado')->where('estado', 'disponible')->get();

        $reporte = [
            'fecha_reporte' => now()->toIso8601String(),
            'resumen' => [
                'total_productos' => $productos->count(),
                'activos' => $productos->where('activo', true)->count(),
                'lotes_disponibles' => $lotes->count(),
                'lotes_proximos_vencer' => $lotes->filter(function($l) {
                    return $l->fecha_vencimiento && 
                           $l->fecha_vencimiento->lte(now()->addDays(30)) &&
                           $l->fecha_vencimiento->gte(now());
                })->count(),
            ],
            'por_producto' => $productos->map(function($producto) use ($lotes) {
                $lotesProducto = $lotes->where('producto_id', $producto->id);
                return [
                    'codigo' => $producto->codigo_producto,
                    'nombre' => $producto->nombre_producto,
                    'lotes_disponibles' => $lotesProducto->count(),
                    'cantidad_total' => $lotesProducto->sum('cantidad_disponible'),
                    'unidad' => $producto->unidad_venta,
                ];
            }),
        ];

        return response()->json($reporte);
    }

    /**
     * Reporte de mantenimiento
     */
    public function mantenimiento(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'maquina_id' => 'nullable|exists:maquinas,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $query = Mantenimiento::with(['maquina', 'tecnico'])
            ->whereBetween('fecha_programada', [$request->fecha_inicio, $request->fecha_fin]);

        if ($request->has('maquina_id')) {
            $query->where('maquina_id', $request->maquina_id);
        }

        $mantenimientos = $query->get();

        $reporte = [
            'periodo' => [
                'inicio' => $request->fecha_inicio,
                'fin' => $request->fecha_fin,
            ],
            'resumen' => [
                'total_mantenimientos' => $mantenimientos->count(),
                'preventivos' => $mantenimientos->where('tipo', 'preventivo')->count(),
                'correctivos' => $mantenimientos->where('tipo', 'correctivo')->count(),
                'predictivos' => $mantenimientos->where('tipo', 'predictivo')->count(),
                'completados' => $mantenimientos->where('estado', 'completado')->count(),
                'atrasados' => $mantenimientos->filter(function($m) {
                    return $m->estado === 'programado' && $m->fecha_programada->lt(now());
                })->count(),
                'costo_total' => round($mantenimientos->where('estado', 'completado')->sum('costo_real'), 2),
                'tiempo_total_horas' => round($mantenimientos->where('estado', 'completado')->sum('duracion_real_horas'), 2),
            ],
            'por_maquina' => $mantenimientos->groupBy(function($m) {
                return $m->maquina->nombre_maquina ?? 'Sin máquina';
            })->map(function($grupo) {
                return [
                    'total' => $grupo->count(),
                    'completados' => $grupo->where('estado', 'completado')->count(),
                    'costo' => round($grupo->where('estado', 'completado')->sum('costo_real'), 2),
                    'tiempo_horas' => round($grupo->where('estado', 'completado')->sum('duracion_real_horas'), 2),
                ];
            }),
        ];

        return response()->json($reporte);
    }

    /**
     * Reporte de trazabilidad de lote
     */
    public function trazabilidad(int $loteId): JsonResponse
    {
        $lote = LoteProduccion::with([
            'productoTerminado',
            'ordenProduccion.maquina',
            'ordenProduccion.operador',
            'ordenProduccion.supervisor',
            'ordenProduccion.formulacion.insumos.insumo',
            'registrosProduccion.operador',
            'registrosProduccion.turno',
            'inspeccionesCalidad.inspector',
            'movimientosSalida'
        ])->findOrFail($loteId);

        $reporte = [
            'lote' => [
                'codigo' => $lote->codigo_lote,
                'producto' => $lote->productoTerminado->nombre_producto ?? null,
                'fecha_produccion' => $lote->fecha_produccion,
                'fecha_vencimiento' => $lote->fecha_vencimiento,
                'cantidad_inicial' => $lote->cantidad_inicial,
                'cantidad_disponible' => $lote->cantidad_disponible,
                'estado' => $lote->estado,
                'certificado_compostable' => $lote->certificado_compostable,
                'numero_certificado' => $lote->numero_certificado,
            ],
            'orden_produccion' => [
                'numero_orden' => $lote->ordenProduccion->numero_orden ?? null,
                'maquina' => $lote->ordenProduccion->maquina->nombre_maquina ?? null,
                'operador' => $lote->ordenProduccion->operador->name ?? null,
                'supervisor' => $lote->ordenProduccion->supervisor->name ?? null,
            ],
            'formulacion' => $lote->ordenProduccion->formulacion ? [
                'nombre' => $lote->ordenProduccion->formulacion->nombre_formulacion,
                'insumos' => $lote->ordenProduccion->formulacion->insumos->map(function($fi) use ($lote) {
                    return [
                        'insumo' => $fi->insumo->nombre_insumo,
                        'porcentaje' => $fi->porcentaje,
                        'lote_materia_prima' => $lote->lote_materia_prima,
                    ];
                }),
            ] : null,
            'registros_produccion' => $lote->registrosProduccion->map(function($r) {
                return [
                    'fecha_hora_inicio' => $r->fecha_hora_inicio,
                    'fecha_hora_fin' => $r->fecha_hora_fin,
                    'turno' => $r->turno->nombre_turno ?? null,
                    'operador' => $r->operador->name ?? null,
                    'cantidad_producida' => $r->cantidad_producida,
                    'cantidad_conforme' => $r->cantidad_conforme,
                    'temperatura_promedio' => $r->temperatura_promedio,
                    'presion_promedio' => $r->presion_promedio,
                ];
            }),
            'inspecciones_calidad' => $lote->inspeccionesCalidad->map(function($i) {
                return [
                    'fecha' => $i->fecha_inspeccion,
                    'tipo' => $i->tipo_inspeccion,
                    'resultado' => $i->resultado,
                    'inspector' => $i->inspector->name ?? null,
                    'certificacion' => $i->certificacion_obtenida,
                    'porcentaje_biodegradado' => $i->porcentaje_biodegradado,
                ];
            }),
            'movimientos_salida' => $lote->movimientosSalida->map(function($m) {
                return [
                    'fecha' => $m->fecha_movimiento,
                    'cantidad' => $m->cantidad,
                    'destino' => $m->destino,
                ];
            }),
        ];

        return response()->json($reporte);
    }

    /**
     * Reporte de movimientos de inventario
     */
    public function movimientosInventario(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'insumo_id' => 'nullable|exists:insumos,id',
            'tipo_movimiento' => 'nullable|in:entrada,salida,ajuste',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $query = MovimientoInventarioInsumo::with(['insumo', 'usuario'])
            ->whereBetween('fecha_movimiento', [$request->fecha_inicio, $request->fecha_fin]);

        if ($request->has('insumo_id')) {
            $query->where('insumo_id', $request->insumo_id);
        }

        if ($request->has('tipo_movimiento')) {
            $query->where('tipo_movimiento', $request->tipo_movimiento);
        }

        $movimientos = $query->orderBy('fecha_movimiento', 'desc')->get();

        $reporte = [
            'periodo' => [
                'inicio' => $request->fecha_inicio,
                'fin' => $request->fecha_fin,
            ],
            'resumen' => [
                'total_movimientos' => $movimientos->count(),
                'entradas' => $movimientos->where('tipo_movimiento', 'entrada')->count(),
                'salidas' => $movimientos->where('tipo_movimiento', 'salida')->count(),
                'ajustes' => $movimientos->where('tipo_movimiento', 'ajuste')->count(),
            ],
            'movimientos' => $movimientos->map(function($m) {
                return [
                    'fecha' => $m->fecha_movimiento,
                    'tipo' => $m->tipo_movimiento,
                    'insumo' => $m->insumo->nombre_insumo ?? null,
                    'cantidad' => $m->cantidad,
                    'unidad' => $m->insumo->unidad_medida ?? null,
                    'referencia' => $m->referencia,
                    'usuario' => $m->usuario->name ?? null,
                ];
            }),
        ];

        return response()->json($reporte);
    }
}
