import api from './api';

export default {
    // KPIs generales
    getKpis(params = {}) {
        return api.get('/kpis', { params });
    },

    // Dashboard de KPIs
    getDashboard(params = {}) {
        return api.get('/kpis/dashboard', { params });
    },

    // KPIs de producción
    getProduccion(params = {}) {
        return api.get('/kpis/produccion', { params });
    },

    // KPIs de calidad
    getCalidad(params = {}) {
        return api.get('/kpis/calidad', { params });
    },

    // KPIs de inventario
    getInventario(params = {}) {
        return api.get('/kpis/inventario', { params });
    },

    // KPIs de mantenimiento
    getMantenimiento(params = {}) {
        return api.get('/kpis/mantenimiento', { params });
    },

    // OEE (Overall Equipment Effectiveness)
    getOEE(params = {}) {
        return api.get('/kpis/oee', { params });
    },
};
