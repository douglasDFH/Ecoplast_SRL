import api from './api';

export default {
    // Alertas
    getAlertas(params = {}) {
        return api.get('/alertas', { params });
    },

    getAlerta(id) {
        return api.get(`/alertas/${id}`);
    },

    // Marcar como leída
    marcarLeida(id) {
        return api.patch(`/alertas/${id}/leida`);
    },

    // Marcar varias como leídas
    marcarVariasLeidas(ids) {
        return api.post('/alertas/marcar-leidas', { ids });
    },

    // Resolver alerta
    resolverAlerta(id, data) {
        return api.post(`/alertas/${id}/resolver`, data);
    },

    // Descartar alerta
    descartarAlerta(id, data) {
        return api.post(`/alertas/${id}/descartar`, data);
    },

    // Resumen de alertas
    getResumen() {
        return api.get('/alertas/resumen');
    },

    // Alertas activas
    getActivas(params = {}) {
        return api.get('/alertas/activas', { params });
    },

    // Alertas críticas
    getCriticas() {
        return api.get('/alertas/criticas');
    },

    // Por tipo
    getPorTipo(tipo, params = {}) {
        return api.get(`/alertas/tipo/${tipo}`, { params });
    },

    // Por prioridad
    getPorPrioridad(prioridad, params = {}) {
        return api.get(`/alertas/prioridad/${prioridad}`, { params });
    },
};
