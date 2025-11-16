<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ordenes_produccion', function (Blueprint $table) {
            $table->id();
            $table->string('numero_orden', 50)->unique();
            $table->foreignId('producto_id')->constrained('productos')->onDelete('restrict');
            $table->integer('cantidad_planificada');
            $table->integer('cantidad_producida')->default(0);
            $table->integer('cantidad_conforme')->default(0);
            $table->integer('cantidad_defectuosa')->default(0);
            $table->foreignId('formulacion_id')->constrained('formulaciones')->onDelete('restrict');
            $table->foreignId('maquina_id')->constrained('maquinas')->onDelete('restrict');
            $table->foreignId('turno_id')->constrained('turnos')->onDelete('restrict');
            $table->dateTime('fecha_programada');
            $table->dateTime('fecha_inicio')->nullable();
            $table->dateTime('fecha_fin')->nullable();
            $table->foreignId('operador_id')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->foreignId('supervisor_id')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->enum('estado', ['pendiente', 'en_proceso', 'pausada', 'completada', 'cancelada'])->default('pendiente');
            $table->enum('prioridad', ['baja', 'normal', 'alta', 'urgente'])->default('normal');
            $table->text('notas_produccion')->nullable();
            $table->text('observaciones_calidad')->nullable();
            $table->foreignId('creado_por')->constrained('usuarios')->onDelete('restrict');
            $table->timestamps();
            
            $table->index('numero_orden');
            $table->index('estado');
            $table->index('fecha_programada');
            $table->index('maquina_id');
            $table->index('producto_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ordenes_produccion');
    }
};
