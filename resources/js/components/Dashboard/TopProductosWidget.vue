<template>
    <div class="p-6 rounded-3xl" style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); box-shadow: 12px 12px 24px #b3d4f1, -12px -12px 24px #f3ffff;">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold" style="color: #263238;">Top Productos del Día</h3>
            <span class="text-sm font-semibold" style="color: #607D8B;">{{ fechaActual }}</span>
        </div>

        <div v-if="productos && productos.length > 0" class="space-y-3">
            <div
                v-for="(producto, index) in productos"
                :key="producto.id"
                class="flex items-center justify-between p-4 rounded-2xl transition-all"
                style="background: linear-gradient(145deg, #f5f9fc, #e3f2fd); box-shadow: 5px 5px 10px #d0dfe8, -5px -5px 10px #ffffff;"
            >
                <!-- Posición -->
                <div class="flex items-center space-x-4 flex-1">
                    <div
                        class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-lg flex-shrink-0"
                        :class="getPosicionClass(index + 1)"
                    >
                        {{ index + 1 }}
                    </div>

                    <!-- Info del producto -->
                    <div class="flex-1 min-w-0">
                        <h4 class="font-semibold text-gray-900 truncate">{{ producto.nombre }}</h4>
                        <p class="text-xs text-gray-500 mt-1">{{ producto.codigo || 'Sin código' }}</p>
                    </div>

                    <!-- Cantidad -->
                    <div class="text-right">
                        <div class="text-2xl font-bold text-blue-600">
                            {{ formatCantidad(producto.cantidad_producida) }}
                        </div>
                        <div class="text-xs text-gray-500">unidades</div>
                    </div>
                </div>
            </div>
        </div>

        <div v-else class="text-center py-8 text-gray-400">
            <svg class="w-16 h-16 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
            <p>No hay datos de producción disponibles</p>
        </div>

        <!-- Resumen total -->
        <div v-if="productos && productos.length > 0" class="mt-6 pt-4 border-t">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-gray-600">Total producido (Top 5)</span>
                <span class="text-lg font-bold text-gray-900">{{ formatCantidad(totalProducido) }} u</span>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    productos: {
        type: Array,
        default: () => []
    }
});

const fechaActual = new Date().toLocaleDateString('es-ES', { 
    day: 'numeric',
    month: 'short'
});

const totalProducido = computed(() => {
    if (!props.productos || props.productos.length === 0) return 0;
    return props.productos.reduce((sum, p) => sum + (p.cantidad_producida || 0), 0);
});

const getPosicionClass = (posicion) => {
    const classes = {
        1: 'bg-gradient-to-br from-yellow-400 to-yellow-600 text-white shadow-md',
        2: 'bg-gradient-to-br from-gray-300 to-gray-500 text-white shadow-md',
        3: 'bg-gradient-to-br from-orange-400 to-orange-600 text-white shadow-md'
    };
    return classes[posicion] || 'bg-blue-100 text-blue-600';
};

const formatCantidad = (cantidad) => {
    if (!cantidad) return '0';
    if (cantidad >= 1000) {
        return (cantidad / 1000).toFixed(1) + 'K';
    }
    return cantidad.toLocaleString();
};
</script>
