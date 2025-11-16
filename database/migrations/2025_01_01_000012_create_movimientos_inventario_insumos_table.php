<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos_inventario_insumos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('insumo_id')->constrained('insumos')->onDelete('restrict');
            $table->enum('tipo_movimiento', ['entrada', 'salida', 'ajuste', 'desperdicio']);
            $table->decimal('cantidad', 10, 2);
            $table->string('lote', 50)->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->decimal('costo_unitario', 10, 2)->nullable();
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('restrict');
            $table->text('motivo')->nullable();
            $table->timestamp('fecha_movimiento')->useCurrent();
            
            $table->index('fecha_movimiento');
            $table->index('insumo_id');
            $table->index('tipo_movimiento');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_inventario_insumos');
    }
};
