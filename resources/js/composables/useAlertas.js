import { ref, computed } from 'vue';
import { alertaService } from '../services';

export function useAlertas() {
    const loading = ref(false);
    const alertas = ref([]);
    const resumen = ref(null);
    const criticas = ref([]);
    const noLeidas = ref(0);

    // Cargar alertas con filtros
    const loadAlertas = async (params = {}) => {
        loading.value = true;
        try {
            const response = await alertaService.getAlertas(params);
            alertas.value = response.data.data || response.data;
            return response.data;
        } catch (err) {
            console.error('Error al cargar alertas:', err);
            throw err;
        } finally {
            loading.value = false;
        }
    };

    // Cargar resumen
    const loadResumen = async () => {
        try {
            const response = await alertaService.getResumen();
            resumen.value = response.data;
            noLeidas.value = response.data.no_leidas || 0;
            return response.data;
        } catch (err) {
            console.error('Error al cargar resumen:', err);
            throw err;
        }
    };

    // Cargar alertas críticas
    const loadCriticas = async () => {
        try {
            const response = await alertaService.getCriticas();
            criticas.value = response.data;
            return response.data;
        } catch (err) {
            console.error('Error al cargar alertas críticas:', err);
            throw err;
        }
    };

    // Cargar alertas activas
    const loadActivas = async (params = {}) => {
        try {
            const response = await alertaService.getActivas(params);
            alertas.value = response.data.data || response.data;
            return response.data;
        } catch (err) {
            console.error('Error al cargar alertas activas:', err);
            throw err;
        }
    };

    // Marcar como leída
    const marcarLeida = async (id) => {
        try {
            await alertaService.marcarLeida(id);
            // Actualizar en el array local
            const alerta = alertas.value.find(a => a.id === id);
            if (alerta) {
                alerta.leida = true;
                noLeidas.value = Math.max(0, noLeidas.value - 1);
            }
        } catch (err) {
            console.error('Error al marcar alerta como leída:', err);
            throw err;
        }
    };

    // Marcar varias como leídas
    const marcarVariasLeidas = async (ids) => {
        try {
            await alertaService.marcarVariasLeidas(ids);
            // Actualizar en el array local
            ids.forEach(id => {
                const alerta = alertas.value.find(a => a.id === id);
                if (alerta && !alerta.leida) {
                    alerta.leida = true;
                    noLeidas.value = Math.max(0, noLeidas.value - 1);
                }
            });
        } catch (err) {
            console.error('Error al marcar alertas como leídas:', err);
            throw err;
        }
    };

    // Resolver alerta
    const resolver = async (id, notas) => {
        try {
            await alertaService.resolverAlerta(id, { notas });
            // Remover del array local
            alertas.value = alertas.value.filter(a => a.id !== id);
        } catch (err) {
            console.error('Error al resolver alerta:', err);
            throw err;
        }
    };

    // Descartar alerta
    const descartar = async (id, notas) => {
        try {
            await alertaService.descartarAlerta(id, { notas });
            // Remover del array local
            alertas.value = alertas.value.filter(a => a.id !== id);
        } catch (err) {
            console.error('Error al descartar alerta:', err);
            throw err;
        }
    };

    // Computed: alertas por prioridad
    const alertasPorPrioridad = computed(() => {
        return {
            critica: alertas.value.filter(a => a.prioridad === 'critica'),
            alta: alertas.value.filter(a => a.prioridad === 'alta'),
            media: alertas.value.filter(a => a.prioridad === 'media'),
            baja: alertas.value.filter(a => a.prioridad === 'baja'),
        };
    });

    // Computed: alertas por tipo
    const alertasPorTipo = computed(() => {
        const tipos = {};
        alertas.value.forEach(alerta => {
            if (!tipos[alerta.tipo]) {
                tipos[alerta.tipo] = [];
            }
            tipos[alerta.tipo].push(alerta);
        });
        return tipos;
    });

    return {
        loading,
        alertas,
        resumen,
        criticas,
        noLeidas,
        alertasPorPrioridad,
        alertasPorTipo,
        loadAlertas,
        loadResumen,
        loadCriticas,
        loadActivas,
        marcarLeida,
        marcarVariasLeidas,
        resolver,
        descartar,
    };
}
