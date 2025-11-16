<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoMaquinariaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tipos_maquina')->insert([
            [
                'nombre_tipo' => 'Extrusora',
                'descripcion' => 'Máquina de extrusión para films y láminas de bioplásticos',
            ],
            [
                'nombre_tipo' => 'Inyectora',
                'descripcion' => 'Máquina de inyección para envases y tapas biodegradables',
            ],
            [
                'nombre_tipo' => 'Sopladora',
                'descripcion' => 'Máquina de soplado para botellas y contenedores biodegradables',
            ],
            [
                'nombre_tipo' => 'Termoformadora',
                'descripcion' => 'Equipo de termoformado para bandejas y vasos biodegradables',
            ],
            [
                'nombre_tipo' => 'Granuladora',
                'descripcion' => 'Equipo de granulación para reciclaje de material biodegradable',
            ],
        ]);
    }
}
