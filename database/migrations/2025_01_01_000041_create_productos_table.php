<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_producto', 50)->unique();
            $table->string('nombre_producto', 150);
            $table->foreignId('categoria_producto_id')->constrained('categorias_productos')->onDelete('restrict');
            $table->text('descripcion')->nullable();
            $table->enum('material_principal', ['PLA', 'PHA', 'PBS', 'PBAT', 'Almidon', 'Mixto']);
            $table->string('certificacion_compostable', 200)->nullable();
            $table->integer('tiempo_compostaje_dias')->nullable();
            $table->decimal('capacidad_carga_kg', 8, 2)->nullable();
            $table->decimal('peso_unitario_gramos', 8, 2);
            $table->string('dimensiones', 100)->nullable();
            $table->string('color', 50)->default('natural');
            $table->integer('espesor_micras')->nullable();
            $table->foreignId('formulacion_id')->nullable()->constrained('formulaciones')->onDelete('set null');
            $table->integer('tiempo_ciclo_segundos')->nullable();
            $table->integer('piezas_por_ciclo')->default(1);
            $table->decimal('temperatura_proceso', 5, 1)->nullable();
            $table->decimal('precio_venta', 10, 2);
            $table->enum('unidad_venta', ['unidad', 'paquete', 'caja', 'kg'])->default('unidad');
            $table->integer('unidades_por_paquete')->default(1);
            $table->integer('stock_minimo')->default(0);
            $table->integer('stock_actual')->default(0);
            $table->string('imagen_producto')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            
            $table->index('codigo_producto');
            $table->index('activo');
            $table->index('stock_actual');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
