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
        Schema::create('proveedores', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_proveedor', 20)->unique();
            $table->string('nombre_comercial', 200);
            $table->string('razon_social', 200)->nullable();
            $table->string('ruc', 20)->nullable()->unique();
            $table->string('contacto', 100)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->text('direccion')->nullable();
            $table->string('ciudad', 100)->nullable();
            $table->string('pais', 100)->default('Paraguay');
            $table->text('notas')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            // Índices
            $table->index('codigo_proveedor');
            $table->index('nombre_comercial');
            $table->index('activo');
        });

        // Insertar proveedores de ejemplo
        DB::table('proveedores')->insert([
            [
                'codigo_proveedor' => 'PROV-001',
                'nombre_comercial' => 'NatureWorks LLC',
                'razon_social' => 'NatureWorks LLC',
                'ruc' => '80012345-1',
                'contacto' => 'Juan Pérez',
                'telefono' => '+595 21 123456',
                'email' => 'ventas@natureworks.com.py',
                'direccion' => 'Av. Eusebio Ayala 1234',
                'ciudad' => 'Asunción',
                'pais' => 'Paraguay',
                'notas' => 'Proveedor principal de PLA',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo_proveedor' => 'PROV-002',
                'nombre_comercial' => 'Biodegradables del Sur',
                'razon_social' => 'Biodegradables del Sur S.A.',
                'ruc' => '80023456-2',
                'contacto' => 'María González',
                'telefono' => '+595 21 234567',
                'email' => 'contacto@biodelsur.com.py',
                'direccion' => 'Ruta 2 km 15',
                'ciudad' => 'San Lorenzo',
                'pais' => 'Paraguay',
                'notas' => 'Proveedor de PHA y PBS',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo_proveedor' => 'PROV-003',
                'nombre_comercial' => 'BASF Paraguay',
                'razon_social' => 'BASF Paraguay S.A.',
                'ruc' => '80034567-3',
                'contacto' => 'Carlos Martínez',
                'telefono' => '+595 21 345678',
                'email' => 'ventas@basf.com.py',
                'direccion' => 'Av. Mariscal López 2500',
                'ciudad' => 'Asunción',
                'pais' => 'Paraguay',
                'notas' => 'Proveedor de PBAT y aditivos',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo_proveedor' => 'PROV-004',
                'nombre_comercial' => 'Almidones Industriales',
                'razon_social' => 'Almidones Industriales del Paraguay',
                'ruc' => '80045678-4',
                'contacto' => 'Ana Silva',
                'telefono' => '+595 21 456789',
                'email' => 'info@almipar.com.py',
                'direccion' => 'Ruta 1 km 25',
                'ciudad' => 'Itá',
                'pais' => 'Paraguay',
                'notas' => 'Proveedor de almidón modificado',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo_proveedor' => 'PROV-005',
                'nombre_comercial' => 'Pigmentos Ecológicos',
                'razon_social' => 'Pigmentos Ecológicos S.R.L.',
                'ruc' => '80056789-5',
                'contacto' => 'Roberto Fernández',
                'telefono' => '+595 21 567890',
                'email' => 'ventas@pigmentoseco.com.py',
                'direccion' => 'Av. Artigas 3000',
                'ciudad' => 'Asunción',
                'pais' => 'Paraguay',
                'notas' => 'Proveedor de pigmentos biodegradables',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proveedores');
    }
};
