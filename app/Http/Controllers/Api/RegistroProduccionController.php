<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RegistroProduccion;
use App\Models\OrdenProduccion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

/**
 * API Controller para Registros de Producción
 * 
 * Gestiona los registros horarios/por lote de producción con métricas
 * de productividad, calidad, mermas y tiempos muertos.
 */
class RegistroProduccionController extends Controller
{
    /**
     * Listar registros de producción con filtros
     */
    public function index(Request $request): JsonResponse
    {
        $query = RegistroProduccion::with([
            'ordenProduccion.productoTerminado',
            'ordenProduccion.maquina',
            'loteProduccion',
            'turno',
            'operador'
        ]);

        // Filtros
        if ($request->has('orden_produccion_id')) {
            $query->where('orden_produccion_id', $request->orden_produccion_id);
        }

        if ($request->has('lote_id')) {
            $query->where('lote_id', $request->lote_id);
        }

        if ($request->has('turno_id')) {
            $query->where('turno_id', $request->turno_id);
        }

        if ($request->has('fecha_inicio') && $request->has('fecha_fin')) {
            $query->whereBetween('fecha_hora_inicio', [
                $request->fecha_inicio,
                $request->fecha_fin
            ]);
        }

        // Búsqueda por código de lote
        if ($request->has('buscar')) {
            $buscar = $request->buscar;
            $query->whereHas('loteProduccion', function($q) use ($buscar) {
                $q->where('codigo_lote', 'like', "%{$buscar}%");
            });
        }

        $registros = $query->orderBy('fecha_hora_inicio', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json($registros);
    }

    /**
     * Mostrar registro específico
     */
    public function show(int $id): JsonResponse
    {
        $registro = RegistroProduccion::with([
            'ordenProduccion.productoTerminado',
            'ordenProduccion.maquina',
            'loteProduccion',
            'turno',
            'operador',
            'supervisor'
        ])->findOrFail($id);

        return response()->json($registro);
    }

    /**
     * Crear nuevo registro de producción
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'orden_produccion_id' => 'required|exists:ordenes_produccion,id',
            'lote_id' => 'nullable|exists:lotes_produccion,id',
            'turno_id' => 'required|exists:turnos,id',
            'operador_id' => 'required|exists:users,id',
            'supervisor_id' => 'nullable|exists:users,id',
            'fecha_hora_inicio' => 'required|date',
            'fecha_hora_fin' => 'nullable|date|after:fecha_hora_inicio',
            'cantidad_producida' => 'required|integer|min:0',
            'cantidad_conforme' => 'required|integer|min:0',
            'cantidad_defectuosa' => 'required|integer|min:0',
            'merma_kg' => 'nullable|numeric|min:0',
            'tiempo_paro_minutos' => 'nullable|integer|min:0',
            'temperatura_promedio' => 'nullable|numeric',
            'presion_promedio' => 'nullable|numeric',
            'velocidad_promedio' => 'nullable|numeric',
            'observaciones' => 'nullable|string|max:500',
            'incidencias' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $registro = RegistroProduccion::create($request->all());

            // Actualizar totales en la orden de producción
            $orden = OrdenProduccion::findOrFail($request->orden_produccion_id);
            $orden->cantidad_producida += $request->cantidad_producida;
            $orden->cantidad_conforme += $request->cantidad_conforme;
            $orden->cantidad_defectuosa += $request->cantidad_defectuosa;
            
            // Si se alcanzó la cantidad planificada, cambiar estado
            if ($orden->cantidad_producida >= $orden->cantidad_planificada) {
                $orden->estado = 'completada';
                $orden->fecha_fin = now();
            }
            
            $orden->save();

            DB::commit();

            return response()->json([
                'message' => 'Registro de producción creado exitosamente',
                'data' => $registro->load([
                    'ordenProduccion',
                    'loteProduccion',
                    'turno',
                    'operador'
                ])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al crear registro de producción',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar registro de producción
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $registro = RegistroProduccion::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'orden_produccion_id' => 'sometimes|exists:ordenes_produccion,id',
            'lote_id' => 'nullable|exists:lotes_produccion,id',
            'turno_id' => 'sometimes|exists:turnos,id',
            'operador_id' => 'sometimes|exists:users,id',
            'supervisor_id' => 'nullable|exists:users,id',
            'fecha_hora_inicio' => 'sometimes|date',
            'fecha_hora_fin' => 'nullable|date|after:fecha_hora_inicio',
            'cantidad_producida' => 'sometimes|integer|min:0',
            'cantidad_conforme' => 'sometimes|integer|min:0',
            'cantidad_defectuosa' => 'sometimes|integer|min:0',
            'merma_kg' => 'nullable|numeric|min:0',
            'tiempo_paro_minutos' => 'nullable|integer|min:0',
            'temperatura_promedio' => 'nullable|numeric',
            'presion_promedio' => 'nullable|numeric',
            'velocidad_promedio' => 'nullable|numeric',
            'observaciones' => 'nullable|string|max:500',
            'incidencias' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Guardar valores anteriores para ajustar la orden
            $cantidadAnterior = $registro->cantidad_producida;
            $conformeAnterior = $registro->cantidad_conforme;
            $defectuosaAnterior = $registro->cantidad_defectuosa;

            $registro->update($request->all());

            // Ajustar orden de producción si cambiaron cantidades
            if ($request->has('cantidad_producida') || 
                $request->has('cantidad_conforme') || 
                $request->has('cantidad_defectuosa')) {
                
                $orden = OrdenProduccion::findOrFail($registro->orden_produccion_id);
                $orden->cantidad_producida += ($registro->cantidad_producida - $cantidadAnterior);
                $orden->cantidad_conforme += ($registro->cantidad_conforme - $conformeAnterior);
                $orden->cantidad_defectuosa += ($registro->cantidad_defectuosa - $defectuosaAnterior);
                $orden->save();
            }

            DB::commit();

            return response()->json([
                'message' => 'Registro actualizado exitosamente',
                'data' => $registro->fresh()->load([
                    'ordenProduccion',
                    'loteProduccion',
                    'turno',
                    'operador'
                ])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al actualizar registro',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar registro de producción
     */
    public function destroy(int $id): JsonResponse
    {
        $registro = RegistroProduccion::findOrFail($id);

        DB::beginTransaction();
        try {
            // Ajustar orden de producción
            $orden = OrdenProduccion::findOrFail($registro->orden_produccion_id);
            $orden->cantidad_producida -= $registro->cantidad_producida;
            $orden->cantidad_conforme -= $registro->cantidad_conforme;
            $orden->cantidad_defectuosa -= $registro->cantidad_defectuosa;
            $orden->save();

            $registro->delete();

            DB::commit();

            return response()->json([
                'message' => 'Registro eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al eliminar registro',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener KPIs de producción para un periodo
     */
    public function kpis(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'turno_id' => 'nullable|exists:turnos,id',
            'orden_produccion_id' => 'nullable|exists:ordenes_produccion,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $query = RegistroProduccion::whereBetween('fecha_hora_inicio', [
            $request->fecha_inicio,
            $request->fecha_fin
        ]);

        if ($request->has('turno_id')) {
            $query->where('turno_id', $request->turno_id);
        }

        if ($request->has('orden_produccion_id')) {
            $query->where('orden_produccion_id', $request->orden_produccion_id);
        }

        $registros = $query->get();

        $kpis = [
            'total_registros' => $registros->count(),
            'cantidad_total_producida' => $registros->sum('cantidad_producida'),
            'cantidad_total_conforme' => $registros->sum('cantidad_conforme'),
            'cantidad_total_defectuosa' => $registros->sum('cantidad_defectuosa'),
            'merma_total_kg' => $registros->sum('merma_kg'),
            'tiempo_paro_total_minutos' => $registros->sum('tiempo_paro_minutos'),
            'productividad_promedio' => $registros->avg('productividad_unidades_hora'),
            'porcentaje_calidad' => $registros->sum('cantidad_producida') > 0 
                ? round(($registros->sum('cantidad_conforme') / $registros->sum('cantidad_producida')) * 100, 2)
                : 0,
            'porcentaje_defectos' => $registros->sum('cantidad_producida') > 0
                ? round(($registros->sum('cantidad_defectuosa') / $registros->sum('cantidad_producida')) * 100, 2)
                : 0,
            'temperatura_promedio' => round($registros->avg('temperatura_promedio'), 1),
            'presion_promedio' => round($registros->avg('presion_promedio'), 2),
            'velocidad_promedio' => round($registros->avg('velocidad_promedio'), 2),
        ];

        return response()->json([
            'periodo' => [
                'inicio' => $request->fecha_inicio,
                'fin' => $request->fecha_fin,
            ],
            'kpis' => $kpis
        ]);
    }

    /**
     * Finalizar registro (cerrar hora de fin)
     */
    public function finalizar(Request $request, int $id): JsonResponse
    {
        $registro = RegistroProduccion::findOrFail($id);

        if ($registro->fecha_hora_fin) {
            return response()->json([
                'message' => 'Este registro ya ha sido finalizado'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'fecha_hora_fin' => 'required|date|after:' . $registro->fecha_hora_inicio,
            'observaciones' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $registro->update([
            'fecha_hora_fin' => $request->fecha_hora_fin,
            'observaciones' => $request->observaciones ?? $registro->observaciones,
        ]);

        return response()->json([
            'message' => 'Registro finalizado exitosamente',
            'data' => $registro->fresh()
        ]);
    }
}
