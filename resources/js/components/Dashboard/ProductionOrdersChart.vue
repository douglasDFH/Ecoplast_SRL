<template>
    <div class="h-64">
        <canvas ref="chartCanvas"></canvas>
    </div>
</template>

<script setup>
import { ref, onMounted, watch, nextTick } from 'vue';
import {
    Chart as ChartJS,
    ArcElement,
    Tooltip,
    Legend
} from 'chart.js';
import { Doughnut } from 'vue-chartjs';

ChartJS.register(ArcElement, Tooltip, Legend);

const props = defineProps({
    orders: {
        type: Array,
        default: () => []
    }
});

const chartCanvas = ref(null);
let chart = null;

const chartData = computed(() => {
    const statusCounts = {
        pendiente: 0,
        programada: 0,
        en_proceso: 0,
        completada: 0,
        cancelada: 0
    };

    props.orders.forEach(order => {
        if (statusCounts.hasOwnProperty(order.estado)) {
            statusCounts[order.estado]++;
        }
    });

    return {
        labels: [
            'Pendiente',
            'Programada',
            'En Proceso',
            'Completada',
            'Cancelada'
        ],
        datasets: [{
            data: [
                statusCounts.pendiente,
                statusCounts.programada,
                statusCounts.en_proceso,
                statusCounts.completada,
                statusCounts.cancelada
            ],
            backgroundColor: [
                '#fbbf24', // yellow
                '#3b82f6', // blue
                '#10b981', // green
                '#6366f1', // indigo
                '#ef4444'  // red
            ],
            borderWidth: 2,
            borderColor: '#ffffff'
        }]
    };
});

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'bottom',
            labels: {
                padding: 20,
                usePointStyle: true
            }
        },
        tooltip: {
            callbacks: {
                label: function(context) {
                    const label = context.label || '';
                    const value = context.parsed || 0;
                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                    const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                    return `${label}: ${value} (${percentage}%)`;
                }
            }
        }
    }
};

const createChart = () => {
    if (chart) {
        chart.destroy();
    }

    if (chartCanvas.value) {
        chart = new ChartJS(chartCanvas.value, {
            type: 'doughnut',
            data: chartData.value,
            options: chartOptions
        });
    }
};

const updateChart = () => {
    if (chart) {
        chart.data = chartData.value;
        chart.update();
    }
};

onMounted(() => {
    nextTick(() => {
        createChart();
    });
});

watch(() => props.orders, () => {
    if (chart) {
        updateChart();
    } else {
        createChart();
    }
}, { deep: true });
</script>