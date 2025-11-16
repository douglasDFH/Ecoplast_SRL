<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Maquinaria;
use App\Models\TipoMaquinaria;
use App\Events\MaquinariaActualizada;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * Controlador API para gestión de Maquinaria Biodegradable
 *
 * Funcionalidades:
 * - CRUD completo de maquinaria
 * - Cálculos de OEE (Overall Equipment Effectiveness)
 * - Gestión de mantenimiento preventivo
 * - Parámetros técnicos para procesamiento biodegradable
 * - Monitoreo de eficiencia energética
 */
class MaquinariaController extends Controller
{
    /**
     * Listar todas las máquinas con filtros y paginación
     */
    public function index(Request $request): JsonResponse
    {
        $query = Maquinaria::with('tipo');

        // Filtros técnicos
        if ($request->has('tipo_maquina_id')) {
            $query->where('tipo_maquina_id', $request->tipo_maquina_id);
        }

        if ($request->has('estado_actual')) {
            $query->where('estado_actual', $request->estado_actual);
        }

        if ($request->has('marca')) {
            $query->where('marca', 'like', '%' . $request->marca . '%');
        }

        if ($request->has('ubicacion')) {
            $query->where('ubicacion', 'like', '%' . $request->ubicacion . '%');
        }

        if ($request->has('activo')) {
            $query->where('activo', $request->boolean('activo'));
        }

        // Filtros de capacidad
        if ($request->has('capacidad_min')) {
            $query->where('capacidad_produccion', '>=', $request->capacidad_min);
        }

        if ($request->has('capacidad_max')) {
            $query->where('capacidad_produccion', '<=', $request->capacidad_max);
        }

        // Ordenamiento
        $sortBy = $request->get('sort_by', 'codigo_maquina');
        $sortDirection = $request->get('sort_direction', 'asc');
        $query->orderBy($sortBy, $sortDirection);

        // Paginación
        $perPage = $request->get('per_page', 15);
        $maquinas = $query->paginate($perPage);

        // Añadir información calculada
        $maquinas->getCollection()->transform(function ($maquina) {
            $maquina->oee_actual = $maquina->calcularOEE();
            $maquina->necesita_mantenimiento = $maquina->necesitaMantenimiento();
            $maquina->ordenes_activas = $maquina->ordenesProduccion()
                ->whereIn('estado', ['pendiente', 'programada', 'en_proceso'])
                ->count();
            $maquina->eficiencia_energetica = $this->calcularEficienciaEnergetica($maquina);
            return $maquina;
        });

        return response()->json([
            'success' => true,
            'data' => $maquinas,
            'message' => 'Maquinaria recuperada exitosamente'
        ]);
    }

    /**
     * Crear una nueva máquina
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'codigo_maquina' => 'required|string|max:50|unique:maquinas',
            'nombre_maquina' => 'required|string|max:150',
            'tipo_maquina_id' => 'required|exists:tipos_maquinaria,id',
            'marca' => 'required|string|max:100',
            'modelo' => 'required|string|max:100',
            'año_fabricacion' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'capacidad_produccion' => 'required|numeric|min:0',
            'unidad_capacidad' => ['required', Rule::in(['kg/h', 'unidades/h', 'm2/h', 'litros/h'])],
            'consumo_energia_kwh' => 'nullable|numeric|min:0',
            'temp_min_operacion' => 'nullable|numeric|min:-50|max:500',
            'temp_max_operacion' => 'nullable|numeric|min:-50|max:500',
            'presion_max_bar' => 'nullable|numeric|min:0',
            'velocidad_max_rpm' => 'nullable|numeric|min:0',
            'fuerza_cierre_ton' => 'nullable|numeric|min:0',
            'diametro_husillo_mm' => 'nullable|numeric|min:0',
            'fecha_instalacion' => 'nullable|date',
            'vida_util_años' => 'nullable|integer|min:1|max:50',
            'ubicacion' => 'nullable|string|max:200',
            'estado_actual' => ['required', Rule::in(['operativa', 'mantenimiento', 'parada', 'averia'])],
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

            $maquina = Maquinaria::create($request->all());

            DB::commit();

            // Emitir evento de creación en tiempo real
            broadcast(new MaquinariaActualizada($maquina, 'creada'))->toOthers();

            return response()->json([
                'success' => true,
                'data' => $maquina->load('tipo'),
                'message' => 'Máquina creada exitosamente'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la máquina: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar una máquina específica con detalles técnicos
     */
    public function show(Maquinaria $maquina): JsonResponse
    {
        $maquina->load('tipo', 'mantenimientos', 'ordenesProduccion');

        // Información calculada
        $maquina->oee_actual = $maquina->calcularOEE();
        $maquina->necesita_mantenimiento = $maquina->necesitaMantenimiento();
        $maquina->eficiencia_energetica = $this->calcularEficienciaEnergetica($maquina);
        $maquina->ordenes_activas = $maquina->ordenesProduccion()
            ->whereIn('estado', ['pendiente', 'programada', 'en_proceso'])
            ->count();

        // Estadísticas de producción
        $maquina->estadisticas_produccion = [
            'ordenes_totales' => $maquina->ordenesProduccion()->count(),
            'ordenes_completadas' => $maquina->ordenesProduccion()->where('estado', 'finalizada')->count(),
            'ordenes_pendientes' => $maquina->ordenesProduccion()->whereIn('estado', ['pendiente', 'programada'])->count(),
            'mantenimientos_realizados' => $maquina->mantenimientos()->count(),
            'ultimo_mantenimiento' => $maquina->mantenimientos()->latest()->first()?->fecha_mantenimiento,
        ];

        return response()->json([
            'success' => true,
            'data' => $maquina,
            'message' => 'Máquina recuperada exitosamente'
        ]);
    }

    /**
     * Actualizar una máquina
     */
    public function update(Request $request, Maquinaria $maquina): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'codigo_maquina' => ['sometimes', 'string', 'max:50', Rule::unique('maquinas')->ignore($maquina->id)],
            'nombre_maquina' => 'sometimes|string|max:150',
            'tipo_maquina_id' => 'sometimes|exists:tipos_maquinaria,id',
            'marca' => 'sometimes|string|max:100',
            'modelo' => 'sometimes|string|max:100',
            'año_fabricacion' => 'sometimes|integer|min:1900|max:' . (date('Y') + 1),
            'capacidad_produccion' => 'sometimes|numeric|min:0',
            'unidad_capacidad' => ['sometimes', Rule::in(['kg/h', 'unidades/h', 'm2/h', 'litros/h'])],
            'consumo_energia_kwh' => 'nullable|numeric|min:0',
            'temp_min_operacion' => 'nullable|numeric|min:-50|max:500',
            'temp_max_operacion' => 'nullable|numeric|min:-50|max:500',
            'presion_max_bar' => 'nullable|numeric|min:0',
            'velocidad_max_rpm' => 'nullable|numeric|min:0',
            'fuerza_cierre_ton' => 'nullable|numeric|min:0',
            'diametro_husillo_mm' => 'nullable|numeric|min:0',
            'fecha_instalacion' => 'nullable|date',
            'vida_util_años' => 'nullable|integer|min:1|max:50',
            'ubicacion' => 'nullable|string|max:200',
            'estado_actual' => ['sometimes', Rule::in(['operativa', 'mantenimiento', 'parada', 'averia'])],
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

            $maquina->update($request->all());

            DB::commit();

            // Emitir evento de actualización en tiempo real
            broadcast(new MaquinariaActualizada($maquina, 'actualizada'))->toOthers();

            return response()->json([
                'success' => true,
                'data' => $maquina->load('tipo'),
                'message' => 'Máquina actualizada exitosamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la máquina: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar una máquina
     */
    public function destroy(Maquinaria $maquina): JsonResponse
    {
        try {
            // Verificar si la máquina tiene órdenes activas
            $ordenesActivas = $maquina->ordenesProduccion()
                ->whereIn('estado', ['pendiente', 'programada', 'en_proceso'])
                ->count();

            if ($ordenesActivas > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar la máquina porque tiene órdenes activas'
                ], 409);
            }

            $maquina->delete();

            return response()->json([
                'success' => true,
                'message' => 'Máquina eliminada exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la máquina: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calcular eficiencia energética de la máquina
     */
    private function calcularEficienciaEnergetica(Maquinaria $maquina): ?float
    {
        if (!$maquina->consumo_energia_kwh || !$maquina->capacidad_produccion) {
            return null;
        }

        // Eficiencia energética = capacidad / consumo energético
        // Valores más altos indican mejor eficiencia
        return $maquina->capacidad_produccion / $maquina->consumo_energia_kwh;
    }

    /**
     * Obtener estadísticas generales de maquinaria
     */
    public function estadisticas(): JsonResponse
    {
        $estadisticas = [
            'total_maquinas' => Maquinaria::count(),
            'maquinas_activas' => Maquinaria::where('activo', true)->count(),
            'maquinas_operativas' => Maquinaria::where('estado_actual', 'operativa')->count(),
            'maquinas_mantenimiento' => Maquinaria::where('estado_actual', 'mantenimiento')->count(),
            'maquinas_paradas' => Maquinaria::where('estado_actual', 'parada')->count(),
            'maquinas_averiadas' => Maquinaria::where('estado_actual', 'averia')->count(),
            'oee_promedio' => Maquinaria::where('activo', true)->get()->avg(function ($maquina) {
                return $maquina->calcularOEE();
            }),
            'consumo_energetico_total' => Maquinaria::where('activo', true)->sum('consumo_energia_kwh'),
            'capacidad_total' => Maquinaria::where('activo', true)->sum('capacidad_produccion'),
            'por_tipo' => Maquinaria::select('tipo_maquina_id', DB::raw('count(*) as total'))
                ->with('tipo')
                ->groupBy('tipo_maquina_id')
                ->get(),
            'por_marca' => Maquinaria::select('marca', DB::raw('count(*) as total'))
                ->groupBy('marca')
                ->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $estadisticas,
            'message' => 'Estadísticas de maquinaria recuperadas exitosamente'
        ]);
    }

    /**
     * Obtener máquinas por estado operativo
     */
    public function porEstado(): JsonResponse
    {
        $estados = ['operativa', 'mantenimiento', 'parada', 'averia'];

        $clasificadas = [];
        foreach ($estados as $estado) {
            $clasificadas[$estado] = Maquinaria::where('estado_actual', $estado)
                ->where('activo', true)
                ->with('tipo')
                ->get()
                ->map(function ($maquina) {
                    $maquina->oee_actual = $maquina->calcularOEE();
                    return $maquina;
                });
        }

        return response()->json([
            'success' => true,
            'data' => $clasificadas,
            'message' => 'Máquinas clasificadas por estado operativo'
        ]);
    }

    /**
     * Obtener máquinas que necesitan mantenimiento
     */
    public function necesitanMantenimiento(): JsonResponse
    {
        $maquinas = Maquinaria::where('activo', true)
            ->get()
            ->filter(function ($maquina) {
                return $maquina->necesitaMantenimiento();
            })
            ->map(function ($maquina) {
                $maquina->load('tipo', 'mantenimientos');
                $maquina->dias_sin_mantenimiento = $this->calcularDiasSinMantenimiento($maquina);
                return $maquina;
            });

        return response()->json([
            'success' => true,
            'data' => $maquinas,
            'message' => 'Máquinas que necesitan mantenimiento'
        ]);
    }

    /**
     * Calcular días desde el último mantenimiento
     */
    private function calcularDiasSinMantenimiento(Maquinaria $maquina): ?int
    {
        $ultimoMantenimiento = $maquina->mantenimientos()->latest('fecha_mantenimiento')->first();

        if (!$ultimoMantenimiento) {
            return null; // Nunca se ha mantenido
        }

        return now()->diffInDays($ultimoMantenimiento->fecha_mantenimiento);
    }

    /**
     * Actualizar estado de máquina
     */
    public function actualizarEstado(Request $request, Maquinaria $maquina): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'estado_actual' => ['required', Rule::in(['operativa', 'mantenimiento', 'parada', 'averia'])],
            'observaciones' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $maquina->update([
                'estado_actual' => $request->estado_actual
            ]);

            // Si cambió a mantenimiento, crear registro de mantenimiento
            if ($request->estado_actual === 'mantenimiento') {
                $this->crearRegistroMantenimiento($maquina, $request->observaciones);
            }

            return response()->json([
                'success' => true,
                'data' => $maquina->load('tipo'),
                'message' => 'Estado de máquina actualizado exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar estado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear registro de mantenimiento
     */
    private function crearRegistroMantenimiento(Maquinaria $maquina, ?string $observaciones): void
    {
        \App\Models\Mantenimiento::create([
            'maquina_id' => $maquina->id,
            'tipo_mantenimiento' => 'correctivo',
            'fecha_mantenimiento' => now(),
            'descripcion' => $observaciones ?? 'Mantenimiento iniciado desde actualización de estado',
            'realizado_por' => Auth::check() ? Auth::id() : null,
            'estado' => 'en_progreso'
        ]);
    }
}
