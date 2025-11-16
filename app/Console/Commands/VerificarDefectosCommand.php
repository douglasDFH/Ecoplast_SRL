<?php

namespace App\Console\Commands;

use App\Jobs\VerificarDefectosCalidadJob;
use Illuminate\Console\Command;

class VerificarDefectosCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ecoplast:verificar-defectos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica inspecciones de calidad y genera alertas sobre defectos';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Iniciando verificación de defectos de calidad...');

        VerificarDefectosCalidadJob::dispatch();

        $this->info('✓ Job de verificación de defectos despachado correctamente');

        return Command::SUCCESS;
    }
}
