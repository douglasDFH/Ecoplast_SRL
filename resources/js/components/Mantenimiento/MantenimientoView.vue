<template>
    <div class="min-h-screen p-8" style="background: linear-gradient(145deg, #e3f2fd, #bbdefb);">
        <!-- Header -->
        <div class="mb-8 rounded-3xl p-6 shadow-neumorphic" style="background: linear-gradient(145deg, #e3f2fd, #bbdefb);">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold mb-2" style="color: #263238;">Mantenimiento Preventivo</h1>
                    <p class="text-lg" style="color: #607D8B;">Calendario y gestión de mantenimientos</p>
                </div>
                <div class="flex items-center space-x-4">
                    <button
                        @click="abrirModalMantenimiento"
                        class="px-6 py-3 rounded-2xl font-semibold transition-all hover-button"
                        style="background: linear-gradient(145deg, #2196F3, #1976D2); box-shadow: 12px 12px 24px #1565c0, -12px -12px 24px #42a5f5; color: white;"
                    >
                        ➕ Programar Mantenimiento
                    </button>
                    <button
                        @click="cargarMantenimientos"
                        class="p-4 rounded-2xl transition-all hover-button"
                        style="background: linear-gradient(145deg, #4CAF50, #388E3C); box-shadow: 12px 12px 24px #2e7031, -12px -12px 24px #5ec962; color: white;"
                        title="Actualizar"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Calendario de Mantenimientos (placeholder) -->
        <div class="mb-8 rounded-3xl p-6 shadow-neumorphic" style="background: linear-gradient(145deg, #e3f2fd, #bbdefb);">
            <h2 class="text-2xl font-bold mb-4" style="color: #263238;">Calendario de Mantenimientos</h2>
            <div class="w-full h-96 flex items-center justify-center text-2xl text-blue-400 bg-blue-50 rounded-2xl">
                (Aquí se integraría FullCalendar o similar para visualizar mantenimientos)
            </div>
        </div>

        <!-- Historial de Mantenimientos -->
        <div class="rounded-3xl p-6 shadow-neumorphic mb-8" style="background: linear-gradient(145deg, #e3f2fd, #bbdefb);">
            <h2 class="text-2xl font-bold mb-4" style="color: #263238;">Historial por Máquina</h2>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr style="background: linear-gradient(145deg, #d0e8f7, #b3d4f1);">
                            <th class="px-6 py-4 text-left text-sm font-bold" style="color: #263238;">Máquina</th>
                            <th class="px-6 py-4 text-left text-sm font-bold" style="color: #263238;">Fecha</th>
                            <th class="px-6 py-4 text-left text-sm font-bold" style="color: #263238;">Tipo</th>
                            <th class="px-6 py-4 text-left text-sm font-bold" style="color: #263238;">Responsable</th>
                            <th class="px-6 py-4 text-left text-sm font-bold" style="color: #263238;">Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="loading">
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex items-center justify-center">
                                    <div class="animate-spin rounded-full h-12 w-12 border-b-2" style="border-color: #2196F3;"></div>
                                    <span class="ml-3 text-lg" style="color: #607D8B;">Cargando historial...</span>
                                </div>
                            </td>
                        </tr>
                        <tr v-else-if="mantenimientos.length === 0">
                            <td colspan="5" class="px-6 py-12 text-center" style="color: #607D8B;">
                                <span class="text-5xl mb-4 block">🛠️</span>
                                <p class="text-lg">No hay mantenimientos registrados</p>
                            </td>
                        </tr>
                        <tr v-else v-for="m in mantenimientos" :key="m.id"
                            class="border-b border-blue-100 hover:bg-gradient-to-r hover:from-blue-50 hover:to-blue-100 transition-all"
                            style="background: linear-gradient(145deg, #f5f9fc, #e3f2fd);">
                            <td class="px-6 py-4">
                                <span class="font-semibold" style="color: #263238;">{{ m.maquina?.nombre_maquina || 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span style="color: #607D8B;">{{ formatearFecha(m.fecha_mantenimiento) }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold" :class="getTipoClase(m.tipo)">
                                    {{ getTipoTexto(m.tipo) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span style="color: #263238;">{{ m.responsable || 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span style="color: #607D8B;">{{ m.observaciones }}</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Programar Mantenimiento -->
        <div v-if="modalMantenimientoAbierto" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="rounded-3xl shadow-neumorphic max-w-2xl w-full max-h-[90vh] overflow-y-auto" style="background: linear-gradient(145deg, #e3f2fd, #bbdefb);">
                <div class="flex items-center justify-between p-6 border-b border-blue-200">
                    <h2 class="text-2xl font-bold" style="color: #263238;">Programar Mantenimiento</h2>
                    <button @click="cerrarModalMantenimiento" class="hover-button rounded-xl px-3 py-2 transition-all" style="color: #607D8B;">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form @submit.prevent="guardarMantenimiento" class="p-6 space-y-6">
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: #455A64;">Máquina <span class="text-red-500">*</span></label>
                        <select v-model="formulario.maquina_id" required class="w-full px-4 py-3 rounded-2xl focus:outline-none" style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); box-shadow: inset 8px 8px 16px #d0dfe8, inset -8px -8px 16px #ffffff; color: #263238;">
                            <option value="">Seleccionar máquina...</option>
                            <option v-for="maquina in maquinas" :key="maquina.id" :value="maquina.id">{{ maquina.nombre_maquina }} ({{ maquina.codigo_maquina }})</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: #455A64;">Fecha <span class="text-red-500">*</span></label>
                        <input type="date" v-model="formulario.fecha_mantenimiento" required class="w-full px-4 py-3 rounded-2xl focus:outline-none" style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); box-shadow: inset 8px 8px 16px #d0dfe8, inset -8px -8px 16px #ffffff; color: #263238;" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: #455A64;">Tipo <span class="text-red-500">*</span></label>
                        <select v-model="formulario.tipo" required class="w-full px-4 py-3 rounded-2xl focus:outline-none" style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); box-shadow: inset 8px 8px 16px #d0dfe8, inset -8px -8px 16px #ffffff; color: #263238;">
                            <option value="">Seleccionar tipo...</option>
                            <option value="preventivo">Preventivo</option>
                            <option value="correctivo">Correctivo</option>
                            <option value="calibracion">Calibración</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: #455A64;">Responsable</label>
                        <input type="text" v-model="formulario.responsable" class="w-full px-4 py-3 rounded-2xl focus:outline-none" style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); box-shadow: inset 8px 8px 16px #d0dfe8, inset -8px -8px 16px #ffffff; color: #263238;" placeholder="Nombre del responsable" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: #455A64;">Observaciones</label>
                        <textarea v-model="formulario.observaciones" rows="3" class="w-full px-4 py-3 rounded-2xl focus:outline-none" style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); box-shadow: inset 8px 8px 16px #d0dfe8, inset -8px -8px 16px #ffffff; color: #263238;" placeholder="Detalles adicionales..."></textarea>
                    </div>
                    <div class="flex items-center justify-end space-x-4 pt-4 border-t border-blue-200">
                        <button type="button" @click="cerrarModalMantenimiento" class="px-6 py-3 rounded-2xl font-semibold transition-all hover-button" style="background: linear-gradient(145deg, #607D8B, #455A64); box-shadow: 12px 12px 24px #4a5a63, -12px -12px 24px #6e8495; color: white;">Cancelar</button>
                        <button type="submit" :disabled="submitting" class="px-6 py-3 rounded-2xl font-semibold transition-all disabled:opacity-50 disabled:cursor-not-allowed" style="background: linear-gradient(145deg, #2196F3, #1976D2); box-shadow: 12px 12px 24px #1565c0, -12px -12px 24px #42a5f5; color: white;">
                            <span v-if="submitting">Guardando...</span>
                            <span v-else>💾 Guardar</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '@/services/api.js';

const loading = ref(false);
const submitting = ref(false);
const mantenimientos = ref([]);
const maquinas = ref([]);
const modalMantenimientoAbierto = ref(false);

const formulario = ref({
    maquina_id: '',
    fecha_mantenimiento: '',
    tipo: '',
    responsable: '',
    observaciones: ''
});

const cargarMantenimientos = async () => {
    loading.value = true;
    try {
        const response = await api.get('/mantenimientos', { params: { incluir: 'maquina' } });
        mantenimientos.value = response.data.data || response.data;
    } catch (error) {
        console.error('Error al cargar mantenimientos:', error);
    } finally {
        loading.value = false;
    }
};

const cargarMaquinas = async () => {
    try {
        const response = await api.get('/maquinas', { params: { activo: true } });
        maquinas.value = response.data.data || response.data;
    } catch (error) {
        console.error('Error al cargar máquinas:', error);
    }
};

const abrirModalMantenimiento = () => {
    formulario.value = {
        maquina_id: '',
        fecha_mantenimiento: '',
        tipo: '',
        responsable: '',
        observaciones: ''
    };
    modalMantenimientoAbierto.value = true;
    cargarMaquinas();
};

const cerrarModalMantenimiento = () => {
    modalMantenimientoAbierto.value = false;
};

const guardarMantenimiento = async () => {
    if (!formulario.value.maquina_id || !formulario.value.fecha_mantenimiento || !formulario.value.tipo) {
        alert('Debe completar los campos obligatorios');
        return;
    }
    submitting.value = true;
    try {
        await api.post('/mantenimientos', formulario.value);
        alert('Mantenimiento programado exitosamente');
        cerrarModalMantenimiento();
        await cargarMantenimientos();
    } catch (error) {
        console.error('Error al guardar mantenimiento:', error);
        alert('Error al guardar el mantenimiento');
    } finally {
        submitting.value = false;
    }
};

const getTipoClase = (tipo) => {
    const clases = {
        'preventivo': 'bg-blue-100 text-blue-800',
        'correctivo': 'bg-orange-100 text-orange-800',
        'calibracion': 'bg-green-100 text-green-800'
    };
    return clases[tipo] || 'bg-gray-100 text-gray-800';
};

const getTipoTexto = (tipo) => {
    const textos = {
        'preventivo': 'Preventivo',
        'correctivo': 'Correctivo',
        'calibracion': 'Calibración'
    };
    return textos[tipo] || tipo;
};

const formatearFecha = (fecha) => {
    if (!fecha) return 'N/A';
    return new Date(fecha).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

onMounted(() => {
    cargarMantenimientos();
});
</script>

<style scoped>
.shadow-neumorphic {
    box-shadow: 15px 15px 30px #b3d4f1, -15px -15px 30px #f3ffff;
}

.hover-button:hover {
    box-shadow: 7px 7px 14px #4a5a63, -7px -7px 14px #6e8495;
}
</style>
