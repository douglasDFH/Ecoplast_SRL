<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriaProductoSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoriasProductos = [
            [
                'nombre_categoria' => 'Envases y Embalajes',
                'descripcion' => 'Envases biodegradables para alimentos y productos',
                'aplicacion' => 'envases',
            ],
            [
                'nombre_categoria' => 'Bolsas y Sacos',
                'descripcion' => 'Bolsas biodegradables para residuos y productos',
                'aplicacion' => 'bolsas',
            ],
            [
                'nombre_categoria' => 'Utensilios Desechables',
                'descripcion' => 'Platos, vasos y utensilios biodegradables',
                'aplicacion' => 'utensilios',
            ],
            [
                'nombre_categoria' => 'Productos Agrícolas',
                'descripcion' => 'Macetas, tutores y productos para agricultura',
                'aplicacion' => 'agricola',
            ],
            [
                'nombre_categoria' => 'Productos Industriales',
                'descripcion' => 'Componentes y piezas industriales biodegradables',
                'aplicacion' => 'industrial',
            ],
            [
                'nombre_categoria' => 'Productos Médicos',
                'descripcion' => 'Dispositivos médicos biodegradables',
                'aplicacion' => 'medico',
            ],
        ];

        DB::table('categorias_productos')->insert($categoriasProductos);
    }
}