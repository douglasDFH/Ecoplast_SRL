<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formulaciones', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_formula', 50)->unique();
            $table->string('nombre_formula', 150);
            $table->text('descripcion')->nullable();
            $table->string('version', 20)->default('1.0');
            $table->string('tipo_producto_destino', 100)->nullable();
            $table->decimal('temperatura_procesamiento_min', 5, 1)->nullable()->comment('°C');
            $table->decimal('temperatura_procesamiento_max', 5, 1)->nullable()->comment('°C');
            $table->integer('tiempo_degradacion_estimado')->nullable()->comment('días');
            $table->text('certificaciones')->nullable();
            $table->boolean('aprobado')->default(false);
            $table->timestamp('fecha_aprobacion')->nullable();
            $table->foreignId('usuario_aprueba_id')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->boolean('activo')->default(true);
            $table->timestamp('created_at')->useCurrent();
            
            $table->index('codigo_formula');
            $table->index('aprobado');
            $table->index('activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formulaciones');
    }
};
