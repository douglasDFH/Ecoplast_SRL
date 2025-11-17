<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('insumos', function (Blueprint $table) {
            // Agregar columna proveedor_id como FK nullable
            $table->unsignedBigInteger('proveedor_id')->nullable()->after('proveedor');

            // Crear índice y constraint de FK
            $table->foreign('proveedor_id')
                  ->references('id')
                  ->on('proveedores')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');

            $table->index('proveedor_id');
        });

        // Migrar datos existentes: intentar asociar proveedores por nombre
        $insumos = DB::table('insumos')->whereNotNull('proveedor')->get();
        foreach ($insumos as $insumo) {
            if ($insumo->proveedor) {
                // Buscar proveedor por nombre comercial similar
                $proveedor = DB::table('proveedores')
                    ->where('nombre_comercial', 'like', '%' . $insumo->proveedor . '%')
                    ->orWhere('razon_social', 'like', '%' . $insumo->proveedor . '%')
                    ->first();

                if ($proveedor) {
                    DB::table('insumos')
                        ->where('id', $insumo->id)
                        ->update(['proveedor_id' => $proveedor->id]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('insumos', function (Blueprint $table) {
            // Eliminar FK y columna
            $table->dropForeign(['proveedor_id']);
            $table->dropIndex(['proveedor_id']);
            $table->dropColumn('proveedor_id');
        });
    }
};
