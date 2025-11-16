<template>
    <div class="space-y-3">
        <div
            v-for="item in items"
            :key="item.id"
            class="flex items-center justify-between p-3 bg-red-50 border border-red-200 rounded-lg"
        >
            <div class="flex items-center">
                <div class="w-2 h-2 bg-red-500 rounded-full mr-3"></div>
                <div>
                    <p class="text-sm font-medium text-gray-900">{{ item.nombre_insumo }}</p>
                    <p class="text-xs text-gray-500">{{ item.categoria?.nombre_categoria || 'Sin categoría' }}</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm font-medium text-red-600">
                    {{ item.stock_actual }} / {{ item.stock_minimo }}
                </p>
                <p class="text-xs text-gray-500">unidades</p>
            </div>
        </div>

        <div v-if="items.length === 0" class="text-center py-8 text-green-500">
            <p>✅ Todos los insumos tienen stock suficiente</p>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    inventory: {
        type: Array,
        default: () => []
    }
});

// Calcular items con stock bajo (esto ya viene filtrado del store, pero por si acaso)
const items = computed(() =>
    props.inventory.filter(item => item.stock_actual <= item.stock_minimo)
);
</script>