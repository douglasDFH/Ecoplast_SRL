import api from './api';

export default {
    // Registros de producción
    getRegistros(params = {}) {
        return api.get('/registros-produccion', { params });
    },

    getRegistro(id) {
        return api.get(`/registros-produccion/${id}`);
    },

    createRegistro(data) {
        return api.post('/registros-produccion', data);
    },

    updateRegistro(id, data) {
        return api.put(`/registros-produccion/${id}`, data);
    },

    deleteRegistro(id) {
        return api.delete(`/registros-produccion/${id}`);
    },

    // Finalizar registro
    finalizarRegistro(id) {
        return api.post(`/registros-produccion/${id}/finalizar`);
    },

    // KPIs de producción
    getKpis(params = {}) {
        return api.get('/registros-produccion/kpis', { params });
    },

    // Lotes de producción
    getLotes(params = {}) {
        return api.get('/lotes-produccion', { params });
    },

    getLote(id) {
        return api.get(`/lotes-produccion/${id}`);
    },

    createLote(data) {
        return api.post('/lotes-produccion', data);
    },

    updateLote(id, data) {
        return api.put(`/lotes-produccion/${id}`, data);
    },

    // Aprobar lote
    aprobarLote(id, data) {
        return api.post(`/lotes-produccion/${id}/aprobar`, data);
    },

    // Rechazar lote
    rechazarLote(id, data) {
        return api.post(`/lotes-produccion/${id}/rechazar`, data);
    },

    // Generar código de lote
    generarCodigoLote(id) {
        return api.post(`/lotes-produccion/${id}/generar-codigo`);
    },

    // Trazabilidad completa
    getTrazabilidad(id) {
        return api.get(`/lotes-produccion/${id}/trazabilidad`);
    },

    // Alertas de lotes
    getAlertasLotes(params = {}) {
        return api.get('/lotes-produccion/alertas', { params });
    },
};
