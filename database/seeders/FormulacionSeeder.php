<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FormulacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener IDs de productos (asumiendo que ya existen algunos)
        $productos = DB::table('productos_terminados')->limit(5)->pluck('id')->toArray();
        
        if (empty($productos)) {
            $this->command->warn('No hay productos terminados. Ejecuta primero el ProductoSeeder.');
            return;
        }

        // Obtener IDs de insumos (asumiendo que ya existen)
        $insumos = DB::table('insumos')->limit(10)->pluck('id')->toArray();
        
        if (empty($insumos)) {
            $this->command->warn('No hay insumos. Ejecuta primero el InsumoSeeder.');
            return;
        }

        $formulaciones = [
            // Formulación 1: PLA Base
            [
                'producto_id' => $productos[0] ?? 1,
                'codigo_formulacion' => 'FORM-PLA-001',
                'nombre' => 'Formulación PLA Estándar',
                'version' => '1.0',
                'descripcion' => 'Formulación base con PLA (ácido poliláctico) para films biodegradables de uso general.',
                'cantidad_lote_kg' => 100.0,
                'tiempo_preparacion_min' => 30,
                'temperatura_procesamiento' => 190.0,
                'instrucciones' => 'Secar PLA a 80°C por 4 horas antes de procesamiento. Mezclar aditivos en mezclador a baja velocidad (30 rpm) por 10 minutos. Mantener temperatura de extrusión entre 185-195°C.',
                'fecha_aprobacion' => now()->subMonths(6),
                'aprobado_por' => 1,
                'activo' => true,
                'insumos' => [
                    ['insumo_id' => $insumos[0] ?? 1, 'porcentaje' => 85.0, 'cantidad_kg_por_lote' => 85.0, 'notas' => 'PLA grado alimenticio'],
                    ['insumo_id' => $insumos[1] ?? 2, 'porcentaje' => 10.0, 'cantidad_kg_por_lote' => 10.0, 'notas' => 'Plastificante natural'],
                    ['insumo_id' => $insumos[2] ?? 3, 'porcentaje' => 5.0, 'cantidad_kg_por_lote' => 5.0, 'notas' => 'Aditivo biodegradable'],
                ],
            ],

            // Formulación 2: PHA Flexible
            [
                'producto_id' => $productos[1] ?? 2,
                'codigo_formulacion' => 'FORM-PHA-002',
                'nombre' => 'Formulación PHA Flexible',
                'version' => '2.1',
                'descripcion' => 'Mezcla de PHA (polihidroxialcanoatos) con plastificantes para aplicaciones flexibles y compostables.',
                'cantidad_lote_kg' => 150.0,
                'tiempo_preparacion_min' => 45,
                'temperatura_procesamiento' => 165.0,
                'instrucciones' => 'PHA no requiere secado previo. Mezclar con plastificantes a temperatura ambiente. Temperatura de procesamiento 160-170°C. Evitar sobrecalentamiento.',
                'fecha_aprobacion' => now()->subMonths(4),
                'aprobado_por' => 1,
                'activo' => true,
                'insumos' => [
                    ['insumo_id' => $insumos[3] ?? 4, 'porcentaje' => 70.0, 'cantidad_kg_por_lote' => 105.0, 'notas' => 'PHA tipo P(3HB-co-3HV)'],
                    ['insumo_id' => $insumos[4] ?? 5, 'porcentaje' => 20.0, 'cantidad_kg_por_lote' => 30.0, 'notas' => 'Plastificante de origen vegetal'],
                    ['insumo_id' => $insumos[5] ?? 6, 'porcentaje' => 8.0, 'cantidad_kg_por_lote' => 12.0, 'notas' => 'Agente nucleante'],
                    ['insumo_id' => $insumos[6] ?? 7, 'porcentaje' => 2.0, 'cantidad_kg_por_lote' => 3.0, 'notas' => 'Antibloqueo natural'],
                ],
            ],

            // Formulación 3: Almidón Termoplástico
            [
                'producto_id' => $productos[2] ?? 3,
                'codigo_formulacion' => 'FORM-TPS-003',
                'nombre' => 'Almidón Termoplástico (TPS)',
                'version' => '1.5',
                'descripcion' => 'Formulación basada en almidón de maíz modificado con plastificantes de glicerina para envases rígidos compostables.',
                'cantidad_lote_kg' => 80.0,
                'tiempo_preparacion_min' => 50,
                'temperatura_procesamiento' => 145.0,
                'instrucciones' => 'Pre-mezclar almidón con glicerina en mezclador de alta velocidad. Dejar reposar 2 horas. Procesar a 140-150°C con humedad controlada <2%. Enfriamiento rápido requerido.',
                'fecha_aprobacion' => now()->subMonths(3),
                'aprobado_por' => 1,
                'activo' => true,
                'insumos' => [
                    ['insumo_id' => $insumos[7] ?? 8, 'porcentaje' => 55.0, 'cantidad_kg_por_lote' => 44.0, 'notas' => 'Almidón de maíz nativo'],
                    ['insumo_id' => $insumos[8] ?? 9, 'porcentaje' => 30.0, 'cantidad_kg_por_lote' => 24.0, 'notas' => 'Glicerina vegetal'],
                    ['insumo_id' => $insumos[9] ?? 10, 'porcentaje' => 10.0, 'cantidad_kg_por_lote' => 8.0, 'notas' => 'Fibra natural (celulosa)'],
                    ['insumo_id' => $insumos[0] ?? 1, 'porcentaje' => 5.0, 'cantidad_kg_por_lote' => 4.0, 'notas' => 'Compatibilizante PLA'],
                ],
            ],

            // Formulación 4: PBAT Blend
            [
                'producto_id' => $productos[3] ?? 4,
                'codigo_formulacion' => 'FORM-PBAT-004',
                'nombre' => 'Blend PBAT/PLA',
                'version' => '3.0',
                'descripcion' => 'Mezcla optimizada de PBAT y PLA para obtener propiedades mecánicas mejoradas manteniendo biodegradabilidad total.',
                'cantidad_lote_kg' => 120.0,
                'tiempo_preparacion_min' => 35,
                'temperatura_procesamiento' => 175.0,
                'instrucciones' => 'Secar PLA y PBAT separadamente a 80°C por 3 horas. Mezclar en extrusora doble husillo a 170-180°C. Velocidad de husillo: 200 rpm. Agregar compatibilizante en la zona 3.',
                'fecha_aprobacion' => now()->subMonths(2),
                'aprobado_por' => 1,
                'activo' => true,
                'insumos' => [
                    ['insumo_id' => $insumos[1] ?? 2, 'porcentaje' => 50.0, 'cantidad_kg_por_lote' => 60.0, 'notas' => 'PBAT grado film'],
                    ['insumo_id' => $insumos[0] ?? 1, 'porcentaje' => 40.0, 'cantidad_kg_por_lote' => 48.0, 'notas' => 'PLA grado inyección'],
                    ['insumo_id' => $insumos[2] ?? 3, 'porcentaje' => 7.0, 'cantidad_kg_por_lote' => 8.4, 'notas' => 'Compatibilizante reactivo'],
                    ['insumo_id' => $insumos[3] ?? 4, 'porcentaje' => 3.0, 'cantidad_kg_por_lote' => 3.6, 'notas' => 'Estabilizante térmico'],
                ],
            ],

            // Formulación 5: Celulosa Acetato
            [
                'producto_id' => $productos[4] ?? 5,
                'codigo_formulacion' => 'FORM-CA-005',
                'nombre' => 'Acetato de Celulosa Modificado',
                'version' => '1.2',
                'descripcion' => 'Formulación con acetato de celulosa para aplicaciones de alta transparencia y resistencia química.',
                'cantidad_lote_kg' => 90.0,
                'tiempo_preparacion_min' => 40,
                'temperatura_procesamiento' => 210.0,
                'instrucciones' => 'Secar celulosa acetato a 90°C por 6 horas (crítico). Mezclar aditivos en seco. Temperatura de proceso: 205-215°C. Presión de inyección moderada.',
                'fecha_aprobacion' => now()->subMonths(1),
                'aprobado_por' => 1,
                'activo' => true,
                'insumos' => [
                    ['insumo_id' => $insumos[4] ?? 5, 'porcentaje' => 80.0, 'cantidad_kg_por_lote' => 72.0, 'notas' => 'Celulosa acetato grado óptico'],
                    ['insumo_id' => $insumos[5] ?? 6, 'porcentaje' => 12.0, 'cantidad_kg_por_lote' => 10.8, 'notas' => 'Plastificante citrato'],
                    ['insumo_id' => $insumos[6] ?? 7, 'porcentaje' => 5.0, 'cantidad_kg_por_lote' => 4.5, 'notas' => 'Agente UV bloqueador'],
                    ['insumo_id' => $insumos[7] ?? 8, 'porcentaje' => 3.0, 'cantidad_kg_por_lote' => 2.7, 'notas' => 'Lubricante interno'],
                ],
            ],
        ];

        foreach ($formulaciones as $formulacionData) {
            $insumos = $formulacionData['insumos'];
            unset($formulacionData['insumos']);

            $formulacionId = DB::table('formulaciones')->insertGetId(array_merge($formulacionData, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));

            // Insertar insumos de la formulación
            foreach ($insumos as $insumo) {
                DB::table('formulacion_insumo')->insert(array_merge($insumo, [
                    'formulacion_id' => $formulacionId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        }

        $this->command->info('✓ Formulaciones biodegradables creadas exitosamente');
    }
}
