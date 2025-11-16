<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LoteProduccion;
use App\Models\ProductoTerminado;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;

/**
 * API Controller para Lotes de Producción
 * 
 * Gestiona la trazabilidad de lotes de productos biodegradables,
 * incluyendo fechas de vencimiento, certificaciones y control de calidad.
 */
class LoteProduccionController extends Controller
{
    /**
     * Listar lotes con filtros
     */
    public function index(Request $request): JsonResponse
    {
        $query = LoteProduccion::with([
            'productoTerminado',
            'ordenProduccion',
            'inspeccionesCalidad'
        ]);

        // Filtros
        if ($request->has('producto_id')) {
            $query->where('producto_id', $request->producto_id);
        }

        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->has('certificado_compostable')) {
            $query->where('certificado_compostable', $request->boolean('certificado_compostable'));
        }

        // Lotes próximos a vencer (30 días)
        if ($request->has('proximo_vencer')) {
            $query->whereBetween('fecha_vencimiento', [
                now(),
                now()->addDays(30)
            ])->where('estado', 'disponible');
        }

        // Lotes vencidos
        if ($request->has('vencidos')) {
            $query->where('fecha_vencimiento', '<', now())
                  ->where('estado', 'disponible');
        }

        // Búsqueda por código de lote
        if ($request->has('buscar')) {
            $query->where('codigo_lote', 'like', "%{$request->buscar}%");
        }

        $lotes = $query->orderBy('fecha_produccion', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json($lotes);
    }

    /**
     * Mostrar lote específico con trazabilidad completa
     */
    public function show(int $id): JsonResponse
    {
        $lote = LoteProduccion::with([
            'productoTerminado.categoria',
            'ordenProduccion.maquina',
            'ordenProduccion.operador',
            'ordenProduccion.supervisor',
            'inspeccionesCalidad.inspector',
            'registrosProduccion.operador',
            'movimientosSalida'
        ])->findOrFail($id);

        return response()->json($lote);
    }

    /**
     * Crear nuevo lote
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'codigo_lote' => 'required|string|max:100|unique:lotes_produccion,codigo_lote',
            'producto_id' => 'required|exists:productos,id',
            'orden_produccion_id' => 'required|exists:ordenes_produccion,id',
            'fecha_produccion' => 'required|date',
            'fecha_vencimiento' => 'required|date|after:fecha_produccion',
            'cantidad_inicial' => 'required|integer|min:1',
            'unidad_medida' => 'required|string|max:20',
            'numero_palet' => 'nullable|string|max:50',
            'ubicacion_almacen' => 'nullable|string|max:100',
            'certificado_compostable' => 'nullable|boolean',
            'numero_certificado' => 'nullable|string|max:100',
            'lote_materia_prima' => 'nullable|string|max:100',
            'estado' => 'nullable|in:disponible,cuarentena,rechazado,agotado',
            'notas' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $lote = LoteProduccion::create(array_merge(
            $request->all(),
            [
                'cantidad_disponible' => $request->cantidad_inicial,
                'estado' => $request->estado ?? 'cuarentena', // Por defecto en cuarentena hasta inspección
            ]
        ));

        return response()->json([
            'message' => 'Lote creado exitosamente',
            'data' => $lote->load('productoTerminado', 'ordenProduccion')
        ], 201);
    }

    /**
     * Actualizar lote
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $lote = LoteProduccion::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'codigo_lote' => 'sometimes|string|max:100|unique:lotes_produccion,codigo_lote,' . $id,
            'producto_id' => 'sometimes|exists:productos,id',
            'fecha_produccion' => 'sometimes|date',
            'fecha_vencimiento' => 'sometimes|date|after:fecha_produccion',
            'cantidad_inicial' => 'sometimes|integer|min:1',
            'cantidad_disponible' => 'sometimes|integer|min:0',
            'unidad_medida' => 'sometimes|string|max:20',
            'numero_palet' => 'nullable|string|max:50',
            'ubicacion_almacen' => 'nullable|string|max:100',
            'certificado_compostable' => 'nullable|boolean',
            'numero_certificado' => 'nullable|string|max:100',
            'lote_materia_prima' => 'nullable|string|max:100',
            'estado' => 'sometimes|in:disponible,cuarentena,rechazado,agotado',
            'notas' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $lote->update($request->all());

        return response()->json([
            'message' => 'Lote actualizado exitosamente',
            'data' => $lote->fresh()->load('productoTerminado', 'ordenProduccion')
        ]);
    }

    /**
     * Eliminar lote
     */
    public function destroy(int $id): JsonResponse
    {
        $lote = LoteProduccion::findOrFail($id);

        // Verificar que no tenga movimientos de salida
        if ($lote->movimientosSalida()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar un lote que tiene movimientos de salida registrados'
            ], 400);
        }

        $lote->delete();

        return response()->json([
            'message' => 'Lote eliminado exitosamente'
        ]);
    }

    /**
     * Aprobar lote (cambiar de cuarentena a disponible)
     */
    public function aprobar(int $id): JsonResponse
    {
        $lote = LoteProduccion::findOrFail($id);

        if ($lote->estado !== 'cuarentena') {
            return response()->json([
                'message' => 'Solo se pueden aprobar lotes en cuarentena'
            ], 400);
        }

        // Verificar que tenga al menos una inspección aprobada
        $tieneInspeccionAprobada = $lote->inspeccionesCalidad()
            ->where('resultado', 'aprobado')
            ->exists();

        if (!$tieneInspeccionAprobada) {
            return response()->json([
                'message' => 'El lote debe tener al menos una inspección de calidad aprobada'
            ], 400);
        }

        $lote->update(['estado' => 'disponible']);

        return response()->json([
            'message' => 'Lote aprobado exitosamente',
            'data' => $lote
        ]);
    }

    /**
     * Rechazar lote
     */
    public function rechazar(Request $request, int $id): JsonResponse
    {
        $lote = LoteProduccion::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'motivo' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $lote->update([
            'estado' => 'rechazado',
            'notas' => ($lote->notas ? $lote->notas . "\n\n" : '') . 
                       "RECHAZADO: " . $request->motivo
        ]);

        return response()->json([
            'message' => 'Lote rechazado',
            'data' => $lote
        ]);
    }

    /**
     * Obtener alertas de lotes (vencimientos, stock bajo)
     */
    public function alertas(): JsonResponse
    {
        // Lotes vencidos
        $vencidos = LoteProduccion::with('productoTerminado')
            ->where('estado', 'disponible')
            ->where('fecha_vencimiento', '<', now())
            ->get();

        // Lotes próximos a vencer (30 días)
        $proximosVencer = LoteProduccion::with('productoTerminado')
            ->where('estado', 'disponible')
            ->whereBetween('fecha_vencimiento', [now(), now()->addDays(30)])
            ->orderBy('fecha_vencimiento')
            ->get();

        // Lotes en cuarentena por más de 7 días
        $cuarentenaLarga = LoteProduccion::with('productoTerminado')
            ->where('estado', 'cuarentena')
            ->where('fecha_produccion', '<', now()->subDays(7))
            ->get();

        return response()->json([
            'vencidos' => [
                'count' => $vencidos->count(),
                'lotes' => $vencidos
            ],
            'proximos_vencer' => [
                'count' => $proximosVencer->count(),
                'lotes' => $proximosVencer
            ],
            'cuarentena_larga' => [
                'count' => $cuarentenaLarga->count(),
                'lotes' => $cuarentenaLarga
            ]
        ]);
    }

    /**
     * Generar código de lote automático
     */
    public function generarCodigo(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'producto_id' => 'required|exists:productos,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $producto = ProductoTerminado::findOrFail($request->producto_id);
        
        // Formato: CODIGO_PRODUCTO-YYMMDD-SECUENCIAL
        $fecha = now()->format('ymd');
        $prefijo = $producto->codigo_producto . '-' . $fecha;
        
        // Obtener último lote del día
        $ultimoLote = LoteProduccion::where('codigo_lote', 'like', $prefijo . '%')
            ->orderBy('codigo_lote', 'desc')
            ->first();

        $secuencial = 1;
        if ($ultimoLote) {
            // Extraer secuencial del último lote
            $partes = explode('-', $ultimoLote->codigo_lote);
            $secuencial = (int)end($partes) + 1;
        }

        $codigoGenerado = $prefijo . '-' . str_pad($secuencial, 3, '0', STR_PAD_LEFT);

        return response()->json([
            'codigo_lote' => $codigoGenerado
        ]);
    }
}
