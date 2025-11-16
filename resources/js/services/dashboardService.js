import api from './api';

export default {
    // Dashboard principal
    getDashboard() {
        return api.get('/dashboard');
    },

    // Producción semanal
    getProduccionSemanal(params = {}) {
        return api.get('/dashboard/produccion-semanal', { params });
    },

    // Producción por turno
    getProduccionPorTurno(params = {}) {
        return api.get('/dashboard/produccion-turno', { params });
    },

    // Estado de máquinas
    getEstadoMaquinas() {
        return api.get('/dashboard/maquinas');
    },

    // Top productos
    getTopProductos(params = {}) {
        return api.get('/dashboard/top-productos', { params });
    },

    // Alertas críticas
    getAlertasCriticas() {
        return api.get('/dashboard/alertas-criticas');
    },

    // OEE general
    getOEE(params = {}) {
        return api.get('/dashboard/oee', { params });
    },

    // Tendencias
    getTendencias(params = {}) {
        return api.get('/dashboard/tendencias', { params });
    },

    // Resumen de calidad
    getResumenCalidad(params = {}) {
        return api.get('/dashboard/calidad', { params });
    },
};
