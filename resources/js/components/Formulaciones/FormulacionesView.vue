<template>
    <div class="space-y-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="gradient-card p-6 rounded-3xl shadow-lg" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/80 text-sm mb-1">Total Formulaciones</p>
                        <p class="text-white text-3xl font-bold">{{ formulaciones.length }}</p>
                    </div>
                    <div class="w-14 h-14 bg-white/20 backdrop-blur-lg rounded-2xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="gradient-card p-6 rounded-3xl shadow-lg" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/80 text-sm mb-1">Activas</p>
                        <p class="text-white text-3xl font-bold">{{ formulacionesActivas }}</p>
                    </div>
                    <div class="w-14 h-14 bg-white/20 backdrop-blur-lg rounded-2xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="gradient-card p-6 rounded-3xl shadow-lg" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/80 text-sm mb-1">Biodegradables</p>
                        <p class="text-white text-3xl font-bold">{{ formulacionesBiodegradables }}</p>
                    </div>
                    <div class="w-14 h-14 bg-white/20 backdrop-blur-lg rounded-2xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="gradient-card p-6 rounded-3xl shadow-lg" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/80 text-sm mb-1">En Prueba</p>
                        <p class="text-white text-3xl font-bold">{{ formulacionesEnPrueba }}</p>
                    </div>
                    <div class="w-14 h-14 bg-white/20 backdrop-blur-lg rounded-2xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
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
                        v-model="filtros.tipo_producto"
                        class="modern-input px-4 py-2.5 rounded-xl"
                        style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                    >
                        <option value="">Todos los tipos</option>
                        <option value="Film biodegradable">Film biodegradable</option>
                        <option value="Bolsa compostable">Bolsa compostable</option>
                        <option value="Empaque sostenible">Empaque sostenible</option>
                        <option value="Contenedor">Contenedor</option>
                    </select>
                    <select
                        v-model="filtros.activa"
                        class="modern-input px-4 py-2.5 rounded-xl"
                        style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                    >
                        <option value="">Todos los estados</option>
                        <option value="1">Activas</option>
                        <option value="0">Inactivas</option>
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
                        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Nueva Formulación
                    </button>
                </div>
            </div>
        </div>

        <!-- Formulas Table -->
        <div class="modern-card rounded-3xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white">Código</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white">Fórmula</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white">Tipo Producto</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-white">Versión</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-white">Tiempo Degradación</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-white">Certificaciones</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-white">Estado</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-white">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-if="loading" class="hover:bg-gray-50/50 transition-colors">
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex items-center justify-center gap-3">
                                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div>
                                    <span>Cargando formulaciones...</span>
                                </div>
                            </td>
                        </tr>
                        <tr v-else-if="formulacionesFiltradas.length === 0" class="hover:bg-gray-50/50 transition-colors">
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                </svg>
                                No se encontraron formulaciones
                            </td>
                        </tr>
                        <tr v-else v-for="formulacion in formulacionesFiltradas" :key="formulacion.id" class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-mono text-sm font-medium text-gray-900">{{ formulacion.codigo_formula }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <p class="font-semibold text-gray-900">{{ formulacion.nombre_formula }}</p>
                                    <p class="text-sm text-gray-500">{{ formulacion.descripcion?.substring(0, 40) }}...</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                                    :style="getTipoProductoBadgeStyle(formulacion.tipo_producto_destino)">
                                    {{ formulacion.tipo_producto_destino || 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-mono bg-gray-100 text-gray-800">
                                    v{{ formulacion.version }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <p class="font-semibold text-gray-900">{{ formulacion.tiempo_degradacion_estimado || 'N/A' }} días</p>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span v-if="formulacion.certificaciones"
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Certificado
                                </span>
                                <span v-else class="text-xs text-gray-400">Sin certificar</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                                    :class="formulacion.activa ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'">
                                    {{ formulacion.activa ? 'Activa' : 'Inactiva' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button
                                        @click="verDetalle(formulacion)"
                                        class="p-2 rounded-lg transition-all hover:bg-green-50 active:scale-95"
                                        title="Ver detalle"
                                    >
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                    <button
                                        @click="abrirModal(formulacion)"
                                        class="p-2 rounded-lg transition-all hover:bg-blue-50 active:scale-95"
                                        title="Editar"
                                    >
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <button
                                        @click="clonarFormulacion(formulacion)"
                                        class="p-2 rounded-lg transition-all hover:bg-purple-50 active:scale-95"
                                        title="Clonar"
                                    >
                                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                    </button>
                                    <button
                                        @click="toggleActiva(formulacion)"
                                        class="p-2 rounded-lg transition-all hover:bg-yellow-50 active:scale-95"
                                        :title="formulacion.activa ? 'Desactivar' : 'Activar'"
                                    >
                                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
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
        <ModalFormulacion
            v-if="mostrarModal"
            :formulacion="formulacionSeleccionada"
            @close="cerrarModal"
            @submit="guardarFormulacion"
        />
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import api from '../../services/api';
import ModalFormulacion from './ModalFormulacion.vue';

const formulaciones = ref([]);
const loading = ref(false);
const mostrarModal = ref(false);
const formulacionSeleccionada = ref(null);

const filtros = ref({
    busqueda: '',
    tipo_producto: '',
    activa: ''
});

// Computed properties
const formulacionesFiltradas = computed(() => {
    return formulaciones.value.filter(formulacion => {
        const matchBusqueda = !filtros.value.busqueda ||
            formulacion.nombre_formula?.toLowerCase().includes(filtros.value.busqueda.toLowerCase()) ||
            formulacion.codigo_formula?.toLowerCase().includes(filtros.value.busqueda.toLowerCase());

        const matchTipo = !filtros.value.tipo_producto ||
            formulacion.tipo_producto_destino === filtros.value.tipo_producto;

        const matchActiva = filtros.value.activa === '' ||
            formulacion.activa === parseInt(filtros.value.activa);

        return matchBusqueda && matchTipo && matchActiva;
    });
});

const formulacionesActivas = computed(() =>
    formulaciones.value.filter(f => f.activa).length
);

const formulacionesBiodegradables = computed(() =>
    formulaciones.value.filter(f => f.certificaciones).length
);

const formulacionesEnPrueba = computed(() =>
    formulaciones.value.filter(f => !f.aprobado && f.activa).length
);

// Methods
const loadFormulaciones = async () => {
    loading.value = true;
    try {
        const response = await api.get('/formulaciones');
        // FormulacionController returns: {success: true, data: {paginationObject}}
        // The actual array is in response.data.data.data
        if (response.data && response.data.data && response.data.data.data) {
            formulaciones.value = response.data.data.data;
        } else {
            formulaciones.value = [];
        }
    } catch (error) {
        console.error('Error al cargar formulaciones:', error);
        formulaciones.value = [];
    } finally {
        loading.value = false;
    }
};

const abrirModal = (formulacion = null) => {
    formulacionSeleccionada.value = formulacion;
    mostrarModal.value = true;
};

const cerrarModal = () => {
    mostrarModal.value = false;
    formulacionSeleccionada.value = null;
};

const guardarFormulacion = async (data) => {
    try {
        if (formulacionSeleccionada.value) {
            await api.put(`/formulaciones/${formulacionSeleccionada.value.id}`, data);
        } else {
            await api.post('/formulaciones', data);
        }
        await loadFormulaciones();
        cerrarModal();
    } catch (error) {
        console.error('Error al guardar formulación:', error);
        throw error;
    }
};

const verDetalle = (formulacion) => {
    alert(`Detalle de ${formulacion.nombre_formula}\n\nCódigo: ${formulacion.codigo_formula}\nVersión: ${formulacion.version}\nTipo: ${formulacion.tipo_producto}\nRendimiento: ${formulacion.rendimiento_estimado}%\n\n${formulacion.descripcion || 'Sin descripción'}`);
};

const clonarFormulacion = async (formulacion) => {
    if (!confirm(`¿Desea crear una copia de "${formulacion.nombre_formula}"?`)) {
        return;
    }

    try {
        await api.post(`/formulaciones/${formulacion.id}/clonar`);
        await loadFormulaciones();
        alert('Formulación clonada exitosamente');
    } catch (error) {
        console.error('Error al clonar formulación:', error);
        alert('Error al clonar la formulación');
    }
};

const toggleActiva = async (formulacion) => {
    if (!confirm(`¿Está seguro de ${formulacion.activa ? 'desactivar' : 'activar'} esta formulación?`)) {
        return;
    }
    try {
        await api.patch(`/formulaciones/${formulacion.id}/toggle-activa`);
        await loadFormulaciones();
    } catch (error) {
        console.error('Error al cambiar estado:', error);
        alert('Error al cambiar el estado de la formulación');
    }
};

const limpiarFiltros = () => {
    filtros.value = {
        busqueda: '',
        tipo_producto: '',
        activa: ''
    };
};

const getTipoProductoBadgeStyle = (tipo) => {
    const styles = {
        'Film biodegradable': 'background: #DBEAFE; color: #1E40AF;',
        'Bolsa compostable': 'background: #D1FAE5; color: #065F46;',
        'Empaque sostenible': 'background: #FEF3C7; color: #92400E;',
        'Contenedor': 'background: #FCE7F3; color: #9F1239;'
    };
    return styles[tipo] || 'background: #F3F4F6; color: #374151;';
};

// Lifecycle
onMounted(() => {
    loadFormulaciones();
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
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.modern-btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

.modern-btn-secondary:hover {
    background: #E2E8F0;
}
</style>
