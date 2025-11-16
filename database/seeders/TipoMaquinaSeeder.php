<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoMaquinaSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tiposMaquinas = [
            [
                'nombre_tipo' => 'Máquina de Inyección',
                'descripcion' => 'Máquina para moldeo por inyección de plásticos biodegradables',
            ],
            [
                'nombre_tipo' => 'Extrusora',
                'descripcion' => 'Máquina para extrusión de plásticos biodegradables',
            ],
            [
                'nombre_tipo' => 'Máquina de Soplo',
                'descripcion' => 'Máquina para moldeo por soplado de botellas y envases',
            ],
            [
                'nombre_tipo' => 'Termoformadora',
                'descripcion' => 'Máquina para termoformado de láminas biodegradables',
            ],
            [
                'nombre_tipo' => 'Granulador',
                'descripcion' => 'Máquina para granulación de materiales biodegradables',
            ],
            [
                'nombre_tipo' => 'Mezclador',
                'descripcion' => 'Mezclador para preparación de formulaciones biodegradables',
            ],
        ];

        DB::table('tipos_maquina')->insert($tiposMaquinas);
    }
}