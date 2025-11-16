<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lotes_produccion', function (Blueprint $table) {
            $table->id();
            $table->string('numero_lote', 50)->unique();
            $table->foreignId('orden_id')->constrained('ordenes_produccion')->onDelete('restrict');
            $table->integer('cantidad');
            $table->dateTime('fecha_fabricacion');
            $table->date('fecha_vencimiento');
            $table->json('trazabilidad_insumos')->nullable();
            $table->enum('estado_lote', ['cuarentena', 'aprobado', 'rechazado', 'distribuido'])->default('cuarentena');
            $table->string('ubicacion_almacen', 100)->nullable();
            
            $table->index('numero_lote');
            $table->index('orden_id');
            $table->index('estado_lote');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lotes_produccion');
    }
};
