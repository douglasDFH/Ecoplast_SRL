import { ref, computed } from 'vue';
import { dashboardService } from '../services';

export function useDashboard() {
    const loading = ref(false);
    const error = ref(null);
    const data = ref(null);
    const produccionSemanal = ref([]);
    const produccionPorTurno = ref([]);
    const estadoMaquinas = ref([]);
    const topProductos = ref([]);
    const alertasCriticas = ref([]);
    const oee = ref(null);

    // Cargar dashboard completo
    const loadDashboard = async () => {
        loading.value = true;
        error.value = null;
        try {
            const response = await dashboardService.getDashboard();
            data.value = response.data;
            return response.data;
        } catch (err) {
            error.value = err.response?.data?.message || 'Error al cargar el dashboard';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    // Cargar producción semanal
    const loadProduccionSemanal = async (params = {}) => {
        try {
            const response = await dashboardService.getProduccionSemanal(params);
            produccionSemanal.value = response.data;
            return response.data;
        } catch (err) {
            console.error('Error al cargar producción semanal:', err);
            throw err;
        }
    };

    // Cargar producción por turno
    const loadProduccionPorTurno = async (params = {}) => {
        try {
            const response = await dashboardService.getProduccionPorTurno(params);
            produccionPorTurno.value = response.data;
            return response.data;
        } catch (err) {
            console.error('Error al cargar producción por turno:', err);
            throw err;
        }
    };

    // Cargar estado de máquinas
    const loadEstadoMaquinas = async () => {
        try {
            const response = await dashboardService.getEstadoMaquinas();
            estadoMaquinas.value = response.data;
            return response.data;
        } catch (err) {
            console.error('Error al cargar estado de máquinas:', err);
            throw err;
        }
    };

    // Cargar top productos
    const loadTopProductos = async (params = {}) => {
        try {
            const response = await dashboardService.getTopProductos(params);
            topProductos.value = response.data;
            return response.data;
        } catch (err) {
            console.error('Error al cargar top productos:', err);
            throw err;
        }
    };

    // Cargar alertas críticas
    const loadAlertasCriticas = async () => {
        try {
            const response = await dashboardService.getAlertasCriticas();
            alertasCriticas.value = response.data;
            return response.data;
        } catch (err) {
            console.error('Error al cargar alertas críticas:', err);
            throw err;
        }
    };

    // Cargar OEE
    const loadOEE = async (params = {}) => {
        try {
            const response = await dashboardService.getOEE(params);
            oee.value = response.data;
            return response.data;
        } catch (err) {
            console.error('Error al cargar OEE:', err);
            throw err;
        }
    };

    // Computed: estadísticas resumidas
    const estadisticas = computed(() => {
        if (!data.value) return null;
        return {
            produccionHoy: data.value.produccion_hoy || 0,
            ordenesActivas: data.value.ordenes_activas || 0,
            alertasActivas: data.value.alertas_activas || 0,
            maquinasOperativas: estadoMaquinas.value.filter(m => m.estado === 'operativa').length,
        };
    });

    // Refrescar todo el dashboard
    const refreshAll = async () => {
        await Promise.all([
            loadDashboard(),
            loadProduccionSemanal(),
            loadProduccionPorTurno(),
            loadEstadoMaquinas(),
            loadTopProductos(),
            loadAlertasCriticas(),
            loadOEE(),
        ]);
    };

    return {
        loading,
        error,
        data,
        produccionSemanal,
        produccionPorTurno,
        estadoMaquinas,
        topProductos,
        alertasCriticas,
        oee,
        estadisticas,
        loadDashboard,
        loadProduccionSemanal,
        loadProduccionPorTurno,
        loadEstadoMaquinas,
        loadTopProductos,
        loadAlertasCriticas,
        loadOEE,
        refreshAll,
    };
}
