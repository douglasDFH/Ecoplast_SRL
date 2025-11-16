<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductoTerminadoSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productos = [
            [
                'codigo_producto' => 'ENV-PLA-001',
                'nombre_producto' => 'Bolsa de Compras Biodegradable PLA',
                'categoria_producto_id' => 1, // Envases y Embalajes
                'descripcion' => 'Bolsa de compras fabricada con PLA biodegradable, resistente y compostable',
                'material_principal' => 'PLA',
                'certificacion_compostable' => 'EN 13432',
                'tiempo_compostaje_dias' => 90,
                'peso_unitario_gramos' => 25.0,
                'temperatura_proceso' => 180.0,
                'precio_venta' => 0.15,
                'unidad_venta' => 'unidad',
                'stock_actual' => 5000,
                'stock_minimo' => 1000,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo_producto' => 'ENV-PBAT-002',
                'nombre_producto' => 'Film Estirable Biodegradable PBAT',
                'categoria_producto_id' => 1, // Envases y Embalajes
                'descripcion' => 'Film estirable biodegradable para embalaje de productos alimenticios',
                'material_principal' => 'PBAT',
                'certificacion_compostable' => 'ASTM D6400',
                'tiempo_compostaje_dias' => 180,
                'peso_unitario_gramos' => 50.0,
                'temperatura_proceso' => 125.0,
                'precio_venta' => 0.25,
                'unidad_venta' => 'kg',
                'stock_actual' => 3000,
                'stock_minimo' => 500,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo_producto' => 'UTENSILIOS-PLA-003',
                'nombre_producto' => 'Plato Desechable Biodegradable PLA',
                'categoria_producto_id' => 3, // Utensilios Desechables
                'descripcion' => 'Plato desechable biodegradable ideal para catering y eventos',
                'material_principal' => 'PLA',
                'certificacion_compostable' => 'EN 13432',
                'tiempo_compostaje_dias' => 60,
                'peso_unitario_gramos' => 15.0,
                'temperatura_proceso' => 180.0,
                'precio_venta' => 0.10,
                'unidad_venta' => 'unidad',
                'stock_actual' => 10000,
                'stock_minimo' => 2000,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo_producto' => 'AGRICOLA-PBS-004',
                'nombre_producto' => 'Maceta Biodegradable PBS 15cm',
                'categoria_producto_id' => 4, // Productos Agrícolas
                'descripcion' => 'Maceta biodegradable para cultivo de plantas, se descompone en el suelo',
                'material_principal' => 'PBS',
                'certificacion_compostable' => 'EN 13432',
                'tiempo_compostaje_dias' => 120,
                'peso_unitario_gramos' => 200.0,
                'temperatura_proceso' => 120.0,
                'precio_venta' => 0.80,
                'unidad_venta' => 'unidad',
                'stock_actual' => 2000,
                'stock_minimo' => 300,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo_producto' => 'INDUSTRIAL-PHA-005',
                'nombre_producto' => 'Contenedor Industrial Biodegradable PHA',
                'categoria_producto_id' => 5, // Productos Industriales
                'descripcion' => 'Contenedor industrial biodegradable para residuos no peligrosos',
                'material_principal' => 'PHA',
                'certificacion_compostable' => 'ASTM D6400',
                'tiempo_compostaje_dias' => 150,
                'peso_unitario_gramos' => 1500.0,
                'temperatura_proceso' => 170.0,
                'precio_venta' => 5.50,
                'unidad_venta' => 'unidad',
                'stock_actual' => 500,
                'stock_minimo' => 100,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('productos')->insert($productos);
    }
}