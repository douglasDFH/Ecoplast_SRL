<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InspeccionCalidad;
use App\Models\LoteProduccion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

/**
 * API Controller para Inspecciones de Calidad
 * 
 * Gestiona inspecciones de calidad de productos biodegradables,
 * pruebas físicas, certificaciones y control de no conformidades.
 */
class InspeccionCalidadController extends Controller
{
    /**
     * Listar inspecciones con filtros
     */
    public function index(Request $request): JsonResponse
    {
        $query = InspeccionCalidad::with([
            'loteProduccion.productoTerminado',
            'inspector',
            'aprobadoPor'
        ]);

        // Filtros
        if ($request->has('lote_id')) {
            $query->where('lote_id', $request->lote_id);
        }

        if ($request->has('tipo_inspeccion')) {
            $query->where('tipo_inspeccion', $request->tipo_inspeccion);
        }

        if ($request->has('resultado')) {
            $query->where('resultado', $request->resultado);
        }

        if ($request->has('inspector_id')) {
            $query->where('inspector_id', $request->inspector_id);
        }

        // Rango de fechas
        if ($request->has('fecha_inicio') && $request->has('fecha_fin')) {
            $query->whereBetween('fecha_inspeccion', [
                $request->fecha_inicio,
                $request->fecha_fin
            ]);
        }

        // Inspecciones pendientes de aprobación
        if ($request->has('pendientes_aprobacion')) {
            $query->whereNull('aprobado_por')->where('resultado', '!=', 'aprobado');
        }

        $inspecciones = $query->orderBy('fecha_inspeccion', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json($inspecciones);
    }

    /**
     * Mostrar inspección específica
     */
    public function show(int $id): JsonResponse
    {
        $inspeccion = InspeccionCalidad::with([
            'loteProduccion.productoTerminado.categoria',
            'loteProduccion.ordenProduccion',
            'inspector',
            'aprobadoPor'
        ])->findOrFail($id);

        return response()->json($inspeccion);
    }

    /**
     * Crear nueva inspección
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'lote_id' => 'required|exists:lotes_produccion,id',
            'tipo_inspeccion' => 'required|in:visual,dimensional,peso,espesor,resistencia,biodegradabilidad,compostabilidad',
            'inspector_id' => 'required|exists:users,id',
            'fecha_inspeccion' => 'required|date',
            'resultado' => 'required|in:aprobado,rechazado,condicional',
            'muestra_cantidad' => 'nullable|integer|min:1',
            'unidades_conformes' => 'nullable|integer|min:0',
            'unidades_defectuosas' => 'nullable|integer|min:0',
            
            // Mediciones dimensionales
            'espesor_micras' => 'nullable|numeric|min:0',
            'ancho_mm' => 'nullable|numeric|min:0',
            'largo_mm' => 'nullable|numeric|min:0',
            'peso_gramos' => 'nullable|numeric|min:0',
            
            // Pruebas mecánicas
            'resistencia_traccion_mpa' => 'nullable|numeric|min:0',
            'elongacion_rotura_percent' => 'nullable|numeric|min:0',
            'resistencia_perforacion_n' => 'nullable|numeric|min:0',
            
            // Pruebas de biodegradabilidad
            'tiempo_degradacion_dias' => 'nullable|integer|min:0',
            'porcentaje_biodegradado' => 'nullable|numeric|min:0|max:100',
            'certificacion_obtenida' => 'nullable|string|max:100',
            'numero_certificado' => 'nullable|string|max:100',
            
            // Defectos encontrados
            'defectos_encontrados' => 'nullable|json',
            'observaciones' => 'nullable|string|max:1000',
            'accion_correctiva' => 'nullable|string|max:500',
            'aprobado_por' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $inspeccion = InspeccionCalidad::create($request->all());

            // Si el resultado es rechazado, actualizar estado del lote
            if ($request->resultado === 'rechazado') {
                $lote = LoteProduccion::findOrFail($request->lote_id);
                $lote->update(['estado' => 'rechazado']);
            }

            DB::commit();

            return response()->json([
                'message' => 'Inspección registrada exitosamente',
                'data' => $inspeccion->load('loteProduccion', 'inspector')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al crear inspección',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar inspección
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $inspeccion = InspeccionCalidad::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'lote_id' => 'sometimes|exists:lotes_produccion,id',
            'tipo_inspeccion' => 'sometimes|in:visual,dimensional,peso,espesor,resistencia,biodegradabilidad,compostabilidad',
            'inspector_id' => 'sometimes|exists:users,id',
            'fecha_inspeccion' => 'sometimes|date',
            'resultado' => 'sometimes|in:aprobado,rechazado,condicional',
            'muestra_cantidad' => 'nullable|integer|min:1',
            'unidades_conformes' => 'nullable|integer|min:0',
            'unidades_defectuosas' => 'nullable|integer|min:0',
            'espesor_micras' => 'nullable|numeric|min:0',
            'ancho_mm' => 'nullable|numeric|min:0',
            'largo_mm' => 'nullable|numeric|min:0',
            'peso_gramos' => 'nullable|numeric|min:0',
            'resistencia_traccion_mpa' => 'nullable|numeric|min:0',
            'elongacion_rotura_percent' => 'nullable|numeric|min:0',
            'resistencia_perforacion_n' => 'nullable|numeric|min:0',
            'tiempo_degradacion_dias' => 'nullable|integer|min:0',
            'porcentaje_biodegradado' => 'nullable|numeric|min:0|max:100',
            'certificacion_obtenida' => 'nullable|string|max:100',
            'numero_certificado' => 'nullable|string|max:100',
            'defectos_encontrados' => 'nullable|json',
            'observaciones' => 'nullable|string|max:1000',
            'accion_correctiva' => 'nullable|string|max:500',
            'aprobado_por' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $resultadoAnterior = $inspeccion->resultado;
            $inspeccion->update($request->all());

            // Si el resultado cambió, actualizar estado del lote
            if ($request->has('resultado') && $request->resultado !== $resultadoAnterior) {
                $lote = LoteProduccion::findOrFail($inspeccion->lote_id);
                
                if ($request->resultado === 'rechazado') {
                    $lote->update(['estado' => 'rechazado']);
                } elseif ($request->resultado === 'aprobado' && $lote->estado === 'cuarentena') {
                    $lote->update(['estado' => 'disponible']);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Inspección actualizada exitosamente',
                'data' => $inspeccion->fresh()->load('loteProduccion', 'inspector')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al actualizar inspección',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar inspección
     */
    public function destroy(int $id): JsonResponse
    {
        $inspeccion = InspeccionCalidad::findOrFail($id);

        // Solo permitir eliminar inspecciones no aprobadas
        if ($inspeccion->aprobado_por) {
            return response()->json([
                'message' => 'No se puede eliminar una inspección que ya fue aprobada'
            ], 400);
        }

        $inspeccion->delete();

        return response()->json([
            'message' => 'Inspección eliminada exitosamente'
        ]);
    }

    /**
     * Aprobar inspección
     */
    public function aprobar(Request $request, int $id): JsonResponse
    {
        $inspeccion = InspeccionCalidad::findOrFail($id);

        if ($inspeccion->aprobado_por) {
            return response()->json([
                'message' => 'Esta inspección ya fue aprobada'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'aprobado_por' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $inspeccion->update([
            'aprobado_por' => $request->aprobado_por,
            'fecha_aprobacion' => now(),
        ]);

        // Si la inspección es aprobada, cambiar lote de cuarentena a disponible
        if ($inspeccion->resultado === 'aprobado') {
            $lote = LoteProduccion::findOrFail($inspeccion->lote_id);
            if ($lote->estado === 'cuarentena') {
                $lote->update(['estado' => 'disponible']);
            }
        }

        return response()->json([
            'message' => 'Inspección aprobada exitosamente',
            'data' => $inspeccion->fresh()
        ]);
    }

    /**
     * Obtener estadísticas de calidad
     */
    public function estadisticas(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'tipo_inspeccion' => 'nullable|in:visual,dimensional,peso,espesor,resistencia,biodegradabilidad,compostabilidad',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $query = InspeccionCalidad::whereBetween('fecha_inspeccion', [
            $request->fecha_inicio,
            $request->fecha_fin
        ]);

        if ($request->has('tipo_inspeccion')) {
            $query->where('tipo_inspeccion', $request->tipo_inspeccion);
        }

        $inspecciones = $query->get();

        $estadisticas = [
            'total_inspecciones' => $inspecciones->count(),
            'aprobadas' => $inspecciones->where('resultado', 'aprobado')->count(),
            'rechazadas' => $inspecciones->where('resultado', 'rechazado')->count(),
            'condicionales' => $inspecciones->where('resultado', 'condicional')->count(),
            'porcentaje_aprobacion' => $inspecciones->count() > 0
                ? round(($inspecciones->where('resultado', 'aprobado')->count() / $inspecciones->count()) * 100, 2)
                : 0,
            'porcentaje_rechazo' => $inspecciones->count() > 0
                ? round(($inspecciones->where('resultado', 'rechazado')->count() / $inspecciones->count()) * 100, 2)
                : 0,
            'por_tipo' => $inspecciones->groupBy('tipo_inspeccion')->map(function($grupo) {
                return [
                    'total' => $grupo->count(),
                    'aprobadas' => $grupo->where('resultado', 'aprobado')->count(),
                    'rechazadas' => $grupo->where('resultado', 'rechazado')->count(),
                ];
            }),
            'promedios' => [
                'espesor_micras' => round($inspecciones->avg('espesor_micras'), 2),
                'resistencia_traccion_mpa' => round($inspecciones->avg('resistencia_traccion_mpa'), 2),
                'elongacion_rotura_percent' => round($inspecciones->avg('elongacion_rotura_percent'), 2),
                'porcentaje_biodegradado' => round($inspecciones->avg('porcentaje_biodegradado'), 2),
            ],
        ];

        return response()->json([
            'periodo' => [
                'inicio' => $request->fecha_inicio,
                'fin' => $request->fecha_fin,
            ],
            'estadisticas' => $estadisticas
        ]);
    }

    /**
     * Obtener defectos más comunes
     */
    public function defectosComunes(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $inspecciones = InspeccionCalidad::whereBetween('fecha_inspeccion', [
                $request->fecha_inicio,
                $request->fecha_fin
            ])
            ->whereNotNull('defectos_encontrados')
            ->get();

        // Consolidar defectos
        $defectos = [];
        foreach ($inspecciones as $inspeccion) {
            if ($inspeccion->defectos_encontrados) {
                $defectosArray = is_string($inspeccion->defectos_encontrados) 
                    ? json_decode($inspeccion->defectos_encontrados, true) 
                    : $inspeccion->defectos_encontrados;
                
                if (is_array($defectosArray)) {
                    foreach ($defectosArray as $defecto) {
                        $tipo = $defecto['tipo'] ?? 'desconocido';
                        if (!isset($defectos[$tipo])) {
                            $defectos[$tipo] = [
                                'tipo' => $tipo,
                                'ocurrencias' => 0,
                                'total_unidades' => 0,
                            ];
                        }
                        $defectos[$tipo]['ocurrencias']++;
                        $defectos[$tipo]['total_unidades'] += $defecto['cantidad'] ?? 1;
                    }
                }
            }
        }

        // Ordenar por ocurrencias
        usort($defectos, function($a, $b) {
            return $b['ocurrencias'] - $a['ocurrencias'];
        });

        return response()->json([
            'periodo' => [
                'inicio' => $request->fecha_inicio,
                'fin' => $request->fecha_fin,
            ],
            'defectos_comunes' => array_values($defectos)
        ]);
    }
}
