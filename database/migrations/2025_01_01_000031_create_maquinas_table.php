<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maquinas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_maquina', 50)->unique();
            $table->string('nombre_maquina', 150);
            $table->foreignId('tipo_maquina_id')->constrained('tipos_maquina')->onDelete('restrict');
            $table->string('marca', 100)->nullable();
            $table->string('modelo', 100)->nullable();
            $table->year('año_fabricacion')->nullable();
            $table->decimal('capacidad_produccion', 10, 2)->nullable()->comment('unidades o kg por hora');
            $table->string('unidad_capacidad', 20)->default('unidades/hora');
            $table->decimal('consumo_energia_kwh', 8, 2)->nullable();
            $table->decimal('temp_min_operacion', 5, 1)->nullable()->comment('°C');
            $table->decimal('temp_max_operacion', 5, 1)->nullable()->comment('°C');
            $table->decimal('presion_max_bar', 6, 2)->nullable()->comment('Bar');
            $table->decimal('velocidad_max_rpm', 8, 2)->nullable()->comment('RPM');
            $table->decimal('fuerza_cierre_ton', 8, 2)->nullable()->comment('Toneladas');
            $table->decimal('diametro_husillo_mm', 6, 2)->nullable()->comment('mm');
            $table->date('fecha_instalacion')->nullable();
            $table->integer('vida_util_años')->default(15);
            $table->string('ubicacion', 100)->nullable();
            $table->enum('estado_actual', ['operativa', 'mantenimiento', 'parada', 'averia'])->default('operativa');
            $table->boolean('activo')->default(true);
            $table->timestamps();
            
            $table->index('codigo_maquina');
            $table->index('estado_actual');
            $table->index('activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maquinas');
    }
};
