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
                                {{ producto ? 'Editar Producto' : 'Nuevo Producto' }}
                            </h3>
                            <p class="text-sm mt-1" style="color: #64748B;">
                                {{ producto ? 'Actualice la información del producto' : 'Complete los datos del nuevo producto biodegradable' }}
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
                        <!-- Código del Producto -->
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: #455A64;">
                                Código del Producto <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="formData.codigo_producto"
                                type="text"
                                required
                                placeholder="Ej: PROD-FILM-001"
                                class="modern-input w-full px-4 py-2.5 rounded-xl"
                                style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                            />
                        </div>

                        <!-- Nombre del Producto -->
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: #455A64;">
                                Nombre del Producto <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="formData.nombre_producto"
                                type="text"
                                required
                                placeholder="Ej: Film Biodegradable PLA 50 micras"
                                class="modern-input w-full px-4 py-2.5 rounded-xl"
                                style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                            />
                        </div>
                    </div>

                    <!-- Descripción -->
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: #455A64;">
                            Descripción
                        </label>
                        <textarea
                            v-model="formData.descripcion"
                            rows="3"
                            placeholder="Descripción detallada del producto..."
                            class="modern-input w-full px-4 py-2.5 rounded-xl resize-none"
                            style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                        ></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Tipo de Material -->
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: #455A64;">
                                Tipo de Material <span class="text-red-500">*</span>
                            </label>
                            <select
                                v-model="formData.tipo_material"
                                required
                                class="modern-input w-full px-4 py-2.5 rounded-xl"
                                style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                            >
                                <option value="">Seleccionar...</option>
                                <option value="PLA">PLA (Ácido Poliláctico)</option>
                                <option value="PHA">PHA (Polihidroxialcanoatos)</option>
                                <option value="PBS">PBS (Polibutil Succinato)</option>
                                <option value="Almidón">Almidón</option>
                                <option value="Celulosa">Celulosa</option>
                                <option value="Mixto">Mixto</option>
                            </select>
                        </div>

                        <!-- Espesor/Dimensión -->
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: #455A64;">
                                Espesor/Dimensión
                            </label>
                            <input
                                v-model="formData.espesor_micras"
                                type="number"
                                step="0.01"
                                placeholder="Ej: 50"
                                class="modern-input w-full px-4 py-2.5 rounded-xl"
                                style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                            />
                        </div>

                        <!-- Unidad de Medida -->
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: #455A64;">
                                Unidad de Medida <span class="text-red-500">*</span>
                            </label>
                            <select
                                v-model="formData.unidad_medida"
                                required
                                class="modern-input w-full px-4 py-2.5 rounded-xl"
                                style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                            >
                                <option value="kg">Kilogramos (kg)</option>
                                <option value="unidad">Unidades</option>
                                <option value="rollo">Rollos</option>
                                <option value="paquete">Paquetes</option>
                                <option value="m2">Metros cuadrados (m²)</option>
                                <option value="m">Metros lineales (m)</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Precio de Venta -->
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: #455A64;">
                                Precio de Venta (S/) <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="formData.precio_venta"
                                type="number"
                                step="0.01"
                                min="0"
                                required
                                placeholder="0.00"
                                class="modern-input w-full px-4 py-2.5 rounded-xl"
                                style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                            />
                        </div>

                        <!-- Costo de Producción -->
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: #455A64;">
                                Costo de Producción (S/) <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="formData.costo_produccion"
                                type="number"
                                step="0.01"
                                min="0"
                                required
                                placeholder="0.00"
                                class="modern-input w-full px-4 py-2.5 rounded-xl"
                                style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                            />
                        </div>
                    </div>

                    <!-- Margen calculado -->
                    <div v-if="margenGanancia !== null" class="p-4 rounded-xl"
                        :style="margenGanancia > 20 ? 'background: #D1FAE5;' : 'background: #FEF3C7;'">
                        <p class="text-sm font-medium"
                            :style="margenGanancia > 20 ? 'color: #065F46;' : 'color: #92400E;'">
                            Margen de ganancia: {{ margenGanancia.toFixed(2) }}%
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Stock Actual -->
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: #455A64;">
                                Stock Actual
                            </label>
                            <input
                                v-model="formData.stock_actual"
                                type="number"
                                min="0"
                                placeholder="0"
                                class="modern-input w-full px-4 py-2.5 rounded-xl"
                                style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                            />
                        </div>

                        <!-- Stock Mínimo -->
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: #455A64;">
                                Stock Mínimo <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="formData.stock_minimo"
                                type="number"
                                min="0"
                                required
                                placeholder="0"
                                class="modern-input w-full px-4 py-2.5 rounded-xl"
                                style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                            />
                        </div>

                        <!-- Stock Máximo -->
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: #455A64;">
                                Stock Máximo
                            </label>
                            <input
                                v-model="formData.stock_maximo"
                                type="number"
                                min="0"
                                placeholder="0"
                                class="modern-input w-full px-4 py-2.5 rounded-xl"
                                style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                            />
                        </div>
                    </div>

                    <!-- Certificación Biodegradable -->
                    <div class="p-6 rounded-xl" style="background: #F0FDF4; border: 2px solid #86EFAC;">
                        <div class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <div class="flex-1">
                                <h4 class="font-semibold text-green-900 mb-3">Certificación Biodegradable</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium mb-2 text-green-800">
                                            Certificación
                                        </label>
                                        <select
                                            v-model="formData.certificacion_compostable"
                                            class="modern-input w-full px-4 py-2.5 rounded-xl bg-white"
                                            style="border: 2px solid #86EFAC;"
                                        >
                                            <option value="">Sin certificación</option>
                                            <option value="EN 13432 - Compostable Industrial">EN 13432 - Compostable Industrial</option>
                                            <option value="ASTM D6400 - Compostable">ASTM D6400 - Compostable</option>
                                            <option value="OK Compost HOME">OK Compost HOME</option>
                                            <option value="DIN CERTCO">DIN CERTCO</option>
                                            <option value="TÜV Austria OK biodegradable">TÜV Austria OK biodegradable</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-2 text-green-800">
                                            Tiempo de Compostaje (días)
                                        </label>
                                        <input
                                            v-model="formData.tiempo_compostaje_dias"
                                            type="number"
                                            min="0"
                                            placeholder="Ej: 90"
                                            class="modern-input w-full px-4 py-2.5 rounded-xl bg-white"
                                            style="border: 2px solid #86EFAC;"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Estado Activo -->
                    <div class="flex items-center gap-3">
                        <input
                            v-model="formData.activo"
                            type="checkbox"
                            id="activo"
                            class="w-5 h-5 rounded text-purple-600 focus:ring-purple-500"
                        />
                        <label for="activo" class="text-sm font-medium" style="color: #455A64;">
                            Producto activo y disponible para producción
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
                            {{ producto ? 'Actualizar' : 'Crear' }} Producto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { reactive, computed } from 'vue';

const props = defineProps({
    producto: {
        type: Object,
        default: null
    }
});

const emit = defineEmits(['close', 'submit']);

const formData = reactive({
    codigo_producto: props.producto?.codigo_producto || '',
    nombre_producto: props.producto?.nombre_producto || '',
    descripcion: props.producto?.descripcion || '',
    tipo_material: props.producto?.tipo_material || '',
    espesor_micras: props.producto?.espesor_micras || null,
    precio_venta: props.producto?.precio_venta || '',
    costo_produccion: props.producto?.costo_produccion || '',
    unidad_medida: props.producto?.unidad_medida || 'kg',
    stock_actual: props.producto?.stock_actual || 0,
    stock_minimo: props.producto?.stock_minimo || 0,
    stock_maximo: props.producto?.stock_maximo || null,
    certificacion_compostable: props.producto?.certificacion_compostable || '',
    tiempo_compostaje_dias: props.producto?.tiempo_compostaje_dias || null,
    activo: props.producto?.activo !== undefined ? Boolean(props.producto.activo) : true
});

const margenGanancia = computed(() => {
    const precio = parseFloat(formData.precio_venta) || 0;
    const costo = parseFloat(formData.costo_produccion) || 0;
    if (precio === 0 || costo === 0) return null;
    return ((precio - costo) / costo) * 100;
});

const handleSubmit = async () => {
    try {
        await emit('submit', {
            ...formData,
            activo: formData.activo ? 1 : 0
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
</style>
