<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('defectos_calidad', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_defecto', 20)->unique();
            $table->string('nombre_defecto', 100);
            $table->text('descripcion')->nullable();
            $table->enum('severidad', ['critico', 'mayor', 'menor']);
            $table->boolean('activo')->default(true);
            
            $table->index('codigo_defecto');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('defectos_calidad');
    }
};
