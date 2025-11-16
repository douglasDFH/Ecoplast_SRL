<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Formulacion;
use App\Models\FormulacionInsumo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

/**
 * API Controller para Formulaciones de Productos
 * 
 * Gestiona las recetas/fórmulas de productos biodegradables
 * con sus insumos, proporciones y especificaciones técnicas.
 */
class FormulacionController extends Controller
{
    /**
     * Listar formulaciones con filtros
     */
    public function index(Request $request): JsonResponse
    {
        $query = Formulacion::with(['producto', 'insumos.insumo']);

        // Filtros
        if ($request->has('producto_id')) {
            $query->where('producto_id', $request->producto_id);
        }

        if ($request->has('activa')) {
            $query->where('activa', $request->boolean('activa'));
        }

        // Búsqueda por código o nombre
        if ($request->has('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('codigo_formulacion', 'like', "%{$buscar}%")
                  ->orWhere('nombre_formulacion', 'like', "%{$buscar}%");
            });
        }

        $formulaciones = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json($formulaciones);
    }

    /**
     * Mostrar formulación específica con detalle de insumos
     */
    public function show(int $id): JsonResponse
    {
        $formulacion = Formulacion::with([
            'producto.categoria',
            'insumos.insumo.categoria',
            'ordenesProduccion'
        ])->findOrFail($id);

        return response()->json($formulacion);
    }

    /**
     * Crear nueva formulación
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'codigo_formulacion' => 'required|string|max:50|unique:formulaciones,codigo_formulacion',
            'nombre_formulacion' => 'required|string|max:150',
            'producto_id' => 'required|exists:productos,id',
            'version' => 'nullable|string|max:20',
            'descripcion' => 'nullable|string|max:500',
            'temperatura_procesamiento' => 'nullable|numeric',
            'presion_procesamiento' => 'nullable|numeric',
            'tiempo_ciclo_segundos' => 'nullable|integer|min:1',
            'rendimiento_kg_hora' => 'nullable|numeric|min:0',
            'notas_produccion' => 'nullable|string|max:1000',
            'activa' => 'nullable|boolean',
            
            // Insumos de la formulación
            'insumos' => 'required|array|min:1',
            'insumos.*.insumo_id' => 'required|exists:insumos,id',
            'insumos.*.porcentaje' => 'required|numeric|min:0|max:100',
            'insumos.*.cantidad_kg_por_lote' => 'required|numeric|min:0',
            'insumos.*.notas' => 'nullable|string|max:200',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        // Validar que los porcentajes sumen 100%
        $sumaPorcentajes = collect($request->insumos)->sum('porcentaje');
        if (abs($sumaPorcentajes - 100) > 0.1) {
            return response()->json([
                'message' => 'Los porcentajes de los insumos deben sumar 100%',
                'suma_actual' => $sumaPorcentajes
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Crear formulación
            $formulacion = Formulacion::create([
                'codigo_formulacion' => $request->codigo_formulacion,
                'nombre_formulacion' => $request->nombre_formulacion,
                'producto_id' => $request->producto_id,
                'version' => $request->version,
                'descripcion' => $request->descripcion,
                'temperatura_procesamiento' => $request->temperatura_procesamiento,
                'presion_procesamiento' => $request->presion_procesamiento,
                'tiempo_ciclo_segundos' => $request->tiempo_ciclo_segundos,
                'rendimiento_kg_hora' => $request->rendimiento_kg_hora,
                'notas_produccion' => $request->notas_produccion,
                'activa' => $request->get('activa', true),
            ]);

            // Crear insumos de la formulación
            foreach ($request->insumos as $insumoData) {
                FormulacionInsumo::create([
                    'formulacion_id' => $formulacion->id,
                    'insumo_id' => $insumoData['insumo_id'],
                    'porcentaje' => $insumoData['porcentaje'],
                    'cantidad_kg_por_lote' => $insumoData['cantidad_kg_por_lote'],
                    'notas' => $insumoData['notas'] ?? null,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Formulación creada exitosamente',
                'data' => $formulacion->load('insumos.insumo')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al crear formulación',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar formulación
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $formulacion = Formulacion::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'codigo_formulacion' => 'sometimes|string|max:50|unique:formulaciones,codigo_formulacion,' . $id,
            'nombre_formulacion' => 'sometimes|string|max:150',
            'producto_id' => 'sometimes|exists:productos,id',
            'version' => 'nullable|string|max:20',
            'descripcion' => 'nullable|string|max:500',
            'temperatura_procesamiento' => 'nullable|numeric',
            'presion_procesamiento' => 'nullable|numeric',
            'tiempo_ciclo_segundos' => 'nullable|integer|min:1',
            'rendimiento_kg_hora' => 'nullable|numeric|min:0',
            'notas_produccion' => 'nullable|string|max:1000',
            'activa' => 'nullable|boolean',
            
            'insumos' => 'sometimes|array|min:1',
            'insumos.*.insumo_id' => 'required|exists:insumos,id',
            'insumos.*.porcentaje' => 'required|numeric|min:0|max:100',
            'insumos.*.cantidad_kg_por_lote' => 'required|numeric|min:0',
            'insumos.*.notas' => 'nullable|string|max:200',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        // Validar suma de porcentajes si se actualizan insumos
        if ($request->has('insumos')) {
            $sumaPorcentajes = collect($request->insumos)->sum('porcentaje');
            if (abs($sumaPorcentajes - 100) > 0.1) {
                return response()->json([
                    'message' => 'Los porcentajes de los insumos deben sumar 100%',
                    'suma_actual' => $sumaPorcentajes
                ], 422);
            }
        }

        DB::beginTransaction();
        try {
            // Actualizar formulación
            $formulacion->update($request->except('insumos'));

            // Actualizar insumos si se enviaron
            if ($request->has('insumos')) {
                // Eliminar insumos anteriores
                FormulacionInsumo::where('formulacion_id', $formulacion->id)->delete();

                // Crear nuevos insumos
                foreach ($request->insumos as $insumoData) {
                    FormulacionInsumo::create([
                        'formulacion_id' => $formulacion->id,
                        'insumo_id' => $insumoData['insumo_id'],
                        'porcentaje' => $insumoData['porcentaje'],
                        'cantidad_kg_por_lote' => $insumoData['cantidad_kg_por_lote'],
                        'notas' => $insumoData['notas'] ?? null,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Formulación actualizada exitosamente',
                'data' => $formulacion->fresh()->load('insumos.insumo')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al actualizar formulación',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar formulación
     */
    public function destroy(int $id): JsonResponse
    {
        $formulacion = Formulacion::findOrFail($id);

        // Verificar que no tenga órdenes de producción
        if ($formulacion->ordenesProduccion()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar una formulación que tiene órdenes de producción asociadas'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Eliminar insumos de la formulación
            FormulacionInsumo::where('formulacion_id', $formulacion->id)->delete();
            
            // Eliminar formulación
            $formulacion->delete();

            DB::commit();

            return response()->json([
                'message' => 'Formulación eliminada exitosamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al eliminar formulación',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Activar/desactivar formulación
     */
    public function toggleActiva(int $id): JsonResponse
    {
        $formulacion = Formulacion::findOrFail($id);
        
        $formulacion->update([
            'activa' => !$formulacion->activa
        ]);

        return response()->json([
            'message' => $formulacion->activa ? 'Formulación activada' : 'Formulación desactivada',
            'data' => $formulacion
        ]);
    }

    /**
     * Calcular costo de producción de una formulación
     */
    public function calcularCosto(int $id): JsonResponse
    {
        $formulacion = Formulacion::with('insumos.insumo')->findOrFail($id);

        $costoTotal = 0;
        $detalleInsumos = [];

        foreach ($formulacion->insumos as $formulacionInsumo) {
            $insumo = $formulacionInsumo->insumo;
            $costoInsumo = $formulacionInsumo->cantidad_kg_por_lote * $insumo->precio_unitario;
            $costoTotal += $costoInsumo;

            $detalleInsumos[] = [
                'insumo' => $insumo->nombre_insumo,
                'cantidad_kg' => $formulacionInsumo->cantidad_kg_por_lote,
                'precio_unitario' => $insumo->precio_unitario,
                'costo_total' => round($costoInsumo, 2),
            ];
        }

        return response()->json([
            'formulacion' => $formulacion->nombre_formulacion,
            'costo_total_por_lote' => round($costoTotal, 2),
            'detalle_insumos' => $detalleInsumos,
        ]);
    }

    /**
     * Verificar disponibilidad de insumos para producir
     */
    public function verificarDisponibilidad(Request $request, int $id): JsonResponse
    {
        $formulacion = Formulacion::with('insumos.insumo')->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'cantidad_lotes' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $cantidadLotes = $request->cantidad_lotes;
        $disponible = true;
        $detalleInsumos = [];

        foreach ($formulacion->insumos as $formulacionInsumo) {
            $insumo = $formulacionInsumo->insumo;
            $cantidadNecesaria = $formulacionInsumo->cantidad_kg_por_lote * $cantidadLotes;
            $suficiente = $insumo->stock_actual >= $cantidadNecesaria;

            if (!$suficiente) {
                $disponible = false;
            }

            $detalleInsumos[] = [
                'insumo_id' => $insumo->id,
                'insumo' => $insumo->nombre_insumo,
                'cantidad_necesaria' => round($cantidadNecesaria, 2),
                'stock_actual' => round($insumo->stock_actual, 2),
                'faltante' => $suficiente ? 0 : round($cantidadNecesaria - $insumo->stock_actual, 2),
                'suficiente' => $suficiente,
            ];
        }

        return response()->json([
            'disponible' => $disponible,
            'cantidad_lotes_solicitados' => $cantidadLotes,
            'detalle_insumos' => $detalleInsumos,
        ]);
    }

    /**
     * Clonar formulación (crear una nueva versión)
     */
    public function clonar(Request $request, int $id): JsonResponse
    {
        $formulacionOriginal = Formulacion::with('insumos')->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'codigo_formulacion' => 'required|string|max:50|unique:formulaciones,codigo_formulacion',
            'nombre_formulacion' => 'required|string|max:150',
            'version' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Crear nueva formulación
            $nuevaFormulacion = Formulacion::create([
                'codigo_formulacion' => $request->codigo_formulacion,
                'nombre_formulacion' => $request->nombre_formulacion,
                'producto_id' => $formulacionOriginal->producto_id,
                'version' => $request->version,
                'descripcion' => $formulacionOriginal->descripcion,
                'temperatura_procesamiento' => $formulacionOriginal->temperatura_procesamiento,
                'presion_procesamiento' => $formulacionOriginal->presion_procesamiento,
                'tiempo_ciclo_segundos' => $formulacionOriginal->tiempo_ciclo_segundos,
                'rendimiento_kg_hora' => $formulacionOriginal->rendimiento_kg_hora,
                'notas_produccion' => $formulacionOriginal->notas_produccion,
                'activa' => false, // Nueva versión inactiva por defecto
            ]);

            // Copiar insumos
            foreach ($formulacionOriginal->insumos as $insumoOriginal) {
                FormulacionInsumo::create([
                    'formulacion_id' => $nuevaFormulacion->id,
                    'insumo_id' => $insumoOriginal->insumo_id,
                    'porcentaje' => $insumoOriginal->porcentaje,
                    'cantidad_kg_por_lote' => $insumoOriginal->cantidad_kg_por_lote,
                    'notas' => $insumoOriginal->notas,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Formulación clonada exitosamente',
                'data' => $nuevaFormulacion->load('insumos.insumo')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al clonar formulación',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
