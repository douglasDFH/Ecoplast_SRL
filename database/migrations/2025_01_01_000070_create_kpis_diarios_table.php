<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kpis_diarios', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->foreignId('maquina_id')->constrained('maquinas')->onDelete('restrict');
            $table->foreignId('turno_id')->constrained('turnos')->onDelete('restrict');
            $table->integer('unidades_planificadas');
            $table->integer('unidades_producidas');
            $table->integer('unidades_conformes');
            $table->integer('unidades_defectuosas');
            $table->decimal('scrap_kg', 10, 2)->default(0);
            $table->integer('tiempo_planificado')->comment('minutos');
            $table->integer('tiempo_operacion')->comment('minutos');
            $table->integer('tiempo_paradas')->comment('minutos');
            $table->integer('tiempo_setup')->default(0)->comment('minutos');
            $table->decimal('disponibilidad', 5, 2)->comment('Tiempo operación / Tiempo planificado * 100');
            $table->decimal('rendimiento', 5, 2)->comment('Producción real / Producción teórica * 100');
            $table->decimal('calidad', 5, 2)->comment('Piezas conformes / Piezas producidas * 100');
            $table->decimal('oee', 5, 2)->comment('Disponibilidad * Rendimiento * Calidad / 100');
            $table->decimal('consumo_energia_kwh', 10, 2)->default(0);
            $table->decimal('consumo_material_kg', 10, 2)->default(0);
            $table->decimal('eficiencia_material', 5, 2)->nullable()->comment('%');
            $table->decimal('costo_produccion', 12, 2)->nullable();
            $table->decimal('tasa_defectos', 5, 2)->comment('PPM o %');
            $table->decimal('first_pass_yield', 5, 2)->nullable();
            $table->timestamp('calculado_en')->useCurrent();
            
            $table->unique(['fecha', 'maquina_id', 'turno_id']);
            $table->index('fecha');
            $table->index('maquina_id');
            $table->index('oee');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kpis_diarios');
    }
};
