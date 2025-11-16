<?php

namespace App\Console\Commands;

use App\Jobs\VerificarMantenimientosJob;
use Illuminate\Console\Command;

class VerificarMantenimientosCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ecoplast:verificar-mantenimientos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica mantenimientos pendientes y atrasados, genera alertas';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Iniciando verificación de mantenimientos...');

        VerificarMantenimientosJob::dispatch();

        $this->info('✓ Job de verificación de mantenimientos despachado correctamente');

        return Command::SUCCESS;
    }
}
