<template>
    <div class="fixed inset-0 z-50 overflow-y-auto" @click.self="$emit('close')">
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>

            <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-4xl transform transition-all max-h-[90vh] overflow-y-auto">
                <!-- Header -->
                <div class="px-8 py-6 border-b border-gray-100 sticky top-0 bg-white z-10 rounded-t-3xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-2xl font-bold" style="color: #1E293B;">
                                {{ formulacion ? 'Editar Formulación' : 'Nueva Formulación' }}
                            </h3>
                            <p class="text-sm mt-1" style="color: #64748B;">
                                {{ formulacion ? 'Actualice la información de la formulación' : 'Complete los datos de la nueva formulación biodegradable' }}
                            </p>
                        </div>
                        <button
                            @click="$emit('close')"
                            class="p-2 rounded-xl transition-all hover:bg-gray-100 active:scale-95"
                        >
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Form -->
                <form @submit.prevent="handleSubmit" class="px-8 py-6 space-y-6">
                    <!-- Información Básica -->
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Información Básica
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Código de Fórmula -->
                            <div>
                                <label class="block text-sm font-medium mb-2" style="color: #455A64;">
                                    Código de Fórmula <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="formData.codigo_formula"
                                    type="text"
                                    required
                                    placeholder="Ej: FORM-PLA-001"
                                    class="modern-input w-full px-4 py-2.5 rounded-xl"
                                    style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                                />
                            </div>

                            <!-- Nombre de Fórmula -->
                            <div>
                                <label class="block text-sm font-medium mb-2" style="color: #455A64;">
                                    Nombre de Fórmula <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="formData.nombre_formula"
                                    type="text"
                                    required
                                    placeholder="Ej: Film PLA Alta Resistencia"
                                    class="modern-input w-full px-4 py-2.5 rounded-xl"
                                    style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                                />
                            </div>
                        </div>

                        <!-- Descripción -->
                        <div class="mt-6">
                            <label class="block text-sm font-medium mb-2" style="color: #455A64;">
                                Descripción
                            </label>
                            <textarea
                                v-model="formData.descripcion"
                                rows="3"
                                placeholder="Descripción detallada de la formulación..."
                                class="modern-input w-full px-4 py-2.5 rounded-xl resize-none"
                                style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                            ></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                            <!-- Tipo de Producto -->
                            <div>
                                <label class="block text-sm font-medium mb-2" style="color: #455A64;">
                                    Tipo de Producto <span class="text-red-500">*</span>
                                </label>
                                <select
                                    v-model="formData.tipo_producto"
                                    required
                                    class="modern-input w-full px-4 py-2.5 rounded-xl"
                                    style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                                >
                                    <option value="">Seleccionar...</option>
                                    <option value="Film biodegradable">Film biodegradable</option>
                                    <option value="Bolsa compostable">Bolsa compostable</option>
                                    <option value="Empaque sostenible">Empaque sostenible</option>
                                    <option value="Contenedor">Contenedor</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>

                            <!-- Versión -->
                            <div>
                                <label class="block text-sm font-medium mb-2" style="color: #455A64;">
                                    Versión <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="formData.version"
                                    type="number"
                                    min="1"
                                    required
                                    placeholder="1"
                                    class="modern-input w-full px-4 py-2.5 rounded-xl"
                                    style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                                />
                            </div>

                            <!-- Rendimiento Estimado -->
                            <div>
                                <label class="block text-sm font-medium mb-2" style="color: #455A64;">
                                    Rendimiento Estimado (%)
                                </label>
                                <input
                                    v-model="formData.rendimiento_estimado"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    max="100"
                                    placeholder="95.5"
                                    class="modern-input w-full px-4 py-2.5 rounded-xl"
                                    style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Características Biodegradables -->
                    <div class="p-6 rounded-xl" style="background: #F0FDF4; border: 2px solid #86EFAC;">
                        <h4 class="font-semibold text-green-900 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Características Biodegradables
                        </h4>

                        <div class="flex items-center gap-3 mb-4">
                            <input
                                v-model="formData.es_biodegradable"
                                type="checkbox"
                                id="biodegradable"
                                class="w-5 h-5 rounded text-green-600 focus:ring-green-500"
                            />
                            <label for="biodegradable" class="text-sm font-medium text-green-900">
                                Esta formulación es biodegradable
                            </label>
                        </div>

                        <div v-if="formData.es_biodegradable" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-2 text-green-800">
                                    Tiempo de Biodegradación (días)
                                </label>
                                <input
                                    v-model="formData.tiempo_biodegradacion_dias"
                                    type="number"
                                    min="0"
                                    placeholder="90"
                                    class="modern-input w-full px-4 py-2.5 rounded-xl bg-white"
                                    style="border: 2px solid #86EFAC;"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2 text-green-800">
                                    Certificación
                                </label>
                                <select
                                    v-model="formData.certificacion_biodegradable"
                                    class="modern-input w-full px-4 py-2.5 rounded-xl bg-white"
                                    style="border: 2px solid #86EFAC;"
                                >
                                    <option value="">Sin certificación</option>
                                    <option value="EN 13432">EN 13432</option>
                                    <option value="ASTM D6400">ASTM D6400</option>
                                    <option value="OK Compost">OK Compost</option>
                                    <option value="TÜV Austria">TÜV Austria</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Parámetros de Proceso -->
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                            </svg>
                            Parámetros de Proceso
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium mb-2" style="color: #455A64;">
                                    Temperatura (°C)
                                </label>
                                <input
                                    v-model="formData.temperatura_proceso"
                                    type="number"
                                    step="0.1"
                                    placeholder="180"
                                    class="modern-input w-full px-4 py-2.5 rounded-xl"
                                    style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-2" style="color: #455A64;">
                                    Presión (bar)
                                </label>
                                <input
                                    v-model="formData.presion_proceso"
                                    type="number"
                                    step="0.1"
                                    placeholder="50"
                                    class="modern-input w-full px-4 py-2.5 rounded-xl"
                                    style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-2" style="color: #455A64;">
                                    Tiempo de Ciclo (min)
                                </label>
                                <input
                                    v-model="formData.tiempo_ciclo"
                                    type="number"
                                    step="0.1"
                                    placeholder="15"
                                    class="modern-input w-full px-4 py-2.5 rounded-xl"
                                    style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Notas Técnicas -->
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: #455A64;">
                            Notas Técnicas
                        </label>
                        <textarea
                            v-model="formData.notas_tecnicas"
                            rows="3"
                            placeholder="Especificaciones técnicas adicionales, recomendaciones de proceso..."
                            class="modern-input w-full px-4 py-2.5 rounded-xl resize-none"
                            style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                        ></textarea>
                    </div>

                    <!-- Estado Activo -->
                    <div class="flex items-center gap-3 p-4 rounded-xl" style="background: #F8FAFC;">
                        <input
                            v-model="formData.activa"
                            type="checkbox"
                            id="activa"
                            class="w-5 h-5 rounded text-purple-600 focus:ring-purple-500"
                        />
                        <label for="activa" class="text-sm font-medium" style="color: #455A64;">
                            Formulación activa y disponible para producción
                        </label>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
                        <button
                            type="button"
                            @click="$emit('close')"
                            class="px-6 py-2.5 rounded-xl font-medium transition-all active:scale-95"
                            style="background: #F1F5F9; color: #475569;"
                        >
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            class="px-6 py-2.5 rounded-xl font-medium text-white transition-all active:scale-95"
                            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"
                        >
                            {{ formulacion ? 'Actualizar' : 'Crear' }} Formulación
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { reactive } from 'vue';

const props = defineProps({
    formulacion: {
        type: Object,
        default: null
    }
});

const emit = defineEmits(['close', 'submit']);

const formData = reactive({
    codigo_formula: props.formulacion?.codigo_formula || '',
    nombre_formula: props.formulacion?.nombre_formula || '',
    descripcion: props.formulacion?.descripcion || '',
    tipo_producto: props.formulacion?.tipo_producto || '',
    version: props.formulacion?.version || 1,
    rendimiento_estimado: props.formulacion?.rendimiento_estimado || null,
    es_biodegradable: props.formulacion?.es_biodegradable !== undefined ? Boolean(props.formulacion.es_biodegradable) : true,
    tiempo_biodegradacion_dias: props.formulacion?.tiempo_biodegradacion_dias || null,
    certificacion_biodegradable: props.formulacion?.certificacion_biodegradable || '',
    temperatura_proceso: props.formulacion?.temperatura_proceso || null,
    presion_proceso: props.formulacion?.presion_proceso || null,
    tiempo_ciclo: props.formulacion?.tiempo_ciclo || null,
    notas_tecnicas: props.formulacion?.notas_tecnicas || '',
    activa: props.formulacion?.activa !== undefined ? Boolean(props.formulacion.activa) : true
});

const handleSubmit = async () => {
    try {
        await emit('submit', {
            ...formData,
            es_biodegradable: formData.es_biodegradable ? 1 : 0,
            activa: formData.activa ? 1 : 0
        });
    } catch (error) {
        console.error('Error en el formulario:', error);
    }
};
</script>

<style scoped>
.modern-input {
    transition: all 0.2s;
}

.modern-input:focus {
    outline: none;
    border-color: #667eea !important;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

/* Scroll styling */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: #667eea;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: #5568d3;
}
</style>
