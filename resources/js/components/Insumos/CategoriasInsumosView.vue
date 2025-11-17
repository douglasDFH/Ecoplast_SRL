<template>
    <div class="space-y-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="gradient-card p-6 rounded-3xl shadow-lg" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/80 text-sm mb-1">Total Categorías</p>
                        <p class="text-white text-3xl font-bold">{{ categorias.length }}</p>
                    </div>
                    <div class="w-14 h-14 bg-white/20 backdrop-blur-lg rounded-2xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="gradient-card p-6 rounded-3xl shadow-lg" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/80 text-sm mb-1">Biodegradables</p>
                        <p class="text-white text-3xl font-bold">{{ categoriasBiodegradables }}</p>
                    </div>
                    <div class="w-14 h-14 bg-white/20 backdrop-blur-lg rounded-2xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="gradient-card p-6 rounded-3xl shadow-lg" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/80 text-sm mb-1">Total Insumos</p>
                        <p class="text-white text-3xl font-bold">{{ totalInsumos }}</p>
                    </div>
                    <div class="w-14 h-14 bg-white/20 backdrop-blur-lg rounded-2xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Actions -->
        <div class="modern-card p-6 rounded-3xl">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex-1">
                    <input
                        v-model="filtros.busqueda"
                        type="text"
                        placeholder="Buscar por nombre..."
                        class="modern-input px-4 py-2.5 rounded-xl w-full"
                        style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                    />
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
                        Nueva Categoría
                    </button>
                </div>
            </div>
        </div>

        <!-- Categories Table -->
        <div class="modern-card rounded-3xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white">Categoría</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white">Descripción</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-white">Insumos</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-white">Biodegradable</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-white">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-if="loading" class="hover:bg-gray-50/50 transition-colors">
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex items-center justify-center gap-3">
                                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div>
                                    <span>Cargando categorías...</span>
                                </div>
                            </td>
                        </tr>
                        <tr v-else-if="categoriasFiltradas.length === 0" class="hover:bg-gray-50/50 transition-colors">
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                                No se encontraron categorías
                            </td>
                        </tr>
                        <tr v-else v-for="categoria in categoriasFiltradas" :key="categoria.id" class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-gray-900">{{ categoria.nombre_categoria }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-600">{{ categoria.descripcion || 'Sin descripción' }}</p>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ categoria.insumos_count || 0 }} insumos
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span v-if="categoria.es_biodegradable" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Sí
                                </span>
                                <span v-else class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    No
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button
                                        @click="abrirModal(categoria)"
                                        class="p-2 rounded-lg transition-all hover:bg-blue-50 active:scale-95"
                                        title="Editar"
                                    >
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <button
                                        @click="eliminarCategoria(categoria)"
                                        class="p-2 rounded-lg transition-all hover:bg-red-50 active:scale-95"
                                        title="Eliminar"
                                        :disabled="categoria.insumos_count > 0"
                                        :class="categoria.insumos_count > 0 ? 'opacity-50 cursor-not-allowed' : ''"
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
        <ModalCategoriaInsumo
            v-if="mostrarModal"
            :categoria="categoriaSeleccionada"
            @close="cerrarModal"
            @submit="guardarCategoria"
        />
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import api from '../../services/api';
import ModalCategoriaInsumo from './ModalCategoriaInsumo.vue';

const categorias = ref([]);
const loading = ref(false);
const mostrarModal = ref(false);
const categoriaSeleccionada = ref(null);

const filtros = ref({
    busqueda: ''
});

// Computed properties
const categoriasFiltradas = computed(() => {
    return categorias.value.filter(categoria => {
        const matchBusqueda = !filtros.value.busqueda ||
            categoria.nombre_categoria?.toLowerCase().includes(filtros.value.busqueda.toLowerCase()) ||
            categoria.descripcion?.toLowerCase().includes(filtros.value.busqueda.toLowerCase());

        return matchBusqueda;
    });
});

const categoriasBiodegradables = computed(() =>
    categorias.value.filter(c => c.es_biodegradable).length
);

const totalInsumos = computed(() =>
    categorias.value.reduce((sum, cat) => sum + (cat.insumos_count || 0), 0)
);

// Methods
const loadCategorias = async () => {
    loading.value = true;
    try {
        const response = await api.get('/categorias-insumos');
        if (response.data && response.data.data && response.data.data.data) {
            categorias.value = response.data.data.data;
        } else {
            categorias.value = [];
        }
    } catch (error) {
        console.error('Error al cargar categorías:', error);
        categorias.value = [];
    } finally {
        loading.value = false;
    }
};

const abrirModal = (categoria = null) => {
    categoriaSeleccionada.value = categoria;
    mostrarModal.value = true;
};

const cerrarModal = () => {
    mostrarModal.value = false;
    categoriaSeleccionada.value = null;
};

const guardarCategoria = async (data) => {
    try {
        if (categoriaSeleccionada.value) {
            await api.put(`/categorias-insumos/${categoriaSeleccionada.value.id}`, data);
        } else {
            await api.post('/categorias-insumos', data);
        }
        await loadCategorias();
        cerrarModal();
    } catch (error) {
        console.error('Error al guardar categoría:', error);
        throw error;
    }
};

const eliminarCategoria = async (categoria) => {
    if (categoria.insumos_count > 0) {
        alert('No se puede eliminar una categoría que tiene insumos asociados');
        return;
    }

    if (!confirm(`¿Está seguro de eliminar la categoría "${categoria.nombre_categoria}"?`)) {
        return;
    }

    try {
        await api.delete(`/categorias-insumos/${categoria.id}`);
        await loadCategorias();
    } catch (error) {
        console.error('Error al eliminar categoría:', error);
        alert('Error al eliminar la categoría');
    }
};

const limpiarFiltros = () => {
    filtros.value = {
        busqueda: ''
    };
};

// Lifecycle
onMounted(() => {
    loadCategorias();
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
