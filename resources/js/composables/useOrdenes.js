import { ref, computed } from 'vue';
import ordenProduccionService from '../services/ordenProduccionService';

export function useOrdenes() {
    const ordenes = ref([]);
    const loading = ref(false);
    const filtros = ref({
        estado: '',
        prioridad: '',
        producto_id: '',
        maquina_id: '',
        fecha_desde: '',
        fecha_hasta: ''
    });

    // Computed properties
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

    // Cargar órdenes
    const loadOrdenes = async (params = {}) => {
        loading.value = true;
        try {
            const response = await ordenProduccionService.getOrdenes(params);
            ordenes.value = response.data.data.data || response.data.data || [];
        } catch (error) {
            console.error('Error al cargar órdenes:', error);
            ordenes.value = [];
        } finally {
            loading.value = false;
        }
    };

    // Aplicar filtros
    const aplicarFiltros = async () => {
        const params = {};
        if (filtros.value.estado) params.estado = filtros.value.estado;
        if (filtros.value.prioridad) params.prioridad = filtros.value.prioridad;
        if (filtros.value.producto_id) params.producto_id = filtros.value.producto_id;
        if (filtros.value.maquina_id) params.maquina_id = filtros.value.maquina_id;
        if (filtros.value.fecha_desde) params.fecha_desde = filtros.value.fecha_desde;
        if (filtros.value.fecha_hasta) params.fecha_hasta = filtros.value.fecha_hasta;
        
        await loadOrdenes(params);
    };

    // Limpiar filtros
    const limpiarFiltros = async () => {
        filtros.value = {
            estado: '',
            prioridad: '',
            producto_id: '',
            maquina_id: '',
            fecha_desde: '',
            fecha_hasta: ''
        };
        await loadOrdenes();
    };

    // Crear orden
    const createOrden = async (data) => {
        loading.value = true;
        try {
            await ordenProduccionService.createOrden(data);
            await loadOrdenes();
        } catch (error) {
            console.error('Error al crear orden:', error);
            throw error;
        } finally {
            loading.value = false;
        }
    };

    // Actualizar orden
    const updateOrden = async (id, data) => {
        loading.value = true;
        try {
            await ordenProduccionService.updateOrden(id, data);
            await loadOrdenes();
        } catch (error) {
            console.error('Error al actualizar orden:', error);
            throw error;
        } finally {
            loading.value = false;
        }
    };

    // Iniciar orden
    const iniciarOrden = async (id) => {
        loading.value = true;
        try {
            await ordenProduccionService.iniciarOrden(id);
            await loadOrdenes();
        } catch (error) {
            console.error('Error al iniciar orden:', error);
            throw error;
        } finally {
            loading.value = false;
        }
    };

    // Finalizar orden
    const finalizarOrden = async (id, data = {}) => {
        loading.value = true;
        try {
            await ordenProduccionService.finalizarOrden(id, data);
            await loadOrdenes();
        } catch (error) {
            console.error('Error al finalizar orden:', error);
            throw error;
        } finally {
            loading.value = false;
        }
    };

    // Cancelar orden
    const cancelarOrden = async (id, data = {}) => {
        loading.value = true;
        try {
            await ordenProduccionService.cancelarOrden(id, data);
            await loadOrdenes();
        } catch (error) {
            console.error('Error al cancelar orden:', error);
            throw error;
        } finally {
            loading.value = false;
        }
    };

    return {
        ordenes,
        loading,
        filtros,
        ordenesPendientes,
        ordenesEnProceso,
        ordenesCompletadas,
        ordenesCanceladas,
        loadOrdenes,
        aplicarFiltros,
        limpiarFiltros,
        createOrden,
        updateOrden,
        iniciarOrden,
        finalizarOrden,
        cancelarOrden
    };
}
