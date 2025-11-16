import api from './api';

export default {
    // Inspecciones de calidad
    getInspecciones(params = {}) {
        return api.get('/inspecciones-calidad', { params });
    },

    getInspeccion(id) {
        return api.get(`/inspecciones-calidad/${id}`);
    },

    createInspeccion(data) {
        return api.post('/inspecciones-calidad', data);
    },

    updateInspeccion(id, data) {
        return api.put(`/inspecciones-calidad/${id}`, data);
    },

    deleteInspeccion(id) {
        return api.delete(`/inspecciones-calidad/${id}`);
    },

    // Aprobar inspección
    aprobarInspeccion(id, data) {
        return api.post(`/inspecciones-calidad/${id}/aprobar`, data);
    },

    // Estadísticas de calidad
    getEstadisticas(params = {}) {
        return api.get('/inspecciones-calidad/estadisticas', { params });
    },

    // Defectos comunes
    getDefectosComunes(params = {}) {
        return api.get('/inspecciones-calidad/defectos-comunes', { params });
    },

    // Por producto
    getPorProducto(params = {}) {
        return api.get('/inspecciones-calidad/por-producto', { params });
    },

    // Resumen
    getResumen(params = {}) {
        return api.get('/inspecciones-calidad/resumen', { params });
    },
};
