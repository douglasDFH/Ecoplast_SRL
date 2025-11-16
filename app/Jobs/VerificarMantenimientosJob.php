<?php

namespace App\Jobs;

use App\Models\Mantenimiento;
use App\Models\Maquinaria;
use App\Models\Alerta;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job: Verificar Mantenimientos Pendientes
 * 
 * Revisa mantenimientos atrasados o próximos y genera alertas.
 */
class VerificarMantenimientosJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Iniciando verificación de mantenimientos pendientes');

        $alertasGeneradas = 0;

        // 1. Mantenimientos atrasados
        $atrasados = Mantenimiento::where('estado', 'programado')
            ->where('fecha_programada', '<', now())
            ->get();

        foreach ($atrasados as $mantenimiento) {
            $this->generarAlertaAtrasado($mantenimiento);
            $alertasGeneradas++;
        }

        // 2. Mantenimientos críticos próximos (7 días)
        $proximos = Mantenimiento::where('estado', 'programado')
            ->where('prioridad', 'critica')
            ->whereBetween('fecha_programada', [now(), now()->addDays(7)])
            ->get();

        foreach ($proximos as $mantenimiento) {
            $this->generarAlertaProximo($mantenimiento);
            $alertasGeneradas++;
        }

        // 3. Máquinas paradas por mucho tiempo
        $this->verificarMaquinasParadas();

        Log::info("Verificación de mantenimientos completada. Alertas generadas: {$alertasGeneradas}");
    }

    private function generarAlertaAtrasado(Mantenimiento $mantenimiento): void
    {
        $alertaExistente = Alerta::where('entidad_tipo', 'Mantenimiento')
            ->where('entidad_id', $mantenimiento->id)
            ->where('tipo', 'mantenimiento_atrasado')
            ->where('estado', 'activa')
            ->exists();

        if ($alertaExistente) {
            return;
        }

        $diasAtraso = now()->diffInDays($mantenimiento->fecha_programada);

        Alerta::create([
            'tipo' => 'mantenimiento_atrasado',
            'prioridad' => $mantenimiento->prioridad === 'critica' ? 'critica' : 'alta',
            'titulo' => 'Mantenimiento atrasado: ' . ($mantenimiento->maquina->nombre_maquina ?? 'Sin máquina'),
            'mensaje' => sprintf(
                'El mantenimiento %s de "%s" está atrasado por %d días. Programado para: %s. Descripción: %s',
                $mantenimiento->tipo,
                $mantenimiento->maquina->nombre_maquina ?? 'Sin máquina',
                $diasAtraso,
                $mantenimiento->fecha_programada->format('d/m/Y'),
                $mantenimiento->descripcion
            ),
            'entidad_tipo' => 'Mantenimiento',
            'entidad_id' => $mantenimiento->id,
            'accion_recomendada' => 'Reprogramar y ejecutar mantenimiento urgentemente. Contactar técnico: ' . ($mantenimiento->tecnico->name ?? 'No asignado'),
            'estado' => 'activa',
            'leida' => false,
        ]);
    }

    private function generarAlertaProximo(Mantenimiento $mantenimiento): void
    {
        $alertaExistente = Alerta::where('entidad_tipo', 'Mantenimiento')
            ->where('entidad_id', $mantenimiento->id)
            ->where('tipo', 'mantenimiento_pendiente')
            ->where('estado', 'activa')
            ->exists();

        if ($alertaExistente) {
            return;
        }

        $diasRestantes = now()->diffInDays($mantenimiento->fecha_programada);

        Alerta::create([
            'tipo' => 'mantenimiento_pendiente',
            'prioridad' => 'alta',
            'titulo' => 'Mantenimiento crítico próximo: ' . ($mantenimiento->maquina->nombre_maquina ?? 'Sin máquina'),
            'mensaje' => sprintf(
                'Mantenimiento crítico programado en %d días para "%s". Fecha: %s. Duración estimada: %.1f horas.',
                $diasRestantes,
                $mantenimiento->maquina->nombre_maquina ?? 'Sin máquina',
                $mantenimiento->fecha_programada->format('d/m/Y H:i'),
                $mantenimiento->duracion_estimada_horas
            ),
            'entidad_tipo' => 'Mantenimiento',
            'entidad_id' => $mantenimiento->id,
            'accion_recomendada' => 'Confirmar disponibilidad de repuestos y técnico. Planificar producción alternativa.',
            'estado' => 'activa',
            'leida' => false,
        ]);
    }

    private function verificarMaquinasParadas(): void
    {
        $maquinasParadas = Maquinaria::where('estado_actual', 'parada')
            ->orWhere('estado_actual', 'averia')
            ->get();

        foreach ($maquinasParadas as $maquina) {
            // Verificar si ya tiene alerta activa
            $alertaExistente = Alerta::where('entidad_tipo', 'Maquinaria')
                ->where('entidad_id', $maquina->id)
                ->where('tipo', 'maquina_parada')
                ->where('estado', 'activa')
                ->exists();

            if (!$alertaExistente) {
                Alerta::create([
                    'tipo' => 'maquina_parada',
                    'prioridad' => $maquina->estado_actual === 'averia' ? 'critica' : 'alta',
                    'titulo' => 'Máquina parada: ' . $maquina->nombre_maquina,
                    'mensaje' => sprintf(
                        'La máquina "%s" (código: %s) está en estado "%s". Ubicación: %s',
                        $maquina->nombre_maquina,
                        $maquina->codigo_maquina,
                        $maquina->estado_actual,
                        $maquina->ubicacion ?? 'No especificada'
                    ),
                    'entidad_tipo' => 'Maquinaria',
                    'entidad_id' => $maquina->id,
                    'accion_recomendada' => 'Revisar causa de la parada y programar reparación urgente.',
                    'estado' => 'activa',
                    'leida' => false,
                ]);
            }
        }
    }
}
