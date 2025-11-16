<template>
    <div class="p-6 rounded-3xl transition-all hover:scale-105" style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); box-shadow: 12px 12px 24px #b3d4f1, -12px -12px 24px #f3ffff;">
        <div class="flex items-center justify-between">
            <div class="flex items-center flex-1">
                <div class="flex-shrink-0">
                    <div
                        class="w-14 h-14 rounded-2xl flex items-center justify-center text-3xl shadow-inner"
                        :class="iconBgClass"
                    >
                        {{ icon }}
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <dt class="text-sm font-semibold truncate" style="color: #607D8B;">
                        {{ title }}
                    </dt>
                    <dd class="text-3xl font-bold mt-1" style="color: #263238;">
                        {{ formattedValue }}
                    </dd>
                    <!-- Tendencia y cambio porcentual -->
                    <div v-if="trend !== null" class="flex items-center mt-2">
                        <span
                            class="text-sm font-bold flex items-center px-3 py-1 rounded-xl"
                            :class="trendColorClass"
                            :style="trendBgStyle"
                        >
                            <span class="text-lg mr-1">{{ trendIcon }}</span>
                            {{ Math.abs(trend) }}%
                        </span>
                        <span class="text-xs font-medium ml-2" style="color: #607D8B;">vs {{ comparisonPeriod }}</span>
                    </div>
                </div>
            </div>
        </div>
        <!-- Meta/Objetivo (opcional) -->
        <div v-if="goal !== null" class="mt-4">
            <div class="flex items-center justify-between text-xs font-semibold mb-2" style="color: #455A64;">
                <span>Meta: {{ formattedGoal }}</span>
                <span>{{ goalProgress }}%</span>
            </div>
            <div class="w-full rounded-full h-3" style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); box-shadow: inset 5px 5px 10px #b3d4f1, inset -5px -5px 10px #f3ffff;">
                <div
                    class="h-3 rounded-full transition-all"
                    :class="goalBarClass"
                    :style="{ width: goalProgress + '%' }"
                ></div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    title: {
        type: String,
        required: true
    },
    value: {
        type: [Number, String],
        required: true
    },
    icon: {
        type: String,
        required: true
    },
    color: {
        type: String,
        default: 'blue'
    },
    trend: {
        type: Number,
        default: null // Porcentaje de cambio: positivo = mejora, negativo = bajó
    },
    comparisonPeriod: {
        type: String,
        default: 'ayer' // 'ayer', 'semana pasada', 'mes pasado'
    },
    goal: {
        type: Number,
        default: null // Meta u objetivo
    },
    unit: {
        type: String,
        default: '' // 'kg', 'unidades', '%', etc.
    }
});

const formattedValue = computed(() => {
    if (typeof props.value === 'number') {
        if (props.value >= 1000000) {
            return (props.value / 1000000).toFixed(1) + 'M';
        } else if (props.value >= 1000) {
            return (props.value / 1000).toFixed(1) + 'K';
        }
        return props.value.toLocaleString();
    }
    return props.value;
});

const formattedGoal = computed(() => {
    if (typeof props.goal === 'number') {
        if (props.goal >= 1000000) {
            return (props.goal / 1000000).toFixed(1) + 'M';
        } else if (props.goal >= 1000) {
            return (props.goal / 1000).toFixed(1) + 'K';
        }
        return props.goal.toLocaleString();
    }
    return props.goal;
});

const goalProgress = computed(() => {
    if (props.goal === null || props.goal === 0) return 0;
    const numValue = typeof props.value === 'number' ? props.value : parseFloat(props.value) || 0;
    const progress = (numValue / props.goal) * 100;
    return Math.min(Math.round(progress), 100);
});

const iconBgClass = computed(() => {
    const colorClasses = {
        blue: 'bg-blue-100 text-blue-600',
        green: 'bg-green-100 text-green-600',
        purple: 'bg-purple-100 text-purple-600',
        red: 'bg-red-100 text-red-600',
        yellow: 'bg-yellow-100 text-yellow-600',
        orange: 'bg-orange-100 text-orange-600',
        gray: 'bg-gray-100 text-gray-600'
    };
    return colorClasses[props.color] || colorClasses.blue;
});

const trendIcon = computed(() => {
    if (props.trend === null) return '';
    if (props.trend > 0) return '↗';
    if (props.trend < 0) return '↘';
    return '→';
});

const trendColorClass = computed(() => {
    if (props.trend === null) return '';
    if (props.trend > 0) return 'text-green-700';
    if (props.trend < 0) return 'text-red-700';
    return 'text-gray-700';
});

const trendBgStyle = computed(() => {
    if (props.trend === null) return '';
    if (props.trend > 0) return 'background: linear-gradient(145deg, #e8f5e9, #c8e6c9); box-shadow: inset 3px 3px 6px #b3d7b5, inset -3px -3px 6px #f1fff1;';
    if (props.trend < 0) return 'background: linear-gradient(145deg, #ffebee, #ffcdd2); box-shadow: inset 3px 3px 6px #e0b3b8, inset -3px -3px 6px #fff5f5;';
    return 'background: linear-gradient(145deg, #f5f5f5, #e0e0e0); box-shadow: inset 3px 3px 6px #d0d0d0, inset -3px -3px 6px #ffffff;';
});

const goalBarClass = computed(() => {
    const progress = goalProgress.value;
    if (progress >= 100) return 'bg-gradient-to-r from-green-500 to-green-600';
    if (progress >= 80) return 'bg-gradient-to-r from-blue-500 to-blue-600';
    if (progress >= 50) return 'bg-gradient-to-r from-yellow-500 to-yellow-600';
    return 'bg-gradient-to-r from-red-500 to-red-600';
});
</script>