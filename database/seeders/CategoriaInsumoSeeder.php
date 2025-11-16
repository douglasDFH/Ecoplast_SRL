<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriaInsumoSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoriasInsumos = [
            [
                'nombre_categoria' => 'Polímeros Biodegradables',
                'descripcion' => 'Materiales poliméricos biodegradables como PLA, PHA, PBS, PBAT',
                'es_biodegradable' => true,
            ],
            [
                'nombre_categoria' => 'Almidones y Féculas',
                'descripcion' => 'Almidones termoplásticos y féculas modificadas',
                'es_biodegradable' => true,
            ],
            [
                'nombre_categoria' => 'Aditivos y Compatibilizantes',
                'descripcion' => 'Aditivos para mejorar propiedades de biodegradabilidad y procesabilidad',
                'es_biodegradable' => true,
            ],
            [
                'nombre_categoria' => 'Pigmentos y Colorantes',
                'descripcion' => 'Pigmentos naturales y colorantes biodegradables',
                'es_biodegradable' => true,
            ],
            [
                'nombre_categoria' => 'Cargas y Refuerzos',
                'descripcion' => 'Cargas minerales y refuerzos naturales biodegradables',
                'es_biodegradable' => true,
            ],
            [
                'nombre_categoria' => 'Materias Primas Auxiliares',
                'descripcion' => 'Otros materiales auxiliares para el proceso de producción',
                'es_biodegradable' => false,
            ],
        ];

        DB::table('categorias_insumos')->insert($categoriasInsumos);
    }
}