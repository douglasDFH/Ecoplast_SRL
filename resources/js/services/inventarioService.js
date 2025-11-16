import api from './api';

export default {
    // Insumos
    getInsumos(params = {}) {
        return api.get('/insumos', { params });
    },

    getInsumo(id) {
        return api.get(`/insumos/${id}`);
    },

    createInsumo(data) {
        return api.post('/insumos', data);
    },

    updateInsumo(id, data) {
        return api.put(`/insumos/${id}`, data);
    },

    deleteInsumo(id) {
        return api.delete(`/insumos/${id}`);
    },

    // Productos terminados
    getProductos(params = {}) {
        return api.get('/productos-terminados', { params });
    },

    getProducto(id) {
        return api.get(`/productos-terminados/${id}`);
    },

    createProducto(data) {
        return api.post('/productos-terminados', data);
    },

    updateProducto(id, data) {
        return api.put(`/productos-terminados/${id}`, data);
    },

    deleteProducto(id) {
        return api.delete(`/productos-terminados/${id}`);
    },

    // Movimientos de inventario
    getMovimientos(params = {}) {
        return api.get('/movimientos-inventario', { params });
    },

    createMovimiento(data) {
        return api.post('/movimientos-inventario', data);
    },

    // Resumen de inventario
    getResumen() {
        return api.get('/movimientos-inventario/resumen');
    },

    // Por tipo
    getPorTipo(tipo, params = {}) {
        return api.get(`/movimientos-inventario/por-tipo/${tipo}`, { params });
    },
};
