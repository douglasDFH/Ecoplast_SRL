<template>
    <div class="p-6 rounded-3xl" style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); box-shadow: 12px 12px 24px #b3d4f1, -12px -12px 24px #f3ffff;">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-3">
                <h3 class="text-lg font-bold" style="color: #263238;">Alertas del Sistema</h3>
                <span v-if="noLeidasCount > 0" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-500 text-white animate-pulse">
                    {{ noLeidasCount }} sin leer
                </span>
            </div>
            <button
                v-if="noLeidasCount > 0"
                @click="$emit('mark-all-as-read')"
                class="text-xs text-blue-600 hover:text-blue-800 font-medium transition-colors"
            >
                Marcar todas como leídas
            </button>
        </div>

        <div class="space-y-3 max-h-[500px] overflow-y-auto pr-2 scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
            <div
                v-for="alert in alerts"
                :key="alert.id"
                class="flex items-start p-4 border-l-4 rounded-2xl transition-all"
                :class="getAlertClass(alert.prioridad)"
                style="background: linear-gradient(145deg, #f5f9fc, #e3f2fd); box-shadow: 5px 5px 10px #d0dfe8, -5px -5px 10px #ffffff;"
            >
                <div class="flex items-start space-x-3 flex-1">
                    <!-- Icono de tipo -->
                    <div
                        class="w-10 h-10 rounded-full flex items-center justify-center text-lg flex-shrink-0"
                        :class="getIconBgClass(alert.tipo)"
                    >
                        {{ getAlertIcon(alert.tipo) }}
                    </div>

                    <!-- Contenido -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-gray-900">{{ alert.titulo }}</h4>
                                <p class="text-sm text-gray-600 mt-1">{{ alert.mensaje }}</p>
                            </div>
                            <span
                                class="ml-2 inline-flex px-2 py-1 text-xs font-bold rounded-full flex-shrink-0"
                                :class="getPriorityBadgeClass(alert.prioridad)"
                            >
                                {{ getPriorityText(alert.prioridad) }}
                            </span>
                        </div>

                        <!-- Tiempo y acción -->
                        <div class="flex items-center justify-between mt-3">
                            <div class="flex items-center space-x-4 text-xs">
                                <span class="text-gray-500 flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                    </svg>
                                    {{ getTimeElapsed(alert.created_at) }}
                                </span>
                                <span v-if="alert.accion" class="text-blue-600 font-medium">
                                    {{ alert.accion }}
                                </span>
                            </div>
                            <button
                                v-if="alert.estado === 'activa'"
                                @click="$emit('mark-as-read', alert.id)"
                                class="text-xs text-blue-600 hover:text-blue-800 font-medium hover:underline transition-colors"
                            >
                                Marcar leída
                            </button>
                            <span
                                v-else
                                class="text-xs text-gray-500"
                            >
                                ✓ {{ getStatusText(alert.estado) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="alerts.length === 0" class="text-center py-8 text-gray-500">
                <p>No hay alertas activas</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    alerts: {
        type: Array,
        default: () => []
    }
});

const emit = defineEmits(['mark-as-read', 'mark-all-as-read']);

const noLeidasCount = computed(() => {
    return props.alerts.filter(a => a.estado === 'activa').length;
});

const getAlertClass = (priority) => {
    const classes = {
        baja: 'border-l-blue-400 bg-blue-50',
        media: 'border-l-yellow-400 bg-yellow-50',
        alta: 'border-l-orange-500 bg-orange-50',
        critica: 'border-l-red-600 bg-red-50'
    };
    return classes[priority] || classes.media;
};

const getPriorityBadgeClass = (priority) => {
    const classes = {
        baja: 'bg-blue-100 text-blue-700',
        media: 'bg-yellow-100 text-yellow-700',
        alta: 'bg-orange-100 text-orange-700',
        critica: 'bg-red-100 text-red-700'
    };
    return classes[priority] || classes.media;
};

const getPriorityText = (priority) => {
    const texts = {
        baja: 'BAJA',
        media: 'MEDIA',
        alta: 'ALTA',
        critica: 'CRÍTICA'
    };
    return texts[priority] || priority.toUpperCase();
};

const getAlertIcon = (tipo) => {
    const icons = {
        'maquina_parada': '⚙️',
        'calidad': '🔍',
        'inventario': '📦',
        'mantenimiento': '🔧',
        'produccion': '📊',
        'sistema': 'ℹ️'
    };
    return icons[tipo] || '⚠️';
};

const getIconBgClass = (tipo) => {
    const classes = {
        'maquina_parada': 'bg-red-100 text-red-600',
        'calidad': 'bg-purple-100 text-purple-600',
        'inventario': 'bg-blue-100 text-blue-600',
        'mantenimiento': 'bg-yellow-100 text-yellow-600',
        'produccion': 'bg-green-100 text-green-600',
        'sistema': 'bg-gray-100 text-gray-600'
    };
    return classes[tipo] || 'bg-orange-100 text-orange-600';
};

const getStatusText = (status) => {
    const texts = {
        activa: 'Activa',
        leida: 'Leída',
        resuelta: 'Resuelta'
    };
    return texts[status] || status;
};

const getTimeElapsed = (dateString) => {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    const now = new Date();
    const diffInMinutes = Math.floor((now - date) / (1000 * 60));
    const diffInHours = Math.floor(diffInMinutes / 60);

    if (diffInMinutes < 1) {
        return 'Justo ahora';
    } else if (diffInMinutes < 60) {
        return `${diffInMinutes} min`;
    } else if (diffInHours < 24) {
        return diffInHours > 1 ? `${diffInHours} horas` : `${diffInHours} hora`;
    } else {
        const diffInDays = Math.floor(diffInHours / 24);
        return diffInDays > 1 ? `${diffInDays} días` : `${diffInDays} día`;
    }
};
</script>