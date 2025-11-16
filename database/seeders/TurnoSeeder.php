<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TurnoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('turnos')->insert([
            [
                'nombre_turno' => 'Matutino',
                'hora_inicio' => '06:00:00',
                'hora_fin' => '14:00:00',
                'activo' => true,
            ],
            [
                'nombre_turno' => 'Vespertino',
                'hora_inicio' => '14:00:00',
                'hora_fin' => '22:00:00',
                'activo' => true,
            ],
            [
                'nombre_turno' => 'Nocturno',
                'hora_inicio' => '22:00:00',
                'hora_fin' => '06:00:00',
                'activo' => true,
            ],
        ]);
    }
}
