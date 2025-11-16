<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductoTerminado;
use App\Models\CategoriaProducto;
use App\Events\ProductoTerminadoActualizado;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Controlador API para gestión de Productos Terminados Biodegradables
 *
 * Funcionalidades:
 * - CRUD completo de productos biodegradables
 * - Gestión de certificaciones compostables
 * - Control de tiempos de compostaje
 * - Filtros por material, certificación, categoría
 */
class ProductoTerminadoController extends Controller
{
    /**
     * Listar todos los productos terminados con filtros y paginación
     */
    public function index(Request $request): JsonResponse
    {
        $query = ProductoTerminado::with('categoria');

        // Filtros biodegradables
        if ($request->has('categoria_producto_id')) {
            $query->where('categoria_producto_id', $request->categoria_producto_id);
        }

        if ($request->has('material_principal')) {
            $query->where('material_principal', $request->material_principal);
        }

        if ($request->has('certificacion_compostable')) {
            $query->where('certificacion_compostable', 'like', '%' . $request->certificacion_compostable . '%');
        }

        if ($request->has('tiempo_compostaje_min')) {
            $query->where('tiempo_compostaje_dias', '>=', $request->tiempo_compostaje_min);
        }

        if ($request->has('tiempo_compostaje_max')) {
            $query->where('tiempo_compostaje_dias', '<=', $request->tiempo_compostaje_max);
        }

        if ($request->has('activo')) {
            $query->where('activo', $request->boolean('activo'));
        }

        // Ordenamiento
        $sortBy = $request->get('sort_by', 'codigo_producto');
        $sortDirection = $request->get('sort_direction', 'asc');
        $query->orderBy($sortBy, $sortDirection);

        // Paginación
        $perPage = $request->get('per_page', 15);
        $productos = $query->paginate($perPage);

        // Añadir información biodegradable
        $productos->getCollection()->transform(function ($producto) {
            $producto->es_biodegradable = $this->esProductoBiodegradable($producto);
            $producto->nivel_sostenibilidad = $this->calcularNivelSostenibilidad($producto);
            $producto->margen_ganancia = $this->calcularMargenGanancia($producto);
            return $producto;
        });

        return response()->json([
            'success' => true,
            'data' => $productos,
            'message' => 'Productos terminados recuperados exitosamente'
        ]);
    }

    /**
     * Crear un nuevo producto terminado biodegradable
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'codigo_producto' => 'required|string|max:50|unique:productos',
            'nombre_producto' => 'required|string|max:150',
            'categoria_producto_id' => 'required|exists:categorias_productos,id',
            'descripcion' => 'nullable|string',
            'material_principal' => ['required', Rule::in(['PLA', 'PHA', 'PBS', 'PBAT', 'Almidon', 'Mixto'])],
            'certificacion_compostable' => 'nullable|string|max:200',
            'tiempo_compostaje_dias' => 'nullable|integer|min:1|max:1000',
            'peso_unitario_gramos' => 'required|numeric|min:0',
            'temperatura_proceso' => 'nullable|numeric|min:0|max:500',
            'precio_venta' => 'required|numeric|min:0',
            'unidad_venta' => ['required', Rule::in(['unidad', 'paquete', 'caja', 'kg'])],
            'unidades_por_paquete' => 'integer|min:1',
            'stock_minimo' => 'integer|min:0',
            'stock_actual' => 'integer|min:0',
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

            $producto = ProductoTerminado::create($request->all());

            // Generar alerta si el producto no tiene certificación
            if (!$producto->certificacion_compostable) {
                $this->generarAlertaSinCertificacion($producto);
            }

            DB::commit();

            // Emitir evento de creación en tiempo real
            broadcast(new ProductoTerminadoActualizado($producto, 'creada'))->toOthers();

            return response()->json([
                'success' => true,
                'data' => $producto->load('categoria'),
                'message' => 'Producto terminado creado exitosamente'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el producto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar un producto específico con detalles biodegradables
     */
    public function show(ProductoTerminado $producto): JsonResponse
    {
        $producto->load('categoria', 'formulacion');

        // Añadir información biodegradable calculada
        $producto->es_biodegradable = $this->esProductoBiodegradable($producto);
        $producto->nivel_sostenibilidad = $this->calcularNivelSostenibilidad($producto);
        $producto->margen_ganancia = $this->calcularMargenGanancia($producto);
        $producto->ordenes_pendientes = $producto->ordenes()
            ->whereIn('estado', ['pendiente', 'programada', 'en_proceso'])
            ->count();

        return response()->json([
            'success' => true,
            'data' => $producto,
            'message' => 'Producto terminado recuperado exitosamente'
        ]);
    }

    /**
     * Actualizar un producto terminado
     */
    public function update(Request $request, ProductoTerminado $producto): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'codigo_producto' => ['sometimes', 'string', 'max:50', Rule::unique('productos')->ignore($producto->id)],
            'nombre_producto' => 'sometimes|string|max:150',
            'categoria_producto_id' => 'sometimes|exists:categorias_productos,id',
            'descripcion' => 'nullable|string',
            'material_principal' => ['sometimes', Rule::in(['PLA', 'PHA', 'PBS', 'PBAT', 'Almidon', 'Mixto'])],
            'certificacion_compostable' => 'nullable|string|max:200',
            'tiempo_compostaje_dias' => 'nullable|integer|min:1|max:1000',
            'peso_unitario_gramos' => 'sometimes|numeric|min:0',
            'temperatura_proceso' => 'nullable|numeric|min:0|max:500',
            'precio_venta' => 'sometimes|numeric|min:0',
            'unidad_venta' => ['sometimes', Rule::in(['unidad', 'paquete', 'caja', 'kg'])],
            'unidades_por_paquete' => 'integer|min:1',
            'stock_minimo' => 'sometimes|integer|min:0',
            'stock_actual' => 'sometimes|integer|min:0',
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

            $producto->update($request->all());

            DB::commit();

            // Emitir evento de actualización en tiempo real
            broadcast(new ProductoTerminadoActualizado($producto, 'actualizada'))->toOthers();

            return response()->json([
                'success' => true,
                'data' => $producto->load('categoria'),
                'message' => 'Producto terminado actualizado exitosamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el producto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar un producto terminado
     */
    public function destroy(ProductoTerminado $producto): JsonResponse
    {
        try {
            // Verificar si el producto tiene órdenes activas
            $ordenesActivas = $producto->ordenes()
                ->whereIn('estado', ['pendiente', 'programada', 'en_proceso'])
                ->count();

            if ($ordenesActivas > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el producto porque tiene órdenes activas'
                ], 409);
            }

            $producto->delete();

            return response()->json([
                'success' => true,
                'message' => 'Producto terminado eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el producto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verificar si un producto es biodegradable
     */
    private function esProductoBiodegradable(ProductoTerminado $producto): bool
    {
        $materialesBiodegradables = ['PLA', 'PHA', 'PBS', 'PBAT', 'Almidon'];
        return in_array($producto->material_principal, $materialesBiodegradables) &&
               !empty($producto->certificacion_compostable);
    }

    /**
     * Calcular nivel de sostenibilidad del producto
     */
    private function calcularNivelSostenibilidad(ProductoTerminado $producto): string
    {
        if (!$this->esProductoBiodegradable($producto)) {
            return 'no_biodegradable';
        }

        $tiempo = $producto->tiempo_compostaje_dias ?? 365;

        if ($tiempo <= 90) return 'excelente'; // Muy rápido de compostar
        if ($tiempo <= 180) return 'muy_bueno';
        if ($tiempo <= 365) return 'bueno';
        return 'regular';
    }

    /**
     * Calcular margen de ganancia estimado
     */
    private function calcularMargenGanancia(ProductoTerminado $producto): ?float
    {
        // Lógica simplificada - en producción calcularía costo real de producción
        // Por ahora, asumimos un costo estimado basado en el material
        $costoBase = match($producto->material_principal) {
            'PLA' => 0.05,
            'PHA' => 0.08,
            'PBS' => 0.04,
            'PBAT' => 0.06,
            'Almidon' => 0.02,
            default => 0.03
        };

        $costoEstimado = $costoBase * ($producto->peso_unitario_gramos / 100);

        if ($costoEstimado <= 0) return null;

        return (($producto->precio_venta - $costoEstimado) / $producto->precio_venta) * 100;
    }

    /**
     * Generar alerta para producto sin certificación
     */
    private function generarAlertaSinCertificacion(ProductoTerminado $producto): void
    {
        \App\Models\Alerta::create([
            'tipo_alerta' => 'certificacion_faltante',
            'severidad' => 'advertencia',
            'titulo' => 'Producto biodegradable sin certificación',
            'mensaje' => "El producto {$producto->codigo_producto} - {$producto->nombre_producto} no tiene certificación compostable. Es necesario obtener certificación para poder comercializarlo como biodegradable.",
            'entidad_tipo' => 'producto',
            'entidad_id' => $producto->id,
        ]);
    }

    /**
     * Obtener estadísticas de productos biodegradables
     */
    public function estadisticas(): JsonResponse
    {
        $estadisticas = [
            'total_productos' => ProductoTerminado::count(),
            'productos_activos' => ProductoTerminado::where('activo', true)->count(),
            'productos_biodegradables' => ProductoTerminado::where('activo', true)
                ->whereIn('material_principal', ['PLA', 'PHA', 'PBS', 'PBAT', 'Almidon'])
                ->whereNotNull('certificacion_compostable')
                ->count(),
            'productos_certificados' => ProductoTerminado::whereNotNull('certificacion_compostable')->count(),
            'por_material' => ProductoTerminado::select('material_principal', DB::raw('count(*) as total'))
                ->groupBy('material_principal')
                ->get(),
            'tiempo_compostaje_promedio' => ProductoTerminado::whereNotNull('tiempo_compostaje_dias')
                ->avg('tiempo_compostaje_dias'),
            'valor_total_inventario' => ProductoTerminado::sum(DB::raw('stock_actual * precio_venta')),
        ];

        return response()->json([
            'success' => true,
            'data' => $estadisticas,
            'message' => 'Estadísticas de productos biodegradables recuperadas exitosamente'
        ]);
    }

    /**
     * Obtener productos por nivel de sostenibilidad
     */
    public function porSostenibilidad(): JsonResponse
    {
        $productos = ProductoTerminado::where('activo', true)->get();

        $clasificados = [
            'excelente' => [],
            'muy_bueno' => [],
            'bueno' => [],
            'regular' => [],
            'no_biodegradable' => []
        ];

        foreach ($productos as $producto) {
            $nivel = $this->calcularNivelSostenibilidad($producto);
            $producto->nivel_sostenibilidad = $nivel;
            $clasificados[$nivel][] = $producto;
        }

        return response()->json([
            'success' => true,
            'data' => $clasificados,
            'message' => 'Productos clasificados por sostenibilidad'
        ]);
    }
}
