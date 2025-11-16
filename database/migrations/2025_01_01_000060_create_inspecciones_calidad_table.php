<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inspecciones_calidad', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_id')->constrained('ordenes_produccion')->onDelete('restrict');
            $table->foreignId('lote_id')->nullable()->constrained('lotes_produccion')->onDelete('set null');
            $table->enum('tipo_inspeccion', ['primera_pieza', 'proceso', 'final', 'auditoria']);
            $table->dateTime('fecha_hora');
            $table->foreignId('inspector_id')->constrained('usuarios')->onDelete('restrict');
            $table->decimal('peso_promedio_gramos', 8, 3)->nullable();
            $table->decimal('desviacion_peso', 6, 3)->nullable();
            $table->decimal('espesor_promedio_micras', 7, 2)->nullable();
            $table->decimal('resistencia_traccion_mpa', 7, 2)->nullable();
            $table->boolean('test_biodegradacion')->nullable();
            $table->integer('dias_compostaje_prueba')->nullable();
            $table->integer('manchas')->default(0);
            $table->integer('deformaciones')->default(0);
            $table->integer('rebabas')->default(0);
            $table->integer('burbujas')->default(0);
            $table->integer('fisuras')->default(0);
            $table->text('otros_defectos')->nullable();
            $table->integer('piezas_inspeccionadas');
            $table->integer('piezas_aprobadas');
            $table->integer('piezas_rechazadas');
            $table->enum('resultado', ['aprobado', 'aprobado_condicional', 'rechazado']);
            $table->text('observaciones')->nullable();
            $table->text('acciones_correctivas')->nullable();
            
            $table->index('orden_id');
            $table->index('lote_id');
            $table->index('fecha_hora');
            $table->index('resultado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspecciones_calidad');
    }
};
