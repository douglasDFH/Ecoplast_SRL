<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos_inventario_productos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->onDelete('restrict');
            $table->enum('tipo_movimiento', ['entrada_produccion', 'salida_venta', 'ajuste', 'merma', 'devolucion']);
            $table->integer('cantidad');
            $table->string('lote_produccion', 50)->nullable();
            $table->date('fecha_fabricacion')->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('restrict');
            $table->string('referencia', 100)->nullable();
            $table->text('motivo')->nullable();
            $table->timestamp('fecha_movimiento')->useCurrent();
            
            $table->index('producto_id');
            $table->index('fecha_movimiento');
            $table->index('tipo_movimiento');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_inventario_productos');
    }
};
