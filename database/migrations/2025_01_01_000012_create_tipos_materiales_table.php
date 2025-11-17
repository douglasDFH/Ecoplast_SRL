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
        Schema::create('tipos_materiales', function (Blueprint $table) {
            $table->id();

            // Identificación
            $table->string('codigo', 20)->unique()->comment('Código único del tipo de material');
            $table->string('nombre', 100)->comment('Nombre del tipo de material');

            // Clasificación
            $table->enum('clasificacion', [
                'Polímero Biodegradable',
                'Aditivo',
                'Pigmento',
                'Otro'
            ])->default('Polímero Biodegradable');

            // Información Técnica
            $table->text('descripcion')->nullable();
            $table->decimal('densidad_min', 6, 3)->nullable()->comment('Densidad mínima en g/cm³');
            $table->decimal('densidad_max', 6, 3)->nullable()->comment('Densidad máxima en g/cm³');
            $table->decimal('temperatura_procesamiento_min', 5, 1)->nullable()->comment('Temperatura mínima en °C');
            $table->decimal('temperatura_procesamiento_max', 5, 1)->nullable()->comment('Temperatura máxima en °C');
            $table->integer('tiempo_degradacion_min')->nullable()->comment('Tiempo mínimo de degradación en días');
            $table->integer('tiempo_degradacion_max')->nullable()->comment('Tiempo máximo de degradación en días');

            // Certificaciones y Normativas
            $table->text('certificaciones_aplicables')->nullable()->comment('Certificaciones aplicables (EN 13432, ASTM D6400, etc.)');

            // UI/UX
            $table->string('color_referencia', 7)->nullable()->comment('Color en formato hex (#RRGGBB)');
            $table->string('icono', 50)->nullable()->comment('Nombre del icono');
            $table->integer('orden_visualizacion')->default(999)->comment('Orden para mostrar en listados');

            // Control
            $table->boolean('activo')->default(true);
            $table->timestamps();

            // Índices
            $table->index('codigo');
            $table->index('clasificacion');
            $table->index('activo');
            $table->index('orden_visualizacion');
        });

        // Insertar datos iniciales basados en los tipos existentes en el ENUM
        DB::table('tipos_materiales')->insert([
            [
                'codigo' => 'PLA',
                'nombre' => 'PLA - Ácido Poliláctico',
                'clasificacion' => 'Polímero Biodegradable',
                'descripcion' => 'Polímero biodegradable derivado de recursos renovables como el almidón de maíz. Excelente para aplicaciones de corto uso.',
                'densidad_min' => 1.210,
                'densidad_max' => 1.250,
                'temperatura_procesamiento_min' => 160.0,
                'temperatura_procesamiento_max' => 190.0,
                'tiempo_degradacion_min' => 90,
                'tiempo_degradacion_max' => 180,
                'certificaciones_aplicables' => 'ASTM D6400, EN 13432, OK Compost',
                'color_referencia' => '#DBEAFE',
                'orden_visualizacion' => 1,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo' => 'PHA',
                'nombre' => 'PHA - Polihidroxialcanoato',
                'clasificacion' => 'Polímero Biodegradable',
                'descripcion' => 'Biopolímero producido por bacterias. Biodegradable en ambientes marinos y terrestres.',
                'densidad_min' => 1.180,
                'densidad_max' => 1.260,
                'temperatura_procesamiento_min' => 140.0,
                'temperatura_procesamiento_max' => 180.0,
                'tiempo_degradacion_min' => 60,
                'tiempo_degradacion_max' => 120,
                'certificaciones_aplicables' => 'ASTM D6400, EN 13432, TUV OK Biodegradable MARINE',
                'color_referencia' => '#D1FAE5',
                'orden_visualizacion' => 2,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo' => 'PBS',
                'nombre' => 'PBS - Polibutileno Succinato',
                'clasificacion' => 'Polímero Biodegradable',
                'descripcion' => 'Poliéster alifático biodegradable con buenas propiedades mecánicas y térmicas.',
                'densidad_min' => 1.230,
                'densidad_max' => 1.260,
                'temperatura_procesamiento_min' => 110.0,
                'temperatura_procesamiento_max' => 130.0,
                'tiempo_degradacion_min' => 120,
                'tiempo_degradacion_max' => 240,
                'certificaciones_aplicables' => 'ASTM D6400, EN 13432',
                'color_referencia' => '#FEF3C7',
                'orden_visualizacion' => 3,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo' => 'PBAT',
                'nombre' => 'PBAT - Adipato Tereftalato de Polibutileno',
                'clasificacion' => 'Polímero Biodegradable',
                'descripcion' => 'Copoliéster biodegradable con alta flexibilidad. Ideal para films y bolsas.',
                'densidad_min' => 1.180,
                'densidad_max' => 1.240,
                'temperatura_procesamiento_min' => 110.0,
                'temperatura_procesamiento_max' => 130.0,
                'tiempo_degradacion_min' => 90,
                'tiempo_degradacion_max' => 180,
                'certificaciones_aplicables' => 'ASTM D6400, EN 13432',
                'color_referencia' => '#FCE7F3',
                'orden_visualizacion' => 4,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo' => 'ALMIDON',
                'nombre' => 'Almidón Termoplástico (TPS)',
                'clasificacion' => 'Polímero Biodegradable',
                'descripcion' => 'Almidón modificado con plastificantes. 100% biodegradable y compostable.',
                'densidad_min' => 1.200,
                'densidad_max' => 1.300,
                'temperatura_procesamiento_min' => 120.0,
                'temperatura_procesamiento_max' => 160.0,
                'tiempo_degradacion_min' => 30,
                'tiempo_degradacion_max' => 90,
                'certificaciones_aplicables' => 'EN 13432, OK Compost HOME',
                'color_referencia' => '#F3E8FF',
                'orden_visualizacion' => 5,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo' => 'CELULOSA',
                'nombre' => 'Acetato de Celulosa',
                'clasificacion' => 'Polímero Biodegradable',
                'descripcion' => 'Derivado de la celulosa natural. Biodegradable y compostable.',
                'densidad_min' => 1.250,
                'densidad_max' => 1.320,
                'temperatura_procesamiento_min' => 180.0,
                'temperatura_procesamiento_max' => 230.0,
                'tiempo_degradacion_min' => 60,
                'tiempo_degradacion_max' => 150,
                'certificaciones_aplicables' => 'ASTM D6400',
                'color_referencia' => '#DBEAFE',
                'orden_visualizacion' => 6,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo' => 'ADITIVO',
                'nombre' => 'Aditivos y Modificadores',
                'clasificacion' => 'Aditivo',
                'descripcion' => 'Aditivos para mejorar procesabilidad, estabilidad y propiedades finales de los biopolímeros.',
                'densidad_min' => null,
                'densidad_max' => null,
                'temperatura_procesamiento_min' => null,
                'temperatura_procesamiento_max' => null,
                'tiempo_degradacion_min' => null,
                'tiempo_degradacion_max' => null,
                'certificaciones_aplicables' => 'Varía según el aditivo',
                'color_referencia' => '#FEE2E2',
                'orden_visualizacion' => 7,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo' => 'PIGMENTO',
                'nombre' => 'Pigmentos y Colorantes',
                'clasificacion' => 'Pigmento',
                'descripcion' => 'Colorantes compatibles con materiales biodegradables.',
                'densidad_min' => null,
                'densidad_max' => null,
                'temperatura_procesamiento_min' => null,
                'temperatura_procesamiento_max' => null,
                'tiempo_degradacion_min' => null,
                'tiempo_degradacion_max' => null,
                'certificaciones_aplicables' => 'FDA, REACH',
                'color_referencia' => '#FEF3C7',
                'orden_visualizacion' => 8,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo' => 'OTRO',
                'nombre' => 'Otros Materiales',
                'clasificacion' => 'Otro',
                'descripcion' => 'Otros tipos de materiales no clasificados en las categorías anteriores.',
                'densidad_min' => null,
                'densidad_max' => null,
                'temperatura_procesamiento_min' => null,
                'temperatura_procesamiento_max' => null,
                'tiempo_degradacion_min' => null,
                'tiempo_degradacion_max' => null,
                'certificaciones_aplicables' => null,
                'color_referencia' => '#F3F4F6',
                'orden_visualizacion' => 999,
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
        Schema::dropIfExists('tipos_materiales');
    }
};
