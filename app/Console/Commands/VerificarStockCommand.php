<?php

namespace App\Console\Commands;

use App\Jobs\VerificarStockMinimoJob;
use Illuminate\Console\Command;

class VerificarStockCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ecoplast:verificar-stock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica los niveles de stock de insumos y genera alertas si están por debajo del mínimo';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Iniciando verificación de stock de insumos...');

        VerificarStockMinimoJob::dispatch();

        $this->info('✓ Job de verificación de stock despachado correctamente');

        return Command::SUCCESS;
    }
}
