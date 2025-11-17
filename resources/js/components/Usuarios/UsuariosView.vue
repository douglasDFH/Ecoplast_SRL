<template>
    <div class="space-y-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="gradient-card p-6 rounded-3xl shadow-lg" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/80 text-sm mb-1">Total Usuarios</p>
                        <p class="text-white text-3xl font-bold">{{ usuarios.length }}</p>
                    </div>
                    <div class="w-14 h-14 bg-white/20 backdrop-blur-lg rounded-2xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="gradient-card p-6 rounded-3xl shadow-lg" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/80 text-sm mb-1">Activos</p>
                        <p class="text-white text-3xl font-bold">{{ usuariosActivos }}</p>
                    </div>
                    <div class="w-14 h-14 bg-white/20 backdrop-blur-lg rounded-2xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="gradient-card p-6 rounded-3xl shadow-lg" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/80 text-sm mb-1">Administradores</p>
                        <p class="text-white text-3xl font-bold">{{ usuariosAdmin }}</p>
                    </div>
                    <div class="w-14 h-14 bg-white/20 backdrop-blur-lg rounded-2xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="gradient-card p-6 rounded-3xl shadow-lg" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/80 text-sm mb-1">Operadores</p>
                        <p class="text-white text-3xl font-bold">{{ usuariosOperadores }}</p>
                    </div>
                    <div class="w-14 h-14 bg-white/20 backdrop-blur-lg rounded-2xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Actions -->
        <div class="modern-card p-6 rounded-3xl">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <input
                        v-model="filtros.busqueda"
                        type="text"
                        placeholder="Buscar por nombre o email..."
                        class="modern-input px-4 py-2.5 rounded-xl"
                        style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                    />
                    <select
                        v-model="filtros.rol_id"
                        class="modern-input px-4 py-2.5 rounded-xl"
                        style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                    >
                        <option value="">Todos los roles</option>
                        <option v-for="rol in roles" :key="rol.id" :value="rol.id">
                            {{ rol.nombre_rol }}
                        </option>
                    </select>
                    <select
                        v-model="filtros.activo"
                        class="modern-input px-4 py-2.5 rounded-xl"
                        style="background: #F8FAFC; border: 2px solid #E2E8F0;"
                    >
                        <option value="">Todos los estados</option>
                        <option value="1">Activos</option>
                        <option value="0">Inactivos</option>
                    </select>
                </div>
                <div class="flex gap-3">
                    <button
                        @click="limpiarFiltros"
                        class="modern-btn-secondary px-6 py-2.5 rounded-xl font-medium transition-all active:scale-95"
                        style="background: #F1F5F9; color: #475569;"
                    >
                        Limpiar
                    </button>
                    <button
                        @click="abrirModal()"
                        class="modern-btn-primary px-6 py-2.5 rounded-xl font-medium transition-all active:scale-95 flex items-center gap-2"
                        style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Nuevo Usuario
                    </button>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="modern-card rounded-3xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white">Usuario</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white">Email</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white">Rol</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-white">Teléfono</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-white">Último Acceso</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-white">Estado</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-white">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-if="loading" class="hover:bg-gray-50/50 transition-colors">
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex items-center justify-center gap-3">
                                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-cyan-600"></div>
                                    <span>Cargando usuarios...</span>
                                </div>
                            </td>
                        </tr>
                        <tr v-else-if="usuariosFiltrados.length === 0" class="hover:bg-gray-50/50 transition-colors">
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                No se encontraron usuarios
                            </td>
                        </tr>
                        <tr v-else v-for="usuario in usuariosFiltrados" :key="usuario.id" class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-white"
                                        :style="getAvatarStyle(usuario.nombre_completo)">
                                        {{ getInitials(usuario.nombre_completo) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ usuario.nombre_completo }}</p>
                                        <p class="text-xs text-gray-500">ID: {{ usuario.id }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-sm text-gray-700">{{ usuario.email }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                                    :style="getRolBadgeStyle(usuario.rol?.nombre_rol)">
                                    {{ usuario.rol?.nombre_rol || 'Sin rol' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span v-if="usuario.telefono" class="text-sm text-gray-700">{{ usuario.telefono }}</span>
                                <span v-else class="text-xs text-gray-400">-</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span v-if="usuario.ultimo_acceso" class="text-sm text-gray-700">
                                    {{ formatFecha(usuario.ultimo_acceso) }}
                                </span>
                                <span v-else class="text-xs text-gray-400">Nunca</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                                    :class="usuario.activo ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'">
                                    {{ usuario.activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button
                                        @click="abrirModal(usuario)"
                                        class="p-2 rounded-lg transition-all hover:bg-blue-50 active:scale-95"
                                        title="Editar"
                                    >
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <button
                                        @click="toggleActivo(usuario)"
                                        class="p-2 rounded-lg transition-all hover:bg-yellow-50 active:scale-95"
                                        :title="usuario.activo ? 'Desactivar' : 'Activar'"
                                    >
                                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                    </button>
                                    <button
                                        @click="resetearPassword(usuario)"
                                        class="p-2 rounded-lg transition-all hover:bg-purple-50 active:scale-95"
                                        title="Resetear contraseña"
                                    >
                                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                        </svg>
                                    </button>
                                    <button
                                        @click="eliminarUsuario(usuario)"
                                        class="p-2 rounded-lg transition-all hover:bg-red-50 active:scale-95"
                                        title="Eliminar"
                                    >
                                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal -->
        <ModalUsuario
            v-if="mostrarModal"
            :usuario="usuarioSeleccionado"
            :roles="roles"
            :turnos="turnos"
            @close="cerrarModal"
            @submit="guardarUsuario"
        />
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import api from '../../services/api';
import ModalUsuario from './ModalUsuario.vue';

const usuarios = ref([]);
const roles = ref([]);
const turnos = ref([]);
const loading = ref(false);
const mostrarModal = ref(false);
const usuarioSeleccionado = ref(null);

const filtros = ref({
    busqueda: '',
    rol_id: '',
    activo: ''
});

// Computed properties
const usuariosFiltrados = computed(() => {
    return usuarios.value.filter(usuario => {
        const matchBusqueda = !filtros.value.busqueda ||
            usuario.nombre_completo?.toLowerCase().includes(filtros.value.busqueda.toLowerCase()) ||
            usuario.email?.toLowerCase().includes(filtros.value.busqueda.toLowerCase());

        const matchRol = !filtros.value.rol_id ||
            usuario.rol_id === parseInt(filtros.value.rol_id);

        const matchActivo = filtros.value.activo === '' ||
            usuario.activo === parseInt(filtros.value.activo);

        return matchBusqueda && matchRol && matchActivo;
    });
});

const usuariosActivos = computed(() =>
    usuarios.value.filter(u => u.activo).length
);

const usuariosAdmin = computed(() =>
    usuarios.value.filter(u => u.rol?.nombre_rol === 'Administrador').length
);

const usuariosOperadores = computed(() =>
    usuarios.value.filter(u => u.rol?.nombre_rol === 'Operador de Producción').length
);

// Methods
const loadUsuarios = async () => {
    loading.value = true;
    try {
        const response = await api.get('/usuarios');
        // Asegurarse de que tenemos la estructura completa de usuarios
        const usuariosData = response.data.data || response.data || [];

        // Si necesitamos cargar información adicional de cada usuario
        usuarios.value = usuariosData;
    } catch (error) {
        console.error('Error al cargar usuarios:', error);
        usuarios.value = [];
    } finally {
        loading.value = false;
    }
};

const loadRoles = async () => {
    try {
        const response = await api.get('/roles');
        roles.value = response.data.data || response.data || [];
    } catch (error) {
        console.error('Error al cargar roles:', error);
        roles.value = [];
    }
};

const loadTurnos = async () => {
    try {
        const response = await api.get('/turnos');
        turnos.value = response.data.data || response.data || [];
    } catch (error) {
        console.error('Error al cargar turnos:', error);
        turnos.value = [];
    }
};

const abrirModal = (usuario = null) => {
    usuarioSeleccionado.value = usuario;
    mostrarModal.value = true;
};

const cerrarModal = () => {
    mostrarModal.value = false;
    usuarioSeleccionado.value = null;
};

const guardarUsuario = async (data) => {
    try {
        if (usuarioSeleccionado.value) {
            await api.put(`/usuarios/${usuarioSeleccionado.value.id}`, data);
        } else {
            await api.post('/usuarios', data);
        }
        await loadUsuarios();
        cerrarModal();
    } catch (error) {
        console.error('Error al guardar usuario:', error);
        throw error;
    }
};

const toggleActivo = async (usuario) => {
    if (!confirm(`¿Está seguro de ${usuario.activo ? 'desactivar' : 'activar'} este usuario?`)) {
        return;
    }
    try {
        await api.put(`/usuarios/${usuario.id}`, {
            ...usuario,
            activo: !usuario.activo
        });
        await loadUsuarios();
    } catch (error) {
        console.error('Error al cambiar estado:', error);
        alert('Error al cambiar el estado del usuario');
    }
};

const resetearPassword = async (usuario) => {
    const nuevaPassword = prompt(`Ingrese la nueva contraseña para ${usuario.nombre_completo}:`);
    if (!nuevaPassword) return;

    if (nuevaPassword.length < 6) {
        alert('La contraseña debe tener al menos 6 caracteres');
        return;
    }

    try {
        await api.post(`/usuarios/${usuario.id}/reset-password`, {
            password: nuevaPassword
        });
        alert('Contraseña actualizada correctamente');
    } catch (error) {
        console.error('Error al resetear contraseña:', error);
        alert('Error al resetear la contraseña');
    }
};

const eliminarUsuario = async (usuario) => {
    if (!confirm(`¿Está seguro de eliminar al usuario "${usuario.nombre_completo}"? Esta acción no se puede deshacer.`)) {
        return;
    }
    try {
        await api.delete(`/usuarios/${usuario.id}`);
        await loadUsuarios();
    } catch (error) {
        console.error('Error al eliminar usuario:', error);
        alert('Error al eliminar el usuario');
    }
};

const limpiarFiltros = () => {
    filtros.value = {
        busqueda: '',
        rol_id: '',
        activo: ''
    };
};

const getInitials = (nombre) => {
    if (!nombre) return '?';
    const parts = nombre.split(' ');
    if (parts.length >= 2) {
        return (parts[0][0] + parts[1][0]).toUpperCase();
    }
    return nombre.substring(0, 2).toUpperCase();
};

const getAvatarStyle = (nombre) => {
    const colors = [
        'background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);',
        'background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);',
        'background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);',
        'background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);',
        'background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);'
    ];
    const hash = nombre.split('').reduce((acc, char) => acc + char.charCodeAt(0), 0);
    return colors[hash % colors.length];
};

const getRolBadgeStyle = (rol) => {
    const styles = {
        'Administrador': 'background: #DBEAFE; color: #1E40AF;',
        'Supervisor de Producción': 'background: #D1FAE5; color: #065F46;',
        'Operador de Producción': 'background: #FEF3C7; color: #92400E;',
        'Control de Calidad': 'background: #FCE7F3; color: #9F1239;',
        'Logística e Inventario': 'background: #E0E7FF; color: #3730A3;'
    };
    return styles[rol] || 'background: #F3F4F6; color: #374151;';
};

const formatFecha = (fecha) => {
    if (!fecha) return 'Nunca';
    return new Date(fecha).toLocaleDateString('es-PE', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

// Lifecycle
onMounted(() => {
    loadUsuarios();
    loadRoles();
    loadTurnos();
});
</script>

<style scoped>
.modern-card {
    background: white;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
}

.gradient-card {
    transition: transform 0.2s, box-shadow 0.2s;
}

.gradient-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.modern-input {
    transition: all 0.2s;
}

.modern-input:focus {
    outline: none;
    border-color: #4facfe;
    box-shadow: 0 0 0 3px rgba(79, 172, 254, 0.1);
}

.modern-btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

.modern-btn-secondary:hover {
    background: #E2E8F0;
}
</style>
