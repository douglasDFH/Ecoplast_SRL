<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Comando de inspiración
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Programación de Jobs automáticos para el sistema de alertas
Schedule::command('ecoplast:verificar-stock')->daily()->at('08:00');
Schedule::command('ecoplast:verificar-vencimientos')->daily()->at('07:00');
Schedule::command('ecoplast:verificar-mantenimientos')->daily()->at('06:00');
Schedule::command('ecoplast:verificar-defectos')->everyThreeHours();
