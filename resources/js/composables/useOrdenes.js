import { ref, computed } from 'vue';
import ordenProduccionService from '../services/ordenProduccionService';

export function useOrdenes() {
    const loading = ref(false);
    const ordenes = ref([]);
    const ordenActual = ref(null);
    const estadisticas = ref(null);
    const error = ref(null);

    // Filtros
    const filtros = ref({
        estado: '',
        prioridad: '',
        producto_id: null,
        maquina_id: null,
        fecha_desde: '',
        fecha_hasta: '',
    });

    // Cargar órdenes con filtros
    const loadOrdenes = async (params = {}) => {
        loading.value = true;
        error.value = null;
        try {
            const response = await ordenProduccionService.getOrdenes({
                ...filtros.value,
                ...params
            });
            ordenes.value = response.data.data || response.data;
            return response.data;
        } catch (err) {
            error.value = 'Error al cargar órdenes';
            console.error('Error al cargar órdenes:', err);
            throw err;
        } finally {
            loading.value = false;
        }
    };

    // Cargar estadísticas
    const loadEstadisticas = async () => {
        try {
            const response = await ordenProduccionService.getEstadisticas();
            estadisticas.value = response.data;
            return response.data;
        } catch (err) {
            console.error('Error al cargar estadísticas:', err);
            throw err;
        }
    };

    // Obtener una orden específica
    const getOrden = async (id) => {
        loading.value = true;
        error.value = null;
        try {
            const response = await ordenProduccionService.getOrden(id);
            ordenActual.value = response.data;
            return response.data;
        } catch (err) {
            error.value = 'Error al cargar orden';
            console.error('Error al cargar orden:', err);
            throw err;
        } finally {
            loading.value = false;
        }
    };

    // Crear nueva orden
    const createOrden = async (data) => {
        loading.value = true;
        error.value = null;
        try {
            const response = await ordenProduccionService.createOrden(data);
            await loadOrdenes(); // Recargar lista
            return response.data;
        } catch (err) {
            error.value = 'Error al crear orden';
            console.error('Error al crear orden:', err);
            throw err;
        } finally {
            loading.value = false;
        }
    };

    // Actualizar orden
    const updateOrden = async (id, data) => {
        loading.value = true;
        error.value = null;
        try {
            const response = await ordenProduccionService.updateOrden(id, data);
            await loadOrdenes(); // Recargar lista
            return response.data;
        } catch (err) {
            error.value = 'Error al actualizar orden';
            console.error('Error al actualizar orden:', err);
            throw err;
        } finally {
            loading.value = false;
        }
    };

    // Eliminar orden
    const deleteOrden = async (id) => {
        loading.value = true;
        error.value = null;
        try {
            await ordenProduccionService.deleteOrden(id);
            ordenes.value = ordenes.value.filter(o => o.id !== id);
        } catch (err) {
            error.value = 'Error al eliminar orden';
            console.error('Error al eliminar orden:', err);
            throw err;
        } finally {
            loading.value = false;
        }
    };

    // Iniciar producción
    const iniciarOrden = async (id) => {
        loading.value = true;
        error.value = null;
        try {
            const response = await ordenProduccionService.iniciarProduccion(id);
            await loadOrdenes(); // Recargar lista
            return response.data;
        } catch (err) {
            error.value = 'Error al iniciar orden';
            console.error('Error al iniciar orden:', err);
            throw err;
        } finally {
            loading.value = false;
        }
    };

    // Finalizar producción
    const finalizarOrden = async (id, data) => {
        loading.value = true;
        error.value = null;
        try {
            const response = await ordenProduccionService.finalizarProduccion(id, data);
            await loadOrdenes(); // Recargar lista
            return response.data;
        } catch (err) {
            error.value = 'Error al finalizar orden';
            console.error('Error al finalizar orden:', err);
            throw err;
        } finally {
            loading.value = false;
        }
    };

    // Cancelar orden
    const cancelarOrden = async (id, motivo) => {
        loading.value = true;
        error.value = null;
        try {
            const response = await ordenProduccionService.cancelarOrden(id, { motivo });
            await loadOrdenes(); // Recargar lista
            return response.data;
        } catch (err) {
            error.value = 'Error al cancelar orden';
            console.error('Error al cancelar orden:', err);
            throw err;
        } finally {
            loading.value = false;
        }
    };

    // Computed
    const ordenesPendientes = computed(() =>
        ordenes.value.filter(o => o.estado === 'pendiente')
    );

    const ordenesEnProceso = computed(() =>
        ordenes.value.filter(o => o.estado === 'en_proceso')
    );

    const ordenesCompletadas = computed(() =>
        ordenes.value.filter(o => o.estado === 'completada')
    );

    const ordenesCanceladas = computed(() =>
        ordenes.value.filter(o => o.estado === 'cancelada')
    );

    // Aplicar filtros
    const aplicarFiltros = async (nuevosFiltros) => {
        filtros.value = { ...filtros.value, ...nuevosFiltros };
        await loadOrdenes();
    };

    // Limpiar filtros
    const limpiarFiltros = async () => {
        filtros.value = {
            estado: '',
            prioridad: '',
            producto_id: null,
            maquina_id: null,
            fecha_desde: '',
            fecha_hasta: '',
        };
        await loadOrdenes();
    };

    return {
        // Estado
        loading,
        ordenes,
        ordenActual,
        estadisticas,
        error,
        filtros,

        // Computed
        ordenesPendientes,
        ordenesEnProceso,
        ordenesCompletadas,
        ordenesCanceladas,

        // Métodos
        loadOrdenes,
        loadEstadisticas,
        getOrden,
        createOrden,
        updateOrden,
        deleteOrden,
        iniciarOrden,
        finalizarOrden,
        cancelarOrden,
        aplicarFiltros,
        limpiarFiltros,
    };
}
