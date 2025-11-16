<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\OrdenProduccion;
use App\Models\Alerta;
use Carbon\Carbon;

class VerificarRetrasoOrdenProduccion implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * La orden de producción a verificar.
     *
     * @var \App\Models\OrdenProduccion
     */
    protected $ordenProduccion;

    /**
     * Create a new job instance.
     */
    public function __construct(OrdenProduccion $ordenProduccion)
    {
        $this->ordenProduccion = $ordenProduccion;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Recargar la orden para obtener el estado más reciente
        $this->ordenProduccion->refresh();

        // Verificar si la orden sigue 'Pendiente' y no ha iniciado en la fecha planificada
        if ($this->ordenProduccion->estado === 'Pendiente' && is_null($this->ordenProduccion->fecha_inicio_real)) {
            
            // Evitar duplicados: buscar si ya existe una alerta de retraso no leída para esta orden
            $alertaExistente = Alerta::where('relacion_tipo', OrdenProduccion::class)
                ->where('relacion_id', $this->ordenProduccion->id)
                ->where('tipo_alerta', 'Produccion Retrasada')
                ->where('leida', false)
                ->exists();

            if (!$alertaExistente) {
                Alerta::create([
                    'tipo_alerta' => 'Produccion Retrasada',
                    'mensaje' => "La orden de producción #{$this->ordenProduccion->id} no ha iniciado. Fecha planificada: {$this->ordenProduccion->fecha_inicio_planificada->format('d/m/Y H:i')}.",
                    'nivel_urgencia' => 'Medio',
                    'relacion_id' => $this->ordenProduccion->id,
                    'relacion_tipo' => OrdenProduccion::class,
                    // Opcional: Asignar a un supervisor o gerente
                    'usuario_destino_id' => $this->ordenProduccion->supervisor_id,
                ]);
            }
        }
    }
}
