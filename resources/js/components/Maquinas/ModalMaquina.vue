<template>
    <div class="fixed inset-0 z-50 overflow-y-auto" @click.self="$emit('close')">
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>

            <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-4xl transform transition-all">
                <!-- Header -->
                <div class="px-8 py-6 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-2xl font-bold" style="color: #1E293B;">
                                {{ maquina ? 'Editar Máquina' : 'Nueva Máquina' }}
                            </h3>
                            <p class="text-sm mt-1" style="color: #64748B;">
                                {{ maquina ? 'Actualice la información de la máquina' : 'Complete los datos de la nueva maquinaria' }}
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
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Código de Máquina -->
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: #455A64;">
                                Código de Máquina <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="formData.codigo_maquina"
                                type="text"
                                required
                                placeholder="Ej: MAQ-EXT-001"
                                class="modern-input w-full px-4 py-2.5 rounded-xl"
                                style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                            />
                        </div>

                        <!-- Nombre de Máquina -->
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: #455A64;">
                                Nombre de Máquina <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="formData.nombre_maquina"
                                type="text"
                                required
                                placeholder="Ej: Extrusora de Film Biodegradable"
                                class="modern-input w-full px-4 py-2.5 rounded-xl"
                                style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Tipo de Máquina -->
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: #455A64;">
                                Tipo de Máquina <span class="text-red-500">*</span>
                            </label>
                            <select
                                v-model="formData.tipo_maquina"
                                required
                                class="modern-input w-full px-4 py-2.5 rounded-xl"
                                style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                            >
                                <option value="">Seleccionar...</option>
                                <option value="Extrusora">Extrusora</option>
                                <option value="Moldeadora">Moldeadora</option>
                                <option value="Selladora">Selladora</option>
                                <option value="Cortadora">Cortadora</option>
                                <option value="Mezcladora">Mezcladora</option>
                                <option value="Termoformadora">Termoformadora</option>
                                <option value="Impresora">Impresora</option>
                            </select>
                        </div>

                        <!-- Marca -->
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: #455A64;">
                                Marca <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="formData.marca"
                                type="text"
                                required
                                placeholder="Ej: Kuhne"
                                class="modern-input w-full px-4 py-2.5 rounded-xl"
                                style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                            />
                        </div>

                        <!-- Modelo -->
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: #455A64;">
                                Modelo <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="formData.modelo"
                                type="text"
                                required
                                placeholder="Ej: KEB-90"
                                class="modern-input w-full px-4 py-2.5 rounded-xl"
                                style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Número de Serie -->
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: #455A64;">
                                Número de Serie
                            </label>
                            <input
                                v-model="formData.numero_serie"
                                type="text"
                                placeholder="Número de serie del fabricante"
                                class="modern-input w-full px-4 py-2.5 rounded-xl"
                                style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                            />
                        </div>

                        <!-- Año de Fabricación -->
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: #455A64;">
                                Año de Fabricación
                            </label>
                            <input
                                v-model="formData.ano_fabricacion"
                                type="number"
                                min="1900"
                                :max="new Date().getFullYear()"
                                placeholder="2020"
                                class="modern-input w-full px-4 py-2.5 rounded-xl"
                                style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                            />
                        </div>
                    </div>

                    <!-- Especificaciones Técnicas -->
                    <div class="p-6 rounded-xl" style="background: #F0F9FF; border: 2px solid #7DD3FC;">
                        <h4 class="font-semibold text-blue-900 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                            </svg>
                            Especificaciones Técnicas
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-2 text-blue-800">
                                    Capacidad de Producción
                                </label>
                                <input
                                    v-model="formData.capacidad_produccion"
                                    type="number"
                                    step="0.01"
                                    placeholder="100"
                                    class="modern-input w-full px-4 py-2.5 rounded-xl bg-white"
                                    style="border: 2px solid #7DD3FC;"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2 text-blue-800">
                                    Unidad de Capacidad
                                </label>
                                <select
                                    v-model="formData.unidad_capacidad"
                                    class="modern-input w-full px-4 py-2.5 rounded-xl bg-white"
                                    style="border: 2px solid #7DD3FC;"
                                >
                                    <option value="kg/h">kg/hora</option>
                                    <option value="unidades/h">unidades/hora</option>
                                    <option value="m/min">metros/minuto</option>
                                    <option value="m2/h">m²/hora</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2 text-blue-800">
                                    Potencia (kW)
                                </label>
                                <input
                                    v-model="formData.potencia_kw"
                                    type="number"
                                    step="0.01"
                                    placeholder="50"
                                    class="modern-input w-full px-4 py-2.5 rounded-xl bg-white"
                                    style="border: 2px solid #7DD3FC;"
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label class="block text-sm font-medium mb-2 text-blue-800">
                                    Voltaje (V)
                                </label>
                                <input
                                    v-model="formData.voltaje"
                                    type="number"
                                    placeholder="380"
                                    class="modern-input w-full px-4 py-2.5 rounded-xl bg-white"
                                    style="border: 2px solid #7DD3FC;"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2 text-blue-800">
                                    Consumo Energético (kWh)
                                </label>
                                <input
                                    v-model="formData.consumo_energia_kwh"
                                    type="number"
                                    step="0.01"
                                    placeholder="45"
                                    class="modern-input w-full px-4 py-2.5 rounded-xl bg-white"
                                    style="border: 2px solid #7DD3FC;"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Estado y Mantenimiento -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: #455A64;">
                                Estado Actual <span class="text-red-500">*</span>
                            </label>
                            <select
                                v-model="formData.estado_actual"
                                required
                                class="modern-input w-full px-4 py-2.5 rounded-xl"
                                style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                            >
                                <option value="operativa">Operativa</option>
                                <option value="en_mantenimiento">En Mantenimiento</option>
                                <option value="fuera_servicio">Fuera de Servicio</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: #455A64;">
                                Horas de Uso Total
                            </label>
                            <input
                                v-model="formData.horas_uso_total"
                                type="number"
                                min="0"
                                placeholder="0"
                                class="modern-input w-full px-4 py-2.5 rounded-xl"
                                style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: #455A64;">
                                Frecuencia Mantenimiento (horas)
                            </label>
                            <input
                                v-model="formData.frecuencia_mantenimiento_horas"
                                type="number"
                                min="0"
                                placeholder="500"
                                class="modern-input w-full px-4 py-2.5 rounded-xl"
                                style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: #455A64;">
                                Fecha Último Mantenimiento
                            </label>
                            <input
                                v-model="formData.fecha_ultimo_mantenimiento"
                                type="date"
                                class="modern-input w-full px-4 py-2.5 rounded-xl"
                                style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: #455A64;">
                                Horas Último Mantenimiento
                            </label>
                            <input
                                v-model="formData.horas_ultimo_mantenimiento"
                                type="number"
                                min="0"
                                placeholder="0"
                                class="modern-input w-full px-4 py-2.5 rounded-xl"
                                style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                            />
                        </div>
                    </div>

                    <!-- Ubicación -->
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: #455A64;">
                            Ubicación en Planta
                        </label>
                        <input
                            v-model="formData.ubicacion"
                            type="text"
                            placeholder="Ej: Planta 1 - Zona A - Línea 2"
                            class="modern-input w-full px-4 py-2.5 rounded-xl"
                            style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                        />
                    </div>

                    <!-- Observaciones -->
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: #455A64;">
                            Observaciones
                        </label>
                        <textarea
                            v-model="formData.observaciones"
                            rows="3"
                            placeholder="Notas adicionales sobre la máquina..."
                            class="modern-input w-full px-4 py-2.5 rounded-xl resize-none"
                            style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                        ></textarea>
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
                            style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);"
                        >
                            {{ maquina ? 'Actualizar' : 'Crear' }} Máquina
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
    maquina: {
        type: Object,
        default: null
    }
});

const emit = defineEmits(['close', 'submit']);

const formData = reactive({
    codigo_maquina: props.maquina?.codigo_maquina || '',
    nombre_maquina: props.maquina?.nombre_maquina || '',
    tipo_maquina: props.maquina?.tipo_maquina || '',
    marca: props.maquina?.marca || '',
    modelo: props.maquina?.modelo || '',
    numero_serie: props.maquina?.numero_serie || '',
    ano_fabricacion: props.maquina?.ano_fabricacion || null,
    capacidad_produccion: props.maquina?.capacidad_produccion || null,
    unidad_capacidad: props.maquina?.unidad_capacidad || 'kg/h',
    potencia_kw: props.maquina?.potencia_kw || null,
    voltaje: props.maquina?.voltaje || null,
    consumo_energia_kwh: props.maquina?.consumo_energia_kwh || null,
    estado_actual: props.maquina?.estado_actual || 'operativa',
    ubicacion: props.maquina?.ubicacion || '',
    horas_uso_total: props.maquina?.horas_uso_total || 0,
    fecha_ultimo_mantenimiento: props.maquina?.fecha_ultimo_mantenimiento || '',
    horas_ultimo_mantenimiento: props.maquina?.horas_ultimo_mantenimiento || 0,
    frecuencia_mantenimiento_horas: props.maquina?.frecuencia_mantenimiento_horas || 500,
    observaciones: props.maquina?.observaciones || ''
});

const handleSubmit = async () => {
    try {
        await emit('submit', formData);
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
    border-color: #f093fb !important;
    box-shadow: 0 0 0 3px rgba(240, 147, 251, 0.1);
}
</style>
