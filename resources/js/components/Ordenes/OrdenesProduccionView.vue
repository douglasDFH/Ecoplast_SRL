<template>
    <div class="ordenes-produccion-view">
        <!-- Header con Acciones -->
        <div class="flex items-center justify-between mb-6 p-6 rounded-3xl" style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); box-shadow: 12px 12px 24px #b3d4f1, -12px -12px 24px #f3ffff;">
            <div>
                <h1 class="text-3xl font-bold" style="color: #263238;">Órdenes de Producción</h1>
                <p class="text-sm mt-1 font-medium" style="color: #607D8B;">Gestión y seguimiento de órdenes de producción</p>
            </div>
            <button
                @click="abrirModalCrear"
                class="px-6 py-3 text-white rounded-2xl font-bold transition-all active:scale-95 flex items-center space-x-2"
                style="background: linear-gradient(145deg, #2E7D32, #4CAF50); box-shadow: 10px 10px 20px #b3d4f1, -10px -10px 20px #f3ffff;"
                @mouseover="e => e.target.style.boxShadow='7px 7px 14px #b3d4f1, -7px -7px 14px #f3ffff'"
                @mouseout="e => e.target.style.boxShadow='10px 10px 20px #b3d4f1, -10px -10px 20px #f3ffff'"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <span>Nueva Orden</span>
            </button>
        </div>

        <!-- Tarjetas de Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="p-6 rounded-3xl border-l-4 border-blue-500" style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); box-shadow: 12px 12px 24px #b3d4f1, -12px -12px 24px #f3ffff;">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold" style="color: #607D8B;">Pendientes</p>
                        <p class="text-3xl font-bold" style="color: #263238;">{{ ordenesPendientes.length }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center" style="background: linear-gradient(145deg, #BBDEFB, #90CAF9); box-shadow: 5px 5px 10px #b3d4f1, -5px -5px 10px #f3ffff;">
                        <span class="text-2xl">📋</span>
                    </div>
                </div>
            </div>

            <div class="p-6 rounded-3xl border-l-4 border-yellow-500" style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); box-shadow: 12px 12px 24px #b3d4f1, -12px -12px 24px #f3ffff;">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold" style="color: #607D8B;">En Proceso</p>
                        <p class="text-3xl font-bold" style="color: #263238;">{{ ordenesEnProceso.length }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center" style="background: linear-gradient(145deg, #FFF9C4, #FFF59D); box-shadow: 5px 5px 10px #b3d4f1, -5px -5px 10px #f3ffff;">
                        <span class="text-2xl">⚙️</span>
                    </div>
                </div>
            </div>

            <div class="p-6 rounded-3xl border-l-4 border-green-500" style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); box-shadow: 12px 12px 24px #b3d4f1, -12px -12px 24px #f3ffff;">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold" style="color: #607D8B;">Completadas</p>
                        <p class="text-3xl font-bold" style="color: #263238;">{{ ordenesCompletadas.length }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center" style="background: linear-gradient(145deg, #C8E6C9, #A5D6A7); box-shadow: 5px 5px 10px #b3d4f1, -5px -5px 10px #f3ffff;">
                        <span class="text-2xl">✓</span>
                    </div>
                </div>
            </div>

            <div class="p-6 rounded-3xl border-l-4 border-red-500" style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); box-shadow: 12px 12px 24px #b3d4f1, -12px -12px 24px #f3ffff;">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold" style="color: #607D8B;">Canceladas</p>
                        <p class="text-3xl font-bold" style="color: #263238;">{{ ordenesCanceladas.length }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center" style="background: linear-gradient(145deg, #FFCDD2, #EF9A9A); box-shadow: 5px 5px 10px #b3d4f1, -5px -5px 10px #f3ffff;">
                        <span class="text-2xl">✕</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="p-6 rounded-3xl mb-6" style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); box-shadow: 12px 12px 24px #b3d4f1, -12px -12px 24px #f3ffff;">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-bold mb-2" style="color: #455A64;">Estado</label>
                    <select
                        v-model="filtros.estado"
                        @change="aplicarFiltros"
                        class="w-full px-3 py-2 border-0 rounded-xl focus:ring-2 focus:ring-blue-500"
                        style="background: linear-gradient(145deg, #f5f9fc, #e3f2fd); box-shadow: inset 5px 5px 10px #d0dfe8, inset -5px -5px 10px #ffffff; color: #263238;"
                    >
                        <option value="">Todos</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="programada">Programada</option>
                        <option value="en_proceso">En Proceso</option>
                        <option value="pausada">Pausada</option>
                        <option value="completada">Completada</option>
                        <option value="cancelada">Cancelada</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold mb-2" style="color: #455A64;">Prioridad</label>
                    <select
                        v-model="filtros.prioridad"
                        @change="aplicarFiltros"
                        class="w-full px-3 py-2 border-0 rounded-xl focus:ring-2 focus:ring-blue-500"
                        style="background: linear-gradient(145deg, #f5f9fc, #e3f2fd); box-shadow: inset 5px 5px 10px #d0dfe8, inset -5px -5px 10px #ffffff; color: #263238;"
                    >
                        <option value="">Todas</option>
                        <option value="baja">Baja</option>
                        <option value="media">Media</option>
                        <option value="alta">Alta</option>
                        <option value="urgente">Urgente</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Desde</label>
                    <input
                        type="date"
                        v-model="filtros.fecha_desde"
                        @change="aplicarFiltros"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    />
                </div>

                <div>
                    <label class="block text-sm font-bold mb-2" style="color: #455A64;">Fecha Hasta</label>
                    <input
                        type="date"
                        v-model="filtros.fecha_hasta"
                        @change="aplicarFiltros"
                        class="w-full px-3 py-2 border-0 rounded-xl focus:ring-2 focus:ring-blue-500"
                        style="background: linear-gradient(145deg, #f5f9fc, #e3f2fd); box-shadow: inset 5px 5px 10px #d0dfe8, inset -5px -5px 10px #ffffff; color: #263238;"
                    />
                </div>

                <div class="flex items-end">
                    <button
                        @click="limpiarFiltros"
                        class="w-full px-4 py-2 rounded-xl font-bold transition-all active:scale-95"
                        style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); box-shadow: 8px 8px 16px #b3d4f1, -8px -8px 16px #f3ffff; color: #455A64;"
                        @mouseover="e => e.target.style.boxShadow='5px 5px 10px #b3d4f1, -5px -5px 10px #f3ffff'"
                        @mouseout="e => e.target.style.boxShadow='8px 8px 16px #b3d4f1, -8px -8px 16px #f3ffff'"
                    >
                        Limpiar Filtros
                    </button>
                </div>
            </div>
        </div>

        <!-- Tabla de Órdenes -->
        <div class="rounded-3xl overflow-hidden" style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); box-shadow: 12px 12px 24px #b3d4f1, -12px -12px 24px #f3ffff;">
            <div v-if="loading" class="flex items-center justify-center py-12">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
            </div>

            <div v-else-if="ordenes.length === 0" class="text-center py-12">
                <svg class="w-16 h-16 mx-auto mb-4" style="color: #90A4AE;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="text-lg font-semibold mb-4" style="color: #607D8B;">No hay órdenes de producción</p>
                <button
                    @click="abrirModalCrear"
                    class="px-6 py-2 text-white rounded-2xl font-bold transition-all active:scale-95"
                    style="background: linear-gradient(145deg, #2E7D32, #4CAF50); box-shadow: 10px 10px 20px #b3d4f1, -10px -10px 20px #f3ffff;"
                >
                    Crear Primera Orden
                </button>
            </div>

            <table v-else class="min-w-full divide-y" style="divide-color: #B3D4F1;">
                <thead style="background: linear-gradient(145deg, #d0e8f7, #b3d4f1);">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider" style="color: #455A64;">Código</th>
                        <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider" style="color: #455A64;">Producto</th>
                        <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider" style="color: #455A64;">Cantidad</th>
                        <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider" style="color: #455A64;">Progreso</th>
                        <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider" style="color: #455A64;">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider" style="color: #455A64;">Prioridad</th>
                        <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider" style="color: #455A64;">Máquina</th>
                        <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider" style="color: #455A64;">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="divide-color: #B3D4F1;">
                    <tr
                        v-for="orden in ordenes"
                        :key="orden.id"
                        class="transition-all hover:bg-opacity-50"
                        style="background: linear-gradient(145deg, #f5f9fc, #e3f2fd);"
                    >
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-mono text-sm font-bold" style="color: #263238;">{{ orden.codigo_orden }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold" style="color: #263238;">{{ orden.producto?.nombre || 'N/A' }}</div>
                            <div class="text-xs font-medium" style="color: #607D8B;">{{ orden.producto?.codigo || '' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold" style="color: #263238;">{{ orden.cantidad_producida || 0 }} / {{ orden.cantidad_requerida }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <div class="w-24 rounded-full h-2" style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); box-shadow: inset 3px 3px 6px #b3d4f1, inset -3px -3px 6px #f3ffff;">
                                    <div
                                        class="h-2 rounded-full transition-all"
                                        :class="getProgresoColorClass(orden.progreso)"
                                        :style="{ width: (orden.progreso || 0) + '%' }"
                                    ></div>
                                </div>
                                <span class="text-sm font-bold" style="color: #263238;">{{ orden.progreso || 0 }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span
                                class="px-3 py-1 text-xs font-bold rounded-full"
                                :class="getEstadoBadgeClass(orden.estado)"
                            >
                                {{ getEstadoTexto(orden.estado) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span
                                class="px-2 py-1 text-xs font-bold rounded"
                                :class="getPrioridadBadgeClass(orden.prioridad)"
                            >
                                {{ orden.prioridad?.toUpperCase() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold" style="color: #263238;">
                            {{ orden.maquina?.nombre_maquina || 'Sin asignar' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="flex items-center space-x-2">
                                <button
                                    v-if="orden.estado === 'pendiente' || orden.estado === 'programada'"
                                    @click="handleIniciar(orden)"
                                    class="p-2 text-green-600 rounded-xl transition-all active:scale-95"
                                    style="background: linear-gradient(145deg, #e8f5e9, #c8e6c9); box-shadow: 5px 5px 10px #d0dfe8, -5px -5px 10px #ffffff;"
                                    title="Iniciar"
                                >
                                    ▶
                                </button>
                                <button
                                    v-if="orden.estado === 'en_proceso'"
                                    @click="handleFinalizar(orden)"
                                    class="p-2 text-blue-600 rounded-xl transition-all active:scale-95"
                                    style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); box-shadow: 5px 5px 10px #d0dfe8, -5px -5px 10px #ffffff;"
                                    title="Finalizar"
                                >
                                    ✓
                                </button>
                                <button
                                    @click="abrirModalEditar(orden)"
                                    class="p-2 rounded-xl transition-all active:scale-95"
                                    style="background: linear-gradient(145deg, #f5f5f5, #e0e0e0); box-shadow: 5px 5px 10px #d0dfe8, -5px -5px 10px #ffffff; color: #455A64;"
                                    title="Editar"
                                >
                                    ✎
                                </button>
                                <button
                                    v-if="orden.estado !== 'completada' && orden.estado !== 'cancelada'"
                                    @click="handleCancelar(orden)"
                                    class="p-2 text-red-600 rounded-xl transition-all active:scale-95"
                                    style="background: linear-gradient(145deg, #ffebee, #ffcdd2); box-shadow: 5px 5px 10px #d0dfe8, -5px -5px 10px #ffffff;"
                                    title="Cancelar"
                                >
                                    ✕
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Modal Crear/Editar -->
        <ModalOrdenProduccion
            v-if="mostrarModal"
            :orden="ordenSeleccionada"
            @close="cerrarModal"
            @guardar="handleGuardar"
        />
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useOrdenes } from '@/composables/useOrdenes.js';
import ModalOrdenProduccion from './ModalOrdenProduccion.vue';

const {
    loading,
    ordenes,
    ordenesPendientes,
    ordenesEnProceso,
    ordenesCompletadas,
    ordenesCanceladas,
    filtros,
    loadOrdenes,
    iniciarOrden,
    finalizarOrden,
    cancelarOrden,
    createOrden,
    updateOrden,
    aplicarFiltros: aplicarFiltrosComposable,
    limpiarFiltros: limpiarFiltrosComposable,
} = useOrdenes();

const mostrarModal = ref(false);
const ordenSeleccionada = ref(null);

// Métodos
const abrirModalCrear = () => {
    ordenSeleccionada.value = null;
    mostrarModal.value = true;
};

const abrirModalEditar = (orden) => {
    ordenSeleccionada.value = { ...orden };
    mostrarModal.value = true;
};

const cerrarModal = () => {
    mostrarModal.value = false;
    ordenSeleccionada.value = null;
};

const handleGuardar = async (data) => {
    try {
        if (ordenSeleccionada.value?.id) {
            await updateOrden(ordenSeleccionada.value.id, data);
        } else {
            await createOrden(data);
        }
        cerrarModal();
    } catch (error) {
        alert('Error al guardar la orden');
    }
};

const handleIniciar = async (orden) => {
    if (confirm(`¿Iniciar producción de la orden ${orden.codigo_orden}?`)) {
        try {
            await iniciarOrden(orden.id);
        } catch (error) {
            alert('Error al iniciar orden');
        }
    }
};

const handleFinalizar = async (orden) => {
    if (confirm(`¿Finalizar producción de la orden ${orden.codigo_orden}?`)) {
        try {
            await finalizarOrden(orden.id, {
                cantidad_producida: orden.cantidad_producida || orden.cantidad_requerida
            });
        } catch (error) {
            alert('Error al finalizar orden');
        }
    }
};

const handleCancelar = async (orden) => {
    const motivo = prompt(`¿Motivo de cancelación de la orden ${orden.codigo_orden}?`);
    if (motivo) {
        try {
            await cancelarOrden(orden.id, motivo);
        } catch (error) {
            alert('Error al cancelar orden');
        }
    }
};

const aplicarFiltros = () => {
    aplicarFiltrosComposable(filtros.value);
};

const limpiarFiltros = () => {
    limpiarFiltrosComposable();
};

// Funciones de estilo
const getEstadoBadgeClass = (estado) => {
    const classes = {
        'pendiente': 'bg-gray-100 text-gray-700',
        'programada': 'bg-blue-100 text-blue-700',
        'en_proceso': 'bg-yellow-100 text-yellow-700',
        'pausada': 'bg-orange-100 text-orange-700',
        'completada': 'bg-green-100 text-green-700',
        'cancelada': 'bg-red-100 text-red-700',
    };
    return classes[estado] || classes.pendiente;
};

const getPrioridadBadgeClass = (prioridad) => {
    const classes = {
        'baja': 'bg-gray-200 text-gray-700',
        'media': 'bg-blue-200 text-blue-700',
        'alta': 'bg-orange-200 text-orange-700',
        'urgente': 'bg-red-200 text-red-700',
    };
    return classes[prioridad] || classes.media;
};

const getProgresoColorClass = (progreso) => {
    if (progreso >= 100) return 'bg-green-500';
    if (progreso >= 75) return 'bg-blue-500';
    if (progreso >= 50) return 'bg-yellow-500';
    return 'bg-orange-500';
};

const getEstadoTexto = (estado) => {
    const textos = {
        'pendiente': 'PENDIENTE',
        'programada': 'PROGRAMADA',
        'en_proceso': 'EN PROCESO',
        'pausada': 'PAUSADA',
        'completada': 'COMPLETADA',
        'cancelada': 'CANCELADA',
    };
    return textos[estado] || estado.toUpperCase();
};

// Lifecycle
onMounted(async () => {
    await loadOrdenes();
});
</script>

<style scoped>
.ordenes-produccion-view {
    padding: 2rem;
    background-color: #f9fafb;
    min-height: 100vh;
}
</style>
