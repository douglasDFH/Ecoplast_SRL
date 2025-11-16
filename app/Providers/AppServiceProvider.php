<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\OrdenProduccion;
use App\Observers\OrdenProduccionObserver;
use App\Models\Insumo;
use App\Observers\InsumoObserver;
use App\Models\RegistroProduccion;
use App\Observers\RegistroProduccionObserver;
use App\Models\Alerta;
use App\Observers\AlertaObserver;

/**
 * AppServiceProvider
 *
 * Registra observers para modelos críticos de Ecoplast SRL
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Observers de modelos críticos
        OrdenProduccion::observe(OrdenProduccionObserver::class);
        Insumo::observe(InsumoObserver::class);
        RegistroProduccion::observe(RegistroProduccionObserver::class);
        Alerta::observe(AlertaObserver::class);
    }
}
