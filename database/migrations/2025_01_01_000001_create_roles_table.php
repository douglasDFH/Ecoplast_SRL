<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_rol', 50)->unique();
            $table->text('descripcion')->nullable();
            $table->enum('nivel_acceso', ['basico', 'intermedio', 'avanzado', 'total'])->default('basico');
            $table->timestamp('created_at')->useCurrent();
            
            $table->index('nombre_rol');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
