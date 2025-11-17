<template>
    <div class="min-h-screen p-8" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-white mb-2">Tipos de Materiales</h1>
                <p class="text-indigo-100">Gestión de tipos de materiales biodegradables y clasificación</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="neo-card p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Tipos</p>
                            <p class="text-3xl font-bold text-gray-800 mt-1">{{ totalTipos }}</p>
                        </div>
                        <div class="neo-icon-circle bg-blue-100">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="neo-card p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Activos</p>
                            <p class="text-3xl font-bold text-green-600 mt-1">{{ tiposActivos }}</p>
                        </div>
                        <div class="neo-icon-circle bg-green-100">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="neo-card p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Polímeros</p>
                            <p class="text-3xl font-bold text-purple-600 mt-1">{{ polimerosBiodegradables }}</p>
                        </div>
                        <div class="neo-icon-circle bg-purple-100">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="neo-card p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Con Insumos</p>
                            <p class="text-3xl font-bold text-indigo-600 mt-1">{{ tiposConInsumos }}</p>
                        </div>
                        <div class="neo-icon-circle bg-indigo-100">
                            <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Controls -->
            <div class="neo-card p-6 mb-6">
                <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
                    <div class="flex-1 w-full md:w-auto">
                        <div class="relative">
                            <input
                                v-model="buscar"
                                type="text"
                                placeholder="Buscar por nombre o código..."
                                class="neo-input w-full pl-10"
                            />
                            <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <select v-model="filtroClasificacion" class="neo-select">
                            <option value="">Todas las clasificaciones</option>
                            <option value="Polímero Biodegradable">Polímero Biodegradable</option>
                            <option value="Aditivo">Aditivo</option>
                            <option value="Pigmento">Pigmento</option>
                            <option value="Otro">Otro</option>
                        </select>

                        <select v-model="filtroActivo" class="neo-select">
                            <option value="">Todos los estados</option>
                            <option value="true">Activos</option>
                            <option value="false">Inactivos</option>
                        </select>

                        <button @click="abrirModal()" class="neo-button bg-gradient-to-r from-purple-500 to-indigo-600 text-white px-6">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Nuevo Tipo
                        </button>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="neo-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b-2 border-gray-200">
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Código</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Nombre</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Clasificación</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Insumos</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Degradación</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-for="tipo in tiposPaginados" :key="tipo.id" class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div
                                            class="w-4 h-4 rounded-full mr-3"
                                            :style="{ backgroundColor: tipo.color_referencia || '#CBD5E0' }"
                                        ></div>
                                        <span class="font-mono text-sm font-semibold text-gray-900">{{ tipo.codigo }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">{{ tipo.nombre }}</div>
                                    <div v-if="tipo.descripcion" class="text-xs text-gray-500 truncate max-w-xs">
                                        {{ tipo.descripcion.substring(0, 60) }}{{ tipo.descripcion.length > 60 ? '...' : '' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span :class="getClasificacionClass(tipo.clasificacion)" class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full">
                                        {{ tipo.clasificacion }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                        <span class="text-sm font-medium text-gray-700">{{ tipo.insumos_count || 0 }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div v-if="tipo.tiempo_degradacion_min && tipo.tiempo_degradacion_max" class="text-sm text-gray-600">
                                        {{ tipo.tiempo_degradacion_min }}-{{ tipo.tiempo_degradacion_max }} días
                                    </div>
                                    <div v-else class="text-sm text-gray-400">N/A</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span v-if="tipo.activo" class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Activo
                                    </span>
                                    <span v-else class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Inactivo
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center space-x-2">
                                        <button @click="abrirModal(tipo)" class="neo-button-small bg-blue-500 text-white hover:bg-blue-600" title="Editar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                        <button @click="eliminarTipo(tipo)" class="neo-button-small bg-red-500 text-white hover:bg-red-600" title="Eliminar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Mostrando {{ tiposFiltrados.length }} de {{ tiposMateriales.length }} tipos
                    </div>
                    <div class="flex gap-2">
                        <button
                            @click="paginaActual > 1 && paginaActual--"
                            :disabled="paginaActual === 1"
                            class="neo-button-small"
                            :class="paginaActual === 1 ? 'opacity-50 cursor-not-allowed' : ''"
                        >
                            Anterior
                        </button>
                        <button
                            @click="paginaActual < totalPaginas && paginaActual++"
                            :disabled="paginaActual === totalPaginas"
                            class="neo-button-small"
                            :class="paginaActual === totalPaginas ? 'opacity-50 cursor-not-allowed' : ''"
                        >
                            Siguiente
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <ModalTipoMaterial
            :visible="mostrarModal"
            :tipoMaterial="tipoSeleccionado"
            @close="cerrarModal"
            @guardado="cargarTipos"
        />
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import ModalTipoMaterial from './ModalTipoMaterial.vue';

const tiposMateriales = ref([]);
const buscar = ref('');
const filtroClasificacion = ref('');
const filtroActivo = ref('');
const mostrarModal = ref(false);
const tipoSeleccionado = ref(null);
const paginaActual = ref(1);
const itemsPorPagina = 10;

// Computed
const totalTipos = computed(() => tiposMateriales.value.length);
const tiposActivos = computed(() => tiposMateriales.value.filter(t => t.activo).length);
const polimerosBiodegradables = computed(() => tiposMateriales.value.filter(t => t.clasificacion === 'Polímero Biodegradable').length);
const tiposConInsumos = computed(() => tiposMateriales.value.filter(t => t.insumos_count > 0).length);

const tiposFiltrados = computed(() => {
    return tiposMateriales.value.filter(tipo => {
        const cumpleBuscar = !buscar.value ||
            tipo.nombre.toLowerCase().includes(buscar.value.toLowerCase()) ||
            tipo.codigo.toLowerCase().includes(buscar.value.toLowerCase()) ||
            (tipo.descripcion && tipo.descripcion.toLowerCase().includes(buscar.value.toLowerCase()));

        const cumpleClasificacion = !filtroClasificacion.value || tipo.clasificacion === filtroClasificacion.value;

        const cumpleActivo = !filtroActivo.value ||
            (filtroActivo.value === 'true' && tipo.activo) ||
            (filtroActivo.value === 'false' && !tipo.activo);

        return cumpleBuscar && cumpleClasificacion && cumpleActivo;
    });
});

const totalPaginas = computed(() => Math.ceil(tiposFiltrados.value.length / itemsPorPagina));

const tiposPaginados = computed(() => {
    const inicio = (paginaActual.value - 1) * itemsPorPagina;
    const fin = inicio + itemsPorPagina;
    return tiposFiltrados.value.slice(inicio, fin);
});

// Methods
const cargarTipos = async () => {
    try {
        const response = await axios.get('/api/tipos-materiales', {
            params: { all: true }
        });

        if (response.data && response.data.data) {
            tiposMateriales.value = response.data.data;
        }
    } catch (error) {
        console.error('Error al cargar tipos de materiales:', error);
        alert('Error al cargar los tipos de materiales');
    }
};

const abrirModal = (tipo = null) => {
    tipoSeleccionado.value = tipo;
    mostrarModal.value = true;
};

const cerrarModal = () => {
    mostrarModal.value = false;
    tipoSeleccionado.value = null;
};

const eliminarTipo = async (tipo) => {
    if (tipo.insumos_count > 0) {
        alert(`No se puede eliminar el tipo "${tipo.nombre}" porque tiene ${tipo.insumos_count} insumo(s) asociado(s).`);
        return;
    }

    if (!confirm(`¿Está seguro de eliminar el tipo de material "${tipo.nombre}"?`)) {
        return;
    }

    try {
        await axios.delete(`/api/tipos-materiales/${tipo.id}`);
        await cargarTipos();
        alert('Tipo de material eliminado exitosamente');
    } catch (error) {
        console.error('Error al eliminar tipo:', error);
        alert(error.response?.data?.message || 'Error al eliminar el tipo de material');
    }
};

const getClasificacionClass = (clasificacion) => {
    const classes = {
        'Polímero Biodegradable': 'bg-green-100 text-green-800',
        'Aditivo': 'bg-yellow-100 text-yellow-800',
        'Pigmento': 'bg-pink-100 text-pink-800',
        'Otro': 'bg-gray-100 text-gray-800'
    };
    return classes[clasificacion] || 'bg-gray-100 text-gray-800';
};

onMounted(() => {
    cargarTipos();
});
</script>

<style scoped>
.neo-card {
    background: #E0E5EC;
    border-radius: 20px;
    box-shadow: 9px 9px 16px rgba(163, 177, 198, 0.6), -9px -9px 16px rgba(255, 255, 255, 0.5);
}

.neo-input, .neo-select {
    background: #E0E5EC;
    border: none;
    border-radius: 12px;
    padding: 12px 16px;
    box-shadow: inset 6px 6px 10px rgba(163, 177, 198, 0.4), inset -6px -6px 10px rgba(255, 255, 255, 0.5);
    transition: all 0.3s ease;
}

.neo-input:focus, .neo-select:focus {
    outline: none;
    box-shadow: inset 4px 4px 8px rgba(163, 177, 198, 0.5), inset -4px -4px 8px rgba(255, 255, 255, 0.6);
}

.neo-button {
    background: #E0E5EC;
    border: none;
    border-radius: 12px;
    padding: 12px 24px;
    font-weight: 600;
    box-shadow: 6px 6px 12px rgba(163, 177, 198, 0.6), -6px -6px 12px rgba(255, 255, 255, 0.5);
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.neo-button:active {
    box-shadow: inset 4px 4px 8px rgba(163, 177, 198, 0.5), inset -4px -4px 8px rgba(255, 255, 255, 0.6);
}

.neo-button-small {
    background: #E0E5EC;
    border: none;
    border-radius: 10px;
    padding: 8px 12px;
    box-shadow: 4px 4px 8px rgba(163, 177, 198, 0.6), -4px -4px 8px rgba(255, 255, 255, 0.5);
    transition: all 0.3s ease;
}

.neo-button-small:active {
    box-shadow: inset 3px 3px 6px rgba(163, 177, 198, 0.5), inset -3px -3px 6px rgba(255, 255, 255, 0.6);
}

.neo-icon-circle {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 6px 6px 12px rgba(163, 177, 198, 0.3), -6px -6px 12px rgba(255, 255, 255, 0.5);
}
</style>
