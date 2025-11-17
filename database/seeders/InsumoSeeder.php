<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InsumoSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener IDs de los tipos de materiales
        $tiposPLA = DB::table('tipos_materiales')->where('codigo', 'PLA')->first();
        $tiposPHA = DB::table('tipos_materiales')->where('codigo', 'PHA')->first();
        $tiposPBS = DB::table('tipos_materiales')->where('codigo', 'PBS')->first();
        $tiposPBAT = DB::table('tipos_materiales')->where('codigo', 'PBAT')->first();
        $tiposAlmidon = DB::table('tipos_materiales')->where('codigo', 'ALMIDON')->first();

        $insumos = [
            [
                'codigo_insumo' => 'PLA-001',
                'nombre_insumo' => 'PLA Ingeo 4043D',
                'categoria_id' => 1,
                'tipo_material' => 'PLA', // Mantener temporalmente para compatibilidad
                'tipo_material_id' => $tiposPLA->id,
                'unidad_medida' => 'kg',
                'densidad' => 1.24,
                'temperatura_fusion' => 170.0,
                'certificacion_biodegradable' => 'ASTM D6400',
                'proveedor' => 'NatureWorks',
                'precio_unitario' => 3.50,
                'stock_minimo' => 500.00,
                'stock_actual' => 2500.00,
                'activo' => true,
            ],
            [
                'codigo_insumo' => 'PHA-001',
                'nombre_insumo' => 'PHA Mirel P1001',
                'categoria_id' => 1,
                'tipo_material' => 'PHA', // Mantener temporalmente para compatibilidad
                'tipo_material_id' => $tiposPHA->id,
                'unidad_medida' => 'kg',
                'densidad' => 1.25,
                'temperatura_fusion' => 160.0,
                'certificacion_biodegradable' => 'ASTM D6400',
                'proveedor' => 'Metabolix',
                'precio_unitario' => 4.20,
                'stock_minimo' => 300.00,
                'stock_actual' => 1800.00,
                'activo' => true,
            ],
            [
                'codigo_insumo' => 'PBS-001',
                'nombre_insumo' => 'PBS Bionolle 1020',
                'categoria_id' => 1,
                'tipo_material' => 'PBS', // Mantener temporalmente para compatibilidad
                'tipo_material_id' => $tiposPBS->id,
                'unidad_medida' => 'kg',
                'densidad' => 1.26,
                'temperatura_fusion' => 114.0,
                'certificacion_biodegradable' => 'EN 13432',
                'proveedor' => 'Showa Denko',
                'precio_unitario' => 2.80,
                'stock_minimo' => 600.00,
                'stock_actual' => 3200.00,
                'activo' => true,
            ],
            [
                'codigo_insumo' => 'PBAT-001',
                'nombre_insumo' => 'PBAT Ecoflex F Blend C1200',
                'categoria_id' => 1,
                'tipo_material' => 'PBAT', // Mantener temporalmente para compatibilidad
                'tipo_material_id' => $tiposPBAT->id,
                'unidad_medida' => 'kg',
                'densidad' => 1.26,
                'temperatura_fusion' => 110.0,
                'certificacion_biodegradable' => 'ASTM D6400',
                'proveedor' => 'BASF',
                'precio_unitario' => 3.10,
                'stock_minimo' => 400.00,
                'stock_actual' => 2100.00,
                'activo' => true,
            ],
            [
                'codigo_insumo' => 'TPS-001',
                'nombre_insumo' => 'Almidón de Maíz Termoplástico',
                'categoria_id' => 2,
                'tipo_material' => 'Almidon', // Mantener temporalmente para compatibilidad
                'tipo_material_id' => $tiposAlmidon->id,
                'unidad_medida' => 'kg',
                'densidad' => 1.35,
                'temperatura_fusion' => 140.0,
                'certificacion_biodegradable' => 'ASTM D6400',
                'proveedor' => 'Local Supplier',
                'precio_unitario' => 1.50,
                'stock_minimo' => 250.00,
                'stock_actual' => 1500.00,
                'activo' => true,
            ],
        ];

        DB::table('insumos')->insert($insumos);
    }
}