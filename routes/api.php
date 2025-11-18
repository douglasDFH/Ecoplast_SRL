<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\InsumoController;
use App\Http\Controllers\Api\ProductoTerminadoController;
use App\Http\Controllers\Api\MaquinariaController;
use App\Http\Controllers\Api\OrdenProduccionController;
use App\Http\Controllers\Api\RegistroProduccionController;
use App\Http\Controllers\Api\LoteProduccionController;
use App\Http\Controllers\Api\MantenimientoController;
use App\Http\Controllers\Api\InspeccionCalidadController;
use App\Http\Controllers\Api\AlertaController;
use App\Http\Controllers\Api\KpiController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\TurnoController;
use App\Http\Controllers\Api\FormulacionController;
use App\Http\Controllers\Api\ReporteController;
use App\Http\Controllers\Api\CategoriaInsumoController;
use App\Http\Controllers\Api\TipoMaterialController;
use App\Http\Controllers\Api\ProveedorController;
use App\Http\Controllers\Api\RolePermissionController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\MovimientoInventarioController;
use App\Http\Controllers\Api\PanelMaquinasController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum,web'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Gestión de Roles y Permisos (Spatie)
    Route::get('/spatie/roles', [RolePermissionController::class, 'indexRoles']);
    Route::post('/spatie/roles', [RolePermissionController::class, 'storeRole']);
    Route::put('/spatie/roles/{id}', [RolePermissionController::class, 'updateRole']);
    Route::delete('/spatie/roles/{id}', [RolePermissionController::class, 'destroyRole']);
    Route::get('/spatie/roles/{id}/permisos', [RolePermissionController::class, 'getRolePermissions']);
    Route::get('/spatie/permisos', [RolePermissionController::class, 'indexPermissions']);
    Route::post('/spatie/permisos', [RolePermissionController::class, 'storePermission']);
    Route::post('/spatie/roles/{id}/permisos', [RolePermissionController::class, 'assignPermissionsToRole']);

    // Gestión de Usuarios
    Route::apiResource('usuarios', UserController::class);
    Route::post('usuarios/{usuario}/reset-password', [UserController::class, 'resetPassword']);

    // Dashboard Principal
    Route::get('dashboard', [DashboardController::class, 'index']);
    Route::get('dashboard/produccion-semanal', [DashboardController::class, 'produccionSemanal']);
    Route::get('dashboard/produccion-turno', [DashboardController::class, 'produccionPorTurno']);
    Route::get('dashboard/mantenimientos-proximos', [DashboardController::class, 'mantenimientosProximos']);
    Route::get('dashboard/top-productos', [DashboardController::class, 'topProductos']);

    // KPIs
    Route::get('kpis/dashboard', [KpiController::class, 'dashboard']);
    Route::get('kpis/produccion', [KpiController::class, 'produccion']);
    Route::get('kpis/calidad', [KpiController::class, 'calidad']);
    Route::get('kpis/inventario', [KpiController::class, 'inventario']);
    Route::get('kpis/mantenimiento', [KpiController::class, 'mantenimiento']);
    Route::get('kpis/oee', [KpiController::class, 'oee']);

    // Rutas para Categorías de Insumos
    Route::apiResource('categorias-insumos', CategoriaInsumoController::class);

    // Rutas para Tipos de Materiales
    Route::apiResource('tipos-materiales', TipoMaterialController::class);
    Route::get('tipos-materiales/estadisticas/general', [TipoMaterialController::class, 'estadisticas']);
    Route::get('tipos-materiales/clasificacion/{clasificacion}', [TipoMaterialController::class, 'porClasificacion']);
    Route::get('tipos-materiales/biodegradables', [TipoMaterialController::class, 'biodegradables']);
    Route::patch('tipos-materiales/{id}/toggle-activo', [TipoMaterialController::class, 'toggleActivo']);

    // Rutas para Proveedores
    Route::apiResource('proveedores', ProveedorController::class);
    Route::get('proveedores/estadisticas/general', [ProveedorController::class, 'estadisticas']);
    Route::get('proveedores/ciudad/{ciudad}', [ProveedorController::class, 'porCiudad']);
    Route::get('proveedores/pais/{pais}', [ProveedorController::class, 'porPais']);
    Route::patch('proveedores/{id}/toggle-activo', [ProveedorController::class, 'toggleActivo']);
    Route::get('proveedores/{id}/insumos', [ProveedorController::class, 'insumos']);

    // Rutas para Insumos biodegradables
    Route::apiResource('insumos', InsumoController::class);
    Route::get('insumos/estadisticas/general', [InsumoController::class, 'estadisticas']);
    Route::get('insumos/por-categoria/{categoria}', [InsumoController::class, 'porCategoria']);
    Route::get('insumos/stock-bajo', [InsumoController::class, 'stockBajo']);
    Route::get('insumos/biodegradables', [InsumoController::class, 'biodegradables']);

    // Rutas para Movimientos de Inventario
    Route::apiResource('movimientos-inventario', MovimientoInventarioController::class)->only(['index', 'store', 'show']);
    Route::get('movimientos-inventario/estadisticas/general', [MovimientoInventarioController::class, 'estadisticas']);
    Route::get('movimientos-inventario/insumo/{insumoId}', [MovimientoInventarioController::class, 'porInsumo']);
    Route::get('movimientos-inventario/resumen/inventario', [MovimientoInventarioController::class, 'resumen']);

    // Rutas para Productos Terminados biodegradables
    Route::apiResource('productos', ProductoTerminadoController::class);
    Route::get('productos/estadisticas/general', [ProductoTerminadoController::class, 'estadisticas']);
    Route::get('productos/por-sostenibilidad', [ProductoTerminadoController::class, 'porSostenibilidad']);

    // Rutas para Maquinaria
    Route::apiResource('maquinaria', MaquinariaController::class);
    Route::get('maquinaria/estadisticas/general', [MaquinariaController::class, 'estadisticas']);
    Route::get('maquinaria/por-estado', [MaquinariaController::class, 'porEstado']);
    Route::get('maquinaria/necesitan-mantenimiento', [MaquinariaController::class, 'necesitanMantenimiento']);
    Route::patch('maquinaria/{maquina}/estado', [MaquinariaController::class, 'actualizarEstado']);

    // Rutas para Panel de Máquinas (Simulación en Tiempo Real)
    Route::get('panel-maquinas', [PanelMaquinasController::class, 'index']);
    Route::post('panel-maquinas/iniciar-produccion', [PanelMaquinasController::class, 'iniciarProduccion']);
    Route::post('panel-maquinas/simular-ciclo', [PanelMaquinasController::class, 'simularCiclo']);
    Route::post('panel-maquinas/completar-produccion/{id}', [PanelMaquinasController::class, 'completarProduccion']);
    Route::post('panel-maquinas/toggle-pausa', [PanelMaquinasController::class, 'togglePausa']);

    // Rutas para Órdenes de Producción
    Route::apiResource('ordenes-produccion', OrdenProduccionController::class);
    Route::get('ordenes-produccion/estadisticas/general', [OrdenProduccionController::class, 'estadisticas']);
    Route::get('ordenes-produccion/por-estado', [OrdenProduccionController::class, 'porEstado']);
    Route::patch('ordenes-produccion/{orden}/iniciar', [OrdenProduccionController::class, 'iniciarProduccion']);
    Route::patch('ordenes-produccion/{orden}/finalizar', [OrdenProduccionController::class, 'finalizarProduccion']);

    // Rutas para Registros de Producción
    Route::apiResource('registros-produccion', RegistroProduccionController::class);
    Route::get('registros-produccion/kpis/periodo', [RegistroProduccionController::class, 'kpis']);
    Route::patch('registros-produccion/{registro}/finalizar', [RegistroProduccionController::class, 'finalizar']);

    // Rutas para Lotes de Producción
    Route::apiResource('lotes-produccion', LoteProduccionController::class);
    Route::get('lotes-produccion/alertas/resumen', [LoteProduccionController::class, 'alertas']);
    Route::post('lotes-produccion/generar-codigo', [LoteProduccionController::class, 'generarCodigo']);
    Route::patch('lotes-produccion/{lote}/aprobar', [LoteProduccionController::class, 'aprobar']);
    Route::patch('lotes-produccion/{lote}/rechazar', [LoteProduccionController::class, 'rechazar']);

    // Rutas para Mantenimiento
    Route::apiResource('mantenimientos', MantenimientoController::class);
    Route::get('mantenimientos/alertas/resumen', [MantenimientoController::class, 'alertas']);
    Route::get('mantenimientos/calendario/periodo', [MantenimientoController::class, 'calendario']);
    Route::get('mantenimientos/historial/{maquina}', [MantenimientoController::class, 'historial']);
    Route::patch('mantenimientos/{mantenimiento}/iniciar', [MantenimientoController::class, 'iniciar']);
    Route::patch('mantenimientos/{mantenimiento}/completar', [MantenimientoController::class, 'completar']);

    // Rutas para Inspecciones de Calidad
    Route::apiResource('inspecciones-calidad', InspeccionCalidadController::class);
    Route::get('inspecciones-calidad/estadisticas/periodo', [InspeccionCalidadController::class, 'estadisticas']);
    Route::get('inspecciones-calidad/defectos/comunes', [InspeccionCalidadController::class, 'defectosComunes']);
    Route::patch('inspecciones-calidad/{inspeccion}/aprobar', [InspeccionCalidadController::class, 'aprobar']);

    // Rutas para Alertas
    Route::apiResource('alertas', AlertaController::class);
    Route::get('alertas/resumen/activas', [AlertaController::class, 'resumen']);
    Route::get('alertas/criticas/recientes', [AlertaController::class, 'criticas']);
    Route::get('alertas/historial/periodo', [AlertaController::class, 'historial']);
    Route::patch('alertas/{alerta}/marcar-leida', [AlertaController::class, 'marcarLeida']);
    Route::post('alertas/marcar-leidas', [AlertaController::class, 'marcarVariasLeidas']);
    Route::patch('alertas/{alerta}/resolver', [AlertaController::class, 'resolver']);
    Route::patch('alertas/{alerta}/descartar', [AlertaController::class, 'descartar']);

    // Rutas para Turnos
    Route::apiResource('turnos', TurnoController::class);
    Route::get('turnos/actual/horario', [TurnoController::class, 'actual']);
    Route::get('turnos/{turno}/estadisticas', [TurnoController::class, 'estadisticas']);
    Route::patch('turnos/{turno}/toggle-activo', [TurnoController::class, 'toggleActivo']);

    // Rutas para Formulaciones
    Route::apiResource('formulaciones', FormulacionController::class);
    Route::get('formulaciones/{formulacion}/calcular-costo', [FormulacionController::class, 'calcularCosto']);
    Route::post('formulaciones/{formulacion}/verificar-disponibilidad', [FormulacionController::class, 'verificarDisponibilidad']);
    Route::post('formulaciones/{formulacion}/clonar', [FormulacionController::class, 'clonar']);
    Route::patch('formulaciones/{formulacion}/toggle-activa', [FormulacionController::class, 'toggleActiva']);

    // Rutas para Reportes
    Route::get('reportes/produccion', [ReporteController::class, 'produccion']);
    Route::get('reportes/calidad', [ReporteController::class, 'calidad']);
    Route::get('reportes/inventario-insumos', [ReporteController::class, 'inventarioInsumos']);
    Route::get('reportes/inventario-productos', [ReporteController::class, 'inventarioProductos']);
    Route::get('reportes/mantenimiento', [ReporteController::class, 'mantenimiento']);
    Route::get('reportes/trazabilidad/{lote}', [ReporteController::class, 'trazabilidad']);
    Route::get('reportes/movimientos-inventario', [ReporteController::class, 'movimientosInventario']);
});
