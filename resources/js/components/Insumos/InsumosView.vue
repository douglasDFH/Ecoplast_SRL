<template>
    <div class="space-y-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="gradient-card p-6 rounded-3xl shadow-lg" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/80 text-sm mb-1">Total Insumos</p>
                        <p class="text-white text-3xl font-bold">{{ insumos.length }}</p>
                    </div>
                    <div class="w-14 h-14 bg-white/20 backdrop-blur-lg rounded-2xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="gradient-card p-6 rounded-3xl shadow-lg" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/80 text-sm mb-1">Stock Bajo</p>
                        <p class="text-white text-3xl font-bold">{{ insumosStockBajo }}</p>
                    </div>
                    <div class="w-14 h-14 bg-white/20 backdrop-blur-lg rounded-2xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="gradient-card p-6 rounded-3xl shadow-lg" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/80 text-sm mb-1">Biodegradables</p>
                        <p class="text-white text-3xl font-bold">{{ insumosBiodegradables }}</p>
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
                        <p class="text-white/80 text-sm mb-1">Activos</p>
                        <p class="text-white text-3xl font-bold">{{ insumosActivos }}</p>
                    </div>
                    <div class="w-14 h-14 bg-white/20 backdrop-blur-lg rounded-2xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
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
                        placeholder="Buscar por código o nombre..."
                        class="modern-input px-4 py-2.5 rounded-xl"
                        style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                    />
                    <select
                        v-model="filtros.categoria_id"
                        class="modern-input px-4 py-2.5 rounded-xl"
                        style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                    >
                        <option value="">Todas las categorías</option>
                        <option v-for="cat in categorias" :key="cat.id" :value="cat.id">
                            {{ cat.nombre_categoria }}
                        </option>
                    </select>
                    <select
                        v-model="filtros.tipo_material_id"
                        class="modern-input px-4 py-2.5 rounded-xl"
                        style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                    >
                        <option value="">Todos los tipos</option>
                        <option v-for="tipo in tiposMateriales" :key="tipo.id" :value="tipo.id">
                            {{ tipo.nombre }}
                        </option>
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
                        Nuevo Insumo
                    </button>
                </div>
            </div>
        </div>

        <!-- Insumos Table -->
        <div class="modern-card rounded-3xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white">Código</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white">Insumo</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white">Categoría</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-white">Tipo Material</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-white">Stock</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-white">Precio</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-white">Estado</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-white">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-if="loading" class="hover:bg-gray-50/50 transition-colors">
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex items-center justify-center gap-3">
                                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div>
                                    <span>Cargando insumos...</span>
                                </div>
                            </td>
                        </tr>
                        <tr v-else-if="insumosFiltrados.length === 0" class="hover:bg-gray-50/50 transition-colors">
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                                No se encontraron insumos
                            </td>
                        </tr>
                        <tr v-else v-for="insumo in insumosFiltrados" :key="insumo.id" class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-mono text-sm font-medium text-gray-900">{{ insumo.codigo_insumo }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <p class="font-semibold text-gray-900">{{ insumo.nombre_insumo }}</p>
                                    <p v-if="insumo.proveedor?.nombre_comercial || insumo.proveedor" class="text-xs text-gray-500">
                                        {{ insumo.proveedor?.nombre_comercial || insumo.proveedor }}
                                    </p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-600">{{ insumo.categoria?.nombre_categoria || 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                                    :style="getTipoMaterialBadgeStyle(insumo.tipo_material_relacion)">
                                    {{ insumo.tipo_material_relacion?.codigo || insumo.tipo_material || 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="text-sm">
                                    <p class="font-semibold" :class="getStockClass(insumo)">
                                        {{ insumo.stock_actual }} {{ insumo.unidad_medida }}
                                    </p>
                                    <p class="text-xs text-gray-500">Mín: {{ insumo.stock_minimo }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <p class="font-semibold text-gray-900">${{ parseFloat(insumo.precio_unitario).toFixed(2) }}</p>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                                    :class="insumo.activo ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'">
                                    {{ insumo.activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button
                                        @click="verDetalle(insumo)"
                                        class="p-2 rounded-lg transition-all hover:bg-green-50 active:scale-95"
                                        title="Ver detalle"
                                    >
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                    <button
                                        @click="abrirModal(insumo)"
                                        class="p-2 rounded-lg transition-all hover:bg-blue-50 active:scale-95"
                                        title="Editar"
                                    >
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <button
                                        @click="toggleActivo(insumo)"
                                        class="p-2 rounded-lg transition-all hover:bg-yellow-50 active:scale-95"
                                        :title="insumo.activo ? 'Desactivar' : 'Activar'"
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
        <ModalInsumo
            v-if="mostrarModal"
            :insumo="insumoSeleccionado"
            :categorias="categorias"
            @close="cerrarModal"
            @submit="guardarInsumo"
        />
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import api from '../../services/api';
import ModalInsumo from './ModalInsumo.vue';

const insumos = ref([]);
const categorias = ref([]);
const tiposMateriales = ref([]);
const loading = ref(false);
const mostrarModal = ref(false);
const insumoSeleccionado = ref(null);

const filtros = ref({
    busqueda: '',
    categoria_id: '',
    tipo_material_id: ''
});

// Computed properties
const insumosFiltrados = computed(() => {
    return insumos.value.filter(insumo => {
        const matchBusqueda = !filtros.value.busqueda ||
            insumo.nombre_insumo?.toLowerCase().includes(filtros.value.busqueda.toLowerCase()) ||
            insumo.codigo_insumo?.toLowerCase().includes(filtros.value.busqueda.toLowerCase());

        const matchCategoria = !filtros.value.categoria_id ||
            insumo.categoria_id == filtros.value.categoria_id;

        const matchTipo = !filtros.value.tipo_material_id ||
            insumo.tipo_material_id == filtros.value.tipo_material_id;

        return matchBusqueda && matchCategoria && matchTipo;
    });
});

const insumosStockBajo = computed(() =>
    insumos.value.filter(i => parseFloat(i.stock_actual) < parseFloat(i.stock_minimo)).length
);

const insumosBiodegradables = computed(() =>
    insumos.value.filter(i => i.tipo_material_relacion?.clasificacion === 'Polímero Biodegradable' ||
                              ['PLA', 'PHA', 'PBS', 'PBAT', 'Almidon', 'Celulosa'].includes(i.tipo_material)).length
);

const insumosActivos = computed(() =>
    insumos.value.filter(i => i.activo).length
);

// Methods
const loadInsumos = async () => {
    loading.value = true;
    try {
        const response = await api.get('/insumos');
        if (response.data && response.data.data && response.data.data.data) {
            // Laravel devuelve las relaciones en camelCase: tipoMaterial
            insumos.value = response.data.data.data.map(insumo => ({
                ...insumo,
                tipo_material_relacion: insumo.tipo_material || insumo.tipoMaterial // Soportar ambos formatos
            }));
        } else {
            insumos.value = [];
        }
    } catch (error) {
        console.error('Error al cargar insumos:', error);
        insumos.value = [];
    } finally {
        loading.value = false;
    }
};

const loadCategorias = async () => {
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
    }
};

const loadTiposMateriales = async () => {
    try {
        const response = await api.get('/tipos-materiales', {
            params: { all: true, activo: true }
        });
        if (response.data && response.data.data) {
            tiposMateriales.value = response.data.data;
        } else {
            tiposMateriales.value = [];
        }
    } catch (error) {
        console.error('Error al cargar tipos de materiales:', error);
        tiposMateriales.value = [];
    }
};

const abrirModal = (insumo = null) => {
    insumoSeleccionado.value = insumo;
    mostrarModal.value = true;
};

const cerrarModal = () => {
    mostrarModal.value = false;
    insumoSeleccionado.value = null;
};

const guardarInsumo = async (data) => {
    try {
        if (insumoSeleccionado.value) {
            await api.put(`/insumos/${insumoSeleccionado.value.id}`, data);
        } else {
            await api.post('/insumos', data);
        }
        await loadInsumos();
        cerrarModal();
    } catch (error) {
        console.error('Error al guardar insumo:', error);
        throw error;
    }
};

const verDetalle = (insumo) => {
    const detalles = [
        `Código: ${insumo.codigo_insumo}`,
        `Nombre: ${insumo.nombre_insumo}`,
        `Tipo: ${insumo.tipo_material}`,
        `Categoría: ${insumo.categoria?.nombre_categoria || 'N/A'}`,
        `Stock Actual: ${insumo.stock_actual} ${insumo.unidad_medida}`,
        `Stock Mínimo: ${insumo.stock_minimo} ${insumo.unidad_medida}`,
        `Precio: $${parseFloat(insumo.precio_unitario).toFixed(2)}`,
        insumo.densidad ? `Densidad: ${insumo.densidad} g/cm³` : '',
        insumo.temperatura_fusion ? `Temp. Fusión: ${insumo.temperatura_fusion}°C` : '',
        insumo.certificacion_biodegradable ? `Certificación: ${insumo.certificacion_biodegradable}` : '',
        (insumo.proveedor?.nombre_comercial || insumo.proveedor) ? `Proveedor: ${insumo.proveedor?.nombre_comercial || insumo.proveedor}` : ''
    ].filter(Boolean).join('\n');

    alert(detalles);
};

const toggleActivo = async (insumo) => {
    if (!confirm(`¿Está seguro de ${insumo.activo ? 'desactivar' : 'activar'} este insumo?`)) {
        return;
    }
    try {
        await api.put(`/insumos/${insumo.id}`, {
            ...insumo,
            activo: !insumo.activo
        });
        await loadInsumos();
    } catch (error) {
        console.error('Error al cambiar estado:', error);
        alert('Error al cambiar el estado del insumo');
    }
};

const limpiarFiltros = () => {
    filtros.value = {
        busqueda: '',
        categoria_id: '',
        tipo_material_id: ''
    };
};

const getStockClass = (insumo) => {
    const actual = parseFloat(insumo.stock_actual);
    const minimo = parseFloat(insumo.stock_minimo);

    if (actual < minimo * 0.5) return 'text-red-600';
    if (actual < minimo) return 'text-yellow-600';
    return 'text-green-600';
};

const getTipoMaterialBadgeStyle = (tipoRelacion) => {
    // Si tiene relación con tipo_material, usar su color
    if (tipoRelacion && tipoRelacion.color_referencia) {
        return `background: ${tipoRelacion.color_referencia}; color: #374151;`;
    }

    // Fallback para compatibilidad con ENUM legacy
    const styles = {
        'PLA': 'background: #DBEAFE; color: #1E40AF;',
        'PHA': 'background: #D1FAE5; color: #065F46;',
        'PBS': 'background: #FEF3C7; color: #92400E;',
        'PBAT': 'background: #FCE7F3; color: #9F1239;',
        'Almidon': 'background: #F3E8FF; color: #6B21A8;',
        'Celulosa': 'background: #DBEAFE; color: #1E3A8A;',
        'Aditivo': 'background: #FEE2E2; color: #991B1B;',
        'Pigmento': 'background: #FEF3C7; color: #78350F;',
        'Otro': 'background: #F3F4F6; color: #374151;'
    };
    return styles[tipoRelacion] || 'background: #F3F4F6; color: #374151;';
};

// Lifecycle
onMounted(() => {
    loadInsumos();
    loadCategorias();
    loadTiposMateriales();
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
