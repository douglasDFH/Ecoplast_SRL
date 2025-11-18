<template>
    <div class="min-h-screen p-8" style="background: linear-gradient(145deg, #e3f2fd, #bbdefb);">
        <!-- Header -->
        <div class="mb-8 rounded-3xl p-6 shadow-neumorphic"
             style="background: linear-gradient(145deg, #e3f2fd, #bbdefb);">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold mb-2" style="color: #263238;">🏭 Panel de Control de Máquinas</h1>
                    <p class="text-lg" style="color: #607D8B;">Simulación en Tiempo Real - Actualización cada 3 segundos</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="px-4 py-2 rounded-xl" :class="autoRefresh ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'">
                        <span class="font-semibold">{{ autoRefresh ? '🔄 Auto-refresh ON' : '⏸️ Auto-refresh OFF' }}</span>
                    </div>
                    <button
                        @click="toggleAutoRefresh"
                        class="px-6 py-3 rounded-2xl font-semibold transition-all"
                        :style="autoRefresh
                            ? 'background: linear-gradient(145deg, #FF9800, #F57C00); color: white;'
                            : 'background: linear-gradient(145deg, #4CAF50, #388E3C); color: white;'"
                    >
                        {{ autoRefresh ? '⏸️ Pausar' : '▶️ Iniciar' }}
                    </button>
                    <button
                        @click="cargarDatos"
                        class="px-6 py-3 rounded-2xl font-semibold transition-all"
                        style="background: linear-gradient(145deg, #2196F3, #1976D2); color: white;"
                    >
                        🔄 Actualizar
                    </button>
                </div>
            </div>
            <p class="text-sm mt-2" style="color: #607D8B;">⏰ Última actualización: {{ ultimaActualizacion }}</p>
        </div>

        <!-- Estadísticas Generales -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
            <div class="rounded-3xl p-6 shadow-neumorphic" style="background: linear-gradient(145deg, #e3f2fd, #bbdefb);">
                <p class="text-sm font-medium" style="color: #607D8B;">Total Máquinas</p>
                <p class="text-4xl font-bold" style="color: #2196F3;">{{ estadisticas.total_maquinas }}</p>
            </div>
            <div class="rounded-3xl p-6 shadow-neumorphic" style="background: linear-gradient(145deg, #C8E6C9, #A5D6A7);">
                <p class="text-sm font-medium" style="color: #1B5E20;">✅ Operativas</p>
                <p class="text-4xl font-bold" style="color: #2E7D32;">{{ estadisticas.operativas }}/{{ estadisticas.total_maquinas }}</p>
            </div>
            <div class="rounded-3xl p-6 shadow-neumorphic" style="background: linear-gradient(145deg, #FFF9C4, #FFF59D);">
                <p class="text-sm font-medium" style="color: #F57F17;">⚙️ En Producción</p>
                <p class="text-4xl font-bold" style="color: #F9A825;">{{ estadisticas.en_produccion }}/{{ estadisticas.total_maquinas }}</p>
            </div>
            <div class="rounded-3xl p-6 shadow-neumorphic" style="background: linear-gradient(145deg, #FFCCBC, #FFAB91);">
                <p class="text-sm font-medium" style="color: #BF360C;">🔧 Mantenimiento</p>
                <p class="text-4xl font-bold" style="color: #D84315;">{{ estadisticas.en_mantenimiento }}</p>
            </div>
            <div class="rounded-3xl p-6 shadow-neumorphic" style="background: linear-gradient(145deg, #B2DFDB, #80CBC4);">
                <p class="text-sm font-medium" style="color: #00695C;">📈 Eficiencia Prom.</p>
                <p class="text-4xl font-bold" style="color: #00897B;">{{ estadisticas.eficiencia_promedio.toFixed(1) }}%</p>
            </div>
        </div>

        <!-- Grid de Máquinas (2 filas x 4 columnas = 8 máquinas) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div v-for="maquina in maquinas" :key="maquina.id"
                 class="rounded-3xl p-6 shadow-neumorphic transition-all hover:shadow-lg"
                 :style="getEstiloMaquina(maquina)">

                <!-- Header de la tarjeta -->
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-bold" style="color: #263238;">{{ getIconoEstado(maquina) }} {{ maquina.codigo }}</h3>
                        <p class="text-sm" style="color: #607D8B;">{{ maquina.nombre }}</p>
                    </div>
                    <div class="px-3 py-1 rounded-xl text-xs font-bold" :class="getClaseEstado(maquina)">
                        {{ getTextoEstado(maquina) }}
                    </div>
                </div>

                <!-- Si está produciendo -->
                <div v-if="maquina.simulacion && maquina.simulacion.estado === 'produciendo'" class="space-y-3">
                    <div class="p-3 rounded-2xl" style="background: linear-gradient(145deg, #E1F5FE, #B3E5FC);">
                        <p class="text-xs font-semibold" style="color: #01579B;">📋 {{ maquina.orden.numero_orden }}</p>
                        <p class="text-sm font-bold" style="color: #0277BD;">{{ maquina.orden.producto }}</p>
                        <p class="text-xs mt-1" style="color: #0288D1;">🎯 Meta: {{ formatearNumero(maquina.orden.cantidad_planificada) }} uds</p>
                    </div>

                    <!-- Barra de progreso -->
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span style="color: #455A64;">Progreso</span>
                            <span class="font-bold" style="color: #1976D2;">{{ maquina.simulacion.porcentaje_progreso.toFixed(1) }}%</span>
                        </div>
                        <div class="w-full h-3 rounded-full overflow-hidden" style="background: #BBDEFB;">
                            <div class="h-full transition-all duration-500 rounded-full"
                                 :style="`width: ${maquina.simulacion.porcentaje_progreso}%; background: linear-gradient(90deg, #1976D2, #42A5F5);`">
                            </div>
                        </div>
                    </div>

                    <!-- Unidades producidas -->
                    <div class="grid grid-cols-3 gap-2 text-center">
                        <div class="p-2 rounded-xl" style="background: linear-gradient(145deg, #E3F2FD, #BBDEFB);">
                            <p class="text-xs" style="color: #607D8B;">Producidas</p>
                            <p class="text-lg font-bold" style="color: #1976D2;">{{ maquina.simulacion.unidades_producidas }}</p>
                        </div>
                        <div class="p-2 rounded-xl" style="background: linear-gradient(145deg, #C8E6C9, #A5D6A7);">
                            <p class="text-xs" style="color: #607D8B;">✅ Buenas</p>
                            <p class="text-lg font-bold" style="color: #388E3C;">{{ maquina.simulacion.unidades_conformes }}</p>
                        </div>
                        <div class="p-2 rounded-xl" style="background: linear-gradient(145deg, #FFCDD2, #EF9A9A);">
                            <p class="text-xs" style="color: #607D8B;">❌ Defec.</p>
                            <p class="text-lg font-bold" style="color: #D32F2F;">{{ maquina.simulacion.unidades_defectuosas }}</p>
                        </div>
                    </div>

                    <!-- Parámetros de máquina -->
                    <div class="text-xs space-y-1" style="color: #455A64;">
                        <p>🌡️ Temp: {{ maquina.simulacion.temperatura_zona1 }}°C / {{ maquina.simulacion.temperatura_zona2 }}°C / {{ maquina.simulacion.temperatura_zona3 }}°C</p>
                        <p>💨 Presión: {{ maquina.simulacion.presion_actual }} Bar</p>
                        <p>🔄 Husillo: {{ maquina.simulacion.velocidad_husillo_actual }} RPM</p>
                        <p>⏱️ Ciclo: {{ maquina.simulacion.tiempo_ciclo_actual }} seg</p>
                        <p>⚡ Consumo: {{ maquina.simulacion.consumo_energia.toFixed(2) }} kWh</p>
                        <p>📈 Eficiencia: {{ maquina.simulacion.eficiencia_actual }}%</p>
                        <p>⏰ Est. final: {{ maquina.simulacion.tiempo_estimado_finalizacion }}</p>
                    </div>

                    <!-- Botones de acción -->
                    <div class="flex space-x-2">
                        <button
                            @click="pausarReanudar(maquina.simulacion)"
                            class="flex-1 px-3 py-2 rounded-xl text-sm font-semibold transition-all"
                            style="background: linear-gradient(145deg, #FF9800, #F57C00); color: white;"
                        >
                            ⏸️ Pausar
                        </button>
                    </div>
                </div>

                <!-- Si está disponible -->
                <div v-else-if="!maquina.tiene_orden && maquina.estado_maquina === 'operativa'" class="space-y-3 text-center py-4">
                    <p class="text-2xl">💤</p>
                    <p class="text-sm font-semibold" style="color: #607D8B;">Sin orden asignada</p>
                    <p class="text-xs" style="color: #90A4AE;">Disponible para producción</p>
                    <button
                        @click="abrirModalAsignarOrden(maquina)"
                        class="w-full px-4 py-2 rounded-xl text-sm font-semibold transition-all"
                        style="background: linear-gradient(145deg, #4CAF50, #388E3C); color: white;"
                    >
                        ➕ Asignar Orden
                    </button>
                </div>

                <!-- Si está en mantenimiento -->
                <div v-else-if="maquina.estado_maquina === 'mantenimiento'" class="space-y-2 text-center py-4">
                    <p class="text-2xl">🔧</p>
                    <p class="text-sm font-semibold" style="color: #FF6F00;">Mantenimiento Preventivo</p>
                    <p class="text-xs" style="color: #607D8B;">En proceso...</p>
                </div>

                <!-- Si está parada -->
                <div v-else-if="maquina.estado_maquina === 'parada'" class="space-y-2 text-center py-4">
                    <p class="text-2xl">⛔</p>
                    <p class="text-sm font-semibold" style="color: #D32F2F;">Máquina Parada</p>
                    <p class="text-xs" style="color: #607D8B;">Requiere atención</p>
                </div>

                <!-- Si tiene orden pero no simulación (pendiente de iniciar) -->
                <div v-else-if="maquina.tiene_orden" class="space-y-3">
                    <div class="p-3 rounded-2xl" style="background: linear-gradient(145deg, #FFF9C4, #FFF59D);">
                        <p class="text-xs font-semibold" style="color: #F57F17;">📋 Orden Asignada</p>
                        <p class="text-sm font-bold" style="color: #F9A825;">{{ maquina.orden.numero_orden }}</p>
                        <p class="text-xs mt-1" style="color: #FBC02D;">{{ maquina.orden.producto }}</p>
                    </div>
                    <button
                        @click="iniciarProduccion(maquina.orden.id)"
                        class="w-full px-4 py-3 rounded-xl text-sm font-bold transition-all"
                        style="background: linear-gradient(145deg, #4CAF50, #388E3C); color: white;"
                    >
                        ▶️ INICIAR PRODUCCIÓN
                    </button>
                </div>
            </div>
        </div>

        <!-- Órdenes Pendientes -->
        <div class="rounded-3xl p-6 shadow-neumorphic mb-8" style="background: linear-gradient(145deg, #e3f2fd, #bbdefb);">
            <h2 class="text-2xl font-bold mb-4" style="color: #263238;">📋 Órdenes Pendientes en Cola</h2>
            <div v-if="ordenesPendientes.length === 0" class="text-center py-8" style="color: #607D8B;">
                <p class="text-lg">No hay órdenes pendientes</p>
            </div>
            <div v-else class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr style="background: linear-gradient(145deg, #d0e8f7, #b3d4f1);">
                            <th class="px-4 py-3 text-left text-sm font-bold" style="color: #263238;">Prioridad</th>
                            <th class="px-4 py-3 text-left text-sm font-bold" style="color: #263238;">Orden</th>
                            <th class="px-4 py-3 text-left text-sm font-bold" style="color: #263238;">Producto</th>
                            <th class="px-4 py-3 text-left text-sm font-bold" style="color: #263238;">Cantidad</th>
                            <th class="px-4 py-3 text-left text-sm font-bold" style="color: #263238;">Máquina</th>
                            <th class="px-4 py-3 text-left text-sm font-bold" style="color: #263238;">Programada</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="orden in ordenesPendientes" :key="orden.id" class="border-b border-blue-100">
                            <td class="px-4 py-3">
                                <span class="px-3 py-1 rounded-full text-xs font-bold" :class="getClasePrioridad(orden.prioridad)">
                                    {{ getPrioridadTexto(orden.prioridad) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-mono font-semibold" style="color: #263238;">{{ orden.numero_orden }}</td>
                            <td class="px-4 py-3" style="color: #455A64;">{{ orden.producto }}</td>
                            <td class="px-4 py-3 font-semibold" style="color: #1976D2;">{{ formatearNumero(orden.cantidad_planificada) }}</td>
                            <td class="px-4 py-3" style="color: #607D8B;">{{ orden.maquina_requerida }}</td>
                            <td class="px-4 py-3 text-sm" style="color: #607D8B;">{{ orden.fecha_programada }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import axios from 'axios';

// Estado
const maquinas = ref([]);
const estadisticas = ref({
    total_maquinas: 0,
    operativas: 0,
    en_produccion: 0,
    en_mantenimiento: 0,
    paradas: 0,
    eficiencia_promedio: 0,
});
const ordenesPendientes = ref([]);
const autoRefresh = ref(true);
const ultimaActualizacion = ref('--:--:--');
let intervalId = null;

// Métodos
const cargarDatos = async () => {
    try {
        const response = await axios.get('/api/panel-maquinas');
        maquinas.value = response.data.maquinas;
        estadisticas.value = response.data.estadisticas;
        ordenesPendientes.value = response.data.ordenes_pendientes;
        ultimaActualizacion.value = new Date().toLocaleTimeString('es-ES');

        // Simular ciclos de las máquinas que están produciendo
        await simularCiclosActivos();
    } catch (error) {
        console.error('Error al cargar datos del panel:', error);
    }
};

const simularCiclosActivos = async () => {
    const simulacionesActivas = maquinas.value
        .filter(m => m.simulacion && m.simulacion.estado === 'produciendo')
        .map(m => m.simulacion);

    for (const sim of simulacionesActivas) {
        try {
            await axios.post('/api/panel-maquinas/simular-ciclo', {
                simulacion_id: sim.id || maquinas.value.find(m => m.simulacion === sim)?.id
            });
        } catch (error) {
            // Silencioso, continuará en el próximo ciclo
        }
    }
};

const iniciarProduccion = async (ordenId) => {
    if (!confirm('¿Iniciar producción de esta orden?\n\nSe descontarán los insumos necesarios del inventario.')) {
        return;
    }

    try {
        const response = await axios.post('/api/panel-maquinas/iniciar-produccion', {
            orden_id: ordenId
        });
        alert('✅ Producción iniciada exitosamente!\n\n' + response.data.message);
        await cargarDatos();
    } catch (error) {
        const mensaje = error.response?.data?.message || 'Error al iniciar producción';
        const detalles = error.response?.data;

        if (detalles?.insumo) {
            alert(`❌ ${mensaje}\n\nInsumo: ${detalles.insumo}\nDisponible: ${detalles.disponible}\nNecesario: ${detalles.necesario}`);
        } else {
            alert(`❌ ${mensaje}`);
        }
    }
};

const pausarReanudar = async (simulacion) => {
    try {
        const response = await axios.post('/api/panel-maquinas/toggle-pausa', {
            simulacion_id: simulacion.id
        });
        alert(response.data.message);
        await cargarDatos();
    } catch (error) {
        alert('Error al pausar/reanudar producción');
    }
};

const toggleAutoRefresh = () => {
    autoRefresh.value = !autoRefresh.value;

    if (autoRefresh.value) {
        iniciarAutoRefresh();
    } else {
        detenerAutoRefresh();
    }
};

const iniciarAutoRefresh = () => {
    if (intervalId) clearInterval(intervalId);
    intervalId = setInterval(cargarDatos, 3000); // Cada 3 segundos
};

const detenerAutoRefresh = () => {
    if (intervalId) {
        clearInterval(intervalId);
        intervalId = null;
    }
};

// Utilidades
const formatearNumero = (numero) => {
    return new Intl.NumberFormat('es-ES').format(numero);
};

const getEstiloMaquina = (maquina) => {
    if (maquina.simulacion && maquina.simulacion.estado === 'produciendo') {
        return 'background: linear-gradient(145deg, #E1F5FE, #B3E5FC); border-left: 4px solid #1976D2;';
    } else if (maquina.estado_maquina === 'operativa') {
        return 'background: linear-gradient(145deg, #F1F8E9, #DCEDC8); border-left: 4px solid #689F38;';
    } else if (maquina.estado_maquina === 'mantenimiento') {
        return 'background: linear-gradient(145deg, #FFF3E0, #FFE0B2); border-left: 4px solid #F57C00;';
    } else {
        return 'background: linear-gradient(145deg, #FFEBEE, #FFCDD2); border-left: 4px solid #D32F2F;';
    }
};

const getIconoEstado = (maquina) => {
    if (maquina.simulacion && maquina.simulacion.estado === 'produciendo') return '🔴';
    if (maquina.estado_maquina === 'operativa') return '🟢';
    if (maquina.estado_maquina === 'mantenimiento') return '🔧';
    return '⛔';
};

const getTextoEstado = (maquina) => {
    if (maquina.simulacion && maquina.simulacion.estado === 'produciendo') return 'PRODUCIENDO';
    if (maquina.estado_maquina === 'operativa') return 'DISPONIBLE';
    if (maquina.estado_maquina === 'mantenimiento') return 'MANTENIMIENTO';
    return 'PARADA';
};

const getClaseEstado = (maquina) => {
    if (maquina.simulacion && maquina.simulacion.estado === 'produciendo') return 'bg-blue-100 text-blue-800';
    if (maquina.estado_maquina === 'operativa') return 'bg-green-100 text-green-800';
    if (maquina.estado_maquina === 'mantenimiento') return 'bg-orange-100 text-orange-800';
    return 'bg-red-100 text-red-800';
};

const getClasePrioridad = (prioridad) => {
    const clases = {
        'urgente': 'bg-red-100 text-red-800',
        'alta': 'bg-orange-100 text-orange-800',
        'normal': 'bg-yellow-100 text-yellow-800',
        'baja': 'bg-gray-100 text-gray-800',
    };
    return clases[prioridad] || 'bg-gray-100 text-gray-800';
};

const getPrioridadTexto = (prioridad) => {
    const textos = {
        'urgente': '🔴 URGENTE',
        'alta': '🟠 ALTA',
        'normal': '🟡 NORMAL',
        'baja': '⚪ BAJA',
    };
    return textos[prioridad] || prioridad.toUpperCase();
};

const abrirModalAsignarOrden = (maquina) => {
    alert(`Funcionalidad de asignar orden a ${maquina.nombre} - Por implementar en módulo de órdenes`);
};

// Lifecycle
onMounted(() => {
    cargarDatos();
    iniciarAutoRefresh();
});

onBeforeUnmount(() => {
    detenerAutoRefresh();
});
</script>

<style scoped>
.shadow-neumorphic {
    box-shadow: 15px 15px 30px #b3d4f1, -15px -15px 30px #f3ffff;
}
</style>
