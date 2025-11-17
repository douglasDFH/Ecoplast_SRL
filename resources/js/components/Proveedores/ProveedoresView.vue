<template>
    <div class="min-h-screen p-8" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-white mb-2">Proveedores</h1>
                <p class="text-indigo-100">Gestión de proveedores de insumos biodegradables</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="neo-card p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Proveedores</p>
                            <p class="text-3xl font-bold text-gray-800 mt-1">{{ totalProveedores }}</p>
                        </div>
                        <div class="neo-icon-circle bg-blue-100">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="neo-card p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Activos</p>
                            <p class="text-3xl font-bold text-green-600 mt-1">{{ proveedoresActivos }}</p>
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
                            <p class="text-sm font-medium text-gray-600">Con Insumos</p>
                            <p class="text-3xl font-bold text-purple-600 mt-1">{{ proveedoresConInsumos }}</p>
                        </div>
                        <div class="neo-icon-circle bg-purple-100">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="neo-card p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Países</p>
                            <p class="text-3xl font-bold text-indigo-600 mt-1">{{ totalPaises }}</p>
                        </div>
                        <div class="neo-icon-circle bg-indigo-100">
                            <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
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
                                placeholder="Buscar por nombre, RUC o código..."
                                class="neo-input w-full pl-10"
                            />
                            <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <select v-model="filtroCiudad" class="neo-select">
                            <option value="">Todas las ciudades</option>
                            <option v-for="ciudad in ciudades" :key="ciudad" :value="ciudad">
                                {{ ciudad }}
                            </option>
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
                            Nuevo Proveedor
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
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Nombre Comercial</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Contacto</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Ubicación</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Insumos</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-for="proveedor in proveedoresPaginados" :key="proveedor.id" class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <span class="font-mono text-sm font-semibold text-gray-900">{{ proveedor.codigo_proveedor }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">{{ proveedor.nombre_comercial }}</div>
                                    <div v-if="proveedor.razon_social && proveedor.razon_social !== proveedor.nombre_comercial" class="text-xs text-gray-500">
                                        {{ proveedor.razon_social }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div v-if="proveedor.contacto" class="text-sm text-gray-900">{{ proveedor.contacto }}</div>
                                    <div v-if="proveedor.telefono" class="text-xs text-gray-500">{{ proveedor.telefono }}</div>
                                    <div v-if="proveedor.email" class="text-xs text-blue-600">{{ proveedor.email }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div v-if="proveedor.ciudad" class="text-sm text-gray-900">{{ proveedor.ciudad }}</div>
                                    <div v-if="proveedor.pais" class="text-xs text-gray-500">{{ proveedor.pais }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                        <span class="text-sm font-medium text-gray-700">{{ proveedor.insumos_count || 0 }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span v-if="proveedor.activo" class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Activo
                                    </span>
                                    <span v-else class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Inactivo
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center space-x-2">
                                        <button @click="abrirModal(proveedor)" class="neo-button-small bg-blue-500 text-white hover:bg-blue-600" title="Editar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                        <button @click="eliminarProveedor(proveedor)" class="neo-button-small bg-red-500 text-white hover:bg-red-600" title="Eliminar">
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
                        Mostrando {{ proveedoresFiltrados.length }} de {{ proveedores.length }} proveedores
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
        <ModalProveedor
            :visible="mostrarModal"
            :proveedor="proveedorSeleccionado"
            @close="cerrarModal"
            @guardado="cargarProveedores"
        />
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import ModalProveedor from './ModalProveedor.vue';

const proveedores = ref([]);
const buscar = ref('');
const filtroCiudad = ref('');
const filtroActivo = ref('');
const mostrarModal = ref(false);
const proveedorSeleccionado = ref(null);
const paginaActual = ref(1);
const itemsPorPagina = 10;

// Computed
const totalProveedores = computed(() => proveedores.value.length);
const proveedoresActivos = computed(() => proveedores.value.filter(p => p.activo).length);
const proveedoresConInsumos = computed(() => proveedores.value.filter(p => p.insumos_count > 0).length);

const ciudades = computed(() => {
    const ciudadesUnicas = [...new Set(proveedores.value.map(p => p.ciudad).filter(c => c))];
    return ciudadesUnicas.sort();
});

const totalPaises = computed(() => {
    const paisesUnicos = new Set(proveedores.value.map(p => p.pais).filter(p => p));
    return paisesUnicos.size;
});

const proveedoresFiltrados = computed(() => {
    return proveedores.value.filter(proveedor => {
        const cumpleBuscar = !buscar.value ||
            proveedor.nombre_comercial.toLowerCase().includes(buscar.value.toLowerCase()) ||
            proveedor.codigo_proveedor.toLowerCase().includes(buscar.value.toLowerCase()) ||
            (proveedor.razon_social && proveedor.razon_social.toLowerCase().includes(buscar.value.toLowerCase())) ||
            (proveedor.ruc && proveedor.ruc.toLowerCase().includes(buscar.value.toLowerCase()));

        const cumpleCiudad = !filtroCiudad.value || proveedor.ciudad === filtroCiudad.value;

        const cumpleActivo = !filtroActivo.value ||
            (filtroActivo.value === 'true' && proveedor.activo) ||
            (filtroActivo.value === 'false' && !proveedor.activo);

        return cumpleBuscar && cumpleCiudad && cumpleActivo;
    });
});

const totalPaginas = computed(() => Math.ceil(proveedoresFiltrados.value.length / itemsPorPagina));

const proveedoresPaginados = computed(() => {
    const inicio = (paginaActual.value - 1) * itemsPorPagina;
    const fin = inicio + itemsPorPagina;
    return proveedoresFiltrados.value.slice(inicio, fin);
});

// Methods
const cargarProveedores = async () => {
    try {
        const response = await axios.get('/api/proveedores', {
            params: { all: true }
        });

        if (response.data && response.data.data) {
            proveedores.value = response.data.data;
        }
    } catch (error) {
        console.error('Error al cargar proveedores:', error);
        alert('Error al cargar los proveedores');
    }
};

const abrirModal = (proveedor = null) => {
    proveedorSeleccionado.value = proveedor;
    mostrarModal.value = true;
};

const cerrarModal = () => {
    mostrarModal.value = false;
    proveedorSeleccionado.value = null;
};

const eliminarProveedor = async (proveedor) => {
    if (proveedor.insumos_count > 0) {
        alert(`No se puede eliminar el proveedor "${proveedor.nombre_comercial}" porque tiene ${proveedor.insumos_count} insumo(s) asociado(s).`);
        return;
    }

    if (!confirm(`¿Está seguro de eliminar el proveedor "${proveedor.nombre_comercial}"?`)) {
        return;
    }

    try {
        await axios.delete(`/api/proveedores/${proveedor.id}`);
        await cargarProveedores();
        alert('Proveedor eliminado exitosamente');
    } catch (error) {
        console.error('Error al eliminar proveedor:', error);
        alert(error.response?.data?.message || 'Error al eliminar el proveedor');
    }
};

onMounted(() => {
    cargarProveedores();
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
