<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Maquinaria;
use App\Models\OrdenProduccion;
use App\Models\SimulacionMaquina;
use App\Models\Insumo;
use App\Models\ProductoTerminado;
use App\Models\MovimientoInventarioInsumo;
use App\Models\MovimientoInventarioProducto;
use App\Models\RegistroProduccion;
use App\Models\FormulacionInsumo;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PanelMaquinasController extends Controller
{
    /**
     * Obtener estado de todas las máquinas con simulaciones activas
     */
    public function index(): JsonResponse
    {
        $maquinas = Maquinaria::with([
            'tipo',
            'ordenesProduccion' => function($query) {
                $query->where('estado', 'en_proceso')
                      ->with(['productoTerminado', 'formulacion']);
            }
        ])->get();

        $simulaciones = SimulacionMaquina::with(['maquina', 'ordenProduccion.productoTerminado'])
            ->activas()
            ->get();

        $data = $maquinas->map(function ($maquina) use ($simulaciones) {
            $simulacion = $simulaciones->firstWhere('maquina_id', $maquina->id);
            $ordenActual = $maquina->ordenesProduccion->first();

            return [
                'id' => $maquina->id,
                'codigo' => $maquina->codigo_maquina,
                'nombre' => $maquina->nombre_maquina,
                'tipo' => $maquina->tipo?->nombre ?? 'N/A',
                'estado_maquina' => $maquina->estado_actual,
                'capacidad_produccion' => $maquina->capacidad_produccion,
                'unidad_capacidad' => $maquina->unidad_capacidad,

                // Orden actual
                'tiene_orden' => $ordenActual !== null,
                'orden' => $ordenActual ? [
                    'id' => $ordenActual->id,
                    'numero_orden' => $ordenActual->numero_orden,
                    'producto' => $ordenActual->productoTerminado?->nombre_producto ?? 'N/A',
                    'cantidad_planificada' => $ordenActual->cantidad_planificada,
                    'estado' => $ordenActual->estado,
                    'prioridad' => $ordenActual->prioridad,
                ] : null,

                // Simulación
                'simulacion' => $simulacion ? [
                    'estado' => $simulacion->estado_simulacion,
                    'unidades_producidas' => $simulacion->unidades_producidas,
                    'unidades_conformes' => $simulacion->unidades_conformes,
                    'unidades_defectuosas' => $simulacion->unidades_defectuosas,
                    'porcentaje_progreso' => (float) $simulacion->porcentaje_progreso,
                    'temperatura_zona1' => (float) $simulacion->temperatura_zona1,
                    'temperatura_zona2' => (float) $simulacion->temperatura_zona2,
                    'temperatura_zona3' => (float) $simulacion->temperatura_zona3,
                    'presion_actual' => (float) $simulacion->presion_actual,
                    'velocidad_husillo_actual' => (float) $simulacion->velocidad_husillo_actual,
                    'tiempo_ciclo_actual' => (float) $simulacion->tiempo_ciclo_actual,
                    'consumo_energia' => (float) $simulacion->consumo_energia_acumulado,
                    'tasa_defectos' => (float) $simulacion->tasa_defectos,
                    'eficiencia_actual' => (float) $simulacion->eficiencia_actual,
                    'tiempo_transcurrido' => $simulacion->tiempo_transcurrido_segundos,
                    'inicio' => $simulacion->inicio_simulacion?->format('H:i:s'),
                    'tiempo_estimado_finalizacion' => $this->calcularTiempoEstimado($simulacion, $ordenActual),
                ] : null,
            ];
        });

        // Estadísticas generales
        $estadisticas = [
            'total_maquinas' => $maquinas->count(),
            'operativas' => $maquinas->where('estado_actual', 'operativa')->count(),
            'en_produccion' => $simulaciones->where('estado_simulacion', 'produciendo')->count(),
            'en_mantenimiento' => $maquinas->where('estado_actual', 'mantenimiento')->count(),
            'paradas' => $maquinas->whereIn('estado_actual', ['parada', 'averia'])->count(),
            'eficiencia_promedio' => $simulaciones->avg('eficiencia_actual') ?? 0,
        ];

        // Órdenes pendientes
        $ordenesPendientes = OrdenProduccion::where('estado', 'pendiente')
            ->with(['productoTerminado', 'maquina'])
            ->orderBy('prioridad', 'desc')
            ->orderBy('fecha_programada', 'asc')
            ->limit(10)
            ->get()
            ->map(function ($orden) {
                return [
                    'id' => $orden->id,
                    'numero_orden' => $orden->numero_orden,
                    'producto' => $orden->productoTerminado?->nombre_producto ?? 'N/A',
                    'cantidad_planificada' => $orden->cantidad_planificada,
                    'maquina_requerida' => $orden->maquina?->nombre_maquina ?? 'N/A',
                    'prioridad' => $orden->prioridad,
                    'fecha_programada' => $orden->fecha_programada->format('Y-m-d H:i'),
                ];
            });

        return response()->json([
            'maquinas' => $data,
            'estadisticas' => $estadisticas,
            'ordenes_pendientes' => $ordenesPendientes,
        ]);
    }

    /**
     * Iniciar producción de una orden (descontar insumos y crear simulación)
     */
    public function iniciarProduccion(Request $request): JsonResponse
    {
        $request->validate([
            'orden_id' => 'required|exists:ordenes_produccion,id',
        ]);

        DB::beginTransaction();

        try {
            $orden = OrdenProduccion::with(['productoTerminado', 'formulacion.componentes.insumo', 'maquina'])
                ->findOrFail($request->orden_id);

            // Validar que la orden esté pendiente
            if ($orden->estado !== 'pendiente') {
                return response()->json(['message' => 'La orden no está en estado pendiente'], 400);
            }

            // Validar que la máquina esté disponible
            if ($orden->maquina->estado_actual !== 'operativa') {
                return response()->json(['message' => 'La máquina no está disponible'], 400);
            }

            // 1. CALCULAR Y VALIDAR INSUMOS NECESARIOS
            $insumosNecesarios = $this->calcularInsumosNecesarios($orden);

            foreach ($insumosNecesarios as $insumoNecesario) {
                $insumo = Insumo::find($insumoNecesario['insumo_id']);

                if ($insumo->stock_actual < $insumoNecesario['cantidad_necesaria']) {
                    DB::rollBack();
                    return response()->json([
                        'message' => 'Stock insuficiente',
                        'insumo' => $insumo->nombre_insumo,
                        'disponible' => $insumo->stock_actual,
                        'necesario' => $insumoNecesario['cantidad_necesaria'],
                    ], 400);
                }
            }

            // 2. DESCONTAR INSUMOS DEL STOCK
            foreach ($insumosNecesarios as $insumoNecesario) {
                $insumo = Insumo::find($insumoNecesario['insumo_id']);
                $insumo->stock_actual -= $insumoNecesario['cantidad_necesaria'];
                $insumo->save();

                // Registrar movimiento de salida
                MovimientoInventarioInsumo::create([
                    'insumo_id' => $insumo->id,
                    'tipo_movimiento' => 'salida',
                    'cantidad' => $insumoNecesario['cantidad_necesaria'],
                    'usuario_id' => Auth::id() ?? 1,
                    'motivo' => "Consumo para orden {$orden->numero_orden}",
                    'orden_produccion_id' => $orden->id,
                    'fecha_movimiento' => now(),
                ]);
            }

            // 3. ACTUALIZAR ESTADO DE LA ORDEN
            $orden->estado = 'en_proceso';
            $orden->fecha_inicio = now();
            $orden->save();

            // 4. CREAR SIMULACIÓN
            $producto = $orden->productoTerminado;
            $maquina = $orden->maquina;

            $simulacion = SimulacionMaquina::create([
                'maquina_id' => $maquina->id,
                'orden_produccion_id' => $orden->id,
                'estado_simulacion' => 'produciendo',
                'unidades_producidas' => 0,
                'unidades_conformes' => 0,
                'unidades_defectuosas' => 0,
                'porcentaje_progreso' => 0,
                'temperatura_zona1' => $this->generarTemperatura($producto->temperatura_proceso, -5, 5),
                'temperatura_zona2' => $this->generarTemperatura($producto->temperatura_proceso, 0, 8),
                'temperatura_zona3' => $this->generarTemperatura($producto->temperatura_proceso, 5, 15),
                'temperatura_zona4' => $this->generarTemperatura($producto->temperatura_proceso, 10, 20),
                'presion_actual' => $maquina->presion_max_bar ? $maquina->presion_max_bar * 0.75 : 100,
                'velocidad_husillo_actual' => $maquina->velocidad_max_rpm ? $maquina->velocidad_max_rpm * 0.6 : 80,
                'tiempo_ciclo_actual' => $producto->tiempo_ciclo_segundos ?? 20,
                'consumo_energia_acumulado' => 0,
                'tasa_defectos' => rand(20, 80) / 10, // 2.0% a 8.0%
                'eficiencia_actual' => rand(800, 950) / 10, // 80% a 95%
                'inicio_simulacion' => now(),
                'ultimo_ciclo' => now(),
                'ciclos_completados' => 0,
            ]);

            // 5. ACTUALIZAR ESTADO DE LA MÁQUINA
            $maquina->estado_actual = 'operativa'; // Produciendo
            $maquina->save();

            DB::commit();

            return response()->json([
                'message' => 'Producción iniciada exitosamente',
                'orden' => $orden->fresh(['productoTerminado', 'maquina']),
                'simulacion' => $simulacion,
                'insumos_consumidos' => $insumosNecesarios,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al iniciar producción', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Simular un ciclo de producción (llamado periódicamente desde el frontend)
     */
    public function simularCiclo(Request $request): JsonResponse
    {
        $request->validate([
            'simulacion_id' => 'required|exists:simulaciones_maquinas,id',
        ]);

        $simulacion = SimulacionMaquina::with(['ordenProduccion.productoTerminado', 'maquina'])
            ->findOrFail($request->simulacion_id);

        if ($simulacion->estado_simulacion !== 'produciendo') {
            return response()->json(['message' => 'La simulación no está activa'], 400);
        }

        $orden = $simulacion->ordenProduccion;
        $producto = $orden->productoTerminado;

        // Calcular tiempo desde último ciclo
        $ahora = now();
        $tiempoCiclo = $producto->tiempo_ciclo_segundos ?? 20;
        $segundosDesdeUltimoCiclo = $simulacion->ultimo_ciclo ? $ahora->diffInSeconds($simulacion->ultimo_ciclo) : $tiempoCiclo;

        // Si ha pasado suficiente tiempo, ejecutar ciclo
        if ($segundosDesdeUltimoCiclo >= $tiempoCiclo) {
            $piezasPorCiclo = $producto->piezas_por_ciclo ?? 1;
            $tasaDefectos = (float) $simulacion->tasa_defectos / 100;

            // Generar producción con defectos aleatorios
            $piezasDefectuosas = 0;
            for ($i = 0; $i < $piezasPorCiclo; $i++) {
                if (rand(1, 1000) / 1000 <= $tasaDefectos) {
                    $piezasDefectuosas++;
                }
            }
            $piezasConformes = $piezasPorCiclo - $piezasDefectuosas;

            // Actualizar contador
            $simulacion->unidades_producidas += $piezasPorCiclo;
            $simulacion->unidades_conformes += $piezasConformes;
            $simulacion->unidades_defectuosas += $piezasDefectuosas;
            $simulacion->ciclos_completados++;
            $simulacion->ultimo_ciclo = $ahora;

            // Actualizar progreso
            $simulacion->porcentaje_progreso = min(100, ($simulacion->unidades_producidas / $orden->cantidad_planificada) * 100);

            // Simular variaciones en parámetros
            $simulacion->temperatura_zona1 += rand(-2, 2) / 10;
            $simulacion->temperatura_zona2 += rand(-2, 2) / 10;
            $simulacion->temperatura_zona3 += rand(-2, 2) / 10;
            $simulacion->presion_actual += rand(-5, 5) / 10;
            $simulacion->velocidad_husillo_actual += rand(-3, 3) / 10;

            // Consumo energético (kWh por ciclo)
            $consumoPorCiclo = ($simulacion->maquina->consumo_energia_kwh ?? 25) * ($tiempoCiclo / 3600);
            $simulacion->consumo_energia_acumulado += $consumoPorCiclo;

            // Tiempo transcurrido
            $simulacion->tiempo_transcurrido_segundos = $ahora->diffInSeconds($simulacion->inicio_simulacion);

            $simulacion->save();

            // Actualizar cantidades en la orden
            $orden->cantidad_producida = $simulacion->unidades_producidas;
            $orden->cantidad_conforme = $simulacion->unidades_conformes;
            $orden->cantidad_defectuosa = $simulacion->unidades_defectuosas;
            $orden->save();

            // Verificar si se completó la producción
            if ($simulacion->unidades_producidas >= $orden->cantidad_planificada) {
                return $this->completarProduccion($simulacion->id);
            }
        }

        return response()->json([
            'simulacion' => $simulacion->fresh(),
            'orden' => $orden->fresh(),
        ]);
    }

    /**
     * Completar producción (incrementar stock de productos y finalizar)
     */
    public function completarProduccion(int $simulacionId): JsonResponse
    {
        DB::beginTransaction();

        try {
            $simulacion = SimulacionMaquina::with(['ordenProduccion.productoTerminado', 'maquina'])
                ->findOrFail($simulacionId);

            $orden = $simulacion->ordenProduccion;
            $producto = $orden->productoTerminado;

            // 1. INCREMENTAR STOCK DE PRODUCTOS TERMINADOS
            $producto->stock_actual += $simulacion->unidades_conformes;
            $producto->save();

            // 2. REGISTRAR MOVIMIENTO DE ENTRADA DE PRODUCTOS
            MovimientoInventarioProducto::create([
                'producto_id' => $producto->id,
                'tipo_movimiento' => 'entrada_produccion',
                'cantidad' => $simulacion->unidades_conformes,
                'lote_produccion' => "LOTE-{$orden->numero_orden}",
                'fecha_fabricacion' => now(),
                'usuario_id' => Auth::id() ?? 1,
                'referencia' => $orden->numero_orden,
                'motivo' => "Producción completada - Orden {$orden->numero_orden}",
                'fecha_movimiento' => now(),
            ]);

            // 3. FINALIZAR ORDEN
            $orden->estado = 'completada';
            $orden->fecha_fin = now();
            $orden->cantidad_producida = $simulacion->unidades_producidas;
            $orden->cantidad_conforme = $simulacion->unidades_conformes;
            $orden->cantidad_defectuosa = $simulacion->unidades_defectuosas;
            $orden->save();

            // 4. FINALIZAR SIMULACIÓN
            $simulacion->estado_simulacion = 'completada';
            $simulacion->fin_simulacion = now();
            $simulacion->porcentaje_progreso = 100;
            $simulacion->save();

            // 5. LIBERAR MÁQUINA
            $simulacion->maquina->estado_actual = 'operativa';
            $simulacion->maquina->save();

            DB::commit();

            return response()->json([
                'message' => 'Producción completada exitosamente',
                'orden' => $orden->fresh(),
                'producto_stock_actualizado' => $producto->fresh(),
                'unidades_conformes_agregadas' => $simulacion->unidades_conformes,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al completar producción', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Pausar/Reanudar simulación
     */
    public function togglePausa(Request $request): JsonResponse
    {
        $request->validate([
            'simulacion_id' => 'required|exists:simulaciones_maquinas,id',
        ]);

        $simulacion = SimulacionMaquina::findOrFail($request->simulacion_id);

        if ($simulacion->estado_simulacion === 'produciendo') {
            $simulacion->estado_simulacion = 'pausada';
            $message = 'Producción pausada';
        } elseif ($simulacion->estado_simulacion === 'pausada') {
            $simulacion->estado_simulacion = 'produciendo';
            $simulacion->ultimo_ciclo = now(); // Reset para evitar saltos
            $message = 'Producción reanudada';
        } else {
            return response()->json(['message' => 'No se puede pausar/reanudar'], 400);
        }

        $simulacion->save();

        return response()->json([
            'message' => $message,
            'simulacion' => $simulacion,
        ]);
    }

    // ==================== MÉTODOS PRIVADOS ====================

    /**
     * Calcular insumos necesarios desde la formulación
     */
    private function calcularInsumosNecesarios(OrdenProduccion $orden): array
    {
        $producto = $orden->productoTerminado;
        $formulacion = $orden->formulacion;
        $cantidadPlanificada = $orden->cantidad_planificada;

        // Peso total necesario = cantidad × peso_unitario (en kg)
        $pesoTotalKg = ($cantidadPlanificada * $producto->peso_unitario_gramos) / 1000;

        $insumosNecesarios = [];

        foreach ($formulacion->componentes as $componente) {
            $cantidadNecesaria = ($pesoTotalKg * $componente->porcentaje) / 100;

            $insumosNecesarios[] = [
                'insumo_id' => $componente->insumo_id,
                'insumo_nombre' => $componente->insumo->nombre_insumo,
                'porcentaje' => $componente->porcentaje,
                'cantidad_necesaria' => round($cantidadNecesaria, 3),
                'unidad' => $componente->insumo->unidad_medida,
            ];
        }

        return $insumosNecesarios;
    }

    /**
     * Generar temperatura con variación
     */
    private function generarTemperatura(?float $tempBase, float $minVar, float $maxVar): float
    {
        if (!$tempBase) {
            $tempBase = 190; // Temperatura por defecto
        }
        return $tempBase + (rand($minVar * 10, $maxVar * 10) / 10);
    }

    /**
     * Calcular tiempo estimado de finalización
     */
    private function calcularTiempoEstimado(?SimulacionMaquina $simulacion, ?OrdenProduccion $orden): ?string
    {
        if (!$simulacion || !$orden) {
            return null;
        }

        $faltantes = $orden->cantidad_planificada - $simulacion->unidades_producidas;
        if ($faltantes <= 0) {
            return '0 min';
        }

        $tiempoCiclo = $simulacion->tiempo_ciclo_actual ?? 20;
        $piezasPorCiclo = $orden->productoTerminado->piezas_por_ciclo ?? 1;

        $ciclosFaltantes = ceil($faltantes / $piezasPorCiclo);
        $segundosFaltantes = $ciclosFaltantes * $tiempoCiclo;

        $minutos = floor($segundosFaltantes / 60);

        if ($minutos < 60) {
            return "$minutos min";
        } else {
            $horas = floor($minutos / 60);
            $mins = $minutos % 60;
            return "{$horas}h {$mins}min";
        }
    }
}
