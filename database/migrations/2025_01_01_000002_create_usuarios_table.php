<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_completo', 100);
            $table->string('email', 100)->unique();
            $table->string('password');
            $table->foreignId('rol_id')->constrained('roles')->onDelete('restrict');
            $table->string('telefono', 20)->nullable();
            $table->string('foto_perfil')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamp('ultimo_acceso')->nullable();
            $table->rememberToken();
            $table->timestamps();
            
            $table->index('email');
            $table->index('activo');
            $table->index('rol_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
