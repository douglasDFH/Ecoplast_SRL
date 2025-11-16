<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Insumo;
use App\Models\CategoriaInsumo;
use App\Events\InventarioActualizado;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Controlador API para gestión de Insumos Biodegradables
 *
 * Funcionalidades:
 * - CRUD completo de insumos
 * - Gestión de inventario con alertas de stock bajo
 * - Filtros por categoría, tipo de material, certificación
 * - Cálculo automático de costos y disponibilidad
 */
class InsumoController extends Controller
{
    /**
     * Listar todos los insumos con filtros y paginación
     */
    public function index(Request $request): JsonResponse
    {
        $query = Insumo::with('categoria');

        // Filtros
        if ($request->has('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }

        if ($request->has('tipo_material')) {
            $query->where('tipo_material', $request->tipo_material);
        }

        if ($request->has('certificacion')) {
            $query->where('certificacion_biodegradable', 'like', '%' . $request->certificacion . '%');
        }

        if ($request->has('activo')) {
            $query->where('activo', $request->boolean('activo'));
        }

        // Alertas de stock bajo
        if ($request->has('stock_bajo')) {
            $query->whereColumn('stock_actual', '<=', 'stock_minimo');
        }

        // Ordenamiento
        $sortBy = $request->get('sort_by', 'codigo_insumo');
        $sortDirection = $request->get('sort_direction', 'asc');
        $query->orderBy($sortBy, $sortDirection);

        // Paginación
        $perPage = $request->get('per_page', 15);
        $insumos = $query->paginate($perPage);

        // Añadir información adicional
        $insumos->getCollection()->transform(function ($insumo) {
            $insumo->estado_stock = $this->calcularEstadoStock($insumo);
            $insumo->disponibilidad_dias = $this->calcularDisponibilidadDias($insumo);
            return $insumo;
        });

        return response()->json([
            'success' => true,
            'data' => $insumos,
            'message' => 'Insumos recuperados exitosamente'
        ]);
    }

    /**
     * Crear un nuevo insumo biodegradable
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'codigo_insumo' => 'required|string|max:50|unique:insumos',
            'nombre_insumo' => 'required|string|max:150',
            'categoria_id' => 'required|exists:categorias_insumos,id',
            'tipo_material' => ['required', Rule::in(['PLA', 'PHA', 'PBS', 'PBAT', 'Almidon', 'Celulosa', 'Aditivo', 'Pigmento', 'Otro'])],
            'unidad_medida' => ['required', Rule::in(['kg', 'ton', 'litro', 'unidad'])],
            'densidad' => 'nullable|numeric|min:0|max:10',
            'temperatura_fusion' => 'nullable|numeric|min:0|max:500',
            'certificacion_biodegradable' => 'nullable|string|max:100',
            'tiempo_biodegradacion_dias' => 'nullable|integer|min:1|max:1000',
            'proveedor' => 'nullable|string|max:150',
            'precio_unitario' => 'required|numeric|min:0',
            'stock_minimo' => 'required|numeric|min:0',
            'stock_actual' => 'required|numeric|min:0',
            'activo' => 'boolean'
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

            $insumo = Insumo::create($request->all());

            // Verificar si necesita alerta de stock
            if ($insumo->stock_actual <= $insumo->stock_minimo) {
                $this->generarAlertaStockBajo($insumo);
            }

            DB::commit();

            // Emitir evento de creación de inventario en tiempo real
            broadcast(new InventarioActualizado($insumo, 'creada'))->toOthers();

            return response()->json([
                'success' => true,
                'data' => $insumo->load('categoria'),
                'message' => 'Insumo creado exitosamente'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el insumo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar un insumo específico con detalles completos
     */
    public function show(Insumo $insumo): JsonResponse
    {
        $insumo->load('categoria');

        // Añadir información calculada
        $insumo->estado_stock = $this->calcularEstadoStock($insumo);
        $insumo->disponibilidad_dias = $this->calcularDisponibilidadDias($insumo);
        $insumo->movimientos_recientes = $insumo->movimientos()
            ->orderBy('fecha_movimiento', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $insumo,
            'message' => 'Insumo recuperado exitosamente'
        ]);
    }

    /**
     * Actualizar un insumo existente
     */
    public function update(Request $request, Insumo $insumo): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'codigo_insumo' => ['sometimes', 'string', 'max:50', Rule::unique('insumos')->ignore($insumo->id)],
            'nombre_insumo' => 'sometimes|string|max:150',
            'categoria_id' => 'sometimes|exists:categorias_insumos,id',
            'tipo_material' => ['sometimes', Rule::in(['PLA', 'PHA', 'PBS', 'PBAT', 'Almidon', 'Celulosa', 'Aditivo', 'Pigmento', 'Otro'])],
            'unidad_medida' => ['sometimes', Rule::in(['kg', 'ton', 'litro', 'unidad'])],
            'densidad' => 'nullable|numeric|min:0|max:10',
            'temperatura_fusion' => 'nullable|numeric|min:0|max:500',
            'certificacion_biodegradable' => 'nullable|string|max:100',
            'tiempo_biodegradacion_dias' => 'nullable|integer|min:1|max:1000',
            'proveedor' => 'nullable|string|max:150',
            'precio_unitario' => 'sometimes|numeric|min:0',
            'stock_minimo' => 'sometimes|numeric|min:0',
            'stock_actual' => 'sometimes|numeric|min:0',
            'activo' => 'boolean'
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

            $stockAnterior = $insumo->stock_actual;
            $insumo->update($request->all());

            // Verificar cambios en stock que puedan generar alertas
            if ($request->has('stock_actual') && $insumo->stock_actual <= $insumo->stock_minimo && $stockAnterior > $insumo->stock_minimo) {
                $this->generarAlertaStockBajo($insumo);
            }

            DB::commit();

            // Emitir evento de actualización de inventario en tiempo real
            broadcast(new InventarioActualizado($insumo, 'actualizada'))->toOthers();

            return response()->json([
                'success' => true,
                'data' => $insumo->load('categoria'),
                'message' => 'Insumo actualizado exitosamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el insumo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar un insumo (soft delete lógico)
     */
    public function destroy(Insumo $insumo): JsonResponse
    {
        try {
            // Verificar si el insumo está siendo usado en formulaciones activas
            $formulacionesActivas = $insumo->componentesFormulacion()
                ->whereHas('formulacion', function($q) {
                    $q->where('activa', true);
                })
                ->count();

            if ($formulacionesActivas > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el insumo porque está siendo usado en formulaciones activas'
                ], 409);
            }

            $insumo->delete();

            return response()->json([
                'success' => true,
                'message' => 'Insumo eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el insumo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calcular el estado del stock
     */
    private function calcularEstadoStock(Insumo $insumo): string
    {
        $porcentaje = ($insumo->stock_actual / $insumo->stock_minimo) * 100;

        if ($porcentaje <= 25) return 'critico';
        if ($porcentaje <= 50) return 'bajo';
        if ($porcentaje <= 75) return 'medio';
        return 'bueno';
    }

    /**
     * Calcular disponibilidad en días basado en consumo promedio
     */
    private function calcularDisponibilidadDias(Insumo $insumo): ?int
    {
        // Lógica simplificada - en producción calcularía basado en consumo histórico
        // Por ahora, asumimos un consumo diario del 1% del stock mínimo
        $consumoDiarioEstimado = $insumo->stock_minimo * 0.01;

        if ($consumoDiarioEstimado <= 0) return null;

        return (int) ($insumo->stock_actual / $consumoDiarioEstimado);
    }

    /**
     * Generar alerta de stock bajo
     */
    private function generarAlertaStockBajo(Insumo $insumo): void
    {
        \App\Models\Alerta::create([
            'tipo_alerta' => 'stock_bajo',
            'severidad' => $insumo->stock_actual <= ($insumo->stock_minimo * 0.25) ? 'critico' : 'advertencia',
            'titulo' => 'Stock bajo de insumo biodegradable',
            'mensaje' => "El insumo {$insumo->codigo_insumo} - {$insumo->nombre_insumo} tiene stock crítico: {$insumo->stock_actual} {$insumo->unidad_medida} (mínimo: {$insumo->stock_minimo} {$insumo->unidad_medida})",
            'entidad_tipo' => 'insumo',
            'entidad_id' => $insumo->id,
        ]);
    }

    /**
     * Obtener estadísticas de insumos
     */
    public function estadisticas(): JsonResponse
    {
        $estadisticas = [
            'total_insumos' => Insumo::count(),
            'insumos_activos' => Insumo::where('activo', true)->count(),
            'stock_bajo' => Insumo::whereColumn('stock_actual', '<=', 'stock_minimo')->count(),
            'por_tipo_material' => Insumo::select('tipo_material', DB::raw('count(*) as total'))
                ->groupBy('tipo_material')
                ->get(),
            'valor_total_inventario' => Insumo::sum(DB::raw('stock_actual * precio_unitario')),
        ];

        return response()->json([
            'success' => true,
            'data' => $estadisticas,
            'message' => 'Estadísticas de insumos recuperadas exitosamente'
        ]);
    }
}
