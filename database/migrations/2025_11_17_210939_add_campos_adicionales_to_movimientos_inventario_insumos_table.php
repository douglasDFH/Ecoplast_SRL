<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('movimientos_inventario_insumos', function (Blueprint $table) {
            // Agregar campos faltantes para el flujo de inventario
            $table->foreignId('orden_produccion_id')->nullable()->after('usuario_id')->constrained('ordenes_produccion')->onDelete('set null');
            $table->string('numero_documento', 100)->nullable()->after('orden_produccion_id');
            $table->foreignId('proveedor_id')->nullable()->after('numero_documento')->constrained('proveedores')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movimientos_inventario_insumos', function (Blueprint $table) {
            $table->dropForeign(['orden_produccion_id']);
            $table->dropForeign(['proveedor_id']);
            $table->dropColumn(['orden_produccion_id', 'numero_documento', 'proveedor_id']);
        });
    }
};
