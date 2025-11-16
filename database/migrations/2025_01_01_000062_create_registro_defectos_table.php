<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registro_defectos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspeccion_id')->constrained('inspecciones_calidad')->onDelete('cascade');
            $table->foreignId('defecto_id')->constrained('defectos_calidad')->onDelete('restrict');
            $table->integer('cantidad');
            $table->string('ubicacion_pieza', 100)->nullable();
            $table->string('imagen_evidencia')->nullable();
            
            $table->index('inspeccion_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registro_defectos');
    }
};
