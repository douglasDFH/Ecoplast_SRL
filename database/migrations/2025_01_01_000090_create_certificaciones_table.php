<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificaciones', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_certificacion', 150);
            $table->enum('tipo_certificacion', ['producto', 'proceso', 'empresa', 'ambiental']);
            $table->string('organismo_certificador', 150)->nullable();
            $table->string('numero_certificado', 100)->nullable();
            $table->date('fecha_emision');
            $table->date('fecha_vencimiento');
            $table->enum('estado', ['vigente', 'por_vencer', 'vencida', 'en_renovacion']);
            $table->text('alcance')->nullable();
            $table->string('documento_pdf')->nullable();
            $table->text('notas')->nullable();
            $table->timestamp('created_at')->useCurrent();
            
            $table->index('fecha_vencimiento');
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificaciones');
    }
};
