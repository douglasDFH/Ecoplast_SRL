-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 16-11-2025 a las 03:37:51
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `ecoplast_produccion`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alertas`
--

CREATE TABLE `alertas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tipo_alerta` enum('stock_bajo','maquina_parada','calidad_deficiente','mantenimiento_vencido','meta_no_cumplida','otro') NOT NULL,
  `severidad` enum('info','advertencia','critico') NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `mensaje` text NOT NULL,
  `entidad_tipo` varchar(50) DEFAULT NULL,
  `entidad_id` bigint(20) UNSIGNED DEFAULT NULL,
  `usuario_destino_id` bigint(20) UNSIGNED DEFAULT NULL,
  `leida` tinyint(1) NOT NULL DEFAULT 0,
  `fecha_lectura` timestamp NULL DEFAULT NULL,
  `accion_tomada` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asignacion_turnos`
--

CREATE TABLE `asignacion_turnos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `usuario_id` bigint(20) UNSIGNED NOT NULL,
  `turno_id` bigint(20) UNSIGNED NOT NULL,
  `fecha_asignacion` date NOT NULL,
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditorias`
--

CREATE TABLE `auditorias` (
  `id` bigint(20) UNSIGNED NOT NULL,
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
  `usuario_responsable_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias_insumos`
--

CREATE TABLE `categorias_insumos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre_categoria` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `es_biodegradable` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias_productos`
--

CREATE TABLE `categorias_productos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre_categoria` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `aplicacion` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `certificaciones`
--

CREATE TABLE `certificaciones` (
  `id` bigint(20) UNSIGNED NOT NULL,
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `componentes_formulacion`
--

CREATE TABLE `componentes_formulacion` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `formulacion_id` bigint(20) UNSIGNED NOT NULL,
  `insumo_id` bigint(20) UNSIGNED NOT NULL,
  `porcentaje` decimal(5,2) NOT NULL COMMENT 'Porcentaje en peso',
  `cantidad_base` decimal(10,3) NOT NULL COMMENT 'Cantidad para 100kg',
  `orden_adicion` tinyint(4) NOT NULL DEFAULT 1,
  `notas` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `defectos_calidad`
--

CREATE TABLE `defectos_calidad` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `codigo_defecto` varchar(20) NOT NULL,
  `nombre_defecto` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `severidad` enum('critico','mayor','menor') NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `formulaciones`
--

CREATE TABLE `formulaciones` (
  `id` bigint(20) UNSIGNED NOT NULL,
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
  `usuario_aprueba_id` bigint(20) UNSIGNED DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inspecciones_calidad`
--

CREATE TABLE `inspecciones_calidad` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `orden_id` bigint(20) UNSIGNED NOT NULL,
  `lote_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tipo_inspeccion` enum('primera_pieza','proceso','final','auditoria') NOT NULL,
  `fecha_hora` datetime NOT NULL,
  `inspector_id` bigint(20) UNSIGNED NOT NULL,
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
  `acciones_correctivas` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `insumos`
--

CREATE TABLE `insumos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `codigo_insumo` varchar(50) NOT NULL,
  `nombre_insumo` varchar(150) NOT NULL,
  `categoria_id` bigint(20) UNSIGNED NOT NULL,
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
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `job_batches`
--

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
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `kpis_diarios`
--

CREATE TABLE `kpis_diarios` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `fecha` date NOT NULL,
  `maquina_id` bigint(20) UNSIGNED NOT NULL,
  `turno_id` bigint(20) UNSIGNED NOT NULL,
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
  `calculado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `kpis_mensuales`
--

CREATE TABLE `kpis_mensuales` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `año` year(4) NOT NULL,
  `mes` tinyint(4) NOT NULL,
  `maquina_id` bigint(20) UNSIGNED DEFAULT NULL,
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
  `calculado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lotes_produccion`
--

CREATE TABLE `lotes_produccion` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `numero_lote` varchar(50) NOT NULL,
  `orden_id` bigint(20) UNSIGNED NOT NULL,
  `cantidad` int(11) NOT NULL,
  `fecha_fabricacion` datetime NOT NULL,
  `fecha_vencimiento` date NOT NULL,
  `trazabilidad_insumos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`trazabilidad_insumos`)),
  `estado_lote` enum('cuarentena','aprobado','rechazado','distribuido') NOT NULL DEFAULT 'cuarentena',
  `ubicacion_almacen` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mantenimientos`
--

CREATE TABLE `mantenimientos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `maquina_id` bigint(20) UNSIGNED NOT NULL,
  `tipo_mantenimiento` enum('preventivo','correctivo','predictivo') NOT NULL,
  `descripcion` text NOT NULL,
  `fecha_programada` datetime NOT NULL,
  `fecha_inicio` datetime DEFAULT NULL,
  `fecha_fin` datetime DEFAULT NULL,
  `duracion_horas` decimal(6,2) DEFAULT NULL,
  `costo` decimal(10,2) DEFAULT NULL,
  `tecnico_id` bigint(20) UNSIGNED DEFAULT NULL,
  `estado` enum('programado','en_proceso','completado','cancelado') NOT NULL DEFAULT 'programado',
  `prioridad` enum('baja','media','alta','critica') NOT NULL DEFAULT 'media',
  `observaciones` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `maquinas`
--

CREATE TABLE `maquinas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `codigo_maquina` varchar(50) NOT NULL,
  `nombre_maquina` varchar(150) NOT NULL,
  `tipo_maquina_id` bigint(20) UNSIGNED NOT NULL,
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
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `maquinas`
--

INSERT INTO `maquinas` (`id`, `codigo_maquina`, `nombre_maquina`, `tipo_maquina_id`, `marca`, `modelo`, `año_fabricacion`, `capacidad_produccion`, `unidad_capacidad`, `consumo_energia_kwh`, `temp_min_operacion`, `temp_max_operacion`, `presion_max_bar`, `velocidad_max_rpm`, `fuerza_cierre_ton`, `diametro_husillo_mm`, `fecha_instalacion`, `vida_util_años`, `ubicacion`, `estado_actual`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'INYECCION-001', 'Máquina de Inyección Arburg 320C', 1, 'Arburg', '320C', '2022', 150.00, 'unidades/hora', 25.00, 150.0, 300.0, 2000.00, 300.00, NULL, NULL, '2023-01-15', 15, 'Planta Principal - Línea 1', 'operativa', 1, '2025-11-16 03:43:17', '2025-11-16 03:43:17'),
(2, 'EXTRUSION-002', 'Extrusora KraussMaffei KMD 75-36', 2, 'KraussMaffei', 'KMD 75-36', '2021', 200.00, 'unidades/hora', 35.00, 140.0, 250.0, 150.00, 400.00, NULL, NULL, '2023-03-20', 12, 'Planta Principal - Línea 2', 'operativa', 1, '2025-11-16 03:43:17', '2025-11-16 03:43:17'),
(3, 'SOPLADO-003', 'Máquina de Soplo Sidel SBO 24', 3, 'Sidel', 'SBO 24', '2020', 18000.00, 'unidades/hora', 45.00, 160.0, 220.0, 40.00, 2000.00, NULL, NULL, '2023-05-10', 10, 'Planta Principal - Línea 3', 'mantenimiento', 1, '2025-11-16 03:43:17', '2025-11-16 03:43:17'),
(4, 'TERMOFORMADO-004', 'Termoformadora Illig RDM 54K', 4, 'Illig', 'RDM 54K', '2023', 12000.00, 'unidades/hora', 30.00, 120.0, 180.0, 8.00, 150.00, NULL, NULL, '2023-07-15', 14, 'Planta Principal - Línea 4', 'operativa', 1, '2025-11-16 03:43:17', '2025-11-16 03:43:17'),
(5, 'GRANULADOR-005', 'Granulador Rapid 150', 5, 'Rapid', '150', '2019', 250.00, 'unidades/hora', 20.00, 80.0, 150.0, 2.00, 800.00, NULL, NULL, '2023-09-01', 8, 'Área de Reciclaje', 'parada', 1, '2025-11-16 03:43:17', '2025-11-16 03:43:17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_01_01_000001_create_roles_table', 1),
(5, '2025_01_01_000002_create_usuarios_table', 1),
(6, '2025_01_01_000003_create_turnos_table', 1),
(7, '2025_01_01_000004_create_asignacion_turnos_table', 1),
(8, '2025_01_01_000010_create_categorias_insumos_table', 1),
(9, '2025_01_01_000011_create_insumos_table', 1),
(10, '2025_01_01_000012_create_movimientos_inventario_insumos_table', 1),
(11, '2025_01_01_000020_create_formulaciones_table', 1),
(12, '2025_01_01_000021_create_componentes_formulacion_table', 1),
(13, '2025_01_01_000030_create_tipos_maquina_table', 1),
(14, '2025_01_01_000031_create_maquinas_table', 1),
(15, '2025_01_01_000032_create_mantenimientos_table', 1),
(16, '2025_01_01_000033_create_paros_maquina_table', 1),
(17, '2025_01_01_000040_create_categorias_productos_table', 1),
(18, '2025_01_01_000041_create_productos_table', 1),
(19, '2025_01_01_000042_create_movimientos_inventario_productos_table', 1),
(20, '2025_01_01_000050_create_ordenes_produccion_table', 1),
(21, '2025_01_01_000051_create_lotes_produccion_table', 1),
(22, '2025_01_01_000052_create_registros_produccion_table', 1),
(23, '2025_01_01_000060_create_inspecciones_calidad_table', 1),
(24, '2025_01_01_000061_create_defectos_calidad_table', 1),
(25, '2025_01_01_000062_create_registro_defectos_table', 1),
(26, '2025_01_01_000070_create_kpis_diarios_table', 1),
(27, '2025_01_01_000071_create_kpis_mensuales_table', 1),
(28, '2025_01_01_000080_create_alertas_table', 1),
(29, '2025_01_01_000090_create_certificaciones_table', 1),
(30, '2025_01_01_000091_create_auditorias_table', 1),
(31, '2025_11_15_023403_create_personal_access_tokens_table', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos_inventario_insumos`
--

CREATE TABLE `movimientos_inventario_insumos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `insumo_id` bigint(20) UNSIGNED NOT NULL,
  `tipo_movimiento` enum('entrada','salida','ajuste','desperdicio') NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `lote` varchar(50) DEFAULT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `costo_unitario` decimal(10,2) DEFAULT NULL,
  `usuario_id` bigint(20) UNSIGNED NOT NULL,
  `motivo` text DEFAULT NULL,
  `fecha_movimiento` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos_inventario_productos`
--

CREATE TABLE `movimientos_inventario_productos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `producto_id` bigint(20) UNSIGNED NOT NULL,
  `tipo_movimiento` enum('entrada_produccion','salida_venta','ajuste','merma','devolucion') NOT NULL,
  `cantidad` int(11) NOT NULL,
  `lote_produccion` varchar(50) DEFAULT NULL,
  `fecha_fabricacion` date DEFAULT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `usuario_id` bigint(20) UNSIGNED NOT NULL,
  `referencia` varchar(100) DEFAULT NULL,
  `motivo` text DEFAULT NULL,
  `fecha_movimiento` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ordenes_produccion`
--

CREATE TABLE `ordenes_produccion` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `numero_orden` varchar(50) NOT NULL,
  `producto_id` bigint(20) UNSIGNED NOT NULL,
  `cantidad_planificada` int(11) NOT NULL,
  `cantidad_producida` int(11) NOT NULL DEFAULT 0,
  `cantidad_conforme` int(11) NOT NULL DEFAULT 0,
  `cantidad_defectuosa` int(11) NOT NULL DEFAULT 0,
  `formulacion_id` bigint(20) UNSIGNED NOT NULL,
  `maquina_id` bigint(20) UNSIGNED NOT NULL,
  `turno_id` bigint(20) UNSIGNED NOT NULL,
  `fecha_programada` datetime NOT NULL,
  `fecha_inicio` datetime DEFAULT NULL,
  `fecha_fin` datetime DEFAULT NULL,
  `operador_id` bigint(20) UNSIGNED DEFAULT NULL,
  `supervisor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `estado` enum('pendiente','en_proceso','pausada','completada','cancelada') NOT NULL DEFAULT 'pendiente',
  `prioridad` enum('baja','normal','alta','urgente') NOT NULL DEFAULT 'normal',
  `notas_produccion` text DEFAULT NULL,
  `observaciones_calidad` text DEFAULT NULL,
  `creado_por` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paros_maquina`
--

CREATE TABLE `paros_maquina` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `maquina_id` bigint(20) UNSIGNED NOT NULL,
  `tipo_paro` enum('averia','mantenimiento','cambio_molde','falta_material','falta_personal','ajuste_calidad','otros') NOT NULL,
  `fecha_inicio` datetime NOT NULL,
  `fecha_fin` datetime DEFAULT NULL,
  `duracion_minutos` int(11) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `causa_raiz` text DEFAULT NULL,
  `accion_correctiva` text DEFAULT NULL,
  `operador_id` bigint(20) UNSIGNED NOT NULL,
  `impacto_produccion` decimal(10,2) DEFAULT NULL COMMENT 'unidades no producidas',
  `costo_estimado` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `codigo_producto` varchar(50) NOT NULL,
  `nombre_producto` varchar(150) NOT NULL,
  `categoria_producto_id` bigint(20) UNSIGNED NOT NULL,
  `descripcion` text DEFAULT NULL,
  `material_principal` enum('PLA','PHA','PBS','PBAT','Almidon','Mixto') NOT NULL,
  `certificacion_compostable` varchar(200) DEFAULT NULL,
  `tiempo_compostaje_dias` int(11) DEFAULT NULL,
  `capacidad_carga_kg` decimal(8,2) DEFAULT NULL,
  `peso_unitario_gramos` decimal(8,2) NOT NULL,
  `dimensiones` varchar(100) DEFAULT NULL,
  `color` varchar(50) NOT NULL DEFAULT 'natural',
  `espesor_micras` int(11) DEFAULT NULL,
  `formulacion_id` bigint(20) UNSIGNED DEFAULT NULL,
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
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registros_produccion`
--

CREATE TABLE `registros_produccion` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `orden_id` bigint(20) UNSIGNED NOT NULL,
  `maquina_id` bigint(20) UNSIGNED NOT NULL,
  `operador_id` bigint(20) UNSIGNED NOT NULL,
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
  `alerta_calidad` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registro_defectos`
--

CREATE TABLE `registro_defectos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `inspeccion_id` bigint(20) UNSIGNED NOT NULL,
  `defecto_id` bigint(20) UNSIGNED NOT NULL,
  `cantidad` int(11) NOT NULL,
  `ubicacion_pieza` varchar(100) DEFAULT NULL,
  `imagen_evidencia` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre_rol` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `nivel_acceso` enum('basico','intermedio','avanzado','total') NOT NULL DEFAULT 'basico',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_maquina`
--

CREATE TABLE `tipos_maquina` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre_tipo` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tipos_maquina`
--

INSERT INTO `tipos_maquina` (`id`, `nombre_tipo`, `descripcion`) VALUES
(1, 'Extrusora', 'Máquina de extrusión para films y láminas de bioplásticos'),
(2, 'Inyectora', 'Máquina de inyección para envases y tapas biodegradables'),
(3, 'Sopladora', 'Máquina de soplado para botellas y contenedores biodegradables'),
(4, 'Termoformadora', 'Equipo de termoformado para bandejas y vasos biodegradables'),
(5, 'Granuladora', 'Equipo de granulación para reciclaje de material biodegradable');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `turnos`
--

CREATE TABLE `turnos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre_turno` varchar(50) NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `turnos`
--

INSERT INTO `turnos` (`id`, `nombre_turno`, `hora_inicio`, `hora_fin`, `activo`) VALUES
(1, 'Matutino', '06:00:00', '14:00:00', 1),
(2, 'Vespertino', '14:00:00', '22:00:00', 1),
(3, 'Nocturno', '22:00:00', '06:00:00', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@ecoplast.com', '2025-11-16 03:43:17', '$2y$12$vO.3DZd9zp0ysSf0/XBBau9TY8SfqP/v3VKKTUAq8C/PM0YMhVua.', NULL, '2025-11-16 03:43:17', '2025-11-16 03:43:17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre_completo` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol_id` bigint(20) UNSIGNED NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `ultimo_acceso` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alertas`
--
ALTER TABLE `alertas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `alertas_usuario_destino_id_index` (`usuario_destino_id`),
  ADD KEY `alertas_leida_index` (`leida`),
  ADD KEY `alertas_severidad_index` (`severidad`),
  ADD KEY `alertas_created_at_index` (`created_at`);

--
-- Indices de la tabla `asignacion_turnos`
--
ALTER TABLE `asignacion_turnos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `asignacion_turnos_usuario_id_fecha_asignacion_unique` (`usuario_id`,`fecha_asignacion`),
  ADD KEY `asignacion_turnos_turno_id_foreign` (`turno_id`),
  ADD KEY `asignacion_turnos_fecha_asignacion_index` (`fecha_asignacion`);

--
-- Indices de la tabla `auditorias`
--
ALTER TABLE `auditorias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `auditorias_usuario_responsable_id_foreign` (`usuario_responsable_id`),
  ADD KEY `auditorias_fecha_auditoria_index` (`fecha_auditoria`),
  ADD KEY `auditorias_tipo_auditoria_index` (`tipo_auditoria`);

--
-- Indices de la tabla `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indices de la tabla `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indices de la tabla `categorias_insumos`
--
ALTER TABLE `categorias_insumos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `categorias_productos`
--
ALTER TABLE `categorias_productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `certificaciones`
--
ALTER TABLE `certificaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `certificaciones_fecha_vencimiento_index` (`fecha_vencimiento`),
  ADD KEY `certificaciones_estado_index` (`estado`);

--
-- Indices de la tabla `componentes_formulacion`
--
ALTER TABLE `componentes_formulacion`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `componentes_formulacion_formulacion_id_insumo_id_unique` (`formulacion_id`,`insumo_id`),
  ADD KEY `componentes_formulacion_insumo_id_foreign` (`insumo_id`),
  ADD KEY `componentes_formulacion_formulacion_id_index` (`formulacion_id`);

--
-- Indices de la tabla `defectos_calidad`
--
ALTER TABLE `defectos_calidad`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `defectos_calidad_codigo_defecto_unique` (`codigo_defecto`),
  ADD KEY `defectos_calidad_codigo_defecto_index` (`codigo_defecto`);

--
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indices de la tabla `formulaciones`
--
ALTER TABLE `formulaciones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `formulaciones_codigo_formula_unique` (`codigo_formula`),
  ADD KEY `formulaciones_usuario_aprueba_id_foreign` (`usuario_aprueba_id`),
  ADD KEY `formulaciones_codigo_formula_index` (`codigo_formula`),
  ADD KEY `formulaciones_aprobado_index` (`aprobado`),
  ADD KEY `formulaciones_activo_index` (`activo`);

--
-- Indices de la tabla `inspecciones_calidad`
--
ALTER TABLE `inspecciones_calidad`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inspecciones_calidad_inspector_id_foreign` (`inspector_id`),
  ADD KEY `inspecciones_calidad_orden_id_index` (`orden_id`),
  ADD KEY `inspecciones_calidad_lote_id_index` (`lote_id`),
  ADD KEY `inspecciones_calidad_fecha_hora_index` (`fecha_hora`),
  ADD KEY `inspecciones_calidad_resultado_index` (`resultado`);

--
-- Indices de la tabla `insumos`
--
ALTER TABLE `insumos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `insumos_codigo_insumo_unique` (`codigo_insumo`),
  ADD KEY `insumos_categoria_id_foreign` (`categoria_id`),
  ADD KEY `insumos_codigo_insumo_index` (`codigo_insumo`),
  ADD KEY `insumos_activo_index` (`activo`),
  ADD KEY `insumos_stock_actual_index` (`stock_actual`),
  ADD KEY `insumos_tipo_material_index` (`tipo_material`);

--
-- Indices de la tabla `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indices de la tabla `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `kpis_diarios`
--
ALTER TABLE `kpis_diarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kpis_diarios_fecha_maquina_id_turno_id_unique` (`fecha`,`maquina_id`,`turno_id`),
  ADD KEY `kpis_diarios_turno_id_foreign` (`turno_id`),
  ADD KEY `kpis_diarios_fecha_index` (`fecha`),
  ADD KEY `kpis_diarios_maquina_id_index` (`maquina_id`),
  ADD KEY `kpis_diarios_oee_index` (`oee`);

--
-- Indices de la tabla `kpis_mensuales`
--
ALTER TABLE `kpis_mensuales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kpis_mensuales_año_mes_maquina_id_unique` (`año`,`mes`,`maquina_id`),
  ADD KEY `kpis_mensuales_maquina_id_foreign` (`maquina_id`),
  ADD KEY `kpis_mensuales_año_mes_index` (`año`,`mes`);

--
-- Indices de la tabla `lotes_produccion`
--
ALTER TABLE `lotes_produccion`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lotes_produccion_numero_lote_unique` (`numero_lote`),
  ADD KEY `lotes_produccion_numero_lote_index` (`numero_lote`),
  ADD KEY `lotes_produccion_orden_id_index` (`orden_id`),
  ADD KEY `lotes_produccion_estado_lote_index` (`estado_lote`);

--
-- Indices de la tabla `mantenimientos`
--
ALTER TABLE `mantenimientos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mantenimientos_tecnico_id_foreign` (`tecnico_id`),
  ADD KEY `mantenimientos_maquina_id_index` (`maquina_id`),
  ADD KEY `mantenimientos_fecha_programada_index` (`fecha_programada`),
  ADD KEY `mantenimientos_estado_index` (`estado`),
  ADD KEY `mantenimientos_tipo_mantenimiento_index` (`tipo_mantenimiento`);

--
-- Indices de la tabla `maquinas`
--
ALTER TABLE `maquinas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `maquinas_codigo_maquina_unique` (`codigo_maquina`),
  ADD KEY `maquinas_tipo_maquina_id_foreign` (`tipo_maquina_id`),
  ADD KEY `maquinas_codigo_maquina_index` (`codigo_maquina`),
  ADD KEY `maquinas_estado_actual_index` (`estado_actual`),
  ADD KEY `maquinas_activo_index` (`activo`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `movimientos_inventario_insumos`
--
ALTER TABLE `movimientos_inventario_insumos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `movimientos_inventario_insumos_usuario_id_foreign` (`usuario_id`),
  ADD KEY `movimientos_inventario_insumos_fecha_movimiento_index` (`fecha_movimiento`),
  ADD KEY `movimientos_inventario_insumos_insumo_id_index` (`insumo_id`),
  ADD KEY `movimientos_inventario_insumos_tipo_movimiento_index` (`tipo_movimiento`);

--
-- Indices de la tabla `movimientos_inventario_productos`
--
ALTER TABLE `movimientos_inventario_productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `movimientos_inventario_productos_usuario_id_foreign` (`usuario_id`),
  ADD KEY `movimientos_inventario_productos_producto_id_index` (`producto_id`),
  ADD KEY `movimientos_inventario_productos_fecha_movimiento_index` (`fecha_movimiento`),
  ADD KEY `movimientos_inventario_productos_tipo_movimiento_index` (`tipo_movimiento`);

--
-- Indices de la tabla `ordenes_produccion`
--
ALTER TABLE `ordenes_produccion`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ordenes_produccion_numero_orden_unique` (`numero_orden`),
  ADD KEY `ordenes_produccion_formulacion_id_foreign` (`formulacion_id`),
  ADD KEY `ordenes_produccion_turno_id_foreign` (`turno_id`),
  ADD KEY `ordenes_produccion_operador_id_foreign` (`operador_id`),
  ADD KEY `ordenes_produccion_supervisor_id_foreign` (`supervisor_id`),
  ADD KEY `ordenes_produccion_creado_por_foreign` (`creado_por`),
  ADD KEY `ordenes_produccion_numero_orden_index` (`numero_orden`),
  ADD KEY `ordenes_produccion_estado_index` (`estado`),
  ADD KEY `ordenes_produccion_fecha_programada_index` (`fecha_programada`),
  ADD KEY `ordenes_produccion_maquina_id_index` (`maquina_id`),
  ADD KEY `ordenes_produccion_producto_id_index` (`producto_id`);

--
-- Indices de la tabla `paros_maquina`
--
ALTER TABLE `paros_maquina`
  ADD PRIMARY KEY (`id`),
  ADD KEY `paros_maquina_operador_id_foreign` (`operador_id`),
  ADD KEY `paros_maquina_maquina_id_index` (`maquina_id`),
  ADD KEY `paros_maquina_fecha_inicio_index` (`fecha_inicio`),
  ADD KEY `paros_maquina_tipo_paro_index` (`tipo_paro`);

--
-- Indices de la tabla `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indices de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `productos_codigo_producto_unique` (`codigo_producto`),
  ADD KEY `productos_categoria_producto_id_foreign` (`categoria_producto_id`),
  ADD KEY `productos_formulacion_id_foreign` (`formulacion_id`),
  ADD KEY `productos_codigo_producto_index` (`codigo_producto`),
  ADD KEY `productos_activo_index` (`activo`),
  ADD KEY `productos_stock_actual_index` (`stock_actual`);

--
-- Indices de la tabla `registros_produccion`
--
ALTER TABLE `registros_produccion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `registros_produccion_maquina_id_foreign` (`maquina_id`),
  ADD KEY `registros_produccion_operador_id_foreign` (`operador_id`),
  ADD KEY `registros_produccion_fecha_hora_index` (`fecha_hora`),
  ADD KEY `registros_produccion_orden_id_maquina_id_index` (`orden_id`,`maquina_id`),
  ADD KEY `registros_produccion_alerta_calidad_index` (`alerta_calidad`);

--
-- Indices de la tabla `registro_defectos`
--
ALTER TABLE `registro_defectos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `registro_defectos_defecto_id_foreign` (`defecto_id`),
  ADD KEY `registro_defectos_inspeccion_id_index` (`inspeccion_id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_nombre_rol_unique` (`nombre_rol`),
  ADD KEY `roles_nombre_rol_index` (`nombre_rol`);

--
-- Indices de la tabla `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indices de la tabla `tipos_maquina`
--
ALTER TABLE `tipos_maquina`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `turnos`
--
ALTER TABLE `turnos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `turnos_activo_index` (`activo`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuarios_email_unique` (`email`),
  ADD KEY `usuarios_email_index` (`email`),
  ADD KEY `usuarios_activo_index` (`activo`),
  ADD KEY `usuarios_rol_id_index` (`rol_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `alertas`
--
ALTER TABLE `alertas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `asignacion_turnos`
--
ALTER TABLE `asignacion_turnos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `auditorias`
--
ALTER TABLE `auditorias`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `categorias_insumos`
--
ALTER TABLE `categorias_insumos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `categorias_productos`
--
ALTER TABLE `categorias_productos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `certificaciones`
--
ALTER TABLE `certificaciones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `componentes_formulacion`
--
ALTER TABLE `componentes_formulacion`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `defectos_calidad`
--
ALTER TABLE `defectos_calidad`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `formulaciones`
--
ALTER TABLE `formulaciones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `inspecciones_calidad`
--
ALTER TABLE `inspecciones_calidad`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `insumos`
--
ALTER TABLE `insumos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `kpis_diarios`
--
ALTER TABLE `kpis_diarios`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `kpis_mensuales`
--
ALTER TABLE `kpis_mensuales`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `lotes_produccion`
--
ALTER TABLE `lotes_produccion`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `mantenimientos`
--
ALTER TABLE `mantenimientos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `maquinas`
--
ALTER TABLE `maquinas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT de la tabla `movimientos_inventario_insumos`
--
ALTER TABLE `movimientos_inventario_insumos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `movimientos_inventario_productos`
--
ALTER TABLE `movimientos_inventario_productos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ordenes_produccion`
--
ALTER TABLE `ordenes_produccion`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `paros_maquina`
--
ALTER TABLE `paros_maquina`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `registros_produccion`
--
ALTER TABLE `registros_produccion`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `registro_defectos`
--
ALTER TABLE `registro_defectos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tipos_maquina`
--
ALTER TABLE `tipos_maquina`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `turnos`
--
ALTER TABLE `turnos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `alertas`
--
ALTER TABLE `alertas`
  ADD CONSTRAINT `alertas_usuario_destino_id_foreign` FOREIGN KEY (`usuario_destino_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `asignacion_turnos`
--
ALTER TABLE `asignacion_turnos`
  ADD CONSTRAINT `asignacion_turnos_turno_id_foreign` FOREIGN KEY (`turno_id`) REFERENCES `turnos` (`id`),
  ADD CONSTRAINT `asignacion_turnos_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `auditorias`
--
ALTER TABLE `auditorias`
  ADD CONSTRAINT `auditorias_usuario_responsable_id_foreign` FOREIGN KEY (`usuario_responsable_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `componentes_formulacion`
--
ALTER TABLE `componentes_formulacion`
  ADD CONSTRAINT `componentes_formulacion_formulacion_id_foreign` FOREIGN KEY (`formulacion_id`) REFERENCES `formulaciones` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `componentes_formulacion_insumo_id_foreign` FOREIGN KEY (`insumo_id`) REFERENCES `insumos` (`id`);

--
-- Filtros para la tabla `formulaciones`
--
ALTER TABLE `formulaciones`
  ADD CONSTRAINT `formulaciones_usuario_aprueba_id_foreign` FOREIGN KEY (`usuario_aprueba_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `inspecciones_calidad`
--
ALTER TABLE `inspecciones_calidad`
  ADD CONSTRAINT `inspecciones_calidad_inspector_id_foreign` FOREIGN KEY (`inspector_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `inspecciones_calidad_lote_id_foreign` FOREIGN KEY (`lote_id`) REFERENCES `lotes_produccion` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inspecciones_calidad_orden_id_foreign` FOREIGN KEY (`orden_id`) REFERENCES `ordenes_produccion` (`id`);

--
-- Filtros para la tabla `insumos`
--
ALTER TABLE `insumos`
  ADD CONSTRAINT `insumos_categoria_id_foreign` FOREIGN KEY (`categoria_id`) REFERENCES `categorias_insumos` (`id`);

--
-- Filtros para la tabla `kpis_diarios`
--
ALTER TABLE `kpis_diarios`
  ADD CONSTRAINT `kpis_diarios_maquina_id_foreign` FOREIGN KEY (`maquina_id`) REFERENCES `maquinas` (`id`),
  ADD CONSTRAINT `kpis_diarios_turno_id_foreign` FOREIGN KEY (`turno_id`) REFERENCES `turnos` (`id`);

--
-- Filtros para la tabla `kpis_mensuales`
--
ALTER TABLE `kpis_mensuales`
  ADD CONSTRAINT `kpis_mensuales_maquina_id_foreign` FOREIGN KEY (`maquina_id`) REFERENCES `maquinas` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `lotes_produccion`
--
ALTER TABLE `lotes_produccion`
  ADD CONSTRAINT `lotes_produccion_orden_id_foreign` FOREIGN KEY (`orden_id`) REFERENCES `ordenes_produccion` (`id`);

--
-- Filtros para la tabla `mantenimientos`
--
ALTER TABLE `mantenimientos`
  ADD CONSTRAINT `mantenimientos_maquina_id_foreign` FOREIGN KEY (`maquina_id`) REFERENCES `maquinas` (`id`),
  ADD CONSTRAINT `mantenimientos_tecnico_id_foreign` FOREIGN KEY (`tecnico_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `maquinas`
--
ALTER TABLE `maquinas`
  ADD CONSTRAINT `maquinas_tipo_maquina_id_foreign` FOREIGN KEY (`tipo_maquina_id`) REFERENCES `tipos_maquina` (`id`);

--
-- Filtros para la tabla `movimientos_inventario_insumos`
--
ALTER TABLE `movimientos_inventario_insumos`
  ADD CONSTRAINT `movimientos_inventario_insumos_insumo_id_foreign` FOREIGN KEY (`insumo_id`) REFERENCES `insumos` (`id`),
  ADD CONSTRAINT `movimientos_inventario_insumos_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `movimientos_inventario_productos`
--
ALTER TABLE `movimientos_inventario_productos`
  ADD CONSTRAINT `movimientos_inventario_productos_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`),
  ADD CONSTRAINT `movimientos_inventario_productos_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `ordenes_produccion`
--
ALTER TABLE `ordenes_produccion`
  ADD CONSTRAINT `ordenes_produccion_creado_por_foreign` FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `ordenes_produccion_formulacion_id_foreign` FOREIGN KEY (`formulacion_id`) REFERENCES `formulaciones` (`id`),
  ADD CONSTRAINT `ordenes_produccion_maquina_id_foreign` FOREIGN KEY (`maquina_id`) REFERENCES `maquinas` (`id`),
  ADD CONSTRAINT `ordenes_produccion_operador_id_foreign` FOREIGN KEY (`operador_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ordenes_produccion_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`),
  ADD CONSTRAINT `ordenes_produccion_supervisor_id_foreign` FOREIGN KEY (`supervisor_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ordenes_produccion_turno_id_foreign` FOREIGN KEY (`turno_id`) REFERENCES `turnos` (`id`);

--
-- Filtros para la tabla `paros_maquina`
--
ALTER TABLE `paros_maquina`
  ADD CONSTRAINT `paros_maquina_maquina_id_foreign` FOREIGN KEY (`maquina_id`) REFERENCES `maquinas` (`id`),
  ADD CONSTRAINT `paros_maquina_operador_id_foreign` FOREIGN KEY (`operador_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_categoria_producto_id_foreign` FOREIGN KEY (`categoria_producto_id`) REFERENCES `categorias_productos` (`id`),
  ADD CONSTRAINT `productos_formulacion_id_foreign` FOREIGN KEY (`formulacion_id`) REFERENCES `formulaciones` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `registros_produccion`
--
ALTER TABLE `registros_produccion`
  ADD CONSTRAINT `registros_produccion_maquina_id_foreign` FOREIGN KEY (`maquina_id`) REFERENCES `maquinas` (`id`),
  ADD CONSTRAINT `registros_produccion_operador_id_foreign` FOREIGN KEY (`operador_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `registros_produccion_orden_id_foreign` FOREIGN KEY (`orden_id`) REFERENCES `ordenes_produccion` (`id`);

--
-- Filtros para la tabla `registro_defectos`
--
ALTER TABLE `registro_defectos`
  ADD CONSTRAINT `registro_defectos_defecto_id_foreign` FOREIGN KEY (`defecto_id`) REFERENCES `defectos_calidad` (`id`),
  ADD CONSTRAINT `registro_defectos_inspeccion_id_foreign` FOREIGN KEY (`inspeccion_id`) REFERENCES `inspecciones_calidad` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_rol_id_foreign` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
