<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Configuración General de Ecoplast SRL
    |--------------------------------------------------------------------------
    */

    'company' => [
        'name' => 'Ecoplast SRL',
        'address' => 'Santa Cruz de la Sierra, Bolivia',
        'phone' => '+591 3 123-4567',
        'email' => 'info@ecoplast.com.bo',
    ],

    /*
    |--------------------------------------------------------------------------
    | Parámetros de Producción
    |--------------------------------------------------------------------------
    */

    'produccion' => [
        'oee_objetivo' => 85.0, // OEE objetivo en porcentaje
        'tasa_defectos_maxima' => 5.0, // Máximo 5% de defectos
        'tiempo_ciclo_alerta' => 15, // % de variación para alerta
        'temperatura_tolerancia' => 3.0, // ±3°C de tolerancia
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Alertas
    |--------------------------------------------------------------------------
    */

    'alertas' => [
        'tiempo_respuesta_critica' => 15, // minutos
        'tiempo_respuesta_advertencia' => 60, // minutos
        'escalar_no_atendidas' => 24, // horas
        'sonido_alertas_criticas' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de KPIs
    |--------------------------------------------------------------------------
    */

    'kpis' => [
        'calcular_cada' => 30, // segundos
        'disponibilidad_objetivo' => 95.0,
        'rendimiento_objetivo' => 90.0,
        'calidad_objetivo' => 98.0,
        'mtbf_objetivo' => 200, // horas
        'mttr_objetivo' => 4, // horas
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Inventario
    |--------------------------------------------------------------------------
    */

    'inventario' => [
        'stock_minimo_dias' => 15,
        'rotacion_objetivo' => 12, // veces al año
        'eficiencia_material_minima' => 90.0, // %
        'scrap_reciclado_objetivo' => 80.0, // %
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Calidad
    |--------------------------------------------------------------------------
    */

    'calidad' => [
        'aql_estandar' => 2.5, // %
        'tiempo_cuarentena_maximo' => 72, // horas
        'muestra_inspeccion_proceso' => 10, // piezas
        'muestra_inspeccion_final' => 50, // piezas
        'test_biodegradacion_dias' => 90,
    ],

    /*
    |--------------------------------------------------------------------------
    | Tipos de Material Biodegradable
    |--------------------------------------------------------------------------
    */

    'materiales' => [
        'PLA' => 'Ácido Poliláctico',
        'PHA' => 'Polihidroxialcanoatos',
        'PBS' => 'Polibutileno Succinato',
        'PBAT' => 'Polibutileno Adipato Tereftalato',
        'Almidon' => 'Almidón Termoplástico',
        'Celulosa' => 'Celulosa',
    ],

    /*
    |--------------------------------------------------------------------------
    | Certificaciones Disponibles
    |--------------------------------------------------------------------------
    */

    'certificaciones' => [
        'EN 13432' => 'Compostabilidad Europea',
        'ASTM D6400' => 'Estándar USA',
        'OK Compost' => 'TÜV Austria',
        'BPI' => 'Biodegradable Products Institute',
        'ISO 9001' => 'Sistema de Gestión de Calidad',
    ],

];
