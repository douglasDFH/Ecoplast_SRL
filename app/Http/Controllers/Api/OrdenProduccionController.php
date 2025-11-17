<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrdenProduccion;
use App\Models\ProductoTerminado;
use App\Models\Maquinaria;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * Controlador API para gestión de Órdenes de Producción Biodegradables
 *
 * Funcionalidades:
 * - CRUD completo de órdenes de producción
 * - Seguimiento de materiales biodegradables
 * - Cálculos de eficiencia y rendimiento
 * - Gestión de calidad para productos compostables
 * - Control de tiempos de producción
 */
class OrdenProduccionController extends Controller
{
    /**
     * Listar todas las órdenes de producción con filtros y paginación
     */
    public function index(Request $request): JsonResponse
    {
        $query = OrdenProduccion::with(['productoTerminado', 'maquina', 'operador', 'supervisor']);

        // Filtros biodegradables
        if ($request->has('producto_id')) {
            $query->where('producto_id', $request->producto_id);
        }

        if ($request->has('maquina_id')) {
            $query->where('maquina_id', $request->maquina_id);
        }

        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->has('prioridad')) {
            $query->where('prioridad', $request->prioridad);
        }

        if ($request->has('fecha_desde')) {
            $query->where('fecha_programada', '>=', $request->fecha_desde);
        }

        if ($request->has('fecha_hasta')) {
            $query->where('fecha_programada', '<=', $request->fecha_hasta);
        }

        if ($request->has('material_principal')) {
            $query->whereHas('productoTerminado', function ($q) use ($request) {
                $q->where('material_principal', $request->material_principal);
            });
        }

        // Ordenamiento
        $sortBy = $request->get('sort_by', 'fecha_programada');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        // Paginación
        $perPage = $request->get('per_page', 15);
        $ordenes = $query->paginate($perPage);

        // Añadir información biodegradable calculada
        $ordenes->getCollection()->transform(function ($orden) {
            $orden->eficiencia_produccion = $this->calcularEficienciaProduccion($orden);
            $orden->tiempo_restante = $this->calcularTiempoRestante($orden);
            $orden->material_biodegradable = $orden->productoTerminado->material_principal ?? null;
            $orden->certificado_compostable = $orden->productoTerminado->certificacion_compostable ?? null;
            return $orden;
        });

        return response()->json([
            'success' => true,
            'data' => $ordenes,
            'message' => 'Órdenes de producción recuperadas exitosamente'
        ]);
    }

    /**
     * Crear una nueva orden de producción biodegradable
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'numero_orden' => 'nullable|string|max:50|unique:ordenes_produccion',
            'producto_id' => 'required|exists:productos,id',
            'cantidad_planificada' => 'required|integer|min:1',
            'formulacion_id' => 'required|exists:formulaciones,id',
            'maquina_id' => 'required|exists:maquinas,id',
            'turno_id' => 'required|exists:turnos,id',
            'fecha_programada' => 'required|date|after_or_equal:today',
            'prioridad' => ['required', Rule::in(['baja', 'normal', 'alta', 'urgente'])],
            'operador_id' => 'nullable|exists:usuarios,id',
            'supervisor_id' => 'nullable|exists:usuarios,id',
            'notas_produccion' => 'nullable|string',
            'observaciones_calidad' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Verificar que el producto sea biodegradable
            $producto = ProductoTerminado::find($request->producto_id);
            if (!$this->esProductoBiodegradable($producto)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo se pueden crear órdenes para productos biodegradables certificados'
                ], 422);
            }

            // Verificar disponibilidad de máquina
            $maquina = Maquinaria::find($request->maquina_id);
            if ($maquina->estado_actual !== 'operativa') {
                return response()->json([
                    'success' => false,
                    'message' => 'La máquina seleccionada no está operativa'
                ], 422);
            }

            $orden = OrdenProduccion::create([
                'numero_orden' => $request->numero_orden ?? 'OP-' . now()->format('Ymd') . '-' . str_pad(OrdenProduccion::count() + 1, 4, '0', STR_PAD_LEFT),
                'producto_id' => $request->producto_id,
                'cantidad_planificada' => $request->cantidad_planificada,
                'formulacion_id' => $request->formulacion_id,
                'maquina_id' => $request->maquina_id,
                'turno_id' => $request->turno_id,
                'fecha_programada' => $request->fecha_programada,
                'prioridad' => $request->prioridad,
                'operador_id' => $request->operador_id,
                'supervisor_id' => $request->supervisor_id,
                'notas_produccion' => $request->notas_produccion,
                'observaciones_calidad' => $request->observaciones_calidad,
                'creado_por' => Auth::check() ? Auth::id() : null,
                'estado' => 'pendiente'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $orden->load(['productoTerminado', 'maquina', 'operador', 'supervisor']),
                'message' => 'Orden de producción creada exitosamente'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la orden: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar una orden específica con detalles
     */
    public function show(OrdenProduccion $orden): JsonResponse
    {
        $orden->load([
            'productoTerminado',
            'maquina',
            'operador',
            'supervisor',
            'registrosProduccion'
        ]);

        // Calcular progreso
        $progreso = 0;
        if ($orden->cantidad_planificada > 0) {
            $progreso = round(($orden->cantidad_producida / $orden->cantidad_planificada) * 100);
        }
        $orden->progreso = $progreso;

        return response()->json([
            'success' => true,
            'data' => $orden,
            'message' => 'Orden de producción recuperada exitosamente'
        ]);
    }

    /**
     * Actualizar una orden de producción
     */
    public function update(Request $request, OrdenProduccion $orden): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'numero_orden' => ['sometimes', 'string', 'max:50', Rule::unique('ordenes_produccion')->ignore($orden->id)],
            'producto_id' => 'sometimes|exists:productos,id',
            'cantidad_planificada' => 'sometimes|integer|min:1',
            'formulacion_id' => 'sometimes|exists:formulaciones,id',
            'maquina_id' => 'sometimes|exists:maquinas,id',
            'turno_id' => 'sometimes|exists:turnos,id',
            'fecha_programada' => 'sometimes|date',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date',
            'operador_id' => 'nullable|exists:usuarios,id',
            'supervisor_id' => 'nullable|exists:usuarios,id',
            'estado' => ['sometimes', Rule::in(['pendiente', 'en_proceso', 'pausada', 'completada', 'cancelada'])],
            'prioridad' => ['sometimes', Rule::in(['baja', 'normal', 'alta', 'urgente'])],
            'notas_produccion' => 'nullable|string',
            'observaciones_calidad' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Validaciones de estado
            if ($request->has('estado')) {
                if ($request->estado === 'en_proceso' && !$orden->fecha_inicio) {
                    $request->merge(['fecha_inicio' => now()]);
                }

                if ($request->estado === 'completada' && !$orden->fecha_fin) {
                    $request->merge(['fecha_fin' => now()]);
                }
            }

            $orden->update($request->all());

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $orden->load(['productoTerminado', 'maquina', 'operador', 'supervisor']),
                'message' => 'Orden de producción actualizada exitosamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la orden: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar una orden de producción
     */
    public function destroy(OrdenProduccion $orden): JsonResponse
    {
        try {
            // Solo permitir eliminar órdenes pendientes
            if (!in_array($orden->estado, ['pendiente', 'cancelada'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar una orden que ya ha iniciado producción'
                ], 409);
            }

            $orden->delete();

            return response()->json([
                'success' => true,
                'message' => 'Orden de producción eliminada exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la orden: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verificar si un producto es biodegradable
     */
    private function esProductoBiodegradable(?ProductoTerminado $producto): bool
    {
        if (!$producto) return false;

        $materialesBiodegradables = ['PLA', 'PHA', 'PBS', 'PBAT', 'Almidon'];
        return in_array($producto->material_principal, $materialesBiodegradables) &&
               !empty($producto->certificacion_compostable);
    }

    /**
     * Calcular eficiencia de producción
     */
    private function calcularEficienciaProduccion(OrdenProduccion $orden): ?float
    {
        if (!$orden->fecha_inicio || !$orden->fecha_fin) {
            return null;
        }

        // Eficiencia basada en cantidad producida vs planificada
        if ($orden->cantidad_planificada <= 0) return null;

        return ($orden->cantidad_producida / $orden->cantidad_planificada) * 100;
    }

    /**
     * Calcular tiempo restante para completar la orden
     */
    private function calcularTiempoRestante(OrdenProduccion $orden): ?int
    {
        if ($orden->estado === 'completada' || $orden->estado === 'cancelada') {
            return 0;
        }

        if (!$orden->fecha_programada) {
            return null;
        }

        return max(0, now()->diffInHours($orden->fecha_programada, false));
    }

    /**
     * Calcular progreso estimado de la orden
     */
    private function calcularProgresoEstimado(OrdenProduccion $orden): float
    {
        switch ($orden->estado) {
            case 'pendiente':
                return 0;
            case 'en_proceso':
                // Progreso basado en cantidad producida
                if ($orden->cantidad_planificada > 0) {
                    return min(95, ($orden->cantidad_producida / $orden->cantidad_planificada) * 100);
                }
                return 50; // Estimación por defecto
            case 'pausada':
                return max(0, $this->calcularProgresoEstimado($orden) - 10);
            case 'completada':
                return 100;
            case 'cancelada':
                return 0;
            default:
                return 0;
        }
    }

    /**
     * Obtener estadísticas generales de órdenes de producción
     */
    public function estadisticas(): JsonResponse
    {
        $estadisticas = [
            'total_ordenes' => OrdenProduccion::count(),
            'ordenes_pendientes' => OrdenProduccion::where('estado', 'pendiente')->count(),
            'ordenes_proceso' => OrdenProduccion::where('estado', 'en_proceso')->count(),
            'ordenes_completadas' => OrdenProduccion::where('estado', 'completada')->count(),
            'ordenes_canceladas' => OrdenProduccion::where('estado', 'cancelada')->count(),
            'produccion_mensual' => OrdenProduccion::where('estado', 'completada')
                ->whereMonth('fecha_fin', now()->month)
                ->sum('cantidad_producida'),
            'eficiencia_promedio' => OrdenProduccion::where('estado', 'completada')
                ->get()
                ->avg(function ($orden) {
                    return $this->calcularEficienciaProduccion($orden);
                }),
            'por_material' => OrdenProduccion::select('producto_id')
                ->with('productoTerminado')
                ->get()
                ->groupBy('productoTerminado.material_principal')
                ->map(function ($ordenes) {
                    return $ordenes->count();
                }),
            'ordenes_retrasadas' => OrdenProduccion::where('estado', '!=', 'completada')
                ->where('fecha_programada', '<', now())
                ->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $estadisticas,
            'message' => 'Estadísticas de órdenes de producción recuperadas exitosamente'
        ]);
    }

    /**
     * Obtener órdenes por estado
     */
    public function porEstado(): JsonResponse
    {
        $estados = ['pendiente', 'en_proceso', 'pausada', 'completada', 'cancelada'];

        $clasificadas = [];
        foreach ($estados as $estado) {
            $clasificadas[$estado] = OrdenProduccion::where('estado', $estado)
                ->with(['productoTerminado', 'maquina'])
                ->orderBy('prioridad', 'desc')
                ->orderBy('fecha_inicio_planificada', 'asc')
                ->get()
                ->map(function ($orden) {
                    $orden->eficiencia_produccion = $this->calcularEficienciaProduccion($orden);
                    $orden->tiempo_restante = $this->calcularTiempoRestante($orden);
                    return $orden;
                });
        }

        return response()->json([
            'success' => true,
            'data' => $clasificadas,
            'message' => 'Órdenes clasificadas por estado'
        ]);
    }

    /**
     * Iniciar producción de una orden
     */
    public function iniciarProduccion(OrdenProduccion $orden): JsonResponse
    {
        if ($orden->estado !== 'pendiente') {
            return response()->json([
                'success' => false,
                'message' => 'La orden debe estar pendiente para iniciar producción'
            ], 422);
        }

        try {
            $orden->update([
                'estado' => 'en_proceso',
                'fecha_inicio' => now()
            ]);

            return response()->json([
                'success' => true,
                'data' => $orden->load(['productoTerminado', 'maquina']),
                'message' => 'Producción iniciada exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al iniciar producción: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Finalizar producción de una orden
     */
    public function finalizarProduccion(Request $request, OrdenProduccion $orden): JsonResponse
    {
        if ($orden->estado !== 'en_proceso') {
            return response()->json([
                'success' => false,
                'message' => 'La orden debe estar en proceso para finalizar producción'
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'cantidad_producida' => 'required|integer|min:0',
            'cantidad_conforme' => 'required|integer|min:0',
            'cantidad_defectuosa' => 'required|integer|min:0',
            'observaciones_calidad' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $orden->update([
                'estado' => 'completada',
                'fecha_fin' => now(),
                'cantidad_producida' => $request->cantidad_producida,
                'cantidad_conforme' => $request->cantidad_conforme,
                'cantidad_defectuosa' => $request->cantidad_defectuosa,
                'observaciones_calidad' => $request->observaciones_calidad
            ]);

            return response()->json([
                'success' => true,
                'data' => $orden->load(['productoTerminado', 'maquina']),
                'message' => 'Producción finalizada exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al finalizar producción: ' . $e->getMessage()
            ], 500);
        }
    }
}
