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
        // Agregar nueva columna tipo_material_id
        Schema::table('insumos', function (Blueprint $table) {
            $table->unsignedBigInteger('tipo_material_id')->nullable()->after('tipo_material');

            // Crear foreign key con restricción ON DELETE RESTRICT
            $table->foreign('tipo_material_id')
                  ->references('id')
                  ->on('tipos_materiales')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');

            // Índice para mejorar performance en consultas
            $table->index('tipo_material_id');
        });

        // Migrar datos existentes del ENUM al FK
        // Mapear cada valor del ENUM al ID correspondiente en tipos_materiales
        $mapeoTipos = [
            'PLA' => 'PLA',
            'PHA' => 'PHA',
            'PBS' => 'PBS',
            'PBAT' => 'PBAT',
            'Almidon' => 'ALMIDON',
            'Celulosa' => 'CELULOSA',
            'Aditivo' => 'ADITIVO',
            'Pigmento' => 'PIGMENTO',
            'Otro' => 'OTRO',
        ];

        foreach ($mapeoTipos as $valorEnum => $codigoTipo) {
            DB::statement("
                UPDATE insumos
                SET tipo_material_id = (
                    SELECT id FROM tipos_materiales WHERE codigo = ?
                )
                WHERE tipo_material = ?
            ", [$codigoTipo, $valorEnum]);
        }

        // Verificar que todos los registros fueron migrados
        $registrosSinMigrar = DB::table('insumos')
            ->whereNull('tipo_material_id')
            ->count();

        if ($registrosSinMigrar > 0) {
            throw new Exception("Hay {$registrosSinMigrar} registros de insumos sin tipo_material_id asignado. Verificar datos.");
        }

        // NOTA: Mantenemos la columna 'tipo_material' (ENUM) temporalmente por seguridad
        // Se podrá eliminar en una migración futura una vez verificado que todo funciona correctamente
        // con el nuevo campo tipo_material_id
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('insumos', function (Blueprint $table) {
            // Eliminar foreign key
            $table->dropForeign(['tipo_material_id']);

            // Eliminar índice
            $table->dropIndex(['tipo_material_id']);

            // Eliminar columna
            $table->dropColumn('tipo_material_id');
        });

        // NOTA: Al hacer rollback, los datos del ENUM 'tipo_material' se mantienen intactos
        // ya que nunca lo eliminamos en la migración up()
    }
};
