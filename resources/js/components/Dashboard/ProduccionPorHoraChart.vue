<template>
    <div class="p-6 rounded-3xl" style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); box-shadow: 12px 12px 24px #b3d4f1, -12px -12px 24px #f3ffff;">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold" style="color: #263238;">Producción por Hora (Hoy)</h3>
            <span class="text-sm font-semibold" style="color: #607D8B;">{{ fechaActual }}</span>
        </div>
        <div class="relative h-64">
            <Bar v-if="chartData" :data="chartData" :options="chartOptions" />
            <div v-else class="flex items-center justify-center h-full text-gray-400">
                <p>Cargando datos de producción...</p>
            </div>
        </div>
        <div class="mt-4 flex items-center justify-between text-sm">
            <div class="flex items-center space-x-4">
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-blue-500 rounded mr-2"></div>
                    <span class="font-medium" style="color: #455A64;">Unidades producidas</span>
                </div>
            </div>
            <div class="text-right">
                <span class="font-medium" style="color: #607D8B;">Total hoy: </span>
                <span class="font-bold" style="color: #263238;">{{ totalProduccion.toLocaleString() }}</span>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { Bar } from 'vue-chartjs';
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale
} from 'chart.js';

// Registrar componentes de Chart.js
ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

const props = defineProps({
    data: {
        type: Array,
        default: () => []
    }
});

const fechaActual = ref(new Date().toLocaleDateString('es-ES', { 
    weekday: 'long', 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric' 
}));

const totalProduccion = computed(() => {
    if (!props.data || props.data.length === 0) return 0;
    return props.data.reduce((sum, item) => sum + (item.cantidad || 0), 0);
});

const chartData = computed(() => {
    if (!props.data || props.data.length === 0) {
        return null;
    }

    // Generar horas del día (6 AM - 11 PM)
    const horas = [];
    for (let i = 6; i <= 23; i++) {
        horas.push(i < 10 ? `0${i}:00` : `${i}:00`);
    }

    // Mapear datos de producción por hora
    const valores = horas.map(hora => {
        const horaNum = parseInt(hora.split(':')[0]);
        const dato = props.data.find(d => d.hora === horaNum);
        return dato ? dato.cantidad : 0;
    });

    return {
        labels: horas,
        datasets: [
            {
                label: 'Unidades producidas',
                backgroundColor: '#3b82f6',
                borderColor: '#2563eb',
                borderWidth: 1,
                borderRadius: 4,
                data: valores
            }
        ]
    };
});

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: false
        },
        tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            padding: 12,
            titleColor: '#fff',
            bodyColor: '#fff',
            callbacks: {
                label: function(context) {
                    return `${context.parsed.y.toLocaleString()} unidades`;
                }
            }
        }
    },
    scales: {
        x: {
            grid: {
                display: false
            },
            ticks: {
                maxRotation: 45,
                minRotation: 45,
                font: {
                    size: 11
                }
            }
        },
        y: {
            beginAtZero: true,
            grid: {
                color: 'rgba(0, 0, 0, 0.05)'
            },
            ticks: {
                callback: function(value) {
                    if (value >= 1000) {
                        return (value / 1000).toFixed(1) + 'K';
                    }
                    return value;
                }
            }
        }
    }
};
</script>
