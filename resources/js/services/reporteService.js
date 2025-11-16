import api from './api';

export default {
    // Reporte de producción
    getProduccion(params = {}) {
        return api.get('/reportes/produccion', { params });
    },

    // Reporte de calidad
    getCalidad(params = {}) {
        return api.get('/reportes/calidad', { params });
    },

    // Reporte de inventario de insumos
    getInventarioInsumos(params = {}) {
        return api.get('/reportes/inventario-insumos', { params });
    },

    // Reporte de inventario de productos
    getInventarioProductos(params = {}) {
        return api.get('/reportes/inventario-productos', { params });
    },

    // Reporte de mantenimiento
    getMantenimiento(params = {}) {
        return api.get('/reportes/mantenimiento', { params });
    },

    // Reporte de trazabilidad
    getTrazabilidad(loteId) {
        return api.get(`/reportes/trazabilidad/${loteId}`);
    },

    // Reporte de movimientos de inventario
    getMovimientosInventario(params = {}) {
        return api.get('/reportes/movimientos-inventario', { params });
    },

    // Exportar reporte (genera PDF o Excel según parámetro)
    exportar(tipo, formato, params = {}) {
        return api.get(`/reportes/${tipo}/exportar`, {
            params: { ...params, formato },
            responseType: 'blob',
        });
    },
};
