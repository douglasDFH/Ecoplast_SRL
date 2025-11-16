import api from './api';

export default {
    // Listar órdenes de producción con filtros
    getOrdenes(params = {}) {
        return api.get('/ordenes-produccion', { params });
    },

    // Obtener una orden específica
    getOrden(id) {
        return api.get(`/ordenes-produccion/${id}`);
    },

    // Crear nueva orden
    createOrden(data) {
        return api.post('/ordenes-produccion', data);
    },

    // Actualizar orden
    updateOrden(id, data) {
        return api.put(`/ordenes-produccion/${id}`, data);
    },

    // Eliminar orden
    deleteOrden(id) {
        return api.delete(`/ordenes-produccion/${id}`);
    },

    // Iniciar orden
    iniciarOrden(id) {
        return api.post(`/ordenes-produccion/${id}/iniciar`);
    },

    // Pausar orden
    pausarOrden(id, data) {
        return api.post(`/ordenes-produccion/${id}/pausar`, data);
    },

    // Reanudar orden
    reanudarOrden(id) {
        return api.post(`/ordenes-produccion/${id}/reanudar`);
    },

    // Finalizar orden
    finalizarOrden(id, data) {
        return api.post(`/ordenes-produccion/${id}/finalizar`, data);
    },

    // Cancelar orden
    cancelarOrden(id, data) {
        return api.post(`/ordenes-produccion/${id}/cancelar`, data);
    },

    // Estadísticas de órdenes
    getEstadisticas(params = {}) {
        return api.get('/ordenes-produccion/estadisticas', { params });
    },
};
