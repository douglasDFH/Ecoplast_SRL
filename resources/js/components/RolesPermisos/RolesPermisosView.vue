<template>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Gestión de Roles y Permisos</h1>
                <p class="text-gray-600 mt-2">Administra roles y permisos del sistema</p>
            </div>
        </div>

        <!-- Tabs -->
        <div class="bg-white rounded-3xl shadow-lg overflow-hidden">
            <div class="border-b border-gray-200">
                <nav class="flex">
                    <button
                        @click="activeTab = 'roles'"
                        :class="['py-4 px-6 text-sm font-medium border-b-2 transition-colors',
                                activeTab === 'roles'
                                    ? 'border-blue-500 text-blue-600'
                                    : 'border-transparent text-gray-500 hover:text-gray-700']"
                    >
                        Roles
                    </button>
                    <button
                        @click="activeTab = 'permisos'"
                        :class="['py-4 px-6 text-sm font-medium border-b-2 transition-colors',
                                activeTab === 'permisos'
                                    ? 'border-blue-500 text-blue-600'
                                    : 'border-transparent text-gray-500 hover:text-gray-700']"
                    >
                        Permisos
                    </button>
                    <button
                        @click="activeTab = 'asignacion'"
                        :class="['py-4 px-6 text-sm font-medium border-b-2 transition-colors',
                                activeTab === 'asignacion'
                                    ? 'border-blue-500 text-blue-600'
                                    : 'border-transparent text-gray-500 hover:text-gray-700']"
                    >
                        Asignar Permisos
                    </button>
                </nav>
            </div>

            <!-- Roles Tab -->
            <div v-if="activeTab === 'roles'" class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-gray-900">Roles del Sistema</h2>
                    <button
                        @click="abrirModalRol()"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl font-medium transition-colors flex items-center gap-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Nuevo Rol
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div v-for="rol in roles" :key="rol.id"
                         class="bg-gradient-to-br from-blue-50 to-indigo-50 p-4 rounded-xl border border-blue-200">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="font-semibold text-gray-900">{{ rol.name }}</h3>
                            <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                                {{ rol.permissions_count || 0 }} permisos
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mb-3">{{ rol.description || 'Sin descripción' }}</p>
                        <div class="flex gap-2">
                            <button
                                @click="verPermisosRol(rol)"
                                class="text-xs bg-white hover:bg-gray-50 text-gray-700 px-3 py-1 rounded-lg border transition-colors"
                            >
                                Ver Permisos
                            </button>
                            <button
                                @click="eliminarRol(rol)"
                                class="text-xs bg-red-50 hover:bg-red-100 text-red-700 px-3 py-1 rounded-lg border border-red-200 transition-colors"
                            >
                                Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Permisos Tab -->
            <div v-if="activeTab === 'permisos'" class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-gray-900">Permisos del Sistema</h2>
                    <button
                        @click="abrirModalPermiso()"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-xl font-medium transition-colors flex items-center gap-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Nuevo Permiso
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div v-for="permiso in permisos" :key="permiso.id"
                         class="bg-gradient-to-br from-green-50 to-emerald-50 p-4 rounded-xl border border-green-200">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="font-medium text-gray-900 text-sm">{{ permiso.name }}</h3>
                            <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">
                                {{ permiso.roles_count || 0 }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-600">{{ permiso.description || 'Sin descripción' }}</p>
                        <button
                            @click="eliminarPermiso(permiso)"
                            class="mt-2 text-xs bg-red-50 hover:bg-red-100 text-red-700 px-2 py-1 rounded border transition-colors w-full"
                        >
                            Eliminar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Asignación Tab -->
            <div v-if="activeTab === 'asignacion'" class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-gray-900">Asignar Permisos a Roles</h2>
                    <button
                        @click="guardarAsignaciones()"
                        class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-xl font-medium transition-colors flex items-center gap-2"
                        :disabled="!hayCambios"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Guardar Cambios
                    </button>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Roles -->
                    <div>
                        <h3 class="font-medium text-gray-900 mb-3">Seleccionar Rol</h3>
                        <select
                            v-model="rolSeleccionado"
                            class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                            <option value="">Seleccionar rol...</option>
                            <option v-for="rol in roles" :key="rol.id" :value="rol.id">
                                {{ rol.name }}
                            </option>
                        </select>
                    </div>

                    <!-- Permisos -->
                    <div v-if="rolSeleccionado">
                        <h3 class="font-medium text-gray-900 mb-3">
                            Permisos para: {{ roles.find(r => r.id == rolSeleccionado)?.name }}
                        </h3>
                        <div class="max-h-96 overflow-y-auto border border-gray-200 rounded-xl p-4">
                            <div v-for="permiso in permisos" :key="permiso.id" class="flex items-center mb-3 last:mb-0">
                                <input
                                    :id="'permiso-' + permiso.id"
                                    type="checkbox"
                                    :value="permiso.id"
                                    v-model="permisosAsignados"
                                    class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500"
                                />
                                <label :for="'permiso-' + permiso.id" class="ml-3 text-sm text-gray-700">
                                    <span class="font-medium">{{ permiso.name }}</span>
                                    <span v-if="permiso.description" class="text-gray-500 block text-xs">
                                        {{ permiso.description }}
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Rol -->
        <div v-if="mostrarModalRol" class="fixed inset-0 z-50 overflow-y-auto" @click.self="cerrarModalRol">
            <div class="flex min-h-screen items-center justify-center p-4">
                <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md transform transition-all">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">Nuevo Rol</h3>
                    </div>
                    <form @submit.prevent="guardarRol" class="px-6 py-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Rol</label>
                            <input
                                v-model="nuevoRol.name"
                                type="text"
                                required
                                placeholder="Ej: Administrador"
                                class="w-full px-3 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Descripción (opcional)</label>
                            <textarea
                                v-model="nuevoRol.description"
                                rows="3"
                                placeholder="Descripción del rol..."
                                class="w-full px-3 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            ></textarea>
                        </div>
                        <div class="flex gap-3 pt-4">
                            <button
                                type="button"
                                @click="cerrarModalRol"
                                class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-xl font-medium transition-colors"
                            >
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl font-medium transition-colors"
                            >
                                Crear Rol
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Permiso -->
        <div v-if="mostrarModalPermiso" class="fixed inset-0 z-50 overflow-y-auto" @click.self="cerrarModalPermiso">
            <div class="flex min-h-screen items-center justify-center p-4">
                <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md transform transition-all">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">Nuevo Permiso</h3>
                    </div>
                    <form @submit.prevent="guardarPermiso" class="px-6 py-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Permiso</label>
                            <input
                                v-model="nuevoPermiso.name"
                                type="text"
                                required
                                placeholder="Ej: crear usuarios"
                                class="w-full px-3 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Descripción (opcional)</label>
                            <textarea
                                v-model="nuevoPermiso.description"
                                rows="3"
                                placeholder="Descripción del permiso..."
                                class="w-full px-3 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            ></textarea>
                        </div>
                        <div class="flex gap-3 pt-4">
                            <button
                                type="button"
                                @click="cerrarModalPermiso"
                                class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-xl font-medium transition-colors"
                            >
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-xl font-medium transition-colors"
                            >
                                Crear Permiso
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Ver Permisos -->
        <div v-if="mostrarModalPermisos" class="fixed inset-0 z-50 overflow-y-auto" @click.self="cerrarModalPermisos">
            <div class="flex min-h-screen items-center justify-center p-4">
                <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md transform transition-all">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">Permisos de {{ rolSeleccionadoPermisos?.name }}</h3>
                    </div>
                    <div class="px-6 py-4 max-h-96 overflow-y-auto">
                        <div v-if="rolSeleccionadoPermisos?.permissions?.length === 0" class="text-center text-gray-500 py-8">
                            Este rol no tiene permisos asignados
                        </div>
                        <div v-else class="space-y-2">
                            <div v-for="permiso in rolSeleccionadoPermisos.permissions" :key="permiso.id"
                                 class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <span class="font-medium text-gray-900">{{ permiso.name }}</span>
                                    <p v-if="permiso.description" class="text-sm text-gray-600">{{ permiso.description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-4 border-t border-gray-100">
                        <button
                            @click="cerrarModalPermisos"
                            class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-xl font-medium transition-colors"
                        >
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import api from '../../services/api';

// Reactive data
const activeTab = ref('roles');
const roles = ref([]);
const permisos = ref([]);
const loading = ref(false);

// Modal states
const mostrarModalRol = ref(false);
const mostrarModalPermiso = ref(false);
const mostrarModalPermisos = ref(false);

// Form data
const nuevoRol = ref({ name: '', description: '' });
const nuevoPermiso = ref({ name: '', description: '' });

// Assignment data
const rolSeleccionado = ref('');
const permisosAsignados = ref([]);
const permisosOriginales = ref([]);
const hayCambios = ref(false);

// Selected role for permissions view
const rolSeleccionadoPermisos = ref(null);

// Methods
const loadRoles = async () => {
    try {
        const response = await api.get('/spatie/roles');
        roles.value = response.data.data || [];
    } catch (error) {
        console.error('Error al cargar roles:', error);
        roles.value = [];
    }
};

const loadPermisos = async () => {
    try {
        const response = await api.get('/spatie/permisos');
        permisos.value = response.data.data || [];
    } catch (error) {
        console.error('Error al cargar permisos:', error);
        permisos.value = [];
    }
};

const abrirModalRol = () => {
    nuevoRol.value = { name: '', description: '' };
    mostrarModalRol.value = true;
};

const cerrarModalRol = () => {
    mostrarModalRol.value = false;
};

const guardarRol = async () => {
    try {
        await api.post('/spatie/roles', nuevoRol.value);
        await loadRoles();
        cerrarModalRol();
        alert('Rol creado exitosamente');
    } catch (error) {
        console.error('Error al crear rol:', error);
        alert('Error al crear el rol');
    }
};

const eliminarRol = async (rol) => {
    if (!confirm(`¿Está seguro de eliminar el rol "${rol.name}"?`)) {
        return;
    }
    try {
        await api.delete(`/spatie/roles/${rol.id}`);
        await loadRoles();
        alert('Rol eliminado exitosamente');
    } catch (error) {
        console.error('Error al eliminar rol:', error);
        alert('Error al eliminar el rol');
    }
};

const abrirModalPermiso = () => {
    nuevoPermiso.value = { name: '', description: '' };
    mostrarModalPermiso.value = true;
};

const cerrarModalPermiso = () => {
    mostrarModalPermiso.value = false;
};

const guardarPermiso = async () => {
    try {
        await api.post('/spatie/permisos', nuevoPermiso.value);
        await loadPermisos();
        cerrarModalPermiso();
        alert('Permiso creado exitosamente');
    } catch (error) {
        console.error('Error al crear permiso:', error);
        alert('Error al crear el permiso');
    }
};

const eliminarPermiso = async (permiso) => {
    if (!confirm(`¿Está seguro de eliminar el permiso "${permiso.name}"?`)) {
        return;
    }
    try {
        await api.delete(`/spatie/permisos/${permiso.id}`);
        await loadPermisos();
        alert('Permiso eliminado exitosamente');
    } catch (error) {
        console.error('Error al eliminar permiso:', error);
        alert('Error al eliminar el permiso');
    }
};

const verPermisosRol = async (rol) => {
    try {
        const response = await api.get(`/spatie/roles/${rol.id}/permisos`);
        rolSeleccionadoPermisos.value = { ...rol, permissions: response.data.data || [] };
        mostrarModalPermisos.value = true;
    } catch (error) {
        console.error('Error al cargar permisos del rol:', error);
        alert('Error al cargar los permisos del rol');
    }
};

const cerrarModalPermisos = () => {
    mostrarModalPermisos.value = false;
    rolSeleccionadoPermisos.value = null;
};

const guardarAsignaciones = async () => {
    if (!rolSeleccionado.value) return;

    try {
        await api.post(`/spatie/roles/${rolSeleccionado.value}/permisos`, {
            permissions: permisosAsignados.value
        });
        hayCambios.value = false;
        permisosOriginales.value = [...permisosAsignados.value];
        await loadRoles();
        alert('Permisos asignados exitosamente');
    } catch (error) {
        console.error('Error al asignar permisos:', error);
        alert('Error al asignar los permisos');
    }
};

// Watch for changes in role selection
watch(rolSeleccionado, async (newRolId) => {
    if (newRolId) {
        try {
            const response = await api.get(`/spatie/roles/${newRolId}/permisos`);
            const permisosRol = response.data.data || [];
            permisosAsignados.value = permisosRol.map(p => p.id);
            permisosOriginales.value = [...permisosAsignados.value];
            hayCambios.value = false;
        } catch (error) {
            console.error('Error al cargar permisos del rol:', error);
            permisosAsignados.value = [];
            permisosOriginales.value = [];
        }
    } else {
        permisosAsignados.value = [];
        permisosOriginales.value = [];
        hayCambios.value = false;
    }
});

// Watch for changes in assigned permissions
watch(permisosAsignados, (newPermisos) => {
    const originales = permisosOriginales.value.sort();
    const actuales = newPermisos.sort();
    hayCambios.value = JSON.stringify(originales) !== JSON.stringify(actuales);
}, { deep: true });

// Lifecycle
onMounted(() => {
    loadRoles();
    loadPermisos();
});
</script>

<style scoped>
/* Additional styles if needed */
</style>