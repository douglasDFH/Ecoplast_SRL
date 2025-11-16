<?php

namespace App\Console\Commands;

use App\Jobs\VerificarVencimientoMaterialesJob;
use Illuminate\Console\Command;

class VerificarVencimientosCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ecoplast:verificar-vencimientos {--dias=30 : Días de anticipación para alertas}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica fechas de vencimiento de lotes de producción y genera alertas';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dias = (int) $this->option('dias');

        $this->info("Iniciando verificación de vencimientos (anticipación: {$dias} días)...");

        VerificarVencimientoMaterialesJob::dispatch($dias);

        $this->info('✓ Job de verificación de vencimientos despachado correctamente');

        return Command::SUCCESS;
    }
}
