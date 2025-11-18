<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('simulaciones_maquinas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maquina_id')->constrained('maquinas')->onDelete('cascade');
            $table->foreignId('orden_produccion_id')->nullable()->constrained('ordenes_produccion')->onDelete('cascade');

            // Estado de la simulación
            $table->enum('estado_simulacion', ['idle', 'produciendo', 'pausada', 'completada'])->default('idle');

            // Progreso de producción en tiempo real
            $table->integer('unidades_producidas')->default(0);
            $table->integer('unidades_conformes')->default(0);
            $table->integer('unidades_defectuosas')->default(0);
            $table->decimal('porcentaje_progreso', 5, 2)->default(0);

            // Parámetros de simulación en tiempo real
            $table->decimal('temperatura_zona1', 5, 1)->nullable();
            $table->decimal('temperatura_zona2', 5, 1)->nullable();
            $table->decimal('temperatura_zona3', 5, 1)->nullable();
            $table->decimal('temperatura_zona4', 5, 1)->nullable();
            $table->decimal('presion_actual', 6, 2)->nullable();
            $table->decimal('velocidad_husillo_actual', 7, 2)->nullable();
            $table->decimal('tiempo_ciclo_actual', 6, 2)->nullable()->comment('segundos');
            $table->decimal('consumo_energia_acumulado', 10, 3)->default(0)->comment('kWh');

            // Tasa de defectos (aleatorio entre 2-8%)
            $table->decimal('tasa_defectos', 5, 2)->default(3.0)->comment('Porcentaje');

            // Control de tiempo
            $table->timestamp('inicio_simulacion')->nullable();
            $table->timestamp('fin_simulacion')->nullable();
            $table->integer('tiempo_transcurrido_segundos')->default(0);
            $table->timestamp('ultimo_ciclo')->nullable();

            // Eficiencia y rendimiento
            $table->decimal('eficiencia_actual', 5, 2)->default(85.0);
            $table->integer('ciclos_completados')->default(0);

            $table->timestamps();

            // Índices
            $table->index('maquina_id');
            $table->index('orden_produccion_id');
            $table->index('estado_simulacion');
            $table->unique(['maquina_id', 'orden_produccion_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('simulaciones_maquinas');
    }
};
