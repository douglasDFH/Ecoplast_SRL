<template>
    <div class="fixed inset-0 z-50 overflow-y-auto" @click.self="$emit('close')">
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>

            <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-3xl transform transition-all">
                <!-- Header -->
                <div class="px-8 py-6 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-2xl font-bold" style="color: #1E293B;">
                                {{ usuario ? 'Editar Usuario' : 'Nuevo Usuario' }}
                            </h3>
                            <p class="text-sm mt-1" style="color: #64748B;">
                                {{ usuario ? 'Actualice la información del usuario' : 'Complete los datos del nuevo usuario del sistema' }}
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
                    <!-- Información Personal -->
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Información Personal
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nombre Completo -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium mb-2" style="color: #455A64;">
                                    Nombre Completo <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="formData.nombre_completo"
                                    type="text"
                                    required
                                    placeholder="Ej: Juan Carlos Pérez López"
                                    class="modern-input w-full px-4 py-2.5 rounded-xl"
                                    style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                                />
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-medium mb-2" style="color: #455A64;">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="formData.email"
                                    type="email"
                                    required
                                    placeholder="usuario@ecoplast.com"
                                    class="modern-input w-full px-4 py-2.5 rounded-xl"
                                    style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                                />
                            </div>

                            <!-- Teléfono -->
                            <div>
                                <label class="block text-sm font-medium mb-2" style="color: #455A64;">
                                    Teléfono
                                </label>
                                <input
                                    v-model="formData.telefono"
                                    type="tel"
                                    placeholder="999 999 999"
                                    class="modern-input w-full px-4 py-2.5 rounded-xl"
                                    style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                                />
                            </div>

                            <!-- DNI -->
                            <div>
                                <label class="block text-sm font-medium mb-2" style="color: #455A64;">
                                    DNI
                                </label>
                                <input
                                    v-model="formData.dni"
                                    type="text"
                                    maxlength="8"
                                    placeholder="12345678"
                                    class="modern-input w-full px-4 py-2.5 rounded-xl"
                                    style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                                />
                            </div>

                            <!-- Dirección -->
                            <div>
                                <label class="block text-sm font-medium mb-2" style="color: #455A64;">
                                    Dirección
                                </label>
                                <input
                                    v-model="formData.direccion"
                                    type="text"
                                    placeholder="Dirección completa"
                                    class="modern-input w-full px-4 py-2.5 rounded-xl"
                                    style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Rol y Permisos -->
                    <div class="p-6 rounded-xl" style="background: #F0F9FF; border: 2px solid #7DD3FC;">
                        <h4 class="font-semibold text-blue-900 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            Rol y Permisos
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-2 text-blue-800">
                                    Rol <span class="text-red-500">*</span>
                                </label>
                                <select
                                    v-model="formData.rol_id"
                                    required
                                    class="modern-input w-full px-4 py-2.5 rounded-xl bg-white"
                                    style="border: 2px solid #7DD3FC;"
                                >
                                    <option value="">Seleccionar rol...</option>
                                    <option v-for="rol in roles" :key="rol.id" :value="rol.id">
                                        {{ rol.nombre_rol }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-2 text-blue-800">
                                    Turno Asignado
                                </label>
                                <select
                                    v-model="formData.turno_id"
                                    class="modern-input w-full px-4 py-2.5 rounded-xl bg-white"
                                    style="border: 2px solid #7DD3FC;"
                                >
                                    <option value="">Sin turno asignado</option>
                                    <option v-for="turno in turnos" :key="turno.id" :value="turno.id">
                                        {{ turno.nombre_turno }} ({{ turno.hora_inicio }} - {{ turno.hora_fin }})
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Descripción del rol seleccionado -->
                        <div v-if="rolSeleccionado" class="mt-4 p-4 rounded-lg bg-blue-50">
                            <p class="text-sm text-blue-900">
                                <strong>Permisos del rol:</strong> {{ rolSeleccionado.descripcion || 'Sin descripción disponible' }}
                            </p>
                        </div>
                    </div>

                    <!-- Credenciales -->
                    <div v-if="!usuario">
                        <h4 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                            </svg>
                            Credenciales de Acceso
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium mb-2" style="color: #455A64;">
                                    Contraseña <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="formData.password"
                                    type="password"
                                    :required="!usuario"
                                    placeholder="Mínimo 6 caracteres"
                                    minlength="6"
                                    class="modern-input w-full px-4 py-2.5 rounded-xl"
                                    style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-2" style="color: #455A64;">
                                    Confirmar Contraseña <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="formData.password_confirmation"
                                    type="password"
                                    :required="!usuario"
                                    placeholder="Repetir contraseña"
                                    minlength="6"
                                    class="modern-input w-full px-4 py-2.5 rounded-xl"
                                    style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                                />
                            </div>
                        </div>

                        <p v-if="formData.password && formData.password !== formData.password_confirmation"
                           class="mt-2 text-sm text-red-600">
                            Las contraseñas no coinciden
                        </p>
                    </div>

                    <!-- Estado Activo -->
                    <div class="flex items-center gap-3 p-4 rounded-xl" style="background: #F8FAFC;">
                        <input
                            v-model="formData.activo"
                            type="checkbox"
                            id="activo"
                            class="w-5 h-5 rounded text-cyan-600 focus:ring-cyan-500"
                        />
                        <label for="activo" class="text-sm font-medium" style="color: #455A64;">
                            Usuario activo y con acceso al sistema
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
                            :disabled="!isFormValid"
                            class="px-6 py-2.5 rounded-xl font-medium text-white transition-all active:scale-95"
                            :class="isFormValid ? 'opacity-100' : 'opacity-50 cursor-not-allowed'"
                            style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);"
                        >
                            {{ usuario ? 'Actualizar' : 'Crear' }} Usuario
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
    usuario: {
        type: Object,
        default: null
    },
    roles: {
        type: Array,
        default: () => []
    },
    turnos: {
        type: Array,
        default: () => []
    }
});

const emit = defineEmits(['close', 'submit']);

const formData = reactive({
    nombre_completo: props.usuario?.nombre_completo || '',
    email: props.usuario?.email || '',
    telefono: props.usuario?.telefono || '',
    dni: props.usuario?.dni || '',
    direccion: props.usuario?.direccion || '',
    rol_id: props.usuario?.rol_id || '',
    turno_id: props.usuario?.turno_id || '',
    password: '',
    password_confirmation: '',
    activo: props.usuario?.activo !== undefined ? Boolean(props.usuario.activo) : true
});

const rolSeleccionado = computed(() => {
    if (!formData.rol_id) return null;
    return props.roles.find(r => r.id === parseInt(formData.rol_id));
});

const isFormValid = computed(() => {
    // Validaciones básicas
    if (!formData.nombre_completo || !formData.email || !formData.rol_id) {
        return false;
    }

    // Si es nuevo usuario, verificar contraseñas
    if (!props.usuario) {
        if (!formData.password || formData.password.length < 6) {
            return false;
        }
        if (formData.password !== formData.password_confirmation) {
            return false;
        }
    }

    return true;
});

const handleSubmit = async () => {
    if (!isFormValid.value) {
        alert('Por favor complete todos los campos requeridos correctamente');
        return;
    }

    try {
        const data = {
            nombre_completo: formData.nombre_completo,
            email: formData.email,
            telefono: formData.telefono || null,
            dni: formData.dni || null,
            direccion: formData.direccion || null,
            rol_id: formData.rol_id,
            turno_id: formData.turno_id || null,
            activo: formData.activo ? 1 : 0
        };

        // Solo incluir contraseña si es nuevo usuario o se está cambiando
        if (!props.usuario && formData.password) {
            data.password = formData.password;
            data.password_confirmation = formData.password_confirmation;
        }

        await emit('submit', data);
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
    border-color: #4facfe !important;
    box-shadow: 0 0 0 3px rgba(79, 172, 254, 0.1);
}
</style>
