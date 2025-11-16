<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registros_produccion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_id')->constrained('ordenes_produccion')->onDelete('restrict');
            $table->foreignId('maquina_id')->constrained('maquinas')->onDelete('restrict');
            $table->foreignId('operador_id')->constrained('usuarios')->onDelete('restrict');
            $table->dateTime('fecha_hora');
            $table->integer('piezas_producidas')->default(0);
            $table->integer('piezas_conformes')->default(0);
            $table->integer('piezas_defectuosas')->default(0);
            $table->string('tipo_defecto', 100)->nullable();
            $table->decimal('temperatura_zona1', 5, 1)->nullable()->comment('°C');
            $table->decimal('temperatura_zona2', 5, 1)->nullable()->comment('°C');
            $table->decimal('temperatura_zona3', 5, 1)->nullable()->comment('°C');
            $table->decimal('temperatura_zona4', 5, 1)->nullable()->comment('°C');
            $table->decimal('presion_inyeccion', 6, 2)->nullable()->comment('Bar');
            $table->decimal('velocidad_husillo', 7, 2)->nullable()->comment('RPM');
            $table->decimal('tiempo_ciclo_real', 6, 2)->nullable()->comment('segundos');
            $table->decimal('consumo_energia_kwh', 8, 3)->nullable();
            $table->decimal('consumo_material_kg', 10, 3)->nullable();
            $table->decimal('scrap_kg', 10, 3)->nullable();
            $table->text('observaciones')->nullable();
            $table->boolean('alerta_calidad')->default(false);
            
            $table->index('fecha_hora');
            $table->index(['orden_id', 'maquina_id']);
            $table->index('alerta_calidad');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registros_produccion');
    }
};
