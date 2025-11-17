<template>
    <transition name="modal">
        <div v-if="visible" class="fixed inset-0 z-50 overflow-y-auto" @click.self="$emit('close')">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="$emit('close')"></div>

                <!-- Modal panel -->
                <div class="relative z-10 inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-2xl font-bold text-white">
                                {{ esEdicion ? 'Editar Tipo de Material' : 'Nuevo Tipo de Material' }}
                            </h3>
                            <button @click="$emit('close')" class="text-white hover:text-gray-200 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="px-6 py-6 max-h-[calc(100vh-200px)] overflow-y-auto">
                        <form @submit.prevent="guardar">
                            <!-- Identificación -->
                            <div class="mb-6">
                                <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                    Identificación
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Código *</label>
                                        <input
                                            v-model="form.codigo"
                                            type="text"
                                            required
                                            maxlength="20"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                            placeholder="Ej: PLA, PBS"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Nombre *</label>
                                        <input
                                            v-model="form.nombre"
                                            type="text"
                                            required
                                            maxlength="100"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                            placeholder="Ej: Ácido Poliláctico"
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Clasificación y Descripción -->
                            <div class="mb-6">
                                <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    Clasificación
                                </h4>
                                <div class="grid grid-cols-1 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Clasificación *</label>
                                        <select
                                            v-model="form.clasificacion"
                                            required
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                        >
                                            <option value="Polímero Biodegradable">Polímero Biodegradable</option>
                                            <option value="Aditivo">Aditivo</option>
                                            <option value="Pigmento">Pigmento</option>
                                            <option value="Otro">Otro</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                                        <textarea
                                            v-model="form.descripcion"
                                            rows="3"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                            placeholder="Descripción del tipo de material..."
                                        ></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Propiedades Físicas -->
                            <div class="mb-6">
                                <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                    Propiedades Físicas
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Densidad Mínima (g/cm³)</label>
                                        <input
                                            v-model.number="form.densidad_min"
                                            type="number"
                                            step="0.001"
                                            min="0"
                                            max="999.999"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                            placeholder="1.210"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Densidad Máxima (g/cm³)</label>
                                        <input
                                            v-model.number="form.densidad_max"
                                            type="number"
                                            step="0.001"
                                            min="0"
                                            max="999.999"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                            placeholder="1.250"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Temp. Procesamiento Mín (°C)</label>
                                        <input
                                            v-model.number="form.temperatura_procesamiento_min"
                                            type="number"
                                            step="0.1"
                                            min="-273"
                                            max="9999.9"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                            placeholder="160.0"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Temp. Procesamiento Máx (°C)</label>
                                        <input
                                            v-model.number="form.temperatura_procesamiento_max"
                                            type="number"
                                            step="0.1"
                                            min="-273"
                                            max="9999.9"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                            placeholder="190.0"
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Tiempo de Degradación -->
                            <div class="mb-6">
                                <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Tiempo de Degradación
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Tiempo Mínimo (días)</label>
                                        <input
                                            v-model.number="form.tiempo_degradacion_min"
                                            type="number"
                                            min="0"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                            placeholder="90"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Tiempo Máximo (días)</label>
                                        <input
                                            v-model.number="form.tiempo_degradacion_max"
                                            type="number"
                                            min="0"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                            placeholder="180"
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Certificaciones y UI -->
                            <div class="mb-6">
                                <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                    </svg>
                                    Certificaciones y Visualización
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Certificaciones Aplicables</label>
                                        <input
                                            v-model="form.certificaciones_aplicables"
                                            type="text"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                            placeholder="ASTM D6400, EN 13432, OK Compost"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Orden Visualización</label>
                                        <input
                                            v-model.number="form.orden_visualizacion"
                                            type="number"
                                            min="0"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                            placeholder="1"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Color Referencia</label>
                                        <div class="flex gap-2">
                                            <input
                                                v-model="form.color_referencia"
                                                type="color"
                                                class="h-10 w-16 border border-gray-300 rounded-lg cursor-pointer"
                                            />
                                            <input
                                                v-model="form.color_referencia"
                                                type="text"
                                                maxlength="7"
                                                pattern="^#[0-9A-Fa-f]{6}$"
                                                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                                placeholder="#DBEAFE"
                                            />
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Icono</label>
                                        <input
                                            v-model="form.icono"
                                            type="text"
                                            maxlength="50"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                            placeholder="icon-name"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                                        <div class="flex items-center h-10">
                                            <label class="inline-flex items-center cursor-pointer">
                                                <input
                                                    v-model="form.activo"
                                                    type="checkbox"
                                                    class="sr-only peer"
                                                />
                                                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                                <span class="ms-3 text-sm font-medium text-gray-700">{{ form.activo ? 'Activo' : 'Inactivo' }}</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Footer -->
                    <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                        <button
                            @click="$emit('close')"
                            type="button"
                            class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors font-medium"
                        >
                            Cancelar
                        </button>
                        <button
                            @click="guardar"
                            type="button"
                            class="px-6 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-lg hover:from-purple-700 hover:to-indigo-700 transition-colors font-medium"
                        >
                            {{ esEdicion ? 'Actualizar' : 'Guardar' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
import axios from 'axios';

const props = defineProps({
    visible: Boolean,
    tipoMaterial: Object
});

const emit = defineEmits(['close', 'guardado']);

const form = ref({
    codigo: '',
    nombre: '',
    clasificacion: 'Polímero Biodegradable',
    descripcion: '',
    densidad_min: null,
    densidad_max: null,
    temperatura_procesamiento_min: null,
    temperatura_procesamiento_max: null,
    tiempo_degradacion_min: null,
    tiempo_degradacion_max: null,
    certificaciones_aplicables: '',
    color_referencia: '#DBEAFE',
    icono: '',
    orden_visualizacion: 999,
    activo: true
});

const esEdicion = computed(() => !!props.tipoMaterial);

watch(() => props.visible, (visible) => {
    if (visible) {
        if (props.tipoMaterial) {
            // Modo edición
            form.value = {
                codigo: props.tipoMaterial.codigo || '',
                nombre: props.tipoMaterial.nombre || '',
                clasificacion: props.tipoMaterial.clasificacion || 'Polímero Biodegradable',
                descripcion: props.tipoMaterial.descripcion || '',
                densidad_min: props.tipoMaterial.densidad_min || null,
                densidad_max: props.tipoMaterial.densidad_max || null,
                temperatura_procesamiento_min: props.tipoMaterial.temperatura_procesamiento_min || null,
                temperatura_procesamiento_max: props.tipoMaterial.temperatura_procesamiento_max || null,
                tiempo_degradacion_min: props.tipoMaterial.tiempo_degradacion_min || null,
                tiempo_degradacion_max: props.tipoMaterial.tiempo_degradacion_max || null,
                certificaciones_aplicables: props.tipoMaterial.certificaciones_aplicables || '',
                color_referencia: props.tipoMaterial.color_referencia || '#DBEAFE',
                icono: props.tipoMaterial.icono || '',
                orden_visualizacion: props.tipoMaterial.orden_visualizacion || 999,
                activo: props.tipoMaterial.activo ?? true
            };
        } else {
            // Modo creación - resetear formulario
            form.value = {
                codigo: '',
                nombre: '',
                clasificacion: 'Polímero Biodegradable',
                descripcion: '',
                densidad_min: null,
                densidad_max: null,
                temperatura_procesamiento_min: null,
                temperatura_procesamiento_max: null,
                tiempo_degradacion_min: null,
                tiempo_degradacion_max: null,
                certificaciones_aplicables: '',
                color_referencia: '#DBEAFE',
                icono: '',
                orden_visualizacion: 999,
                activo: true
            };
        }
    }
});

const guardar = async () => {
    try {
        // Validación básica
        if (!form.value.codigo || !form.value.nombre) {
            alert('Por favor complete los campos requeridos (Código y Nombre)');
            return;
        }

        // Convertir valores nulos/vacíos a null explícito
        const data = {
            ...form.value,
            densidad_min: form.value.densidad_min || null,
            densidad_max: form.value.densidad_max || null,
            temperatura_procesamiento_min: form.value.temperatura_procesamiento_min || null,
            temperatura_procesamiento_max: form.value.temperatura_procesamiento_max || null,
            tiempo_degradacion_min: form.value.tiempo_degradacion_min || null,
            tiempo_degradacion_max: form.value.tiempo_degradacion_max || null,
            certificaciones_aplicables: form.value.certificaciones_aplicables || null,
            icono: form.value.icono || null
        };

        if (esEdicion.value) {
            // Actualizar
            await axios.put(`/api/tipos-materiales/${props.tipoMaterial.id}`, data);
            alert('Tipo de material actualizado exitosamente');
        } else {
            // Crear
            await axios.post('/api/tipos-materiales', data);
            alert('Tipo de material creado exitosamente');
        }

        emit('guardado');
        emit('close');
    } catch (error) {
        console.error('Error al guardar tipo de material:', error);
        const mensaje = error.response?.data?.message || error.response?.data?.errors || 'Error al guardar el tipo de material';
        alert(typeof mensaje === 'object' ? JSON.stringify(mensaje) : mensaje);
    }
};
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}
</style>
