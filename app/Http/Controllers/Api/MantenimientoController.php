<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mantenimiento;
use App\Models\Maquinaria;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * API Controller para Mantenimientos
 * 
 * Gestiona el mantenimiento preventivo y correctivo de maquinaria,
 * programación, costos y seguimiento de intervenciones.
 */
class MantenimientoController extends Controller
{
    /**
     * Listar mantenimientos con filtros
     */
    public function index(Request $request): JsonResponse
    {
        $query = Mantenimiento::with([
            'maquina',
            'tecnico',
            'aprobadoPor'
        ]);

        // Filtros
        if ($request->has('maquina_id')) {
            $query->where('maquina_id', $request->maquina_id);
        }

        if ($request->has('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->has('prioridad')) {
            $query->where('prioridad', $request->prioridad);
        }

        // Mantenimientos pendientes
        if ($request->has('pendientes')) {
            $query->whereIn('estado', ['programado', 'en_proceso']);
        }

        // Mantenimientos atrasados
        if ($request->has('atrasados')) {
            $query->where('estado', 'programado')
                  ->where('fecha_programada', '<', now());
        }

        // Rango de fechas
        if ($request->has('fecha_inicio') && $request->has('fecha_fin')) {
            $query->whereBetween('fecha_programada', [
                $request->fecha_inicio,
                $request->fecha_fin
            ]);
        }

        $mantenimientos = $query->orderBy('fecha_programada', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json($mantenimientos);
    }

    /**
     * Mostrar mantenimiento específico
     */
    public function show(int $id): JsonResponse
    {
        $mantenimiento = Mantenimiento::with([
            'maquina.tipo',
            'tecnico',
            'aprobadoPor'
        ])->findOrFail($id);

        return response()->json($mantenimiento);
    }

    /**
     * Crear nuevo mantenimiento
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'maquina_id' => 'required|exists:maquinas,id',
            'tipo' => 'required|in:preventivo,correctivo,predictivo',
            'descripcion' => 'required|string|max:500',
            'fecha_programada' => 'required|date',
            'duracion_estimada_horas' => 'required|numeric|min:0.5',
            'prioridad' => 'required|in:baja,media,alta,critica',
            'tecnico_id' => 'nullable|exists:users,id',
            'costo_estimado' => 'nullable|numeric|min:0',
            'repuestos_necesarios' => 'nullable|string|max:1000',
            'notas' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $mantenimiento = Mantenimiento::create(array_merge(
            $request->all(),
            ['estado' => 'programado']
        ));

        // Si es mantenimiento preventivo, actualizar estado de la máquina
        if ($request->tipo === 'preventivo' && $request->fecha_programada <= now()->addDays(3)) {
            $maquina = Maquinaria::findOrFail($request->maquina_id);
            if ($maquina->estado_actual === 'operativa') {
                $maquina->update(['estado_actual' => 'mantenimiento']);
            }
        }

        return response()->json([
            'message' => 'Mantenimiento programado exitosamente',
            'data' => $mantenimiento->load('maquina', 'tecnico')
        ], 201);
    }

    /**
     * Actualizar mantenimiento
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $mantenimiento = Mantenimiento::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'maquina_id' => 'sometimes|exists:maquinas,id',
            'tipo' => 'sometimes|in:preventivo,correctivo,predictivo',
            'descripcion' => 'sometimes|string|max:500',
            'fecha_programada' => 'sometimes|date',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'duracion_estimada_horas' => 'sometimes|numeric|min:0.5',
            'duracion_real_horas' => 'nullable|numeric|min:0',
            'prioridad' => 'sometimes|in:baja,media,alta,critica',
            'estado' => 'sometimes|in:programado,en_proceso,completado,cancelado',
            'tecnico_id' => 'nullable|exists:users,id',
            'costo_estimado' => 'nullable|numeric|min:0',
            'costo_real' => 'nullable|numeric|min:0',
            'repuestos_necesarios' => 'nullable|string|max:1000',
            'repuestos_utilizados' => 'nullable|string|max:1000',
            'trabajo_realizado' => 'nullable|string|max:1000',
            'notas' => 'nullable|string|max:1000',
            'aprobado_por' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $mantenimiento->update($request->all());

        // Actualizar estado de máquina según estado del mantenimiento
        if ($request->has('estado')) {
            $maquina = Maquinaria::findOrFail($mantenimiento->maquina_id);
            
            switch ($request->estado) {
                case 'en_proceso':
                    $maquina->update(['estado_actual' => 'mantenimiento']);
                    break;
                case 'completado':
                    $maquina->update(['estado_actual' => 'operativa']);
                    break;
                case 'cancelado':
                    // Solo cambiar a operativa si no hay otros mantenimientos activos
                    $hayOtrosActivos = Mantenimiento::where('maquina_id', $maquina->id)
                        ->where('id', '!=', $mantenimiento->id)
                        ->whereIn('estado', ['programado', 'en_proceso'])
                        ->exists();
                    
                    if (!$hayOtrosActivos && $maquina->estado_actual === 'mantenimiento') {
                        $maquina->update(['estado_actual' => 'operativa']);
                    }
                    break;
            }
        }

        return response()->json([
            'message' => 'Mantenimiento actualizado exitosamente',
            'data' => $mantenimiento->fresh()->load('maquina', 'tecnico')
        ]);
    }

    /**
     * Eliminar mantenimiento
     */
    public function destroy(int $id): JsonResponse
    {
        $mantenimiento = Mantenimiento::findOrFail($id);

        // Solo permitir eliminar si está programado o cancelado
        if (!in_array($mantenimiento->estado, ['programado', 'cancelado'])) {
            return response()->json([
                'message' => 'Solo se pueden eliminar mantenimientos programados o cancelados'
            ], 400);
        }

        $mantenimiento->delete();

        return response()->json([
            'message' => 'Mantenimiento eliminado exitosamente'
        ]);
    }

    /**
     * Iniciar mantenimiento
     */
    public function iniciar(int $id): JsonResponse
    {
        $mantenimiento = Mantenimiento::findOrFail($id);

        if ($mantenimiento->estado !== 'programado') {
            return response()->json([
                'message' => 'Solo se pueden iniciar mantenimientos programados'
            ], 400);
        }

        $mantenimiento->update([
            'estado' => 'en_proceso',
            'fecha_inicio' => now(),
        ]);

        // Cambiar estado de máquina
        $maquina = Maquinaria::findOrFail($mantenimiento->maquina_id);
        $maquina->update(['estado_actual' => 'mantenimiento']);

        return response()->json([
            'message' => 'Mantenimiento iniciado',
            'data' => $mantenimiento->fresh()
        ]);
    }

    /**
     * Completar mantenimiento
     */
    public function completar(Request $request, int $id): JsonResponse
    {
        $mantenimiento = Mantenimiento::findOrFail($id);

        if ($mantenimiento->estado !== 'en_proceso') {
            return response()->json([
                'message' => 'Solo se pueden completar mantenimientos en proceso'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'trabajo_realizado' => 'required|string|max:1000',
            'repuestos_utilizados' => 'nullable|string|max:1000',
            'costo_real' => 'nullable|numeric|min:0',
            'duracion_real_horas' => 'required|numeric|min:0',
            'aprobado_por' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $mantenimiento->update(array_merge(
            $request->all(),
            [
                'estado' => 'completado',
                'fecha_fin' => now(),
            ]
        ));

        // Cambiar estado de máquina a operativa
        $maquina = Maquinaria::findOrFail($mantenimiento->maquina_id);
        
        // Verificar si hay otros mantenimientos activos
        $hayOtrosActivos = Mantenimiento::where('maquina_id', $maquina->id)
            ->where('id', '!=', $mantenimiento->id)
            ->whereIn('estado', ['programado', 'en_proceso'])
            ->exists();
        
        if (!$hayOtrosActivos) {
            $maquina->update(['estado_actual' => 'operativa']);
        }

        return response()->json([
            'message' => 'Mantenimiento completado exitosamente',
            'data' => $mantenimiento->fresh()
        ]);
    }

    /**
     * Obtener historial de mantenimiento de una máquina
     */
    public function historial(int $maquinaId): JsonResponse
    {
        $maquina = Maquinaria::findOrFail($maquinaId);

        $historial = Mantenimiento::with('tecnico', 'aprobadoPor')
            ->where('maquina_id', $maquinaId)
            ->orderBy('fecha_programada', 'desc')
            ->get();

        $estadisticas = [
            'total_mantenimientos' => $historial->count(),
            'preventivos' => $historial->where('tipo', 'preventivo')->count(),
            'correctivos' => $historial->where('tipo', 'correctivo')->count(),
            'predictivos' => $historial->where('tipo', 'predictivo')->count(),
            'completados' => $historial->where('estado', 'completado')->count(),
            'costo_total' => $historial->where('estado', 'completado')->sum('costo_real'),
            'tiempo_total_horas' => $historial->where('estado', 'completado')->sum('duracion_real_horas'),
        ];

        return response()->json([
            'maquina' => $maquina,
            'historial' => $historial,
            'estadisticas' => $estadisticas
        ]);
    }

    /**
     * Obtener calendario de mantenimientos programados
     */
    public function calendario(Request $request): JsonResponse
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

        $query = Mantenimiento::with('maquina', 'tecnico')
            ->whereBetween('fecha_programada', [
                $request->fecha_inicio,
                $request->fecha_fin
            ])
            ->whereIn('estado', ['programado', 'en_proceso']);

        if ($request->has('maquina_id')) {
            $query->where('maquina_id', $request->maquina_id);
        }

        $mantenimientos = $query->orderBy('fecha_programada')->get();

        return response()->json($mantenimientos);
    }

    /**
     * Obtener alertas de mantenimiento
     */
    public function alertas(): JsonResponse
    {
        // Mantenimientos atrasados
        $atrasados = Mantenimiento::with('maquina')
            ->where('estado', 'programado')
            ->where('fecha_programada', '<', now())
            ->orderBy('fecha_programada')
            ->get();

        // Mantenimientos urgentes (próximos 7 días)
        $urgentes = Mantenimiento::with('maquina')
            ->where('estado', 'programado')
            ->where('prioridad', 'critica')
            ->whereBetween('fecha_programada', [now(), now()->addDays(7)])
            ->orderBy('fecha_programada')
            ->get();

        // Mantenimientos en proceso por más de la duración estimada
        $demorados = Mantenimiento::with('maquina')
            ->where('estado', 'en_proceso')
            ->whereNotNull('fecha_inicio')
            ->get()
            ->filter(function($m) {
                if (!$m->fecha_inicio) return false;
                $horasTranscurridas = now()->diffInHours($m->fecha_inicio);
                return $horasTranscurridas > $m->duracion_estimada_horas;
            })
            ->values();

        return response()->json([
            'atrasados' => [
                'count' => $atrasados->count(),
                'mantenimientos' => $atrasados
            ],
            'urgentes' => [
                'count' => $urgentes->count(),
                'mantenimientos' => $urgentes
            ],
            'demorados' => [
                'count' => $demorados->count(),
                'mantenimientos' => $demorados
            ]
        ]);
    }
}
