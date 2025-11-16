<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Turno;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * API Controller para Turnos de Trabajo
 * 
 * Gestiona la configuración de turnos de trabajo en la planta
 * (matutino, vespertino, nocturno) con horarios y días activos.
 */
class TurnoController extends Controller
{
    /**
     * Listar todos los turnos
     */
    public function index(): JsonResponse
    {
        $turnos = Turno::orderBy('hora_inicio')->get();

        return response()->json($turnos);
    }

    /**
     * Mostrar turno específico
     */
    public function show(int $id): JsonResponse
    {
        $turno = Turno::findOrFail($id);

        return response()->json($turno);
    }

    /**
     * Crear nuevo turno
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nombre_turno' => 'required|string|max:50|unique:turnos,nombre_turno',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i',
            'dias_activos' => 'nullable|json',
            'activo' => 'nullable|boolean',
            'descripcion' => 'nullable|string|max:200',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $turno = Turno::create(array_merge(
            $request->all(),
            ['activo' => $request->get('activo', true)]
        ));

        return response()->json([
            'message' => 'Turno creado exitosamente',
            'data' => $turno
        ], 201);
    }

    /**
     * Actualizar turno
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $turno = Turno::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nombre_turno' => 'sometimes|string|max:50|unique:turnos,nombre_turno,' . $id,
            'hora_inicio' => 'sometimes|date_format:H:i',
            'hora_fin' => 'sometimes|date_format:H:i',
            'dias_activos' => 'nullable|json',
            'activo' => 'nullable|boolean',
            'descripcion' => 'nullable|string|max:200',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $turno->update($request->all());

        return response()->json([
            'message' => 'Turno actualizado exitosamente',
            'data' => $turno->fresh()
        ]);
    }

    /**
     * Eliminar turno
     */
    public function destroy(int $id): JsonResponse
    {
        $turno = Turno::findOrFail($id);

        // Verificar que no haya órdenes o registros asociados
        if ($turno->ordenesProduccion()->exists() || $turno->registrosProduccion()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar un turno que tiene órdenes o registros de producción asociados'
            ], 400);
        }

        $turno->delete();

        return response()->json([
            'message' => 'Turno eliminado exitosamente'
        ]);
    }

    /**
     * Obtener turno actual según la hora
     */
    public function actual(): JsonResponse
    {
        $horaActual = now()->format('H:i');
        
        $turno = Turno::where('activo', true)
            ->where(function($query) use ($horaActual) {
                // Turnos que no cruzan medianoche
                $query->where(function($q) use ($horaActual) {
                    $q->where('hora_inicio', '<=', 'hora_fin')
                      ->where('hora_inicio', '<=', $horaActual)
                      ->where('hora_fin', '>=', $horaActual);
                })
                // Turnos que cruzan medianoche (ej: 22:00 a 06:00)
                ->orWhere(function($q) use ($horaActual) {
                    $q->where('hora_inicio', '>', 'hora_fin')
                      ->where(function($q2) use ($horaActual) {
                          $q2->where('hora_inicio', '<=', $horaActual)
                             ->orWhere('hora_fin', '>=', $horaActual);
                      });
                });
            })
            ->first();

        if (!$turno) {
            return response()->json([
                'message' => 'No hay turno activo en este momento',
                'hora_actual' => $horaActual
            ], 404);
        }

        return response()->json($turno);
    }

    /**
     * Activar/desactivar turno
     */
    public function toggleActivo(int $id): JsonResponse
    {
        $turno = Turno::findOrFail($id);
        
        $turno->update([
            'activo' => !$turno->activo
        ]);

        return response()->json([
            'message' => $turno->activo ? 'Turno activado' : 'Turno desactivado',
            'data' => $turno
        ]);
    }

    /**
     * Obtener estadísticas de producción por turno
     */
    public function estadisticas(Request $request, int $id): JsonResponse
    {
        $turno = Turno::findOrFail($id);

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

        $registros = $turno->registrosProduccion()
            ->whereBetween('fecha_hora_inicio', [
                $request->fecha_inicio,
                $request->fecha_fin
            ])
            ->get();

        $ordenes = $turno->ordenesProduccion()
            ->whereBetween('fecha_inicio', [
                $request->fecha_inicio,
                $request->fecha_fin
            ])
            ->get();

        $estadisticas = [
            'ordenes_totales' => $ordenes->count(),
            'ordenes_completadas' => $ordenes->where('estado', 'completada')->count(),
            'unidades_producidas' => $registros->sum('cantidad_producida'),
            'unidades_conformes' => $registros->sum('cantidad_conforme'),
            'unidades_defectuosas' => $registros->sum('cantidad_defectuosa'),
            'porcentaje_calidad' => $registros->sum('cantidad_producida') > 0
                ? round(($registros->sum('cantidad_conforme') / $registros->sum('cantidad_producida')) * 100, 2)
                : 0,
            'productividad_promedio' => round($registros->avg('productividad_unidades_hora'), 2),
            'merma_total_kg' => round($registros->sum('merma_kg'), 2),
            'tiempo_paro_total_minutos' => $registros->sum('tiempo_paro_minutos'),
        ];

        return response()->json([
            'turno' => $turno,
            'periodo' => [
                'inicio' => $request->fecha_inicio,
                'fin' => $request->fecha_fin,
            ],
            'estadisticas' => $estadisticas
        ]);
    }
}
