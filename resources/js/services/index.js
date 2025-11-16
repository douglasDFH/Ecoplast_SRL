// Servicios centralizados para consumir la API del backend

import ordenProduccionService from './ordenProduccionService';
import produccionService from './produccionService';
import calidadService from './calidadService';
import inventarioService from './inventarioService';
import mantenimientoService from './mantenimientoService';
import alertaService from './alertaService';
import dashboardService from './dashboardService';
import kpiService from './kpiService';
import reporteService from './reporteService';

export {
    ordenProduccionService,
    produccionService,
    calidadService,
    inventarioService,
    mantenimientoService,
    alertaService,
    dashboardService,
    kpiService,
    reporteService,
};

export default {
    ordenProduccion: ordenProduccionService,
    produccion: produccionService,
    calidad: calidadService,
    inventario: inventarioService,
    mantenimiento: mantenimientoService,
    alerta: alertaService,
    dashboard: dashboardService,
    kpi: kpiService,
    reporte: reporteService,
};
