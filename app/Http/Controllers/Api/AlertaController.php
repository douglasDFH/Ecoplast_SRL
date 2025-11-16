<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alerta;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * API Controller para Alertas del Sistema
 * 
 * Gestiona alertas automáticas de stock, calidad, mantenimiento,
 * producción y vencimientos de materiales biodegradables.
 */
class AlertaController extends Controller
{
    /**
     * Listar alertas con filtros
     */
    public function index(Request $request): JsonResponse
    {
        $query = Alerta::with('usuario');

        // Filtros
        if ($request->has('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->has('prioridad')) {
            $query->where('prioridad', $request->prioridad);
        }

        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->has('usuario_id')) {
            $query->where('usuario_id', $request->usuario_id);
        }

        // Alertas activas (no leídas ni resueltas)
        if ($request->has('activas')) {
            $query->where('estado', 'activa');
        }

        // Alertas no leídas
        if ($request->has('no_leidas')) {
            $query->where('leida', false);
        }

        // Alertas críticas
        if ($request->has('criticas')) {
            $query->where('prioridad', 'critica')->where('estado', 'activa');
        }

        $alertas = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 20));

        return response()->json($alertas);
    }

    /**
     * Mostrar alerta específica
     */
    public function show(int $id): JsonResponse
    {
        $alerta = Alerta::with('usuario')->findOrFail($id);

        return response()->json($alerta);
    }

    /**
     * Crear nueva alerta manualmente
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'tipo' => 'required|in:stock_minimo,stock_critico,vencimiento_proximo,vencimiento_pasado,defecto_calidad,mantenimiento_pendiente,mantenimiento_atrasado,maquina_parada,produccion_atrasada,alerta_sistema',
            'prioridad' => 'required|in:baja,media,alta,critica',
            'titulo' => 'required|string|max:200',
            'mensaje' => 'required|string|max:1000',
            'usuario_id' => 'nullable|exists:users,id',
            'entidad_tipo' => 'nullable|string|max:100',
            'entidad_id' => 'nullable|integer',
            'accion_recomendada' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $alerta = Alerta::create(array_merge(
            $request->all(),
            [
                'estado' => 'activa',
                'leida' => false,
            ]
        ));

        return response()->json([
            'message' => 'Alerta creada exitosamente',
            'data' => $alerta
        ], 201);
    }

    /**
     * Actualizar alerta
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $alerta = Alerta::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'tipo' => 'sometimes|in:stock_minimo,stock_critico,vencimiento_proximo,vencimiento_pasado,defecto_calidad,mantenimiento_pendiente,mantenimiento_atrasado,maquina_parada,produccion_atrasada,alerta_sistema',
            'prioridad' => 'sometimes|in:baja,media,alta,critica',
            'titulo' => 'sometimes|string|max:200',
            'mensaje' => 'sometimes|string|max:1000',
            'estado' => 'sometimes|in:activa,resuelta,descartada',
            'leida' => 'sometimes|boolean',
            'usuario_id' => 'nullable|exists:users,id',
            'entidad_tipo' => 'nullable|string|max:100',
            'entidad_id' => 'nullable|integer',
            'accion_recomendada' => 'nullable|string|max:500',
            'notas_resolucion' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $alerta->update($request->all());

        return response()->json([
            'message' => 'Alerta actualizada exitosamente',
            'data' => $alerta->fresh()
        ]);
    }

    /**
     * Eliminar alerta
     */
    public function destroy(int $id): JsonResponse
    {
        $alerta = Alerta::findOrFail($id);
        $alerta->delete();

        return response()->json([
            'message' => 'Alerta eliminada exitosamente'
        ]);
    }

    /**
     * Marcar alerta como leída
     */
    public function marcarLeida(int $id): JsonResponse
    {
        $alerta = Alerta::findOrFail($id);
        
        $alerta->update([
            'leida' => true,
            'fecha_lectura' => now(),
        ]);

        return response()->json([
            'message' => 'Alerta marcada como leída',
            'data' => $alerta
        ]);
    }

    /**
     * Marcar múltiples alertas como leídas
     */
    public function marcarVariasLeidas(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'exists:alertas,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        Alerta::whereIn('id', $request->ids)->update([
            'leida' => true,
            'fecha_lectura' => now(),
        ]);

        return response()->json([
            'message' => 'Alertas marcadas como leídas',
            'cantidad' => count($request->ids)
        ]);
    }

    /**
     * Resolver alerta
     */
    public function resolver(Request $request, int $id): JsonResponse
    {
        $alerta = Alerta::findOrFail($id);

        if ($alerta->estado === 'resuelta') {
            return response()->json([
                'message' => 'Esta alerta ya fue resuelta'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'notas_resolucion' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $alerta->update([
            'estado' => 'resuelta',
            'fecha_resolucion' => now(),
            'notas_resolucion' => $request->notas_resolucion,
        ]);

        return response()->json([
            'message' => 'Alerta resuelta exitosamente',
            'data' => $alerta->fresh()
        ]);
    }

    /**
     * Descartar alerta
     */
    public function descartar(Request $request, int $id): JsonResponse
    {
        $alerta = Alerta::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'notas_resolucion' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $alerta->update([
            'estado' => 'descartada',
            'notas_resolucion' => $request->notas_resolucion,
        ]);

        return response()->json([
            'message' => 'Alerta descartada',
            'data' => $alerta->fresh()
        ]);
    }

    /**
     * Obtener resumen de alertas por tipo
     */
    public function resumen(): JsonResponse
    {
        $alertasActivas = Alerta::where('estado', 'activa')->get();

        $resumen = [
            'total_activas' => $alertasActivas->count(),
            'no_leidas' => $alertasActivas->where('leida', false)->count(),
            'por_prioridad' => [
                'critica' => $alertasActivas->where('prioridad', 'critica')->count(),
                'alta' => $alertasActivas->where('prioridad', 'alta')->count(),
                'media' => $alertasActivas->where('prioridad', 'media')->count(),
                'baja' => $alertasActivas->where('prioridad', 'baja')->count(),
            ],
            'por_tipo' => [
                'stock_minimo' => $alertasActivas->where('tipo', 'stock_minimo')->count(),
                'stock_critico' => $alertasActivas->where('tipo', 'stock_critico')->count(),
                'vencimiento_proximo' => $alertasActivas->where('tipo', 'vencimiento_proximo')->count(),
                'vencimiento_pasado' => $alertasActivas->where('tipo', 'vencimiento_pasado')->count(),
                'defecto_calidad' => $alertasActivas->where('tipo', 'defecto_calidad')->count(),
                'mantenimiento_pendiente' => $alertasActivas->where('tipo', 'mantenimiento_pendiente')->count(),
                'mantenimiento_atrasado' => $alertasActivas->where('tipo', 'mantenimiento_atrasado')->count(),
                'maquina_parada' => $alertasActivas->where('tipo', 'maquina_parada')->count(),
                'produccion_atrasada' => $alertasActivas->where('tipo', 'produccion_atrasada')->count(),
            ],
        ];

        return response()->json($resumen);
    }

    /**
     * Obtener alertas críticas más recientes
     */
    public function criticas(): JsonResponse
    {
        $alertas = Alerta::with('usuario')
            ->where('prioridad', 'critica')
            ->where('estado', 'activa')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json($alertas);
    }

    /**
     * Obtener historial de alertas resueltas
     */
    public function historial(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'tipo' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $query = Alerta::with('usuario')
            ->whereIn('estado', ['resuelta', 'descartada'])
            ->whereBetween('created_at', [
                $request->fecha_inicio,
                $request->fecha_fin
            ]);

        if ($request->has('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        $alertas = $query->orderBy('fecha_resolucion', 'desc')
            ->paginate($request->get('per_page', 20));

        return response()->json($alertas);
    }
}
