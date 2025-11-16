<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auditorias', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo_auditoria', ['interna', 'externa', 'certificacion', 'cliente']);
            $table->date('fecha_auditoria');
            $table->string('auditor', 150);
            $table->text('alcance');
            $table->text('hallazgos')->nullable();
            $table->integer('no_conformidades')->default(0);
            $table->integer('observaciones')->default(0);
            $table->integer('oportunidades_mejora')->default(0);
            $table->enum('resultado', ['satisfactorio', 'condicional', 'no_satisfactorio']);
            $table->text('plan_accion')->nullable();
            $table->string('documento_informe')->nullable();
            $table->foreignId('usuario_responsable_id')->constrained('usuarios')->onDelete('restrict');
            
            $table->index('fecha_auditoria');
            $table->index('tipo_auditoria');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auditorias');
    }
};
