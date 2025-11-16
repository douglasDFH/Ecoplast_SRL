<template>
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="rounded-3xl shadow-neumorphic max-w-2xl w-full max-h-[90vh] overflow-y-auto" 
             style="background: linear-gradient(145deg, #e3f2fd, #bbdefb);">
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-blue-200">
                <h2 class="text-2xl font-bold" style="color: #263238;">
                    {{ orden ? 'Editar Orden' : 'Nueva Orden de Producción' }}
                </h2>
                <button
                    @click="$emit('close')"
                    class="hover-button rounded-xl px-3 py-2 transition-all"
                    style="color: #607D8B;"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Body -->
            <form @submit.prevent="handleSubmit" class="p-6 space-y-6">
                <!-- Código de Orden (solo para edición) -->
                <div v-if="orden" class="rounded-2xl p-4" 
                     style="background: linear-gradient(145deg, #d0e8f7, #b3d4f1); box-shadow: inset 8px 8px 16px #d0dfe8, inset -8px -8px 16px #ffffff;">
                    <label class="block text-sm font-medium mb-2" style="color: #455A64;">Código de Orden</label>
                    <p class="font-mono text-lg font-bold" style="color: #263238;">{{ orden.codigo_orden }}</p>
                </div>

                <!-- Producto -->
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: #455A64;">
                        Producto <span class="text-red-500">*</span>
                    </label>
                    <select
                        v-model="formData.producto_id"
                        required
                        class="w-full px-4 py-3 rounded-2xl focus:outline-none"
                        style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); 
                               box-shadow: inset 8px 8px 16px #d0dfe8, inset -8px -8px 16px #ffffff; 
                               color: #263238;"
                    >
                        <option value="">Seleccionar producto...</option>
                        <option v-for="producto in productos" :key="producto.id" :value="producto.id">
                            {{ producto.nombre_producto }} ({{ producto.codigo_producto }})
                        </option>
                    </select>
                </div>

                <!-- Cantidad Requerida -->
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: #455A64;">
                        Cantidad Requerida <span class="text-red-500">*</span>
                    </label>
                    <input
                           type="number"
                           v-model.number="formData.cantidad_planificada"
                           required
                           min="1"
                           class="w-full px-4 py-3 rounded-2xl focus:outline-none"
                           style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); 
                               box-shadow: inset 8px 8px 16px #d0dfe8, inset -8px -8px 16px #ffffff; 
                               color: #263238;"
                           placeholder="Ej: 10000"
                    />
                </div>

                <!-- Máquina -->
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: #455A64;">
                        Máquina <span class="text-red-500">*</span>
                    </label>
                    <select
                        v-model="formData.maquina_id"
                        required
                        class="w-full px-4 py-3 rounded-2xl focus:outline-none"
                        style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); 
                               box-shadow: inset 8px 8px 16px #d0dfe8, inset -8px -8px 16px #ffffff; 
                               color: #263238;"
                    >
                        <option value="">Seleccionar máquina...</option>
                        <option v-for="maquina in maquinas" :key="maquina.id" :value="maquina.id">
                            {{ maquina.nombre_maquina }} ({{ maquina.codigo_maquina }})
                        </option>
                    </select>
                </div>

                <!-- Prioridad -->
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: #455A64;">
                        Prioridad <span class="text-red-500">*</span>
                    </label>
                    <select
                        v-model="formData.prioridad"
                        required
                        class="w-full px-4 py-3 rounded-2xl focus:outline-none"
                        style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); 
                               box-shadow: inset 8px 8px 16px #d0dfe8, inset -8px -8px 16px #ffffff; 
                               color: #263238;"
                    >
                        <option value="baja">🟢 Baja</option>
                        <option value="media">🟡 Media</option>
                        <option value="alta">🟠 Alta</option>
                        <option value="urgente">🔴 Urgente</option>
                    </select>
                </div>

                <!-- Fechas -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: #455A64;">
                            Fecha Programada <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="date"
                            v-model="formData.fecha_programada"
                            required
                            class="w-full px-4 py-3 rounded-2xl focus:outline-none"
                            style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); 
                                   box-shadow: inset 8px 8px 16px #d0dfe8, inset -8px -8px 16px #ffffff; 
                                   color: #263238;"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: #455A64;">
                            Fecha Límite
                        </label>
                        <input
                            type="date"
                            v-model="formData.fecha_limite"
                            class="w-full px-4 py-3 rounded-2xl focus:outline-none"
                            style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); 
                                   box-shadow: inset 8px 8px 16px #d0dfe8, inset -8px -8px 16px #ffffff; 
                                   color: #263238;"
                        />
                    </div>
                </div>

                <!-- Turno -->
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: #455A64;">Turno</label>
                    <select
                        v-model="formData.turno_id"
                        class="w-full px-4 py-3 rounded-2xl focus:outline-none"
                        style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); 
                               box-shadow: inset 8px 8px 16px #d0dfe8, inset -8px -8px 16px #ffffff; 
                               color: #263238;"
                    >
                        <option value="">Sin turno asignado</option>
                        <option v-for="turno in turnos" :key="turno.id" :value="turno.id">
                            {{ turno.nombre_turno }} ({{ turno.hora_inicio }} - {{ turno.hora_fin }})
                        </option>
                    </select>
                </div>

                <!-- Observaciones -->
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: #455A64;">Observaciones</label>
                    <textarea
                        v-model="formData.observaciones"
                        rows="3"
                        class="w-full px-4 py-3 rounded-2xl focus:outline-none"
                        style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); 
                               box-shadow: inset 8px 8px 16px #d0dfe8, inset -8px -8px 16px #ffffff; 
                               color: #263238;"
                        placeholder="Notas adicionales sobre la orden..."
                    ></textarea>
                </div>

                <!-- Errores -->
                <div v-if="errors.length > 0" class="rounded-2xl p-4 border-l-4 border-red-500" 
                     style="background: linear-gradient(145deg, #ffebee, #ffcdd2);">
                    <div class="flex items-start">
                        <span class="text-red-600 text-lg mr-2">⚠</span>
                        <div>
                            <p class="font-semibold text-red-800">Errores de validación:</p>
                            <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                <li v-for="(error, index) in errors" :key="index">{{ error }}</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex items-center justify-end space-x-4 pt-4 border-t border-blue-200">
                    <button
                        type="button"
                        @click="$emit('close')"
                        class="px-6 py-3 rounded-2xl font-semibold transition-all hover-button"
                        style="background: linear-gradient(145deg, #607D8B, #455A64); 
                               box-shadow: 12px 12px 24px #4a5a63, -12px -12px 24px #6e8495; 
                               color: white;"
                    >
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        :disabled="submitting"
                        class="px-6 py-3 rounded-2xl font-semibold transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                        style="background: linear-gradient(145deg, #4CAF50, #388E3C); 
                               box-shadow: 12px 12px 24px #2e7031, -12px -12px 24px #5ec962; 
                               color: white;"
                    >
                        <span v-if="submitting">Guardando...</span>
                        <span v-else>{{ orden ? 'Actualizar' : 'Crear Orden' }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import api from '@/services/api.js';

const props = defineProps({
    orden: {
        type: Object,
        default: null
    }
});

const emit = defineEmits(['close', 'guardar']);

// Estado
const submitting = ref(false);
const errors = ref([]);
const productos = ref([]);
const maquinas = ref([]);
const turnos = ref([]);

// Formulario
const formData = reactive({
    producto_id: props.orden?.producto_id || '',
    cantidad_requerida: props.orden?.cantidad_requerida || '',
    maquina_id: props.orden?.maquina_id || '',
    prioridad: props.orden?.prioridad || 'media',
    fecha_programada: props.orden?.fecha_programada || '',
    fecha_limite: props.orden?.fecha_limite || '',
    turno_id: props.orden?.turno_id || '',
    observaciones: props.orden?.observaciones || '',
});

// Métodos
const handleSubmit = async () => {
    errors.value = [];
    
    // Validaciones
    if (!formData.producto_id) {
        errors.value.push('Debe seleccionar un producto');
    }
    if (!formData.cantidad_requerida || formData.cantidad_requerida < 1) {
        errors.value.push('La cantidad debe ser mayor a 0');
    }
    if (!formData.maquina_id) {
        errors.value.push('Debe seleccionar una máquina');
    }
    if (!formData.fecha_programada) {
        errors.value.push('Debe especificar la fecha programada');
    }

    if (errors.value.length > 0) {
        return;
    }

    submitting.value = true;
    try {
        emit('guardar', formData);
    } catch (error) {
        errors.value.push('Error al guardar la orden');
    } finally {
        submitting.value = false;
    }
};

const loadProductos = async () => {
    try {
        const response = await api.get('/productos-terminados');
        productos.value = response.data.data || response.data;
    } catch (error) {
        console.error('Error al cargar productos:', error);
    }
};

const loadMaquinas = async () => {
    try {
        const response = await api.get('/maquinas', { params: { activo: true } });
        maquinas.value = response.data.data || response.data;
    } catch (error) {
        console.error('Error al cargar máquinas:', error);
    }
};

const loadTurnos = async () => {
    try {
        const response = await api.get('/turnos');
        turnos.value = response.data.data || response.data;
    } catch (error) {
        console.error('Error al cargar turnos:', error);
    }
};

// Lifecycle
onMounted(async () => {
    await Promise.all([
        loadProductos(),
        loadMaquinas(),
        loadTurnos()
    ]);
});
</script>

<style scoped>
.shadow-neumorphic {
    box-shadow: 15px 15px 30px #b3d4f1, -15px -15px 30px #f3ffff;
}

.hover-button:hover {
    box-shadow: 7px 7px 14px #4a5a63, -7px -7px 14px #6e8495;
}

/* Estilos para inputs en navegadores webkit */
input[type="date"]::-webkit-calendar-picker-indicator,
select::-webkit-calendar-picker-indicator {
    filter: invert(0.3) sepia(1) saturate(3) hue-rotate(180deg);
}
</style>
