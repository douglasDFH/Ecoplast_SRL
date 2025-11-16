<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('insumos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_insumo', 50)->unique();
            $table->string('nombre_insumo', 150);
            $table->foreignId('categoria_id')->constrained('categorias_insumos')->onDelete('restrict');
            $table->enum('tipo_material', ['PLA', 'PHA', 'PBS', 'PBAT', 'Almidon', 'Celulosa', 'Aditivo', 'Pigmento', 'Otro']);
            $table->enum('unidad_medida', ['kg', 'ton', 'litro', 'unidad'])->default('kg');
            $table->decimal('densidad', 6, 3)->nullable()->comment('g/cm³');
            $table->decimal('temperatura_fusion', 5, 1)->nullable()->comment('°C');
            $table->string('certificacion_biodegradable', 100)->nullable();
            $table->string('proveedor', 150)->nullable();
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('stock_minimo', 10, 2);
            $table->decimal('stock_actual', 10, 2)->default(0);
            $table->date('fecha_caducidad_lote')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            
            $table->index('codigo_insumo');
            $table->index('activo');
            $table->index('stock_actual');
            $table->index('tipo_material');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insumos');
    }
};
