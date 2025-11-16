<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alertas', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo_alerta', ['stock_bajo', 'maquina_parada', 'calidad_deficiente', 'mantenimiento_vencido', 'meta_no_cumplida', 'otro']);
            $table->enum('severidad', ['info', 'advertencia', 'critico']);
            $table->string('titulo', 200);
            $table->text('mensaje');
            $table->string('entidad_tipo', 50)->nullable();
            $table->unsignedBigInteger('entidad_id')->nullable();
            $table->foreignId('usuario_destino_id')->nullable()->constrained('usuarios')->onDelete('cascade');
            $table->boolean('leida')->default(false);
            $table->timestamp('fecha_lectura')->nullable();
            $table->text('accion_tomada')->nullable();
            $table->timestamp('created_at')->useCurrent();
            
            $table->index('usuario_destino_id');
            $table->index('leida');
            $table->index('severidad');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alertas');
    }
};
