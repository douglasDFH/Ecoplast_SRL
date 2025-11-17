<template>
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="modern-card rounded-3xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <!-- Header -->
            <div class="sticky top-0 bg-white px-8 py-6 border-b border-gray-200 rounded-t-3xl z-10">
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-bold text-gray-900">
                        {{ insumo ? 'Editar Insumo' : 'Nuevo Insumo' }}
                    </h2>
                    <button
                        @click="$emit('close')"
                        class="p-2 hover:bg-gray-100 rounded-xl transition-colors"
                    >
                        <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Body -->
            <div class="px-8 py-6 space-y-6">
                <!-- Información Básica -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Código -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Código del Insumo *
                        </label>
                        <input
                            v-model="form.codigo_insumo"
                            type="text"
                            placeholder="Ej: PLA-001"
                            class="modern-input w-full px-4 py-2.5 rounded-xl"
                            style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                            required
                        />
                    </div>

                    <!-- Nombre -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nombre del Insumo *
                        </label>
                        <input
                            v-model="form.nombre_insumo"
                            type="text"
                            placeholder="Ej: PLA Ingeo 4043D"
                            class="modern-input w-full px-4 py-2.5 rounded-xl"
                            style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                            required
                        />
                    </div>

                    <!-- Categoría -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Categoría *
                        </label>
                        <select
                            v-model="form.categoria_id"
                            class="modern-input w-full px-4 py-2.5 rounded-xl"
                            style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                            required
                        >
                            <option value="">Seleccionar categoría...</option>
                            <option v-for="cat in categorias" :key="cat.id" :value="cat.id">
                                {{ cat.nombre_categoria }}
                            </option>
                        </select>
                    </div>

                    <!-- Tipo Material -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tipo de Material *
                        </label>
                        <select
                            v-model="form.tipo_material"
                            class="modern-input w-full px-4 py-2.5 rounded-xl"
                            style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                            required
                        >
                            <option value="">Seleccionar tipo...</option>
                            <option value="PLA">PLA</option>
                            <option value="PHA">PHA</option>
                            <option value="PBS">PBS</option>
                            <option value="PBAT">PBAT</option>
                            <option value="Almidon">Almidón</option>
                            <option value="Celulosa">Celulosa</option>
                            <option value="Aditivo">Aditivo</option>
                            <option value="Pigmento">Pigmento</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>

                    <!-- Unidad de Medida -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Unidad de Medida *
                        </label>
                        <select
                            v-model="form.unidad_medida"
                            class="modern-input w-full px-4 py-2.5 rounded-xl"
                            style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                            required
                        >
                            <option value="kg">Kilogramo (kg)</option>
                            <option value="ton">Tonelada (ton)</option>
                            <option value="litro">Litro (L)</option>
                            <option value="unidad">Unidad</option>
                        </select>
                    </div>

                    <!-- Proveedor -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Proveedor
                        </label>
                        <input
                            v-model="form.proveedor"
                            type="text"
                            placeholder="Nombre del proveedor"
                            class="modern-input w-full px-4 py-2.5 rounded-xl"
                            style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                        />
                    </div>
                </div>

                <!-- Propiedades Físicas -->
                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Propiedades Físicas</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Densidad -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Densidad (g/cm³)
                            </label>
                            <input
                                v-model="form.densidad"
                                type="number"
                                step="0.001"
                                placeholder="1.25"
                                class="modern-input w-full px-4 py-2.5 rounded-xl"
                                style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                            />
                        </div>

                        <!-- Temperatura de Fusión -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Temperatura de Fusión (°C)
                            </label>
                            <input
                                v-model="form.temperatura_fusion"
                                type="number"
                                step="0.1"
                                placeholder="175"
                                class="modern-input w-full px-4 py-2.5 rounded-xl"
                                style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                            />
                        </div>

                        <!-- Certificación -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Certificación Biodegradable
                            </label>
                            <input
                                v-model="form.certificacion_biodegradable"
                                type="text"
                                placeholder="Ej: EN 13432, ASTM D6400"
                                class="modern-input w-full px-4 py-2.5 rounded-xl"
                                style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                            />
                        </div>
                    </div>
                </div>

                <!-- Inventario y Precio -->
                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Inventario y Precio</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Precio Unitario -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Precio Unitario ($) *
                            </label>
                            <input
                                v-model="form.precio_unitario"
                                type="number"
                                step="0.01"
                                min="0"
                                placeholder="0.00"
                                class="modern-input w-full px-4 py-2.5 rounded-xl"
                                style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                                required
                            />
                        </div>

                        <!-- Stock Actual -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Stock Actual *
                            </label>
                            <input
                                v-model="form.stock_actual"
                                type="number"
                                step="0.01"
                                min="0"
                                placeholder="0"
                                class="modern-input w-full px-4 py-2.5 rounded-xl"
                                style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                                required
                            />
                        </div>

                        <!-- Stock Mínimo -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Stock Mínimo *
                            </label>
                            <input
                                v-model="form.stock_minimo"
                                type="number"
                                step="0.01"
                                min="0"
                                placeholder="0"
                                class="modern-input w-full px-4 py-2.5 rounded-xl"
                                style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                                required
                            />
                        </div>
                    </div>
                </div>

                <!-- Estado -->
                <div class="flex items-center gap-3 p-4 bg-blue-50 rounded-xl">
                    <input
                        v-model="form.activo"
                        type="checkbox"
                        id="activo"
                        class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500"
                    />
                    <label for="activo" class="text-sm font-medium text-gray-700 cursor-pointer">
                        Insumo activo y disponible para uso
                    </label>
                </div>

                <!-- Error Message -->
                <div v-if="errorMsg" class="p-4 bg-red-50 border border-red-200 rounded-xl">
                    <p class="text-sm text-red-600">{{ errorMsg }}</p>
                </div>
            </div>

            <!-- Footer -->
            <div class="sticky bottom-0 bg-gray-50 px-8 py-6 border-t border-gray-200 rounded-b-3xl flex justify-end gap-3">
                <button
                    @click="$emit('close')"
                    class="modern-btn-secondary px-6 py-2.5 rounded-xl font-medium transition-all active:scale-95"
                    style="background: #F1F5F9; color: #475569;"
                    :disabled="saving"
                >
                    Cancelar
                </button>
                <button
                    @click="handleSubmit"
                    class="modern-btn-primary px-6 py-2.5 rounded-xl font-medium transition-all active:scale-95"
                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;"
                    :disabled="saving"
                >
                    {{ saving ? 'Guardando...' : (insumo ? 'Actualizar' : 'Crear') }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    insumo: {
        type: Object,
        default: null
    },
    categorias: {
        type: Array,
        required: true
    }
});

const emit = defineEmits(['close', 'submit']);

const form = ref({
    codigo_insumo: '',
    nombre_insumo: '',
    categoria_id: '',
    tipo_material: '',
    unidad_medida: 'kg',
    densidad: null,
    temperatura_fusion: null,
    certificacion_biodegradable: '',
    proveedor: '',
    precio_unitario: 0,
    stock_minimo: 0,
    stock_actual: 0,
    activo: true
});

const saving = ref(false);
const errorMsg = ref('');

// Watch para cargar datos cuando se edita
watch(() => props.insumo, (newInsumo) => {
    if (newInsumo) {
        form.value = {
            codigo_insumo: newInsumo.codigo_insumo || '',
            nombre_insumo: newInsumo.nombre_insumo || '',
            categoria_id: newInsumo.categoria_id || '',
            tipo_material: newInsumo.tipo_material || '',
            unidad_medida: newInsumo.unidad_medida || 'kg',
            densidad: newInsumo.densidad || null,
            temperatura_fusion: newInsumo.temperatura_fusion || null,
            certificacion_biodegradable: newInsumo.certificacion_biodegradable || '',
            proveedor: newInsumo.proveedor || '',
            precio_unitario: newInsumo.precio_unitario || 0,
            stock_minimo: newInsumo.stock_minimo || 0,
            stock_actual: newInsumo.stock_actual || 0,
            activo: newInsumo.activo !== undefined ? newInsumo.activo : true
        };
    } else {
        form.value = {
            codigo_insumo: '',
            nombre_insumo: '',
            categoria_id: '',
            tipo_material: '',
            unidad_medida: 'kg',
            densidad: null,
            temperatura_fusion: null,
            certificacion_biodegradable: '',
            proveedor: '',
            precio_unitario: 0,
            stock_minimo: 0,
            stock_actual: 0,
            activo: true
        };
    }
}, { immediate: true });

const handleSubmit = async () => {
    errorMsg.value = '';

    // Validaciones básicas
    if (!form.value.codigo_insumo?.trim()) {
        errorMsg.value = 'El código del insumo es obligatorio';
        return;
    }

    if (!form.value.nombre_insumo?.trim()) {
        errorMsg.value = 'El nombre del insumo es obligatorio';
        return;
    }

    if (!form.value.categoria_id) {
        errorMsg.value = 'La categoría es obligatoria';
        return;
    }

    if (!form.value.tipo_material) {
        errorMsg.value = 'El tipo de material es obligatorio';
        return;
    }

    if (!form.value.unidad_medida) {
        errorMsg.value = 'La unidad de medida es obligatoria';
        return;
    }

    if (form.value.precio_unitario < 0) {
        errorMsg.value = 'El precio unitario no puede ser negativo';
        return;
    }

    if (form.value.stock_actual < 0) {
        errorMsg.value = 'El stock actual no puede ser negativo';
        return;
    }

    if (form.value.stock_minimo < 0) {
        errorMsg.value = 'El stock mínimo no puede ser negativo';
        return;
    }

    saving.value = true;
    try {
        await emit('submit', form.value);
    } catch (error) {
        errorMsg.value = error.response?.data?.message || 'Error al guardar el insumo';
    } finally {
        saving.value = false;
    }
};
</script>

<style scoped>
.modern-card {
    background: white;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.modern-input {
    transition: all 0.2s;
}

.modern-input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.modern-btn-primary:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

.modern-btn-secondary:hover:not(:disabled) {
    background: #E2E8F0;
}

.modern-btn-primary:disabled,
.modern-btn-secondary:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>
