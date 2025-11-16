<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kpis_mensuales', function (Blueprint $table) {
            $table->id();
            $table->year('año');
            $table->tinyInteger('mes');
            $table->foreignId('maquina_id')->nullable()->constrained('maquinas')->onDelete('set null');
            $table->bigInteger('total_unidades_producidas');
            $table->bigInteger('total_unidades_conformes');
            $table->decimal('total_scrap_kg', 12, 2)->default(0);
            $table->decimal('oee_promedio', 5, 2);
            $table->decimal('disponibilidad_promedio', 5, 2);
            $table->decimal('rendimiento_promedio', 5, 2);
            $table->decimal('calidad_promedio', 5, 2);
            $table->decimal('mtbf', 10, 2)->nullable()->comment('horas');
            $table->decimal('mttr', 10, 2)->nullable()->comment('horas');
            $table->integer('numero_paros')->default(0);
            $table->decimal('tiempo_total_paros_horas', 10, 2)->default(0);
            $table->decimal('costo_total_produccion', 15, 2)->nullable();
            $table->decimal('costo_unitario', 10, 4)->nullable();
            $table->decimal('costo_energia', 12, 2)->nullable();
            $table->decimal('costo_material', 12, 2)->nullable();
            $table->decimal('costo_mantenimiento', 12, 2)->nullable();
            $table->decimal('porcentaje_material_biodegradable', 5, 2)->nullable();
            $table->decimal('cumplimiento_certificaciones', 5, 2)->nullable();
            $table->timestamp('calculado_en')->useCurrent();
            
            $table->unique(['año', 'mes', 'maquina_id']);
            $table->index(['año', 'mes']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kpis_mensuales');
    }
};
