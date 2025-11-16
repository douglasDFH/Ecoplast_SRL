<template>
    <div class="p-6 rounded-3xl" style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); box-shadow: 12px 12px 24px #b3d4f1, -12px -12px 24px #f3ffff;">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold" style="color: #263238;">Cumplimiento de Metas</h3>
            <span class="text-sm font-semibold" style="color: #607D8B;">{{ periodoActual }}</span>
        </div>

        <div v-if="metas && metas.length > 0" class="space-y-4">
            <div
                v-for="meta in metas"
                :key="meta.nombre"
                class="space-y-2"
            >
                <!-- Encabezado de la meta -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <span class="text-sm font-medium text-gray-700">{{ meta.nombre }}</span>
                        <span v-if="meta.icono" class="text-lg">{{ meta.icono }}</span>
                    </div>
                    <div class="text-right">
                        <span class="text-lg font-bold" :class="getPorcentajeColorClass(meta.porcentaje)">
                            {{ meta.porcentaje }}%
                        </span>
                    </div>
                </div>

                <!-- Barra de progreso -->
                <div class="relative">
                    <div class="w-full rounded-full h-3 overflow-hidden" style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); box-shadow: inset 5px 5px 10px #b3d4f1, inset -5px -5px 10px #f3ffff;">
                        <div
                            class="h-3 rounded-full transition-all duration-500 flex items-center justify-end pr-1"
                            :class="getBarColorClass(meta.porcentaje)"
                            :style="{ width: Math.min(meta.porcentaje, 100) + '%' }"
                        >
                            <span v-if="meta.porcentaje >= 15" class="text-xs font-bold text-white">
                                {{ getBarSymbol(meta.porcentaje) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Valores -->
                <div class="flex items-center justify-between text-xs text-gray-500">
                    <span>Actual: <span class="font-semibold text-gray-700">{{ formatValor(meta.actual, meta.unidad) }}</span></span>
                    <span>Meta: <span class="font-semibold text-gray-700">{{ formatValor(meta.objetivo, meta.unidad) }}</span></span>
                </div>
            </div>
        </div>

        <div v-else class="text-center py-8 text-gray-400">
            <svg class="w-16 h-16 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <p>No hay metas configuradas</p>
        </div>

        <!-- Resumen general -->
        <div v-if="metas && metas.length > 0" class="mt-6 pt-4 border-t">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-gray-600">Promedio General</span>
                <div class="flex items-center space-x-2">
                    <span class="text-2xl font-bold" :class="getPorcentajeColorClass(promedioGeneral)">
                        {{ promedioGeneral }}%
                    </span>
                    <span class="text-2xl">{{ getEmojiStatus(promedioGeneral) }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    metas: {
        type: Array,
        default: () => []
    }
});

const periodoActual = new Date().toLocaleDateString('es-ES', { 
    month: 'long',
    year: 'numeric'
});

const promedioGeneral = computed(() => {
    if (!props.metas || props.metas.length === 0) return 0;
    const suma = props.metas.reduce((acc, meta) => acc + meta.porcentaje, 0);
    return Math.round(suma / props.metas.length);
});

const getBarColorClass = (porcentaje) => {
    if (porcentaje >= 100) return 'bg-gradient-to-r from-green-500 to-green-600';
    if (porcentaje >= 90) return 'bg-gradient-to-r from-green-400 to-green-500';
    if (porcentaje >= 70) return 'bg-gradient-to-r from-blue-400 to-blue-500';
    if (porcentaje >= 50) return 'bg-gradient-to-r from-yellow-400 to-yellow-500';
    return 'bg-gradient-to-r from-red-400 to-red-500';
};

const getPorcentajeColorClass = (porcentaje) => {
    if (porcentaje >= 100) return 'text-green-600';
    if (porcentaje >= 90) return 'text-green-500';
    if (porcentaje >= 70) return 'text-blue-600';
    if (porcentaje >= 50) return 'text-yellow-600';
    return 'text-red-600';
};

const getBarSymbol = (porcentaje) => {
    if (porcentaje >= 100) return '✓';
    if (porcentaje >= 50) return '▶';
    return '!';
};

const getEmojiStatus = (porcentaje) => {
    if (porcentaje >= 95) return '🏆';
    if (porcentaje >= 85) return '✅';
    if (porcentaje >= 70) return '👍';
    if (porcentaje >= 50) return '⚠️';
    return '🔴';
};

const formatValor = (valor, unidad) => {
    if (!valor) return '0';
    
    if (unidad === '%') {
        return `${valor}%`;
    }
    
    if (valor >= 1000000) {
        return `${(valor / 1000000).toFixed(1)}M`;
    }
    
    if (valor >= 1000) {
        return `${(valor / 1000).toFixed(1)}K`;
    }
    
    return valor.toLocaleString();
};
</script>
