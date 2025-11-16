<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MaquinariaSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $maquinas = [
            [
                'codigo_maquina' => 'INYECCION-001',
                'nombre_maquina' => 'Máquina de Inyección Arburg 320C',
                'tipo_maquina_id' => 1, // Máquina de Inyección
                'marca' => 'Arburg',
                'modelo' => '320C',
                'año_fabricacion' => 2022,
                'capacidad_produccion' => 150.00, // kg/hora
                'consumo_energia_kwh' => 25.0,
                'temp_min_operacion' => 150,
                'temp_max_operacion' => 300,
                'presion_max_bar' => 2000,
                'velocidad_max_rpm' => 300,
                'fecha_instalacion' => '2023-01-15',
                'vida_util_años' => 15,
                'ubicacion' => 'Planta Principal - Línea 1',
                'estado_actual' => 'operativa',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo_maquina' => 'EXTRUSION-002',
                'nombre_maquina' => 'Extrusora KraussMaffei KMD 75-36',
                'tipo_maquina_id' => 2, // Extrusora
                'marca' => 'KraussMaffei',
                'modelo' => 'KMD 75-36',
                'año_fabricacion' => 2021,
                'capacidad_produccion' => 200.00, // kg/hora
                'consumo_energia_kwh' => 35.0,
                'temp_min_operacion' => 140,
                'temp_max_operacion' => 250,
                'presion_max_bar' => 150,
                'velocidad_max_rpm' => 400,
                'fecha_instalacion' => '2023-03-20',
                'vida_util_años' => 12,
                'ubicacion' => 'Planta Principal - Línea 2',
                'estado_actual' => 'operativa',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo_maquina' => 'SOPLADO-003',
                'nombre_maquina' => 'Máquina de Soplo Sidel SBO 24',
                'tipo_maquina_id' => 3, // Máquina de Soplo
                'marca' => 'Sidel',
                'modelo' => 'SBO 24',
                'año_fabricacion' => 2020,
                'capacidad_produccion' => 18000, // botellas/hora
                'consumo_energia_kwh' => 45.0,
                'temp_min_operacion' => 160,
                'temp_max_operacion' => 220,
                'presion_max_bar' => 40,
                'velocidad_max_rpm' => 2000,
                'fecha_instalacion' => '2023-05-10',
                'vida_util_años' => 10,
                'ubicacion' => 'Planta Principal - Línea 3',
                'estado_actual' => 'mantenimiento',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo_maquina' => 'TERMOFORMADO-004',
                'nombre_maquina' => 'Termoformadora Illig RDM 54K',
                'tipo_maquina_id' => 4, // Termoformadora
                'marca' => 'Illig',
                'modelo' => 'RDM 54K',
                'año_fabricacion' => 2023,
                'capacidad_produccion' => 12000, // unidades/hora
                'consumo_energia_kwh' => 30.0,
                'temp_min_operacion' => 120,
                'temp_max_operacion' => 180,
                'presion_max_bar' => 8,
                'velocidad_max_rpm' => 150,
                'fecha_instalacion' => '2023-07-15',
                'vida_util_años' => 14,
                'ubicacion' => 'Planta Principal - Línea 4',
                'estado_actual' => 'operativa',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo_maquina' => 'GRANULADOR-005',
                'nombre_maquina' => 'Granulador Rapid 150',
                'tipo_maquina_id' => 5, // Granulador
                'marca' => 'Rapid',
                'modelo' => '150',
                'año_fabricacion' => 2019,
                'capacidad_produccion' => 250.00, // kg/hora
                'consumo_energia_kwh' => 20.0,
                'temp_min_operacion' => 80,
                'temp_max_operacion' => 150,
                'presion_max_bar' => 2,
                'velocidad_max_rpm' => 800,
                'fecha_instalacion' => '2023-09-01',
                'vida_util_años' => 8,
                'ubicacion' => 'Área de Reciclaje',
                'estado_actual' => 'parada',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('maquinas')->insert($maquinas);
    }
}