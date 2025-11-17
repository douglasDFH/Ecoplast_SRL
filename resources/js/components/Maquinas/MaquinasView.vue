<template>
    <div class="space-y-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="gradient-card p-6 rounded-3xl shadow-lg" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/80 text-sm mb-1">Total Máquinas</p>
                        <p class="text-white text-3xl font-bold">{{ maquinas.length }}</p>
                    </div>
                    <div class="w-14 h-14 bg-white/20 backdrop-blur-lg rounded-2xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="gradient-card p-6 rounded-3xl shadow-lg" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/80 text-sm mb-1">Operativas</p>
                        <p class="text-white text-3xl font-bold">{{ maquinasOperativas }}</p>
                    </div>
                    <div class="w-14 h-14 bg-white/20 backdrop-blur-lg rounded-2xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="gradient-card p-6 rounded-3xl shadow-lg" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/80 text-sm mb-1">En Mantenimiento</p>
                        <p class="text-white text-3xl font-bold">{{ maquinasEnMantenimiento }}</p>
                    </div>
                    <div class="w-14 h-14 bg-white/20 backdrop-blur-lg rounded-2xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="gradient-card p-6 rounded-3xl shadow-lg" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/80 text-sm mb-1">Fuera de Servicio</p>
                        <p class="text-white text-3xl font-bold">{{ maquinasFueraServicio }}</p>
                    </div>
                    <div class="w-14 h-14 bg-white/20 backdrop-blur-lg rounded-2xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Actions -->
        <div class="modern-card p-6 rounded-3xl">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <input
                        v-model="filtros.busqueda"
                        type="text"
                        placeholder="Buscar por nombre o código..."
                        class="modern-input px-4 py-2.5 rounded-xl"
                        style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                    />
                    <select
                        v-model="filtros.tipo_maquina"
                        class="modern-input px-4 py-2.5 rounded-xl"
                        style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                    >
                        <option value="">Todos los tipos</option>
                        <option value="Extrusora">Extrusora</option>
                        <option value="Moldeadora">Moldeadora</option>
                        <option value="Selladora">Selladora</option>
                        <option value="Cortadora">Cortadora</option>
                        <option value="Mezcladora">Mezcladora</option>
                    </select>
                    <select
                        v-model="filtros.estado"
                        class="modern-input px-4 py-2.5 rounded-xl"
                        style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                    >
                        <option value="">Todos los estados</option>
                        <option value="operativa">Operativa</option>
                        <option value="mantenimiento">En Mantenimiento</option>
                        <option value="parada">Parada</option>
                        <option value="averia">Avería</option>
                    </select>
                </div>
                <div class="flex gap-3">
                    <button
                        @click="limpiarFiltros"
                        class="modern-btn-secondary px-6 py-2.5 rounded-xl font-medium transition-all active:scale-95"
                        style="background: #F1F5F9; color: #475569;"
                    >
                        Limpiar
                    </button>
                    <button
                        @click="abrirModal()"
                        class="modern-btn-primary px-6 py-2.5 rounded-xl font-medium transition-all active:scale-95 flex items-center gap-2"
                        style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Nueva Máquina
                    </button>
                </div>
            </div>
        </div>

        <!-- Machines Table -->
        <div class="modern-card rounded-3xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white">Código</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white">Máquina</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white">Tipo</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-white">Capacidad</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-white">Horas Uso</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-white">Estado</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-white">Mantenimiento</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-white">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-if="loading" class="hover:bg-gray-50/50 transition-colors">
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex items-center justify-center gap-3">
                                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-pink-600"></div>
                                    <span>Cargando máquinas...</span>
                                </div>
                            </td>
                        </tr>
                        <tr v-else-if="maquinasFiltradas.length === 0" class="hover:bg-gray-50/50 transition-colors">
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                </svg>
                                No se encontraron máquinas
                            </td>
                        </tr>
                        <tr v-else v-for="maquina in maquinasFiltradas" :key="maquina.id" class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-mono text-sm font-medium text-gray-900">{{ maquina.codigo_maquina }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <p class="font-semibold text-gray-900">{{ maquina.nombre_maquina }}</p>
                                    <p class="text-sm text-gray-500">{{ maquina.marca }} - {{ maquina.modelo }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                                    :style="getTipoMaquinaBadgeStyle(maquina.tipo?.nombre_tipo || 'N/A')">
                                    {{ maquina.tipo?.nombre_tipo || 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <p class="font-semibold text-gray-900">{{ maquina.capacidad_produccion || 'N/A' }}</p>
                                <p class="text-xs text-gray-500">{{ maquina.unidad_capacidad || '' }}</p>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <p class="font-semibold text-gray-900">{{ maquina.horas_uso_total || 0 }}</p>
                                <p class="text-xs text-gray-500">horas</p>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                                    :class="getEstadoBadgeClass(maquina.estado_actual)">
                                    {{ getEstadoLabel(maquina.estado_actual) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div v-if="maquina.fecha_ultimo_mantenimiento">
                                    <p class="text-sm text-gray-900">{{ formatFecha(maquina.fecha_ultimo_mantenimiento) }}</p>
                                    <p class="text-xs" :class="getNecesitaMantenimientoClass(maquina)">
                                        {{ getNecesitaMantenimientoTexto(maquina) }}
                                    </p>
                                </div>
                                <span v-else class="text-xs text-gray-400">Sin mantenimiento</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button
                                        @click="abrirModal(maquina)"
                                        class="p-2 rounded-lg transition-all hover:bg-blue-50 active:scale-95"
                                        title="Editar"
                                    >
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <button
                                        @click="cambiarEstado(maquina)"
                                        class="p-2 rounded-lg transition-all hover:bg-green-50 active:scale-95"
                                        title="Cambiar estado"
                                    >
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                        </svg>
                                    </button>
                                    <button
                                        @click="eliminarMaquina(maquina)"
                                        class="p-2 rounded-lg transition-all hover:bg-red-50 active:scale-95"
                                        title="Eliminar"
                                    >
                                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal -->
        <ModalMaquina
            v-if="mostrarModal"
            :maquina="maquinaSeleccionada"
            @close="cerrarModal"
            @submit="guardarMaquina"
        />
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import api from '../../services/api';
import ModalMaquina from './ModalMaquina.vue';

const maquinas = ref([]);
const loading = ref(false);
const mostrarModal = ref(false);
const maquinaSeleccionada = ref(null);

const filtros = ref({
    busqueda: '',
    tipo_maquina: '',
    estado: ''
});

// Computed properties
const maquinasFiltradas = computed(() => {
    return maquinas.value.filter(maquina => {
        const matchBusqueda = !filtros.value.busqueda ||
            maquina.nombre_maquina?.toLowerCase().includes(filtros.value.busqueda.toLowerCase()) ||
            maquina.codigo_maquina?.toLowerCase().includes(filtros.value.busqueda.toLowerCase());

        const matchTipo = !filtros.value.tipo_maquina ||
            maquina.tipo?.nombre_tipo === filtros.value.tipo_maquina;

        const matchEstado = !filtros.value.estado ||
            maquina.estado_actual === filtros.value.estado;

        return matchBusqueda && matchTipo && matchEstado;
    });
});

const maquinasOperativas = computed(() =>
    maquinas.value.filter(m => m.estado_actual === 'operativa').length
);

const maquinasEnMantenimiento = computed(() =>
    maquinas.value.filter(m => m.estado_actual === 'mantenimiento').length
);

const maquinasFueraServicio = computed(() =>
    maquinas.value.filter(m => m.estado_actual === 'parada' || m.estado_actual === 'averia').length
);

// Methods
const loadMaquinas = async () => {
    loading.value = true;
    try {
        const response = await api.get('/maquinaria');
        // MaquinariaController returns paginated data in response.data.data.data
        if (response.data.data && response.data.data.data) {
            maquinas.value = response.data.data.data;
        } else if (response.data.data) {
            maquinas.value = Array.isArray(response.data.data) ? response.data.data : [];
        } else {
            maquinas.value = [];
        }
    } catch (error) {
        console.error('Error al cargar máquinas:', error);
        maquinas.value = [];
    } finally {
        loading.value = false;
    }
};

const abrirModal = (maquina = null) => {
    maquinaSeleccionada.value = maquina;
    mostrarModal.value = true;
};

const cerrarModal = () => {
    mostrarModal.value = false;
    maquinaSeleccionada.value = null;
};

const guardarMaquina = async (data) => {
    try {
        if (maquinaSeleccionada.value) {
            await api.put(`/maquinaria/${maquinaSeleccionada.value.id}`, data);
        } else {
            await api.post('/maquinaria', data);
        }
        await loadMaquinas();
        cerrarModal();
    } catch (error) {
        console.error('Error al guardar máquina:', error);
        throw error;
    }
};

const cambiarEstado = async (maquina) => {
    const estados = ['operativa', 'mantenimiento', 'parada', 'averia'];
    const estadoActualIndex = estados.indexOf(maquina.estado_actual);
    const nuevoEstado = estados[(estadoActualIndex + 1) % estados.length];

    if (!confirm(`¿Cambiar estado de "${maquina.nombre_maquina}" a "${getEstadoLabel(nuevoEstado)}"?`)) {
        return;
    }

    try {
        await api.patch(`/maquinaria/${maquina.id}/estado`, {
            estado_actual: nuevoEstado
        });
        await loadMaquinas();
    } catch (error) {
        console.error('Error al cambiar estado:', error);
        alert('Error al cambiar el estado de la máquina');
    }
};

const eliminarMaquina = async (maquina) => {
    if (!confirm(`¿Está seguro de eliminar la máquina "${maquina.nombre_maquina}"? Esta acción no se puede deshacer.`)) {
        return;
    }
    try {
        await api.delete(`/maquinaria/${maquina.id}`);
        await loadMaquinas();
    } catch (error) {
        console.error('Error al eliminar máquina:', error);
        alert('Error al eliminar la máquina');
    }
};

const limpiarFiltros = () => {
    filtros.value = {
        busqueda: '',
        tipo_maquina: '',
        estado: ''
    };
};

const getTipoMaquinaBadgeStyle = (tipo) => {
    const styles = {
        'Extrusora': 'background: #DBEAFE; color: #1E40AF;',
        'Moldeadora': 'background: #D1FAE5; color: #065F46;',
        'Selladora': 'background: #FEF3C7; color: #92400E;',
        'Cortadora': 'background: #FCE7F3; color: #9F1239;',
        'Mezcladora': 'background: #E0E7FF; color: #3730A3;'
    };
    return styles[tipo] || 'background: #F3F4F6; color: #374151;';
};

const getEstadoBadgeClass = (estado) => {
    const classes = {
        'operativa': 'bg-green-100 text-green-800',
        'mantenimiento': 'bg-yellow-100 text-yellow-800',
        'parada': 'bg-orange-100 text-orange-800',
        'averia': 'bg-red-100 text-red-800'
    };
    return classes[estado] || 'bg-gray-100 text-gray-800';
};

const getEstadoLabel = (estado) => {
    const labels = {
        'operativa': 'Operativa',
        'mantenimiento': 'En Mantenimiento',
        'parada': 'Parada',
        'averia': 'Avería'
    };
    return labels[estado] || estado;
};

const formatFecha = (fecha) => {
    if (!fecha) return 'N/A';
    return new Date(fecha).toLocaleDateString('es-PE', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    });
};

const getNecesitaMantenimientoClass = (maquina) => {
    const horasDesdeMantenimiento = (maquina.horas_uso_total || 0) - (maquina.horas_ultimo_mantenimiento || 0);
    const frecuencia = maquina.frecuencia_mantenimiento_horas || 500;

    if (horasDesdeMantenimiento >= frecuencia) return 'text-red-600';
    if (horasDesdeMantenimiento >= frecuencia * 0.8) return 'text-yellow-600';
    return 'text-green-600';
};

const getNecesitaMantenimientoTexto = (maquina) => {
    const horasDesdeMantenimiento = (maquina.horas_uso_total || 0) - (maquina.horas_ultimo_mantenimiento || 0);
    const frecuencia = maquina.frecuencia_mantenimiento_horas || 500;
    const horasRestantes = frecuencia - horasDesdeMantenimiento;

    if (horasRestantes <= 0) return 'Mantenimiento vencido';
    return `Faltan ${horasRestantes}h`;
};

// Lifecycle
onMounted(() => {
    loadMaquinas();
});
</script>

<style scoped>
.modern-card {
    background: white;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
}

.gradient-card {
    transition: transform 0.2s, box-shadow 0.2s;
}

.gradient-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.modern-input {
    transition: all 0.2s;
}

.modern-input:focus {
    outline: none;
    border-color: #f093fb;
    box-shadow: 0 0 0 3px rgba(240, 147, 251, 0.1);
}

.modern-btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

.modern-btn-secondary:hover {
    background: #E2E8F0;
}
</style>
