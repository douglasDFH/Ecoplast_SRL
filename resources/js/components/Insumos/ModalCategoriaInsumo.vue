<template>
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="modern-card rounded-3xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <!-- Header -->
            <div class="sticky top-0 bg-white px-8 py-6 border-b border-gray-200 rounded-t-3xl z-10">
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-bold text-gray-900">
                        {{ categoria ? 'Editar Categoría' : 'Nueva Categoría' }}
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
                <!-- Nombre -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nombre de la Categoría *
                    </label>
                    <input
                        v-model="form.nombre_categoria"
                        type="text"
                        placeholder="Ej: Polímeros Biodegradables"
                        class="modern-input w-full px-4 py-2.5 rounded-xl"
                        style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                        required
                    />
                </div>

                <!-- Descripción -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Descripción
                    </label>
                    <textarea
                        v-model="form.descripcion"
                        rows="3"
                        placeholder="Descripción de la categoría..."
                        class="modern-input w-full px-4 py-2.5 rounded-xl resize-none"
                        style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                    ></textarea>
                </div>

                <!-- Biodegradable -->
                <div class="flex items-center gap-3 p-4 bg-green-50 rounded-xl">
                    <input
                        v-model="form.es_biodegradable"
                        type="checkbox"
                        id="es_biodegradable"
                        class="w-5 h-5 text-green-600 rounded focus:ring-green-500"
                    />
                    <label for="es_biodegradable" class="text-sm font-medium text-gray-700 cursor-pointer">
                        Categoría de materiales biodegradables
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
                    {{ saving ? 'Guardando...' : (categoria ? 'Actualizar' : 'Crear') }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    categoria: {
        type: Object,
        default: null
    }
});

const emit = defineEmits(['close', 'submit']);

const form = ref({
    nombre_categoria: '',
    descripcion: '',
    es_biodegradable: false
});

const saving = ref(false);
const errorMsg = ref('');

// Watch para cargar datos cuando se edita
watch(() => props.categoria, (newCategoria) => {
    if (newCategoria) {
        form.value = {
            nombre_categoria: newCategoria.nombre_categoria || '',
            descripcion: newCategoria.descripcion || '',
            es_biodegradable: newCategoria.es_biodegradable || false
        };
    } else {
        form.value = {
            nombre_categoria: '',
            descripcion: '',
            es_biodegradable: false
        };
    }
}, { immediate: true });

const handleSubmit = async () => {
    errorMsg.value = '';

    // Validaciones
    if (!form.value.nombre_categoria?.trim()) {
        errorMsg.value = 'El nombre de la categoría es obligatorio';
        return;
    }

    saving.value = true;
    try {
        await emit('submit', form.value);
    } catch (error) {
        errorMsg.value = error.response?.data?.message || 'Error al guardar la categoría';
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
