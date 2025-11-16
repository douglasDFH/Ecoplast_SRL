<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('componentes_formulacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formulacion_id')->constrained('formulaciones')->onDelete('cascade');
            $table->foreignId('insumo_id')->constrained('insumos')->onDelete('restrict');
            $table->decimal('porcentaje', 5, 2)->comment('Porcentaje en peso');
            $table->decimal('cantidad_base', 10, 3)->comment('Cantidad para 100kg');
            $table->tinyInteger('orden_adicion')->default(1);
            $table->text('notas')->nullable();
            
            $table->unique(['formulacion_id', 'insumo_id']);
            $table->index('formulacion_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('componentes_formulacion');
    }
};
