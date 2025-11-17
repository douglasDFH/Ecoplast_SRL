<template>
    <transition name="modal">
        <div v-if="visible" class="fixed inset-0 z-[9999] overflow-y-auto" @click.self="$emit('close')">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="$emit('close')"></div>

                <!-- Modal panel -->
                <div class="relative z-10 inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-2xl font-bold text-white">
                                {{ esEdicion ? 'Editar Proveedor' : 'Nuevo Proveedor' }}
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
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                                    </svg>
                                    Identificación
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Código *</label>
                                        <input
                                            v-model="form.codigo_proveedor"
                                            type="text"
                                            required
                                            maxlength="20"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                            placeholder="Ej: PROV-001"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">RUC</label>
                                        <input
                                            v-model="form.ruc"
                                            type="text"
                                            maxlength="20"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                            placeholder="80012345-1"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Nombre Comercial *</label>
                                        <input
                                            v-model="form.nombre_comercial"
                                            type="text"
                                            required
                                            maxlength="200"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                            placeholder="Ej: Biodegradables del Sur"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Razón Social</label>
                                        <input
                                            v-model="form.razon_social"
                                            type="text"
                                            maxlength="200"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                            placeholder="Ej: Biodegradables del Sur S.A."
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Contacto -->
                            <div class="mb-6">
                                <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    Información de Contacto
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Contacto</label>
                                        <input
                                            v-model="form.contacto"
                                            type="text"
                                            maxlength="100"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                            placeholder="Juan Pérez"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono</label>
                                        <input
                                            v-model="form.telefono"
                                            type="text"
                                            maxlength="20"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                            placeholder="+595 21 123456"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                        <input
                                            v-model="form.email"
                                            type="email"
                                            maxlength="100"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                            placeholder="contacto@ejemplo.com"
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Ubicación -->
                            <div class="mb-6">
                                <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Ubicación
                                </h4>
                                <div class="grid grid-cols-1 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Dirección</label>
                                        <textarea
                                            v-model="form.direccion"
                                            rows="2"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                            placeholder="Av. Eusebio Ayala 1234"
                                        ></textarea>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Ciudad</label>
                                            <input
                                                v-model="form.ciudad"
                                                type="text"
                                                maxlength="100"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                                placeholder="Asunción"
                                            />
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">País</label>
                                            <input
                                                v-model="form.pais"
                                                type="text"
                                                maxlength="100"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                                placeholder="Paraguay"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Notas y Estado -->
                            <div class="mb-6">
                                <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Información Adicional
                                </h4>
                                <div class="grid grid-cols-1 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Notas</label>
                                        <textarea
                                            v-model="form.notas"
                                            rows="3"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                            placeholder="Información adicional del proveedor..."
                                        ></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                                        <div class="flex items-center">
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
    proveedor: Object
});

const emit = defineEmits(['close', 'guardado']);

const form = ref({
    codigo_proveedor: '',
    nombre_comercial: '',
    razon_social: '',
    ruc: '',
    contacto: '',
    telefono: '',
    email: '',
    direccion: '',
    ciudad: '',
    pais: 'Paraguay',
    notas: '',
    activo: true
});

const esEdicion = computed(() => !!props.proveedor);

watch(() => props.visible, (visible) => {
    if (visible) {
        if (props.proveedor) {
            // Modo edición
            form.value = {
                codigo_proveedor: props.proveedor.codigo_proveedor || '',
                nombre_comercial: props.proveedor.nombre_comercial || '',
                razon_social: props.proveedor.razon_social || '',
                ruc: props.proveedor.ruc || '',
                contacto: props.proveedor.contacto || '',
                telefono: props.proveedor.telefono || '',
                email: props.proveedor.email || '',
                direccion: props.proveedor.direccion || '',
                ciudad: props.proveedor.ciudad || '',
                pais: props.proveedor.pais || 'Paraguay',
                notas: props.proveedor.notas || '',
                activo: props.proveedor.activo ?? true
            };
        } else {
            // Modo creación - resetear formulario
            form.value = {
                codigo_proveedor: '',
                nombre_comercial: '',
                razon_social: '',
                ruc: '',
                contacto: '',
                telefono: '',
                email: '',
                direccion: '',
                ciudad: '',
                pais: 'Paraguay',
                notas: '',
                activo: true
            };
        }
    }
});

const guardar = async () => {
    try {
        // Validación básica
        if (!form.value.codigo_proveedor || !form.value.nombre_comercial) {
            alert('Por favor complete los campos requeridos (Código y Nombre Comercial)');
            return;
        }

        // Convertir valores nulos/vacíos a null explícito
        const data = {
            ...form.value,
            razon_social: form.value.razon_social || null,
            ruc: form.value.ruc || null,
            contacto: form.value.contacto || null,
            telefono: form.value.telefono || null,
            email: form.value.email || null,
            direccion: form.value.direccion || null,
            ciudad: form.value.ciudad || null,
            notas: form.value.notas || null
        };

        if (esEdicion.value) {
            // Actualizar
            await axios.put(`/api/proveedores/${props.proveedor.id}`, data);
            alert('Proveedor actualizado exitosamente');
        } else {
            // Crear
            await axios.post('/api/proveedores', data);
            alert('Proveedor creado exitosamente');
        }

        emit('guardado');
        emit('close');
    } catch (error) {
        console.error('Error al guardar proveedor:', error);
        const mensaje = error.response?.data?.message || error.response?.data?.errors || 'Error al guardar el proveedor';
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
