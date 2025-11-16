<?php

namespace App\Jobs;

use App\Models\LoteProduccion;
use App\Models\Alerta;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job: Verificar Vencimiento de Materiales Biodegradables
 * 
 * Revisa lotes de producción próximos a vencer o vencidos
 * y genera alertas para tomar acción.
 */
class VerificarVencimientoMaterialesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private int $diasAnticipacion;

    /**
     * Create a new job instance.
     */
    public function __construct(int $diasAnticipacion = 30)
    {
        $this->diasAnticipacion = $diasAnticipacion;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Iniciando verificación de vencimientos (anticipación: {$this->diasAnticipacion} días)");

        $alertasGeneradas = 0;

        // 1. Verificar lotes vencidos
        $lotesVencidos = LoteProduccion::where('estado', 'disponible')
            ->where('fecha_vencimiento', '<', now())
            ->get();

        foreach ($lotesVencidos as $lote) {
            $this->generarAlertaVencido($lote);
            $alertasGeneradas++;
        }

        // 2. Verificar lotes próximos a vencer
        $lotesProximos = LoteProduccion::where('estado', 'disponible')
            ->whereBetween('fecha_vencimiento', [now(), now()->addDays($this->diasAnticipacion)])
            ->get();

        foreach ($lotesProximos as $lote) {
            $this->generarAlertaProximoVencer($lote);
            $alertasGeneradas++;
        }

        Log::info("Verificación de vencimientos completada. Alertas generadas: {$alertasGeneradas}");
    }

    /**
     * Generar alerta de lote vencido
     */
    private function generarAlertaVencido(LoteProduccion $lote): void
    {
        // Evitar duplicados
        $alertaExistente = Alerta::where('entidad_tipo', 'LoteProduccion')
            ->where('entidad_id', $lote->id)
            ->where('tipo', 'vencimiento_pasado')
            ->where('estado', 'activa')
            ->exists();

        if ($alertaExistente) {
            return;
        }

        $diasVencido = now()->diffInDays($lote->fecha_vencimiento);

        Alerta::create([
            'tipo' => 'vencimiento_pasado',
            'prioridad' => 'critica',
            'titulo' => 'Lote vencido: ' . $lote->codigo_lote,
            'mensaje' => sprintf(
                'El lote "%s" de producto "%s" venció hace %d días (fecha vencimiento: %s). Cantidad disponible: %d %s. Código certificado: %s.',
                $lote->codigo_lote,
                $lote->productoTerminado->nombre_producto ?? 'Desconocido',
                $diasVencido,
                $lote->fecha_vencimiento->format('d/m/Y'),
                $lote->cantidad_disponible,
                $lote->unidad_medida,
                $lote->numero_certificado ?? 'N/A'
            ),
            'entidad_tipo' => 'LoteProduccion',
            'entidad_id' => $lote->id,
            'accion_recomendada' => 'Rechazar lote inmediatamente y gestionar disposición adecuada del material biodegradable según normas.',
            'estado' => 'activa',
            'leida' => false,
        ]);

        Log::warning("Lote vencido detectado: {$lote->codigo_lote} (ID: {$lote->id})");
    }

    /**
     * Generar alerta de lote próximo a vencer
     */
    private function generarAlertaProximoVencer(LoteProduccion $lote): void
    {
        // Evitar duplicados
        $alertaExistente = Alerta::where('entidad_tipo', 'LoteProduccion')
            ->where('entidad_id', $lote->id)
            ->whereIn('tipo', ['vencimiento_proximo', 'vencimiento_pasado'])
            ->where('estado', 'activa')
            ->exists();

        if ($alertaExistente) {
            return;
        }

        $diasRestantes = now()->diffInDays($lote->fecha_vencimiento);
        $prioridad = $diasRestantes <= 7 ? 'alta' : ($diasRestantes <= 15 ? 'media' : 'baja');

        Alerta::create([
            'tipo' => 'vencimiento_proximo',
            'prioridad' => $prioridad,
            'titulo' => 'Lote próximo a vencer: ' . $lote->codigo_lote,
            'mensaje' => sprintf(
                'El lote "%s" de producto "%s" vencerá en %d días (fecha vencimiento: %s). Cantidad disponible: %d %s. Ubicación: %s.',
                $lote->codigo_lote,
                $lote->productoTerminado->nombre_producto ?? 'Desconocido',
                $diasRestantes,
                $lote->fecha_vencimiento->format('d/m/Y'),
                $lote->cantidad_disponible,
                $lote->unidad_medida,
                $lote->ubicacion_almacen ?? 'No especificada'
            ),
            'entidad_tipo' => 'LoteProduccion',
            'entidad_id' => $lote->id,
            'accion_recomendada' => $diasRestantes <= 7 
                ? 'Priorizar despacho urgente del lote o considerar uso interno inmediato.'
                : 'Planificar despacho prioritario en los próximos días.',
            'estado' => 'activa',
            'leida' => false,
        ]);

        Log::info("Lote próximo a vencer: {$lote->codigo_lote} ({$diasRestantes} días)");
    }
}
