<?php

namespace App\Observers;

use App\Models\RegistroProduccion;
use App\Models\OrdenProduccion;
use App\Models\Alerta;
use App\Events\ProduccionRegistrada;

/**
 * Observer para RegistroProduccion
 *
 * Funciones:
 * - Actualizar orden de producción en tiempo real
 * - Calcular KPIs automáticamente
 * - Detectar anomalías en parámetros de proceso
 * - Generar alertas de calidad
 * - Broadcasting de actualización en tiempo real
 */
class RegistroProduccionObserver
{
    /**
     * Handle the RegistroProduccion "created" event.
     */
    public function created(RegistroProduccion $registro): void
    {
        // 1. Actualizar orden de producción
        $this->actualizarOrdenProduccion($registro);

        // 2. Detectar anomalías en parámetros
        $this->detectarAnomalias($registro);

        // 3. Generar alertas de calidad si es necesario
        $this->verificarCalidad($registro);

        // 4. Broadcast evento en tiempo real
        broadcast(new ProduccionRegistrada($registro))->toOthers();
    }

    /**
     * Actualiza las cantidades de la orden de producción.
     */
    protected function actualizarOrdenProduccion(RegistroProduccion $registro): void
    {
        $orden = $registro->orden;

        if (!$orden) {
            return;
        }

        // Sumar cantidades del registro a la orden
        $orden->cantidad_producida += $registro->piezas_producidas;
        $orden->cantidad_conforme += $registro->piezas_conformes;
        $orden->cantidad_defectuosa += $registro->piezas_defectuosas;

        // Verificar si se completó la orden
        if ($orden->cantidad_producida >= $orden->cantidad_planificada) {
            $orden->estado = 'completada';
            $orden->fecha_fin = now();
        }

        $orden->save();
    }

    /**
     * Detecta anomalías en parámetros de proceso.
     */
    protected function detectarAnomalias(RegistroProduccion $registro): void
    {
        $anomalias = [];

        // Validar temperaturas
        if ($registro->temperatura_zona1 && ($registro->temperatura_zona1 < 150 || $registro->temperatura_zona1 > 200)) {
            $anomalias[] = "Temperatura Zona 1 fuera de rango: {$registro->temperatura_zona1}°C";
        }

        if ($registro->temperatura_zona2 && ($registro->temperatura_zona2 < 160 || $registro->temperatura_zona2 > 210)) {
            $anomalias[] = "Temperatura Zona 2 fuera de rango: {$registro->temperatura_zona2}°C";
        }

        // Validar presión
        if ($registro->presion_inyeccion && $registro->presion_inyeccion > 150) {
            $anomalias[] = "Presión de inyección alta: {$registro->presion_inyeccion} bar";
        }

        // Validar tiempo de ciclo
        if ($registro->tiempo_ciclo_real) {
            $orden = $registro->orden;
            $tiempoCicloEstandar = $orden?->producto?->tiempo_ciclo_segundos;

            if ($tiempoCicloEstandar && $registro->tiempo_ciclo_real > ($tiempoCicloEstandar * 1.2)) {
                $anomalias[] = "Tiempo de ciclo excedido: {$registro->tiempo_ciclo_real}s (estándar: {$tiempoCicloEstandar}s)";
            }
        }

        // Crear alerta si hay anomalías
        if (!empty($anomalias)) {
            Alerta::create([
                'tipo_alerta' => 'parametros_proceso',
                'severidad' => 'advertencia',
                'titulo' => 'Anomalías detectadas en proceso',
                'mensaje' => implode('. ', $anomalias),
                'entidad_tipo' => 'registro_produccion',
                'entidad_id' => $registro->id,
            ]);

            // Marcar flag en el registro
            $registro->alerta_calidad = true;
            $registro->saveQuietly(); // No disparar observer nuevamente
        }
    }

    /**
     * Verifica la calidad y genera alertas si el porcentaje de defectos es alto.
     */
    protected function verificarCalidad(RegistroProduccion $registro): void
    {
        if ($registro->piezas_producidas == 0) {
            return;
        }

        $porcentajeDefectos = ($registro->piezas_defectuosas / $registro->piezas_producidas) * 100;

        // Alerta si defectos > 5%
        if ($porcentajeDefectos > 5) {
            Alerta::create([
                'tipo_alerta' => 'calidad_deficiente',
                'severidad' => $porcentajeDefectos > 10 ? 'critico' : 'advertencia',
                'titulo' => 'Porcentaje de defectos alto',
                'mensaje' => "Registro #{$registro->id}: {$porcentajeDefectos}% de defectos ({$registro->piezas_defectuosas}/{$registro->piezas_producidas}). Máquina: {$registro->maquina->nombre_maquina}",
                'entidad_tipo' => 'registro_produccion',
                'entidad_id' => $registro->id,
                'usuario_destino_id' => $registro->orden?->supervisor_id,
            ]);
        }
    }

    /**
     * Handle the RegistroProduccion "updated" event.
     */
    public function updated(RegistroProduccion $registro): void
    {
        // Revalidar calidad si se modifican cantidades
        if ($registro->wasChanged(['piezas_producidas', 'piezas_defectuosas'])) {
            $this->verificarCalidad($registro);
        }
    }
}
