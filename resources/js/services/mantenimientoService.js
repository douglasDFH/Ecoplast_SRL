import api from './api';

export default {
    // Mantenimientos
    getMantenimientos(params = {}) {
        return api.get('/mantenimientos', { params });
    },

    getMantenimiento(id) {
        return api.get(`/mantenimientos/${id}`);
    },

    createMantenimiento(data) {
        return api.post('/mantenimientos', data);
    },

    updateMantenimiento(id, data) {
        return api.put(`/mantenimientos/${id}`, data);
    },

    deleteMantenimiento(id) {
        return api.delete(`/mantenimientos/${id}`);
    },

    // Iniciar mantenimiento
    iniciarMantenimiento(id, data) {
        return api.post(`/mantenimientos/${id}/iniciar`, data);
    },

    // Completar mantenimiento
    completarMantenimiento(id, data) {
        return api.post(`/mantenimientos/${id}/completar`, data);
    },

    // Historial por máquina
    getHistorial(maquinaId, params = {}) {
        return api.get(`/mantenimientos/historial/${maquinaId}`, { params });
    },

    // Calendario
    getCalendario(params = {}) {
        return api.get('/mantenimientos/calendario', { params });
    },

    // Alertas
    getAlertas() {
        return api.get('/mantenimientos/alertas');
    },

    // Próximos
    getProximos(params = {}) {
        return api.get('/mantenimientos/proximos', { params });
    },

    // Estadísticas
    getEstadisticas(params = {}) {
        return api.get('/mantenimientos/estadisticas', { params });
    },
};
