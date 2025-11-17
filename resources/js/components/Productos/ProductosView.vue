<template>
    <div class="space-y-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="gradient-card p-6 rounded-3xl shadow-lg" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/80 text-sm mb-1">Total Productos</p>
                        <p class="text-white text-3xl font-bold">{{ productos.length }}</p>
                    </div>
                    <div class="w-14 h-14 bg-white/20 backdrop-blur-lg rounded-2xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="gradient-card p-6 rounded-3xl shadow-lg" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/80 text-sm mb-1">Activos</p>
                        <p class="text-white text-3xl font-bold">{{ productosActivos }}</p>
                    </div>
                    <div class="w-14 h-14 bg-white/20 backdrop-blur-lg rounded-2xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="gradient-card p-6 rounded-3xl shadow-lg" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/80 text-sm mb-1">Biodegradables</p>
                        <p class="text-white text-3xl font-bold">{{ productosBiodegradables }}</p>
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
                        <p class="text-white/80 text-sm mb-1">Stock Bajo</p>
                        <p class="text-white text-3xl font-bold">{{ productosStockBajo }}</p>
                    </div>
                    <div class="w-14 h-14 bg-white/20 backdrop-blur-lg rounded-2xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
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
                        v-model="filtros.material_principal"
                        class="modern-input px-4 py-2.5 rounded-xl"
                        style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                    >
                        <option value="">Todos los materiales</option>
                        <option value="PLA">PLA</option>
                        <option value="PHA">PHA</option>
                        <option value="PBS">PBS</option>
                        <option value="PBAT">PBAT</option>
                        <option value="Almidon">Almidón</option>
                        <option value="Mixto">Mixto</option>
                    </select>
                    <select
                        v-model="filtros.activo"
                        class="modern-input px-4 py-2.5 rounded-xl"
                        style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                    >
                        <option value="">Todos los estados</option>
                        <option value="1">Activos</option>
                        <option value="0">Inactivos</option>
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
                        Nuevo Producto
                    </button>
                </div>
            </div>
        </div>

        <!-- Products Table -->
        <div class="modern-card rounded-3xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white">Código</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white">Producto</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white">Material</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-white">Precio</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-white">Stock</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-white">Certificación</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-white">Estado</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-white">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-if="loading" class="hover:bg-gray-50/50 transition-colors">
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex items-center justify-center gap-3">
                                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div>
                                    <span>Cargando productos...</span>
                                </div>
                            </td>
                        </tr>
                        <tr v-else-if="productosFiltrados.length === 0" class="hover:bg-gray-50/50 transition-colors">
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                No se encontraron productos
                            </td>
                        </tr>
                        <tr v-else v-for="producto in productosFiltrados" :key="producto.id" class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-mono text-sm font-medium text-gray-900">{{ producto.codigo_producto }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <p class="font-semibold text-gray-900">{{ producto.nombre_producto }}</p>
                                    <p class="text-sm text-gray-500">{{ producto.descripcion?.substring(0, 50) }}...</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                                    :style="getMaterialBadgeStyle(producto.material_principal)">
                                    {{ producto.material_principal }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <p class="font-semibold text-gray-900">S/ {{ Number(producto.precio_venta || 0).toFixed(2) }}</p>
                                <p class="text-xs text-gray-500">Costo: S/ {{ Number(producto.costo_produccion || 0).toFixed(2) }}</p>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div>
                                    <p class="font-semibold" :class="getStockClass(producto)">
                                        {{ producto.stock_actual || 0 }} {{ producto.unidad_medida }}
                                    </p>
                                    <p class="text-xs text-gray-500">Min: {{ producto.stock_minimo }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span v-if="producto.certificacion_compostable"
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
                                    :class="producto.activo ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'">
                                    {{ producto.activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button
                                        @click="abrirModal(producto)"
                                        class="p-2 rounded-lg transition-all hover:bg-blue-50 active:scale-95"
                                        title="Editar"
                                    >
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <button
                                        @click="toggleActivo(producto)"
                                        class="p-2 rounded-lg transition-all hover:bg-yellow-50 active:scale-95"
                                        :title="producto.activo ? 'Desactivar' : 'Activar'"
                                    >
                                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                    </button>
                                    <button
                                        @click="eliminarProducto(producto)"
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
        <ModalProducto
            v-if="mostrarModal"
            :producto="productoSeleccionado"
            @close="cerrarModal"
            @submit="guardarProducto"
        />
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import api from '../../services/api';
import ModalProducto from './ModalProducto.vue';

const productos = ref([]);
const loading = ref(false);
const mostrarModal = ref(false);
const productoSeleccionado = ref(null);

const filtros = ref({
    busqueda: '',
    material_principal: '',
    activo: ''
});

// Computed properties
const productosFiltrados = computed(() => {
    return productos.value.filter(producto => {
        const matchBusqueda = !filtros.value.busqueda ||
            producto.nombre_producto?.toLowerCase().includes(filtros.value.busqueda.toLowerCase()) ||
            producto.codigo_producto?.toLowerCase().includes(filtros.value.busqueda.toLowerCase());

        const matchMaterial = !filtros.value.material_principal ||
            producto.material_principal === filtros.value.material_principal;

        const matchActivo = filtros.value.activo === '' ||
            producto.activo === parseInt(filtros.value.activo);

        return matchBusqueda && matchMaterial && matchActivo;
    });
});

const productosActivos = computed(() =>
    productos.value.filter(p => p.activo).length
);

const productosBiodegradables = computed(() =>
    productos.value.filter(p => p.certificacion_compostable).length
);

const productosStockBajo = computed(() =>
    productos.value.filter(p => (p.stock_actual || 0) <= (p.stock_minimo || 0)).length
);

// Methods
const loadProductos = async () => {
    loading.value = true;
    try {
        const response = await api.get('/productos-terminados');
        // ProductoTerminadoController returns paginated data in response.data.data.data
        if (response.data.data && response.data.data.data) {
            productos.value = response.data.data.data;
        } else if (response.data.data) {
            productos.value = Array.isArray(response.data.data) ? response.data.data : [];
        } else {
            productos.value = [];
        }
    } catch (error) {
        console.error('Error al cargar productos:', error);
        productos.value = [];
    } finally {
        loading.value = false;
    }
};

const abrirModal = (producto = null) => {
    productoSeleccionado.value = producto;
    mostrarModal.value = true;
};

const cerrarModal = () => {
    mostrarModal.value = false;
    productoSeleccionado.value = null;
};

const guardarProducto = async (data) => {
    try {
        if (productoSeleccionado.value) {
            await api.put(`/productos-terminados/${productoSeleccionado.value.id}`, data);
        } else {
            await api.post('/productos-terminados', data);
        }
        await loadProductos();
        cerrarModal();
    } catch (error) {
        console.error('Error al guardar producto:', error);
        throw error;
    }
};

const toggleActivo = async (producto) => {
    if (!confirm(`¿Está seguro de ${producto.activo ? 'desactivar' : 'activar'} este producto?`)) {
        return;
    }
    try {
        await api.put(`/productos-terminados/${producto.id}`, {
            ...producto,
            activo: !producto.activo
        });
        await loadProductos();
    } catch (error) {
        console.error('Error al cambiar estado:', error);
        alert('Error al cambiar el estado del producto');
    }
};

const eliminarProducto = async (producto) => {
    if (!confirm(`¿Está seguro de eliminar el producto "${producto.nombre_producto}"? Esta acción no se puede deshacer.`)) {
        return;
    }
    try {
        await api.delete(`/productos-terminados/${producto.id}`);
        await loadProductos();
    } catch (error) {
        console.error('Error al eliminar producto:', error);
        alert('Error al eliminar el producto');
    }
};

const limpiarFiltros = () => {
    filtros.value = {
        busqueda: '',
        material_principal: '',
        activo: ''
    };
};

const getMaterialBadgeStyle = (material) => {
    const styles = {
        'PLA': 'background: #DBEAFE; color: #1E40AF;',
        'PHA': 'background: #D1FAE5; color: #065F46;',
        'PBS': 'background: #FEF3C7; color: #92400E;',
        'PBAT': 'background: #E0E7FF; color: #3730A3;',
        'Almidon': 'background: #FCE7F3; color: #9F1239;',
        'Mixto': 'background: #F3E8FF; color: #6B21A8;'
    };
    return styles[material] || 'background: #F3F4F6; color: #374151;';
};

const getStockClass = (producto) => {
    const stock = producto.stock_actual || 0;
    const minimo = producto.stock_minimo || 0;
    if (stock === 0) return 'text-red-600';
    if (stock <= minimo) return 'text-yellow-600';
    return 'text-green-600';
};

// Lifecycle
onMounted(() => {
    loadProductos();
});

// Watch filtros
watch(filtros, () => {
    // Los filtros se aplican automáticamente por el computed
}, { deep: true });
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
