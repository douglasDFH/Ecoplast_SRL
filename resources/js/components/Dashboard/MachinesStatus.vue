<template>
    <div class="space-y-4">
        <div v-for="machine in machines" :key="machine.id" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
            <div class="flex items-center">
                <div
                    class="w-3 h-3 rounded-full mr-3"
                    :class="getStatusColor(machine.estado_actual)"
                ></div>
                <div>
                    <p class="text-sm font-medium text-gray-900">{{ machine.nombre_maquina }}</p>
                    <p class="text-xs text-gray-500">{{ machine.tipo?.nombre_tipo || 'Sin tipo' }}</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm font-medium text-gray-900">{{ machine.estado_actual }}</p>
                <p v-if="machine.oee_actual" class="text-xs text-gray-500">
                    OEE: {{ machine.oee_actual.toFixed(1) }}%
                </p>
            </div>
        </div>

        <div v-if="machines.length === 0" class="text-center py-8 text-gray-500">
            <p>No hay máquinas registradas</p>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    machines: {
        type: Array,
        default: () => []
    }
});

const getStatusColor = (status) => {
    const colors = {
        operativa: 'bg-green-500',
        mantenimiento: 'bg-yellow-500',
        parada: 'bg-orange-500',
        averia: 'bg-red-500'
    };
    return colors[status] || 'bg-gray-500';
};
</script>