/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `alertas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alertas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tipo_alerta` enum('stock_bajo','maquina_parada','calidad_deficiente','mantenimiento_vencido','meta_no_cumplida','otro') NOT NULL,
  `severidad` enum('info','advertencia','critico') NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `mensaje` text NOT NULL,
  `entidad_tipo` varchar(50) DEFAULT NULL,
  `entidad_id` bigint(20) unsigned DEFAULT NULL,
  `usuario_destino_id` bigint(20) unsigned DEFAULT NULL,
  `leida` tinyint(1) NOT NULL DEFAULT 0,
  `fecha_lectura` timestamp NULL DEFAULT NULL,
  `accion_tomada` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `alertas_usuario_destino_id_index` (`usuario_destino_id`),
  KEY `alertas_leida_index` (`leida`),
  KEY `alertas_severidad_index` (`severidad`),
  KEY `alertas_created_at_index` (`created_at`),
  CONSTRAINT `alertas_usuario_destino_id_foreign` FOREIGN KEY (`usuario_destino_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `asignacion_turnos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asignacion_turnos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `usuario_id` bigint(20) unsigned NOT NULL,
  `turno_id` bigint(20) unsigned NOT NULL,
  `fecha_asignacion` date NOT NULL,
  `observaciones` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `asignacion_turnos_usuario_id_fecha_asignacion_unique` (`usuario_id`,`fecha_asignacion`),
  KEY `asignacion_turnos_turno_id_foreign` (`turno_id`),
  KEY `asignacion_turnos_fecha_asignacion_index` (`fecha_asignacion`),
  CONSTRAINT `asignacion_turnos_turno_id_foreign` FOREIGN KEY (`turno_id`) REFERENCES `turnos` (`id`),
  CONSTRAINT `asignacion_turnos_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `auditorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auditorias` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tipo_auditoria` enum('interna','externa','certificacion','cliente') NOT NULL,
  `fecha_auditoria` date NOT NULL,
  `auditor` varchar(150) NOT NULL,
  `alcance` text NOT NULL,
  `hallazgos` text DEFAULT NULL,
  `no_conformidades` int(11) NOT NULL DEFAULT 0,
  `observaciones` int(11) NOT NULL DEFAULT 0,
  `oportunidades_mejora` int(11) NOT NULL DEFAULT 0,
  `resultado` enum('satisfactorio','condicional','no_satisfactorio') NOT NULL,
  `plan_accion` text DEFAULT NULL,
  `documento_informe` varchar(255) DEFAULT NULL,
  `usuario_responsable_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `auditorias_usuario_responsable_id_foreign` (`usuario_responsable_id`),
  KEY `auditorias_fecha_auditoria_index` (`fecha_auditoria`),
  KEY `auditorias_tipo_auditoria_index` (`tipo_auditoria`),
  CONSTRAINT `auditorias_usuario_responsable_id_foreign` FOREIGN KEY (`usuario_responsable_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `categorias_insumos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categorias_insumos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre_categoria` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `es_biodegradable` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `categorias_productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categorias_productos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre_categoria` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `aplicacion` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `certificaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `certificaciones` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre_certificacion` varchar(150) NOT NULL,
  `tipo_certificacion` enum('producto','proceso','empresa','ambiental') NOT NULL,
  `organismo_certificador` varchar(150) DEFAULT NULL,
  `numero_certificado` varchar(100) DEFAULT NULL,
  `fecha_emision` date NOT NULL,
  `fecha_vencimiento` date NOT NULL,
  `estado` enum('vigente','por_vencer','vencida','en_renovacion') NOT NULL,
  `alcance` text DEFAULT NULL,
  `documento_pdf` varchar(255) DEFAULT NULL,
  `notas` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `certificaciones_fecha_vencimiento_index` (`fecha_vencimiento`),
  KEY `certificaciones_estado_index` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `componentes_formulacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `componentes_formulacion` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `formulacion_id` bigint(20) unsigned NOT NULL,
  `insumo_id` bigint(20) unsigned NOT NULL,
  `porcentaje` decimal(5,2) NOT NULL COMMENT 'Porcentaje en peso',
  `cantidad_base` decimal(10,3) NOT NULL COMMENT 'Cantidad para 100kg',
  `orden_adicion` tinyint(4) NOT NULL DEFAULT 1,
  `notas` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `componentes_formulacion_formulacion_id_insumo_id_unique` (`formulacion_id`,`insumo_id`),
  KEY `componentes_formulacion_insumo_id_foreign` (`insumo_id`),
  KEY `componentes_formulacion_formulacion_id_index` (`formulacion_id`),
  CONSTRAINT `componentes_formulacion_formulacion_id_foreign` FOREIGN KEY (`formulacion_id`) REFERENCES `formulaciones` (`id`) ON DELETE CASCADE,
  CONSTRAINT `componentes_formulacion_insumo_id_foreign` FOREIGN KEY (`insumo_id`) REFERENCES `insumos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `defectos_calidad`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `defectos_calidad` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `codigo_defecto` varchar(20) NOT NULL,
  `nombre_defecto` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `severidad` enum('critico','mayor','menor') NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `defectos_calidad_codigo_defecto_unique` (`codigo_defecto`),
  KEY `defectos_calidad_codigo_defecto_index` (`codigo_defecto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `formulaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formulaciones` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `codigo_formula` varchar(50) NOT NULL,
  `nombre_formula` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `version` varchar(20) NOT NULL DEFAULT '1.0',
  `tipo_producto_destino` varchar(100) DEFAULT NULL,
  `temperatura_procesamiento_min` decimal(5,1) DEFAULT NULL COMMENT '°C',
  `temperatura_procesamiento_max` decimal(5,1) DEFAULT NULL COMMENT '°C',
  `tiempo_degradacion_estimado` int(11) DEFAULT NULL COMMENT 'días',
  `certificaciones` text DEFAULT NULL,
  `aprobado` tinyint(1) NOT NULL DEFAULT 0,
  `fecha_aprobacion` timestamp NULL DEFAULT NULL,
  `usuario_aprueba_id` bigint(20) unsigned DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `formulaciones_codigo_formula_unique` (`codigo_formula`),
  KEY `formulaciones_usuario_aprueba_id_foreign` (`usuario_aprueba_id`),
  KEY `formulaciones_codigo_formula_index` (`codigo_formula`),
  KEY `formulaciones_aprobado_index` (`aprobado`),
  KEY `formulaciones_activo_index` (`activo`),
  CONSTRAINT `formulaciones_usuario_aprueba_id_foreign` FOREIGN KEY (`usuario_aprueba_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `inspecciones_calidad`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inspecciones_calidad` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `orden_id` bigint(20) unsigned NOT NULL,
  `lote_id` bigint(20) unsigned DEFAULT NULL,
  `tipo_inspeccion` enum('primera_pieza','proceso','final','auditoria') NOT NULL,
  `fecha_hora` datetime NOT NULL,
  `inspector_id` bigint(20) unsigned NOT NULL,
  `peso_promedio_gramos` decimal(8,3) DEFAULT NULL,
  `desviacion_peso` decimal(6,3) DEFAULT NULL,
  `espesor_promedio_micras` decimal(7,2) DEFAULT NULL,
  `resistencia_traccion_mpa` decimal(7,2) DEFAULT NULL,
  `test_biodegradacion` tinyint(1) DEFAULT NULL,
  `dias_compostaje_prueba` int(11) DEFAULT NULL,
  `manchas` int(11) NOT NULL DEFAULT 0,
  `deformaciones` int(11) NOT NULL DEFAULT 0,
  `rebabas` int(11) NOT NULL DEFAULT 0,
  `burbujas` int(11) NOT NULL DEFAULT 0,
  `fisuras` int(11) NOT NULL DEFAULT 0,
  `otros_defectos` text DEFAULT NULL,
  `piezas_inspeccionadas` int(11) NOT NULL,
  `piezas_aprobadas` int(11) NOT NULL,
  `piezas_rechazadas` int(11) NOT NULL,
  `resultado` enum('aprobado','aprobado_condicional','rechazado') NOT NULL,
  `observaciones` text DEFAULT NULL,
  `acciones_correctivas` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inspecciones_calidad_inspector_id_foreign` (`inspector_id`),
  KEY `inspecciones_calidad_orden_id_index` (`orden_id`),
  KEY `inspecciones_calidad_lote_id_index` (`lote_id`),
  KEY `inspecciones_calidad_fecha_hora_index` (`fecha_hora`),
  KEY `inspecciones_calidad_resultado_index` (`resultado`),
  CONSTRAINT `inspecciones_calidad_inspector_id_foreign` FOREIGN KEY (`inspector_id`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `inspecciones_calidad_lote_id_foreign` FOREIGN KEY (`lote_id`) REFERENCES `lotes_produccion` (`id`) ON DELETE SET NULL,
  CONSTRAINT `inspecciones_calidad_orden_id_foreign` FOREIGN KEY (`orden_id`) REFERENCES `ordenes_produccion` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `insumos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `insumos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `codigo_insumo` varchar(50) NOT NULL,
  `nombre_insumo` varchar(150) NOT NULL,
  `categoria_id` bigint(20) unsigned NOT NULL,
  `tipo_material` enum('PLA','PHA','PBS','PBAT','Almidon','Celulosa','Aditivo','Pigmento','Otro') NOT NULL,
  `unidad_medida` enum('kg','ton','litro','unidad') NOT NULL DEFAULT 'kg',
  `densidad` decimal(6,3) DEFAULT NULL COMMENT 'g/cm³',
  `temperatura_fusion` decimal(5,1) DEFAULT NULL COMMENT '°C',
  `certificacion_biodegradable` varchar(100) DEFAULT NULL,
  `proveedor` varchar(150) DEFAULT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `stock_minimo` decimal(10,2) NOT NULL,
  `stock_actual` decimal(10,2) NOT NULL DEFAULT 0.00,
  `fecha_caducidad_lote` date DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `insumos_codigo_insumo_unique` (`codigo_insumo`),
  KEY `insumos_categoria_id_foreign` (`categoria_id`),
  KEY `insumos_codigo_insumo_index` (`codigo_insumo`),
  KEY `insumos_activo_index` (`activo`),
  KEY `insumos_stock_actual_index` (`stock_actual`),
  KEY `insumos_tipo_material_index` (`tipo_material`),
  CONSTRAINT `insumos_categoria_id_foreign` FOREIGN KEY (`categoria_id`) REFERENCES `categorias_insumos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `kpis_diarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kpis_diarios` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `maquina_id` bigint(20) unsigned NOT NULL,
  `turno_id` bigint(20) unsigned NOT NULL,
  `unidades_planificadas` int(11) NOT NULL,
  `unidades_producidas` int(11) NOT NULL,
  `unidades_conformes` int(11) NOT NULL,
  `unidades_defectuosas` int(11) NOT NULL,
  `scrap_kg` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tiempo_planificado` int(11) NOT NULL COMMENT 'minutos',
  `tiempo_operacion` int(11) NOT NULL COMMENT 'minutos',
  `tiempo_paradas` int(11) NOT NULL COMMENT 'minutos',
  `tiempo_setup` int(11) NOT NULL DEFAULT 0 COMMENT 'minutos',
  `disponibilidad` decimal(5,2) NOT NULL COMMENT 'Tiempo operación / Tiempo planificado * 100',
  `rendimiento` decimal(5,2) NOT NULL COMMENT 'Producción real / Producción teórica * 100',
  `calidad` decimal(5,2) NOT NULL COMMENT 'Piezas conformes / Piezas producidas * 100',
  `oee` decimal(5,2) NOT NULL COMMENT 'Disponibilidad * Rendimiento * Calidad / 100',
  `consumo_energia_kwh` decimal(10,2) NOT NULL DEFAULT 0.00,
  `consumo_material_kg` decimal(10,2) NOT NULL DEFAULT 0.00,
  `eficiencia_material` decimal(5,2) DEFAULT NULL COMMENT '%',
  `costo_produccion` decimal(12,2) DEFAULT NULL,
  `tasa_defectos` decimal(5,2) NOT NULL COMMENT 'PPM o %',
  `first_pass_yield` decimal(5,2) DEFAULT NULL,
  `calculado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `kpis_diarios_fecha_maquina_id_turno_id_unique` (`fecha`,`maquina_id`,`turno_id`),
  KEY `kpis_diarios_turno_id_foreign` (`turno_id`),
  KEY `kpis_diarios_fecha_index` (`fecha`),
  KEY `kpis_diarios_maquina_id_index` (`maquina_id`),
  KEY `kpis_diarios_oee_index` (`oee`),
  CONSTRAINT `kpis_diarios_maquina_id_foreign` FOREIGN KEY (`maquina_id`) REFERENCES `maquinas` (`id`),
  CONSTRAINT `kpis_diarios_turno_id_foreign` FOREIGN KEY (`turno_id`) REFERENCES `turnos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `kpis_mensuales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kpis_mensuales` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `año` year(4) NOT NULL,
  `mes` tinyint(4) NOT NULL,
  `maquina_id` bigint(20) unsigned DEFAULT NULL,
  `total_unidades_producidas` bigint(20) NOT NULL,
  `total_unidades_conformes` bigint(20) NOT NULL,
  `total_scrap_kg` decimal(12,2) NOT NULL DEFAULT 0.00,
  `oee_promedio` decimal(5,2) NOT NULL,
  `disponibilidad_promedio` decimal(5,2) NOT NULL,
  `rendimiento_promedio` decimal(5,2) NOT NULL,
  `calidad_promedio` decimal(5,2) NOT NULL,
  `mtbf` decimal(10,2) DEFAULT NULL COMMENT 'horas',
  `mttr` decimal(10,2) DEFAULT NULL COMMENT 'horas',
  `numero_paros` int(11) NOT NULL DEFAULT 0,
  `tiempo_total_paros_horas` decimal(10,2) NOT NULL DEFAULT 0.00,
  `costo_total_produccion` decimal(15,2) DEFAULT NULL,
  `costo_unitario` decimal(10,4) DEFAULT NULL,
  `costo_energia` decimal(12,2) DEFAULT NULL,
  `costo_material` decimal(12,2) DEFAULT NULL,
  `costo_mantenimiento` decimal(12,2) DEFAULT NULL,
  `porcentaje_material_biodegradable` decimal(5,2) DEFAULT NULL,
  `cumplimiento_certificaciones` decimal(5,2) DEFAULT NULL,
  `calculado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `kpis_mensuales_año_mes_maquina_id_unique` (`año`,`mes`,`maquina_id`),
  KEY `kpis_mensuales_maquina_id_foreign` (`maquina_id`),
  KEY `kpis_mensuales_año_mes_index` (`año`,`mes`),
  CONSTRAINT `kpis_mensuales_maquina_id_foreign` FOREIGN KEY (`maquina_id`) REFERENCES `maquinas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `lotes_produccion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lotes_produccion` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `numero_lote` varchar(50) NOT NULL,
  `orden_id` bigint(20) unsigned NOT NULL,
  `cantidad` int(11) NOT NULL,
  `fecha_fabricacion` datetime NOT NULL,
  `fecha_vencimiento` date NOT NULL,
  `trazabilidad_insumos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`trazabilidad_insumos`)),
  `estado_lote` enum('cuarentena','aprobado','rechazado','distribuido') NOT NULL DEFAULT 'cuarentena',
  `ubicacion_almacen` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lotes_produccion_numero_lote_unique` (`numero_lote`),
  KEY `lotes_produccion_numero_lote_index` (`numero_lote`),
  KEY `lotes_produccion_orden_id_index` (`orden_id`),
  KEY `lotes_produccion_estado_lote_index` (`estado_lote`),
  CONSTRAINT `lotes_produccion_orden_id_foreign` FOREIGN KEY (`orden_id`) REFERENCES `ordenes_produccion` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `mantenimientos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mantenimientos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `maquina_id` bigint(20) unsigned NOT NULL,
  `tipo_mantenimiento` enum('preventivo','correctivo','predictivo') NOT NULL,
  `descripcion` text NOT NULL,
  `fecha_programada` datetime NOT NULL,
  `fecha_inicio` datetime DEFAULT NULL,
  `fecha_fin` datetime DEFAULT NULL,
  `duracion_horas` decimal(6,2) DEFAULT NULL,
  `costo` decimal(10,2) DEFAULT NULL,
  `tecnico_id` bigint(20) unsigned DEFAULT NULL,
  `estado` enum('programado','en_proceso','completado','cancelado') NOT NULL DEFAULT 'programado',
  `prioridad` enum('baja','media','alta','critica') NOT NULL DEFAULT 'media',
  `observaciones` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `mantenimientos_tecnico_id_foreign` (`tecnico_id`),
  KEY `mantenimientos_maquina_id_index` (`maquina_id`),
  KEY `mantenimientos_fecha_programada_index` (`fecha_programada`),
  KEY `mantenimientos_estado_index` (`estado`),
  KEY `mantenimientos_tipo_mantenimiento_index` (`tipo_mantenimiento`),
  CONSTRAINT `mantenimientos_maquina_id_foreign` FOREIGN KEY (`maquina_id`) REFERENCES `maquinas` (`id`),
  CONSTRAINT `mantenimientos_tecnico_id_foreign` FOREIGN KEY (`tecnico_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `maquinas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `maquinas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `codigo_maquina` varchar(50) NOT NULL,
  `nombre_maquina` varchar(150) NOT NULL,
  `tipo_maquina_id` bigint(20) unsigned NOT NULL,
  `marca` varchar(100) DEFAULT NULL,
  `modelo` varchar(100) DEFAULT NULL,
  `año_fabricacion` year(4) DEFAULT NULL,
  `capacidad_produccion` decimal(10,2) DEFAULT NULL COMMENT 'unidades o kg por hora',
  `unidad_capacidad` varchar(20) NOT NULL DEFAULT 'unidades/hora',
  `consumo_energia_kwh` decimal(8,2) DEFAULT NULL,
  `temp_min_operacion` decimal(5,1) DEFAULT NULL COMMENT '°C',
  `temp_max_operacion` decimal(5,1) DEFAULT NULL COMMENT '°C',
  `presion_max_bar` decimal(6,2) DEFAULT NULL COMMENT 'Bar',
  `velocidad_max_rpm` decimal(8,2) DEFAULT NULL COMMENT 'RPM',
  `fuerza_cierre_ton` decimal(8,2) DEFAULT NULL COMMENT 'Toneladas',
  `diametro_husillo_mm` decimal(6,2) DEFAULT NULL COMMENT 'mm',
  `fecha_instalacion` date DEFAULT NULL,
  `vida_util_años` int(11) NOT NULL DEFAULT 15,
  `ubicacion` varchar(100) DEFAULT NULL,
  `estado_actual` enum('operativa','mantenimiento','parada','averia') NOT NULL DEFAULT 'operativa',
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `maquinas_codigo_maquina_unique` (`codigo_maquina`),
  KEY `maquinas_tipo_maquina_id_foreign` (`tipo_maquina_id`),
  KEY `maquinas_codigo_maquina_index` (`codigo_maquina`),
  KEY `maquinas_estado_actual_index` (`estado_actual`),
  KEY `maquinas_activo_index` (`activo`),
  CONSTRAINT `maquinas_tipo_maquina_id_foreign` FOREIGN KEY (`tipo_maquina_id`) REFERENCES `tipos_maquina` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `movimientos_inventario_insumos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `movimientos_inventario_insumos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `insumo_id` bigint(20) unsigned NOT NULL,
  `tipo_movimiento` enum('entrada','salida','ajuste','desperdicio') NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `lote` varchar(50) DEFAULT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `costo_unitario` decimal(10,2) DEFAULT NULL,
  `usuario_id` bigint(20) unsigned NOT NULL,
  `motivo` text DEFAULT NULL,
  `fecha_movimiento` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `movimientos_inventario_insumos_usuario_id_foreign` (`usuario_id`),
  KEY `movimientos_inventario_insumos_fecha_movimiento_index` (`fecha_movimiento`),
  KEY `movimientos_inventario_insumos_insumo_id_index` (`insumo_id`),
  KEY `movimientos_inventario_insumos_tipo_movimiento_index` (`tipo_movimiento`),
  CONSTRAINT `movimientos_inventario_insumos_insumo_id_foreign` FOREIGN KEY (`insumo_id`) REFERENCES `insumos` (`id`),
  CONSTRAINT `movimientos_inventario_insumos_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `movimientos_inventario_productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `movimientos_inventario_productos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `producto_id` bigint(20) unsigned NOT NULL,
  `tipo_movimiento` enum('entrada_produccion','salida_venta','ajuste','merma','devolucion') NOT NULL,
  `cantidad` int(11) NOT NULL,
  `lote_produccion` varchar(50) DEFAULT NULL,
  `fecha_fabricacion` date DEFAULT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `usuario_id` bigint(20) unsigned NOT NULL,
  `referencia` varchar(100) DEFAULT NULL,
  `motivo` text DEFAULT NULL,
  `fecha_movimiento` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `movimientos_inventario_productos_usuario_id_foreign` (`usuario_id`),
  KEY `movimientos_inventario_productos_producto_id_index` (`producto_id`),
  KEY `movimientos_inventario_productos_fecha_movimiento_index` (`fecha_movimiento`),
  KEY `movimientos_inventario_productos_tipo_movimiento_index` (`tipo_movimiento`),
  CONSTRAINT `movimientos_inventario_productos_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`),
  CONSTRAINT `movimientos_inventario_productos_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ordenes_produccion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ordenes_produccion` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `numero_orden` varchar(50) NOT NULL,
  `producto_id` bigint(20) unsigned NOT NULL,
  `cantidad_planificada` int(11) NOT NULL,
  `cantidad_producida` int(11) NOT NULL DEFAULT 0,
  `cantidad_conforme` int(11) NOT NULL DEFAULT 0,
  `cantidad_defectuosa` int(11) NOT NULL DEFAULT 0,
  `formulacion_id` bigint(20) unsigned NOT NULL,
  `maquina_id` bigint(20) unsigned NOT NULL,
  `turno_id` bigint(20) unsigned NOT NULL,
  `fecha_programada` datetime NOT NULL,
  `fecha_inicio` datetime DEFAULT NULL,
  `fecha_fin` datetime DEFAULT NULL,
  `operador_id` bigint(20) unsigned DEFAULT NULL,
  `supervisor_id` bigint(20) unsigned DEFAULT NULL,
  `estado` enum('pendiente','en_proceso','pausada','completada','cancelada') NOT NULL DEFAULT 'pendiente',
  `prioridad` enum('baja','normal','alta','urgente') NOT NULL DEFAULT 'normal',
  `notas_produccion` text DEFAULT NULL,
  `observaciones_calidad` text DEFAULT NULL,
  `creado_por` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ordenes_produccion_numero_orden_unique` (`numero_orden`),
  KEY `ordenes_produccion_formulacion_id_foreign` (`formulacion_id`),
  KEY `ordenes_produccion_turno_id_foreign` (`turno_id`),
  KEY `ordenes_produccion_operador_id_foreign` (`operador_id`),
  KEY `ordenes_produccion_supervisor_id_foreign` (`supervisor_id`),
  KEY `ordenes_produccion_creado_por_foreign` (`creado_por`),
  KEY `ordenes_produccion_numero_orden_index` (`numero_orden`),
  KEY `ordenes_produccion_estado_index` (`estado`),
  KEY `ordenes_produccion_fecha_programada_index` (`fecha_programada`),
  KEY `ordenes_produccion_maquina_id_index` (`maquina_id`),
  KEY `ordenes_produccion_producto_id_index` (`producto_id`),
  CONSTRAINT `ordenes_produccion_creado_por_foreign` FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `ordenes_produccion_formulacion_id_foreign` FOREIGN KEY (`formulacion_id`) REFERENCES `formulaciones` (`id`),
  CONSTRAINT `ordenes_produccion_maquina_id_foreign` FOREIGN KEY (`maquina_id`) REFERENCES `maquinas` (`id`),
  CONSTRAINT `ordenes_produccion_operador_id_foreign` FOREIGN KEY (`operador_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ordenes_produccion_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`),
  CONSTRAINT `ordenes_produccion_supervisor_id_foreign` FOREIGN KEY (`supervisor_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ordenes_produccion_turno_id_foreign` FOREIGN KEY (`turno_id`) REFERENCES `turnos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `paros_maquina`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paros_maquina` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `maquina_id` bigint(20) unsigned NOT NULL,
  `tipo_paro` enum('averia','mantenimiento','cambio_molde','falta_material','falta_personal','ajuste_calidad','otros') NOT NULL,
  `fecha_inicio` datetime NOT NULL,
  `fecha_fin` datetime DEFAULT NULL,
  `duracion_minutos` int(11) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `causa_raiz` text DEFAULT NULL,
  `accion_correctiva` text DEFAULT NULL,
  `operador_id` bigint(20) unsigned NOT NULL,
  `impacto_produccion` decimal(10,2) DEFAULT NULL COMMENT 'unidades no producidas',
  `costo_estimado` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `paros_maquina_operador_id_foreign` (`operador_id`),
  KEY `paros_maquina_maquina_id_index` (`maquina_id`),
  KEY `paros_maquina_fecha_inicio_index` (`fecha_inicio`),
  KEY `paros_maquina_tipo_paro_index` (`tipo_paro`),
  CONSTRAINT `paros_maquina_maquina_id_foreign` FOREIGN KEY (`maquina_id`) REFERENCES `maquinas` (`id`),
  CONSTRAINT `paros_maquina_operador_id_foreign` FOREIGN KEY (`operador_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `productos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `codigo_producto` varchar(50) NOT NULL,
  `nombre_producto` varchar(150) NOT NULL,
  `categoria_producto_id` bigint(20) unsigned NOT NULL,
  `descripcion` text DEFAULT NULL,
  `material_principal` enum('PLA','PHA','PBS','PBAT','Almidon','Mixto') NOT NULL,
  `certificacion_compostable` varchar(200) DEFAULT NULL,
  `tiempo_compostaje_dias` int(11) DEFAULT NULL,
  `capacidad_carga_kg` decimal(8,2) DEFAULT NULL,
  `peso_unitario_gramos` decimal(8,2) NOT NULL,
  `dimensiones` varchar(100) DEFAULT NULL,
  `color` varchar(50) NOT NULL DEFAULT 'natural',
  `espesor_micras` int(11) DEFAULT NULL,
  `formulacion_id` bigint(20) unsigned DEFAULT NULL,
  `tiempo_ciclo_segundos` int(11) DEFAULT NULL,
  `piezas_por_ciclo` int(11) NOT NULL DEFAULT 1,
  `temperatura_proceso` decimal(5,1) DEFAULT NULL,
  `precio_venta` decimal(10,2) NOT NULL,
  `unidad_venta` enum('unidad','paquete','caja','kg') NOT NULL DEFAULT 'unidad',
  `unidades_por_paquete` int(11) NOT NULL DEFAULT 1,
  `stock_minimo` int(11) NOT NULL DEFAULT 0,
  `stock_actual` int(11) NOT NULL DEFAULT 0,
  `imagen_producto` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `productos_codigo_producto_unique` (`codigo_producto`),
  KEY `productos_categoria_producto_id_foreign` (`categoria_producto_id`),
  KEY `productos_formulacion_id_foreign` (`formulacion_id`),
  KEY `productos_codigo_producto_index` (`codigo_producto`),
  KEY `productos_activo_index` (`activo`),
  KEY `productos_stock_actual_index` (`stock_actual`),
  CONSTRAINT `productos_categoria_producto_id_foreign` FOREIGN KEY (`categoria_producto_id`) REFERENCES `categorias_productos` (`id`),
  CONSTRAINT `productos_formulacion_id_foreign` FOREIGN KEY (`formulacion_id`) REFERENCES `formulaciones` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `registro_defectos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `registro_defectos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `inspeccion_id` bigint(20) unsigned NOT NULL,
  `defecto_id` bigint(20) unsigned NOT NULL,
  `cantidad` int(11) NOT NULL,
  `ubicacion_pieza` varchar(100) DEFAULT NULL,
  `imagen_evidencia` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `registro_defectos_defecto_id_foreign` (`defecto_id`),
  KEY `registro_defectos_inspeccion_id_index` (`inspeccion_id`),
  CONSTRAINT `registro_defectos_defecto_id_foreign` FOREIGN KEY (`defecto_id`) REFERENCES `defectos_calidad` (`id`),
  CONSTRAINT `registro_defectos_inspeccion_id_foreign` FOREIGN KEY (`inspeccion_id`) REFERENCES `inspecciones_calidad` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `registros_produccion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `registros_produccion` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `orden_id` bigint(20) unsigned NOT NULL,
  `maquina_id` bigint(20) unsigned NOT NULL,
  `operador_id` bigint(20) unsigned NOT NULL,
  `fecha_hora` datetime NOT NULL,
  `piezas_producidas` int(11) NOT NULL DEFAULT 0,
  `piezas_conformes` int(11) NOT NULL DEFAULT 0,
  `piezas_defectuosas` int(11) NOT NULL DEFAULT 0,
  `tipo_defecto` varchar(100) DEFAULT NULL,
  `temperatura_zona1` decimal(5,1) DEFAULT NULL COMMENT '°C',
  `temperatura_zona2` decimal(5,1) DEFAULT NULL COMMENT '°C',
  `temperatura_zona3` decimal(5,1) DEFAULT NULL COMMENT '°C',
  `temperatura_zona4` decimal(5,1) DEFAULT NULL COMMENT '°C',
  `presion_inyeccion` decimal(6,2) DEFAULT NULL COMMENT 'Bar',
  `velocidad_husillo` decimal(7,2) DEFAULT NULL COMMENT 'RPM',
  `tiempo_ciclo_real` decimal(6,2) DEFAULT NULL COMMENT 'segundos',
  `consumo_energia_kwh` decimal(8,3) DEFAULT NULL,
  `consumo_material_kg` decimal(10,3) DEFAULT NULL,
  `scrap_kg` decimal(10,3) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `alerta_calidad` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `registros_produccion_maquina_id_foreign` (`maquina_id`),
  KEY `registros_produccion_operador_id_foreign` (`operador_id`),
  KEY `registros_produccion_fecha_hora_index` (`fecha_hora`),
  KEY `registros_produccion_orden_id_maquina_id_index` (`orden_id`,`maquina_id`),
  KEY `registros_produccion_alerta_calidad_index` (`alerta_calidad`),
  CONSTRAINT `registros_produccion_maquina_id_foreign` FOREIGN KEY (`maquina_id`) REFERENCES `maquinas` (`id`),
  CONSTRAINT `registros_produccion_operador_id_foreign` FOREIGN KEY (`operador_id`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `registros_produccion_orden_id_foreign` FOREIGN KEY (`orden_id`) REFERENCES `ordenes_produccion` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre_rol` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `nivel_acceso` enum('basico','intermedio','avanzado','total') NOT NULL DEFAULT 'basico',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_nombre_rol_unique` (`nombre_rol`),
  KEY `roles_nombre_rol_index` (`nombre_rol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tipos_maquina`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipos_maquina` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre_tipo` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `turnos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `turnos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre_turno` varchar(50) NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `turnos_activo_index` (`activo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `ultimo_acceso` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre_completo` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol_id` bigint(20) unsigned NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `ultimo_acceso` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuarios_email_unique` (`email`),
  KEY `usuarios_email_index` (`email`),
  KEY `usuarios_activo_index` (`activo`),
  KEY `usuarios_rol_id_index` (`rol_id`),
  CONSTRAINT `usuarios_rol_id_foreign` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'0001_01_01_000000_create_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'0001_01_01_000001_create_cache_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'0001_01_01_000002_create_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'2025_01_01_000001_create_roles_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2025_01_01_000002_create_usuarios_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2025_01_01_000003_create_turnos_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2025_01_01_000004_create_asignacion_turnos_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2025_01_01_000010_create_categorias_insumos_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9,'2025_01_01_000011_create_insumos_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10,'2025_01_01_000012_create_movimientos_inventario_insumos_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (11,'2025_01_01_000020_create_formulaciones_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (12,'2025_01_01_000021_create_componentes_formulacion_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (13,'2025_01_01_000030_create_tipos_maquina_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (14,'2025_01_01_000031_create_maquinas_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (15,'2025_01_01_000032_create_mantenimientos_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (16,'2025_01_01_000033_create_paros_maquina_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (17,'2025_01_01_000040_create_categorias_productos_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (18,'2025_01_01_000041_create_productos_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (19,'2025_01_01_000042_create_movimientos_inventario_productos_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (20,'2025_01_01_000050_create_ordenes_produccion_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (21,'2025_01_01_000051_create_lotes_produccion_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (22,'2025_01_01_000052_create_registros_produccion_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (23,'2025_01_01_000060_create_inspecciones_calidad_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (24,'2025_01_01_000061_create_defectos_calidad_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (25,'2025_01_01_000062_create_registro_defectos_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (26,'2025_01_01_000070_create_kpis_diarios_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (27,'2025_01_01_000071_create_kpis_mensuales_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (28,'2025_01_01_000080_create_alertas_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (29,'2025_01_01_000090_create_certificaciones_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (30,'2025_01_01_000091_create_auditorias_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (31,'2025_11_15_023403_create_personal_access_tokens_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (32,'2025_11_16_045255_create_permission_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (33,'2025_11_17_053236_create_permission_tables',1);
