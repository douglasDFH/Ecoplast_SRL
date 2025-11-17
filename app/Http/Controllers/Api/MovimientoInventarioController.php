<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MovimientoInventarioInsumo;
use App\Models\Insumo;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MovimientoInventarioController extends Controller
{
    /**
     * Listar movimientos de inventario con filtros
     */
    public function index(Request $request): JsonResponse
    {
        $query = MovimientoInventarioInsumo::query();

        // Filtrar por insumo
        if ($request->has('insumo_id')) {
            $query->where('insumo_id', $request->insumo_id);
        }

        // Filtrar por tipo de movimiento
        if ($request->has('tipo_movimiento')) {
            $query->where('tipo_movimiento', $request->tipo_movimiento);
        }

        // Filtrar por rango de fechas
        if ($request->has('fecha_desde')) {
            $query->where('fecha_movimiento', '>=', $request->fecha_desde);
        }

        if ($request->has('fecha_hasta')) {
            $query->where('fecha_movimiento', '<=', $request->fecha_hasta);
        }

        // Incluir relaciones
        $incluir = $request->input('incluir', '');
        if ($incluir) {
            $relaciones = explode(',', $incluir);
            $query->with($relaciones);
        }

        // Ordenar
        $orden = $request->input('orden', 'fecha_movimiento,desc');
        [$campo, $direccion] = explode(',', $orden);
        $query->orderBy($campo, $direccion);

        // Límite
        $limite = $request->input('limite', null);
        if ($limite) {
            $query->limit($limite);
        }

        // Paginación o listado completo
        if ($request->boolean('all', false)) {
            $movimientos = $query->get();
            return response()->json(['data' => $movimientos]);
        }

        $movimientos = $query->paginate($request->input('per_page', 15));

        return response()->json($movimientos);
    }

    /**
     * Registrar un nuevo movimiento de inventario
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'insumo_id' => 'required|exists:insumos,id',
            'tipo' => 'required|in:entrada,salida',
            'cantidad' => 'required|numeric|min:0.01',
            'motivo' => 'nullable|string|max:255',
            'lote' => 'nullable|string|max:50',
            'fecha_vencimiento' => 'nullable|date',
            'costo_unitario' => 'nullable|numeric|min:0',
            'orden_produccion_id' => 'nullable|exists:ordenes_produccion,id',
            'proveedor_id' => 'nullable|exists:proveedores,id',
            'numero_documento' => 'nullable|string|max:100',
            'observaciones' => 'nullable|string',
            'fecha_movimiento' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Obtener el insumo
            $insumo = Insumo::findOrFail($request->insumo_id);

            // Validar stock suficiente para salidas
            if ($request->tipo === 'salida' && $insumo->stock_actual < $request->cantidad) {
                return response()->json([
                    'message' => 'Stock insuficiente',
                    'disponible' => $insumo->stock_actual,
                    'solicitado' => $request->cantidad
                ], 400);
            }

            // Convertir tipo a tipo_movimiento del ENUM
            $tipoMovimiento = $request->tipo;
            if ($request->tipo === 'salida' && $request->motivo) {
                $tipoMovimiento = match($request->motivo) {
                    'desperdicio', 'merma', 'vencimiento' => 'desperdicio',
                    'ajuste' => 'ajuste',
                    default => 'salida'
                };
            } elseif ($request->tipo === 'entrada') {
                $tipoMovimiento = 'entrada';
            }

            // Crear el movimiento
            $movimiento = MovimientoInventarioInsumo::create([
                'insumo_id' => $request->insumo_id,
                'tipo_movimiento' => $tipoMovimiento,
                'cantidad' => $request->cantidad,
                'lote' => $request->lote,
                'fecha_vencimiento' => $request->fecha_vencimiento,
                'costo_unitario' => $request->costo_unitario,
                'usuario_id' => Auth::id() ?? 1,
                'motivo' => $request->observaciones ?? $request->motivo,
                'fecha_movimiento' => $request->fecha_movimiento ?? now(),
            ]);

            // Actualizar stock del insumo
            if ($request->tipo === 'entrada') {
                $insumo->stock_actual += $request->cantidad;
            } else {
                $insumo->stock_actual -= $request->cantidad;
            }

            // Actualizar precio si viene en la entrada
            if ($request->tipo === 'entrada' && $request->costo_unitario) {
                $insumo->precio_unitario = $request->costo_unitario;
            }

            $insumo->save();

            DB::commit();

            // Cargar relaciones para la respuesta
            $movimiento->load(['insumo', 'usuario']);

            return response()->json([
                'message' => 'Movimiento registrado exitosamente',
                'data' => $movimiento,
                'stock_actual' => $insumo->stock_actual
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al registrar el movimiento',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar un movimiento específico
     */
    public function show(string $id): JsonResponse
    {
        $movimiento = MovimientoInventarioInsumo::with(['insumo', 'usuario', 'ordenProduccion'])
            ->findOrFail($id);

        return response()->json(['data' => $movimiento]);
    }

    /**
     * Estadísticas de movimientos
     */
    public function estadisticas(Request $request): JsonResponse
    {
        $fechaDesde = $request->input('fecha_desde', now()->subDays(30));
        $fechaHasta = $request->input('fecha_hasta', now());

        $stats = [
            'total_entradas' => MovimientoInventarioInsumo::where('tipo_movimiento', 'entrada')
                ->whereBetween('fecha_movimiento', [$fechaDesde, $fechaHasta])
                ->sum('cantidad'),

            'total_salidas' => MovimientoInventarioInsumo::whereIn('tipo_movimiento', ['salida', 'desperdicio'])
                ->whereBetween('fecha_movimiento', [$fechaDesde, $fechaHasta])
                ->sum('cantidad'),

            'total_ajustes' => MovimientoInventarioInsumo::where('tipo_movimiento', 'ajuste')
                ->whereBetween('fecha_movimiento', [$fechaDesde, $fechaHasta])
                ->count(),

            'total_desperdicios' => MovimientoInventarioInsumo::where('tipo_movimiento', 'desperdicio')
                ->whereBetween('fecha_movimiento', [$fechaDesde, $fechaHasta])
                ->sum('cantidad'),

            'movimientos_recientes' => MovimientoInventarioInsumo::with(['insumo', 'usuario'])
                ->whereBetween('fecha_movimiento', [$fechaDesde, $fechaHasta])
                ->orderBy('fecha_movimiento', 'desc')
                ->limit(10)
                ->get(),

            'insumos_mas_movidos' => MovimientoInventarioInsumo::select('insumo_id', DB::raw('count(*) as total_movimientos'))
                ->whereBetween('fecha_movimiento', [$fechaDesde, $fechaHasta])
                ->groupBy('insumo_id')
                ->orderByDesc('total_movimientos')
                ->with('insumo')
                ->limit(10)
                ->get(),
        ];

        return response()->json($stats);
    }

    /**
     * Movimientos por insumo
     */
    public function porInsumo(string $insumoId): JsonResponse
    {
        $movimientos = MovimientoInventarioInsumo::with(['usuario'])
            ->where('insumo_id', $insumoId)
            ->orderBy('fecha_movimiento', 'desc')
            ->limit(50)
            ->get();

        return response()->json(['data' => $movimientos]);
    }

    /**
     * Resumen de inventario
     */
    public function resumen(): JsonResponse
    {
        $insumos = Insumo::with(['categoria', 'tipoMaterial', 'proveedor'])
            ->where('activo', true)
            ->get();

        $resumen = $insumos->map(function ($insumo) {
            return [
                'id' => $insumo->id,
                'codigo' => $insumo->codigo_insumo,
                'nombre' => $insumo->nombre_insumo,
                'descripcion' => $insumo->descripcion,
                'categoria' => $insumo->categoria?->nombre ?? 'Sin categoría',
                'tipo_material' => $insumo->tipoMaterial?->nombre ?? $insumo->tipo_material,
                'cantidad_actual' => (float) $insumo->stock_actual,
                'stock_minimo' => (float) $insumo->stock_minimo,
                'unidad_medida' => $insumo->unidad_medida,
                'fecha_vencimiento' => $insumo->fecha_caducidad_lote,
                'costo_unitario' => (float) $insumo->precio_unitario,
                'proveedor_id' => $insumo->proveedor_id,
                'proveedor' => $insumo->proveedor,
                'estado' => $this->calcularEstado($insumo),
                'valor_total' => (float) $insumo->stock_actual * (float) $insumo->precio_unitario,
            ];
        });

        return response()->json(['data' => $resumen]);
    }

    /**
     * Calcular estado del insumo
     */
    private function calcularEstado(Insumo $insumo): string
    {
        if ($insumo->stock_actual <= 0) {
            return 'stock_critico';
        } elseif ($insumo->stock_actual <= $insumo->stock_minimo) {
            return 'stock_bajo';
        }
        return 'stock_ok';
    }
}
