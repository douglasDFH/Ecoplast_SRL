<template>
    <div class="p-6 rounded-3xl" style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); box-shadow: 12px 12px 24px #b3d4f1, -12px -12px 24px #f3ffff;">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold" style="color: #263238;">Estado de Máquinas en Tiempo Real</h3>
            <div class="flex items-center space-x-2">
                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                <span class="text-sm font-semibold text-green-700">En vivo</span>
            </div>
        </div>

        <div v-if="machines && machines.length > 0" class="space-y-3">
            <div
                v-for="machine in machines"
                :key="machine.id"
                class="flex items-center justify-between p-4 rounded-2xl border-2 transition-all"
                :class="getBorderClass(machine.estado_actual)"
                style="background: linear-gradient(145deg, #f5f9fc, #e3f2fd); box-shadow: 5px 5px 10px #d0dfe8, -5px -5px 10px #ffffff;"
            >
                <!-- Info de la máquina -->
                <div class="flex items-center space-x-4 flex-1">
                    <!-- Icono de estado -->
                    <div class="flex-shrink-0">
                        <div
                            class="w-12 h-12 rounded-full flex items-center justify-center text-2xl"
                            :class="getStatusBgClass(machine.estado_actual)"
                        >
                            {{ getStatusIcon(machine.estado_actual) }}
                        </div>
                    </div>

                    <!-- Nombre y estado -->
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-900">{{ machine.nombre_maquina }}</h4>
                        <div class="flex items-center space-x-2 mt-1">
                            <span
                                class="px-2 py-1 text-xs font-semibold rounded-full"
                                :class="getStatusBadgeClass(machine.estado_actual)"
                            >
                                {{ getStatusText(machine.estado_actual) }}
                            </span>
                            <span v-if="machine.turno" class="text-xs text-gray-500">
                                Turno: {{ machine.turno }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- OEE y progreso -->
                <div class="flex items-center space-x-6">
                    <!-- OEE -->
                    <div class="text-right">
                        <div class="text-xs text-gray-500 mb-1">OEE</div>
                        <div class="text-2xl font-bold" :class="getOeeColorClass(machine.oee)">
                            {{ machine.oee ? machine.oee.toFixed(0) : 0 }}%
                        </div>
                    </div>

                    <!-- Barra de progreso -->
                    <div class="w-32">
                        <div class="flex items-center justify-between text-xs text-gray-600 mb-1">
                            <span>Progreso</span>
                            <span>{{ machine.progreso || 0 }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div
                                class="h-2 rounded-full transition-all"
                                :class="getProgressBarClass(machine.progreso)"
                                :style="{ width: (machine.progreso || 0) + '%' }"
                            ></div>
                        </div>
                    </div>

                    <!-- Icono de alerta -->
                    <div v-if="machine.alerta" class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center animate-pulse">
                            <span class="text-red-600 text-lg">⚠</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-else class="text-center py-8 text-gray-400">
            <p>No hay datos de máquinas disponibles</p>
        </div>

        <!-- Resumen -->
        <div v-if="machines && machines.length > 0" class="mt-6 pt-4 border-t flex items-center justify-around text-sm">
            <div class="text-center">
                <div class="text-2xl font-bold text-green-600">{{ operativas }}</div>
                <div class="text-xs text-gray-500">Operativas</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-yellow-600">{{ enSetup }}</div>
                <div class="text-xs text-gray-500">En Setup</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-red-600">{{ paradas }}</div>
                <div class="text-xs text-gray-500">Paradas</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-600">{{ enMantenimiento }}</div>
                <div class="text-xs text-gray-500">Mantenimiento</div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    machines: {
        type: Array,
        default: () => []
    }
});

// Contadores por estado
const operativas = computed(() => 
    props.machines.filter(m => m.estado_actual === 'operativa').length
);

const paradas = computed(() => 
    props.machines.filter(m => m.estado_actual === 'parada' || m.estado_actual === 'averia').length
);

const enSetup = computed(() => 
    props.machines.filter(m => m.estado_actual === 'setup').length
);

const enMantenimiento = computed(() => 
    props.machines.filter(m => m.estado_actual === 'mantenimiento').length
);

// Funciones de estilo
const getStatusIcon = (estado) => {
    const icons = {
        'operativa': '✓',
        'parada': '■',
        'averia': '✕',
        'mantenimiento': '🔧',
        'setup': '⚙'
    };
    return icons[estado] || '?';
};

const getStatusText = (estado) => {
    const texts = {
        'operativa': 'OPERANDO',
        'parada': 'PARADA',
        'averia': 'AVERÍA',
        'mantenimiento': 'MANTENIMIENTO',
        'setup': 'SETUP'
    };
    return texts[estado] || 'DESCONOCIDO';
};

const getStatusBgClass = (estado) => {
    const classes = {
        'operativa': 'bg-green-100 text-green-600',
        'parada': 'bg-red-100 text-red-600',
        'averia': 'bg-red-100 text-red-600',
        'mantenimiento': 'bg-blue-100 text-blue-600',
        'setup': 'bg-yellow-100 text-yellow-600'
    };
    return classes[estado] || 'bg-gray-100 text-gray-600';
};

const getStatusBadgeClass = (estado) => {
    const classes = {
        'operativa': 'bg-green-100 text-green-700',
        'parada': 'bg-red-100 text-red-700',
        'averia': 'bg-red-100 text-red-700',
        'mantenimiento': 'bg-blue-100 text-blue-700',
        'setup': 'bg-yellow-100 text-yellow-700'
    };
    return classes[estado] || 'bg-gray-100 text-gray-700';
};

const getBorderClass = (estado) => {
    const classes = {
        'operativa': 'border-green-200 hover:border-green-300',
        'parada': 'border-red-200 hover:border-red-300',
        'averia': 'border-red-300 hover:border-red-400',
        'mantenimiento': 'border-blue-200 hover:border-blue-300',
        'setup': 'border-yellow-200 hover:border-yellow-300'
    };
    return classes[estado] || 'border-gray-200 hover:border-gray-300';
};

const getOeeColorClass = (oee) => {
    if (!oee) return 'text-gray-400';
    if (oee >= 85) return 'text-green-600';
    if (oee >= 70) return 'text-blue-600';
    if (oee >= 50) return 'text-yellow-600';
    return 'text-red-600';
};

const getProgressBarClass = (progreso) => {
    if (!progreso) return 'bg-gray-400';
    if (progreso >= 80) return 'bg-green-500';
    if (progreso >= 50) return 'bg-blue-500';
    if (progreso >= 25) return 'bg-yellow-500';
    return 'bg-red-500';
};
</script>
