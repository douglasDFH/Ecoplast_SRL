<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paros_maquina', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maquina_id')->constrained('maquinas')->onDelete('restrict');
            $table->enum('tipo_paro', ['averia', 'mantenimiento', 'cambio_molde', 'falta_material', 'falta_personal', 'ajuste_calidad', 'otros']);
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_fin')->nullable();
            $table->integer('duracion_minutos')->nullable();
            $table->text('descripcion')->nullable();
            $table->text('causa_raiz')->nullable();
            $table->text('accion_correctiva')->nullable();
            $table->foreignId('operador_id')->constrained('usuarios')->onDelete('restrict');
            $table->decimal('impacto_produccion', 10, 2)->nullable()->comment('unidades no producidas');
            $table->decimal('costo_estimado', 10, 2)->nullable();
            
            $table->index('maquina_id');
            $table->index('fecha_inicio');
            $table->index('tipo_paro');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paros_maquina');
    }
};
