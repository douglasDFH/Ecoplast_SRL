<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asignacion_turnos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->foreignId('turno_id')->constrained('turnos')->onDelete('restrict');
            $table->date('fecha_asignacion');
            $table->text('observaciones')->nullable();
            
            $table->unique(['usuario_id', 'fecha_asignacion']);
            $table->index('fecha_asignacion');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asignacion_turnos');
    }
};
