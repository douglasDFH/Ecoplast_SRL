<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mantenimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maquina_id')->constrained('maquinas')->onDelete('restrict');
            $table->enum('tipo_mantenimiento', ['preventivo', 'correctivo', 'predictivo']);
            $table->text('descripcion');
            $table->dateTime('fecha_programada');
            $table->dateTime('fecha_inicio')->nullable();
            $table->dateTime('fecha_fin')->nullable();
            $table->decimal('duracion_horas', 6, 2)->nullable();
            $table->decimal('costo', 10, 2)->nullable();
            $table->foreignId('tecnico_id')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->enum('estado', ['programado', 'en_proceso', 'completado', 'cancelado'])->default('programado');
            $table->enum('prioridad', ['baja', 'media', 'alta', 'critica'])->default('media');
            $table->text('observaciones')->nullable();
            $table->timestamp('created_at')->useCurrent();
            
            $table->index('maquina_id');
            $table->index('fecha_programada');
            $table->index('estado');
            $table->index('tipo_mantenimiento');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mantenimientos');
    }
};
