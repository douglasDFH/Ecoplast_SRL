<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categorias_productos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_categoria', 100);
            $table->text('descripcion')->nullable();
            $table->string('aplicacion', 200)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categorias_productos');
    }
};
