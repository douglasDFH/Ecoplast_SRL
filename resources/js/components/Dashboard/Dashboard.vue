<template>
    <div>
        <!-- Header con estilo neomórfico -->
        <header class="mb-6 p-6 rounded-3xl" style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); box-shadow: 15px 15px 30px #b3d4f1, -15px -15px 30px #f3ffff;">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2">
                        <div
                            class="w-3 h-3 rounded-full animate-pulse"
                            :class="isConnected ? 'bg-green-500' : 'bg-red-500'"
                        ></div>
                        <span class="text-sm font-semibold" :class="isConnected ? 'text-green-700' : 'text-red-700'">
                            {{ connectionStatus }}
                        </span>
                    </div>
                    <div class="flex items-center text-sm px-4 py-2 rounded-xl" style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); box-shadow: inset 5px 5px 10px #b3d4f1, inset -5px -5px 10px #f3ffff; color: #455A64;">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                        </svg>
                        <span class="font-medium">{{ lastUpdate }}</span>
                    </div>
                </div>
                <button
                    @click="refreshData"
                    :disabled="loading"
                    class="px-6 py-3 rounded-2xl text-sm font-bold text-white transition-all active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed"
                    style="background: linear-gradient(145deg, #2E7D32, #4CAF50); box-shadow: 10px 10px 20px #b3d4f1, -10px -10px 20px #f3ffff;"
                    @mouseover="e => e.target.style.boxShadow='7px 7px 14px #b3d4f1, -7px -7px 14px #f3ffff'"
                    @mouseout="e => e.target.style.boxShadow='10px 10px 20px #b3d4f1, -10px -10px 20px #f3ffff'"
                >
                    <span v-if="loading" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Actualizando...
                    </span>
                    <span v-else class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Actualizar
                    </span>
                </button>
            </div>
        </header>

        <!-- Main Content -->
        <main>
            <!-- KPI Cards - Indicadores Clave del Día -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <KpiCard
                    title="OEE de Planta"
                    :value="kpisData.oee || 0"
                    icon="📊"
                    color="blue"
                    :trend="kpisData.oee_trend"
                    comparison-period="vs ayer"
                    :goal="85"
                    unit="%"
                />
                <KpiCard
                    title="Producción del Día"
                    :value="kpisData.produccion_dia || 0"
                    icon="📦"
                    color="green"
                    :trend="kpisData.produccion_trend"
                    comparison-period="vs ayer"
                    :goal="kpisData.meta_produccion"
                    unit="u"
                />
                <KpiCard
                    title="Calidad Global"
                    :value="kpisData.calidad || 0"
                    icon="✓"
                    color="purple"
                    :trend="kpisData.calidad_trend"
                    comparison-period="vs semana pasada"
                    :goal="98"
                    unit="%"
                />
                <KpiCard
                    title="Disponibilidad"
                    :value="kpisData.disponibilidad || 0"
                    icon="⚡"
                    :color="kpisData.disponibilidad >= 90 ? 'green' : 'orange'"
                    :trend="kpisData.disponibilidad_trend"
                    comparison-period="vs ayer"
                    :goal="95"
                    unit="%"
                />
            </div>

            <!-- Gráficos Principales -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Producción por Hora -->
                <ProduccionPorHoraChart :data="produccionHora" />

                <!-- Estado de Máquinas -->
                <MachinesStatusList :machines="machines" />
            </div>

            <!-- Widgets Inferiores -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Top Productos -->
                <TopProductosWidget :productos="topProductos" />

                <!-- Cumplimiento de Metas -->
                <CumplimientoMetasWidget :metas="metas" />
            </div>

            <!-- Panel de Alertas -->
            <AlertsPanel
                :alerts="alertas"
                @mark-as-read="markAlertAsRead"
                @mark-all-as-read="markAllAlertsAsRead"
            />
        </main>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import dashboardService from '@/services/dashboardService.js';
import kpiService from '@/services/kpiService.js';
import { useAlertas } from '@/composables/useAlertas.js';
import { useBroadcasting } from '@/composables/useBroadcasting.js';
import KpiCard from './KpiCard.vue';
import ProduccionPorHoraChart from './ProduccionPorHoraChart.vue';
import MachinesStatusList from './MachinesStatusList.vue';
import TopProductosWidget from './TopProductosWidget.vue';
import CumplimientoMetasWidget from './CumplimientoMetasWidget.vue';
import AlertsPanel from './AlertsPanel.vue';

// Estado local
const loading = ref(false);
const lastUpdate = ref('Nunca');
const kpisData = ref({
    oee: 0,
    oee_trend: null,
    produccion_dia: 0,
    produccion_trend: null,
    meta_produccion: 0,
    calidad: 0,
    calidad_trend: null,
    disponibilidad: 0,
    disponibilidad_trend: null,
});
const produccionHora = ref([]);
const machines = ref([]);
const topProductos = ref([]);
const metas = ref([]);

// Auto-refresh interval (3 segundos)
let refreshInterval = null;

// Composables
const {
    alertas,
    noLeidas,
    loadActivas,
    marcarLeida,
    marcarVariasLeidas,
} = useAlertas();

const {
    connected: isConnected,
    listenToProduccion,
    listenToAlertas,
    listenToOrdenes,
    listenToMaquinaria,
    listenToCalidad,
    cleanup: cleanupBroadcasting,
} = useBroadcasting();

// Computed
const connectionStatus = computed(() => isConnected.value ? '🟢 En vivo' : '🔴 Desconectado');

// Métodos
const formatTime = () => {
    const now = new Date();
    return now.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
};

const loadDashboardData = async () => {
    try {
        const [dashboard, kpis, produccionSemanal, estadoMaquinas, productos] = await Promise.all([
            dashboardService.getDashboard(),
            kpiService.getDashboard(),
            dashboardService.getProduccionSemanal({ dias: 1 }),
            dashboardService.getEstadoMaquinas(),
            dashboardService.getTopProductos({ fecha: new Date().toISOString().split('T')[0] }),
        ]);

        // Procesar KPIs
        if (kpis) {
            kpisData.value = {
                oee: kpis.oee_promedio || 0,
                oee_trend: kpis.oee_trend || null,
                produccion_dia: kpis.produccion_total || 0,
                produccion_trend: kpis.produccion_trend || null,
                meta_produccion: kpis.meta_produccion || 10000,
                calidad: kpis.calidad_promedio || 0,
                calidad_trend: kpis.calidad_trend || null,
                disponibilidad: kpis.disponibilidad_promedio || 0,
                disponibilidad_trend: kpis.disponibilidad_trend || null,
            };
        }

        // Procesar producción por hora
        if (produccionSemanal && produccionSemanal.length > 0) {
            const hoy = produccionSemanal.find(d => d.fecha === new Date().toISOString().split('T')[0]);
            if (hoy && hoy.por_hora) {
                produccionHora.value = hoy.por_hora.map((cantidad, index) => ({
                    hora: index + 6, // Desde las 6 AM
                    cantidad: cantidad || 0,
                }));
            }
        }

        // Procesar máquinas
        if (estadoMaquinas) {
            machines.value = estadoMaquinas.map(m => ({
                id: m.id,
                nombre_maquina: m.nombre,
                estado_actual: m.estado,
                oee: m.oee || 0,
                progreso: m.progreso || 0,
                turno: m.turno || 'Diurno',
                alerta: m.tiene_alerta || false,
            }));
        }

        // Procesar top productos
        if (productos) {
            topProductos.value = productos.slice(0, 5).map(p => ({
                id: p.id,
                nombre: p.nombre,
                codigo: p.codigo,
                cantidad_producida: p.cantidad || 0,
            }));
        }

        // Procesar metas
        metas.value = [
            {
                nombre: 'Producción',
                icono: '📦',
                porcentaje: Math.round((kpisData.value.produccion_dia / kpisData.value.meta_produccion) * 100),
                actual: kpisData.value.produccion_dia,
                objetivo: kpisData.value.meta_produccion,
                unidad: 'u',
            },
            {
                nombre: 'OEE Objetivo',
                icono: '🎯',
                porcentaje: Math.round((kpisData.value.oee / 85) * 100),
                actual: kpisData.value.oee,
                objetivo: 85,
                unidad: '%',
            },
            {
                nombre: 'Calidad',
                icono: '✓',
                porcentaje: Math.round((kpisData.value.calidad / 98) * 100),
                actual: kpisData.value.calidad,
                objetivo: 98,
                unidad: '%',
            },
            {
                nombre: 'Disponibilidad',
                icono: '⚡',
                porcentaje: Math.round((kpisData.value.disponibilidad / 95) * 100),
                actual: kpisData.value.disponibilidad,
                objetivo: 95,
                unidad: '%',
            },
        ];

        lastUpdate.value = formatTime();
    } catch (error) {
        console.error('Error cargando datos del dashboard:', error);
    }
};

const refreshData = async () => {
    if (loading.value) return;
    
    loading.value = true;
    try {
        await Promise.all([
            loadDashboardData(),
            loadActivas(),
        ]);
    } finally {
        loading.value = false;
    }
};

const markAlertAsRead = async (alertId) => {
    await marcarLeida(alertId);
};

const markAllAlertsAsRead = async () => {
    const idsActivas = alertas.value
        .filter(a => a.estado === 'activa')
        .map(a => a.id);
    
    if (idsActivas.length > 0) {
        await marcarVariasLeidas(idsActivas);
    }
};

const setupBroadcasting = () => {
    // Escuchar eventos de producción en tiempo real
    listenToProduccion((event) => {
        console.log('🔄 Producción actualizada:', event);
        loadDashboardData();
    });

    // Escuchar nuevas alertas
    listenToAlertas((event) => {
        console.log('🚨 Nueva alerta:', event);
        loadActivas();
    });

    // Escuchar órdenes completadas
    listenToOrdenes((event) => {
        console.log('✅ Orden completada:', event);
        loadDashboardData();
    });

    // Escuchar máquinas paradas
    listenToMaquinaria((event) => {
        console.log('⚠️ Máquina parada:', event);
        loadDashboardData();
    });

    // Escuchar defectos de calidad
    listenToCalidad((event) => {
        console.log('🔍 Defecto detectado:', event);
        loadActivas();
    });
};

// Lifecycle
onMounted(async () => {
    // Cargar datos iniciales
    await refreshData();
    
    // Configurar broadcasting
    setupBroadcasting();

    // Auto-refresh cada 3 segundos
    refreshInterval = setInterval(() => {
        loadDashboardData();
    }, 3000);
});

onUnmounted(() => {
    if (refreshInterval) {
        clearInterval(refreshInterval);
    }
    cleanupBroadcasting();
});
</script>