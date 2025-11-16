<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CategoriaProducto;
use App\Models\ProductoTerminado;
use App\Models\Maquinaria;
use App\Models\Turno;
use App\Models\OrdenProduccion;
use App\Models\RegistroProduccion;
use App\Models\User;

class DemoFlujoProduccionSeeder extends Seeder
{
    public function run()
    {
        // 1. Categoría y producto
        $categoria = CategoriaProducto::firstOrCreate([
            'nombre' => 'Bolsas',
        ], [
            'descripcion' => 'Bolsas plásticas de ejemplo',
        ]);

        $producto = ProductoTerminado::firstOrCreate([
            'nombre' => 'Bolsa camiseta 30x40',
            'categoria_id' => $categoria->id,
        ], [
            'stock' => 1000,
            'unidad' => 'paquete',
        ]);

        // 2. Máquina y turno
        $maquina = Maquinaria::firstOrCreate([
            'nombre_maquina' => 'Extrusora 1',
            'codigo_maquina' => 'EXT-001',
        ]);

        $turno = Turno::firstOrCreate([
            'nombre_turno' => 'Mañana',
            'hora_inicio' => '06:00',
            'hora_fin' => '14:00',
        ]);

        // 3. Usuario operador
        $operador = User::firstOrCreate([
            'email' => 'operador@demo.com',
        ], [
            'name' => 'Operador Demo',
            'password' => bcrypt('password'),
        ]);

        // 4. Crear orden de producción
        $orden = OrdenProduccion::create([
            'producto_id' => $producto->id,
            'cantidad_requerida' => 5000,
            'maquina_id' => $maquina->id,
            'turno_id' => $turno->id,
            'prioridad' => 'alta',
            'fecha_programada' => now()->toDateString(),
            'estado' => 'en_proceso',
            'operador_id' => $operador->id,
        ]);

        // 5. Registrar una producción para esa orden
        RegistroProduccion::create([
            'orden_produccion_id' => $orden->id,
            'usuario_id' => $operador->id,
            'cantidad_producida' => 1200,
            'fecha_hora' => now(),
            'turno_id' => $turno->id,
            'maquina_id' => $maquina->id,
        ]);
    }
}
