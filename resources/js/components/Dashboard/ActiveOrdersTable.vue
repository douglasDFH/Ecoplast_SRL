<template>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Orden
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Producto
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Estado
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Progreso
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="order in orders" :key="order.id">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">
                            {{ order.numero_orden }}
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ formatDate(order.fecha_inicio) }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                            {{ order.producto?.nombre_producto || 'N/A' }}
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ order.cantidad_planificada }} unidades
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span
                            class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                            :class="getStatusClass(order.estado)"
                        >
                            {{ getStatusText(order.estado) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-1 bg-gray-200 rounded-full h-2 mr-2">
                                <div
                                    class="bg-blue-600 h-2 rounded-full"
                                    :style="{ width: progressPercentage(order) + '%' }"
                                ></div>
                            </div>
                            <span class="text-sm text-gray-600">
                                {{ progressPercentage(order) }}%
                            </span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <div v-if="orders.length === 0" class="text-center py-8 text-gray-500">
            <p>No hay órdenes activas</p>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    orders: {
        type: Array,
        default: () => []
    }
});

const getStatusClass = (status) => {
    const classes = {
        pendiente: 'bg-yellow-100 text-yellow-800',
        programada: 'bg-blue-100 text-blue-800',
        en_proceso: 'bg-green-100 text-green-800'
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const getStatusText = (status) => {
    const texts = {
        pendiente: 'Pendiente',
        programada: 'Programada',
        en_proceso: 'En Proceso'
    };
    return texts[status] || status;
};

const progressPercentage = (order) => {
    if (!order.cantidad_planificada || order.cantidad_planificada === 0) {
        return 0;
    }
    const percentage = (order.cantidad_producida / order.cantidad_planificada) * 100;
    return Math.min(Math.max(percentage, 0), 100);
};

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
};
</script>