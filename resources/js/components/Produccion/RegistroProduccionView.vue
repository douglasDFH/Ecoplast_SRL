<template>
    <div class="registro-produccion-view">
        <!-- Header Simplificado para Operadores -->
        <div class="mb-6 p-6 rounded-3xl" style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); box-shadow: 12px 12px 24px #b3d4f1, -12px -12px 24px #f3ffff;">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold" style="color: #263238;">Registro de Producción</h1>
                    <p class="text-lg mt-2 font-semibold" style="color: #607D8B;">{{ turnoActual }} - {{ operadorNombre }}</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <div class="text-sm font-bold" style="color: #607D8B;">Hora Actual</div>
                        <div class="text-3xl font-bold" style="color: #263238;">{{ horaActual }}</div>
                    </div>
                    <!-- Botón de Emergencia -->
                    <button
                        @click="reportarEmergencia"
                        class="px-8 py-6 text-white rounded-3xl font-black text-2xl transition-all active:scale-95 animate-pulse"
                        style="background: linear-gradient(145deg, #c62828, #d32f2f); box-shadow: 12px 12px 24px #b3d4f1, -12px -12px 24px #f3ffff;"
                        @mouseover="e => e.target.style.boxShadow='8px 8px 16px #b3d4f1, -8px -8px 16px #f3ffff'"
                        @mouseout="e => e.target.style.boxShadow='12px 12px 24px #b3d4f1, -12px -12px 24px #f3ffff'"
                    >
                        🚨 EMERGENCIA
                    </button>
                </div>
            </div>
        </div>

        <!-- Selección de Orden Activa -->
        <div class="mb-6 p-6 rounded-3xl" style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); box-shadow: 12px 12px 24px #b3d4f1, -12px -12px 24px #f3ffff;">
            <label class="block text-2xl font-bold mb-4" style="color: #455A64;">Orden de Producción Activa</label>
            <select
                v-model="ordenSeleccionada"
                @change="cargarOrden"
                class="w-full px-6 py-6 text-2xl font-bold border-0 rounded-2xl focus:ring-4 focus:ring-blue-500"
                style="background: linear-gradient(145deg, #f5f9fc, #e3f2fd); box-shadow: inset 8px 8px 16px #d0dfe8, inset -8px -8px 16px #ffffff; color: #263238;"
            >
                <option value="">Seleccionar orden...</option>
                <option v-for="orden in ordenesActivas" :key="orden.id" :value="orden.id">
                    {{ orden.codigo_orden }} - {{ orden.producto?.nombre }} ({{ orden.cantidad_producida }}/{{ orden.cantidad_requerida }})
                </option>
            </select>
        </div>

        <!-- Información de la Orden Actual -->
        <div v-if="ordenActual" class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <div class="p-6 rounded-3xl" style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); box-shadow: 12px 12px 24px #b3d4f1, -12px -12px 24px #f3ffff;">
                <div class="text-xl font-bold mb-2" style="color: #607D8B;">Producto</div>
                <div class="text-3xl font-black" style="color: #263238;">{{ ordenActual.producto?.nombre }}</div>
                <div class="text-lg font-semibold mt-2" style="color: #607D8B;">{{ ordenActual.producto?.codigo }}</div>
            </div>
            
            <div class="p-6 rounded-3xl" style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); box-shadow: 12px 12px 24px #b3d4f1, -12px -12px 24px #f3ffff;">
                <div class="text-xl font-bold mb-2" style="color: #607D8B;">Meta de Producción</div>
                <div class="text-5xl font-black" style="color: #263238;">{{ ordenActual.cantidad_requerida }}</div>
                <div class="text-lg font-semibold mt-2" style="color: #607D8B;">unidades</div>
            </div>
            
            <div class="p-6 rounded-3xl" style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); box-shadow: 12px 12px 24px #b3d4f1, -12px -12px 24px #f3ffff;">
                <div class="text-xl font-bold mb-2" style="color: #607D8B;">Progreso</div>
                <div class="text-5xl font-black" style="color: #2E7D32;">{{ ordenActual.cantidad_producida || 0 }}</div>
                <div class="w-full rounded-full h-4 mt-4" style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); box-shadow: inset 5px 5px 10px #b3d4f1, inset -5px -5px 10px #f3ffff;">
                    <div
                        class="h-4 rounded-full bg-gradient-to-r from-green-500 to-green-600 transition-all"
                        :style="{ width: progreso + '%' }"
                    ></div>
                </div>
                <div class="text-lg font-bold mt-2 text-center" style="color: #455A64;">{{ progreso }}%</div>
            </div>
        </div>

        <!-- Formulario de Registro por Hora -->
        <div v-if="ordenActual" class="p-8 rounded-3xl mb-6" style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); box-shadow: 12px 12px 24px #b3d4f1, -12px -12px 24px #f3ffff;">
            <h2 class="text-3xl font-black mb-6" style="color: #263238;">Registrar Producción - Hora {{ horaActual.split(':')[0] }}:00</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Cantidad Producida -->
                <div>
                    <label class="block text-2xl font-bold mb-4" style="color: #455A64;">Unidades Producidas</label>
                    <div class="flex items-center space-x-4">
                        <button
                            @click="decrementar('producidas')"
                            class="w-20 h-20 text-4xl font-black text-white rounded-2xl transition-all active:scale-95"
                            style="background: linear-gradient(145deg, #EF5350, #E53935); box-shadow: 8px 8px 16px #b3d4f1, -8px -8px 16px #f3ffff;"
                        >
                            -
                        </button>
                        <input
                            type="number"
                            v-model.number="registro.cantidad_producida"
                            min="0"
                            class="flex-1 text-center text-5xl font-black border-0 rounded-2xl focus:ring-4 focus:ring-green-500"
                            style="background: linear-gradient(145deg, #f5f9fc, #e3f2fd); box-shadow: inset 8px 8px 16px #d0dfe8, inset -8px -8px 16px #ffffff; color: #2E7D32;"
                        />
                        <button
                            @click="incrementar('producidas')"
                            class="w-20 h-20 text-4xl font-black text-white rounded-2xl transition-all active:scale-95"
                            style="background: linear-gradient(145deg, #66BB6A, #4CAF50); box-shadow: 8px 8px 16px #b3d4f1, -8px -8px 16px #f3ffff;"
                        >
                            +
                        </button>
                    </div>
                </div>

                <!-- Cantidad Defectuosa -->
                <div>
                    <label class="block text-2xl font-bold mb-4" style="color: #455A64;">Unidades Defectuosas</label>
                    <div class="flex items-center space-x-4">
                        <button
                            @click="decrementar('defectuosas')"
                            class="w-20 h-20 text-4xl font-black text-white rounded-2xl transition-all active:scale-95"
                            style="background: linear-gradient(145deg, #78909C, #607D8B); box-shadow: 8px 8px 16px #b3d4f1, -8px -8px 16px #f3ffff;"
                        >
                            -
                        </button>
                        <input
                            type="number"
                            v-model.number="registro.cantidad_defectuosa"
                            min="0"
                            class="flex-1 text-center text-5xl font-black border-0 rounded-2xl focus:ring-4 focus:ring-red-500"
                            style="background: linear-gradient(145deg, #f5f9fc, #e3f2fd); box-shadow: inset 8px 8px 16px #d0dfe8, inset -8px -8px 16px #ffffff; color: #D32F2F;"
                        />
                        <button
                            @click="incrementar('defectuosas')"
                            class="w-20 h-20 text-4xl font-black text-white rounded-2xl transition-all active:scale-95"
                            style="background: linear-gradient(145deg, #FFA726, #FF9800); box-shadow: 8px 8px 16px #b3d4f1, -8px -8px 16px #f3ffff;"
                        >
                            +
                        </button>
                    </div>
                </div>
            </div>

            <!-- Observaciones -->
            <div class="mt-8">
                <label class="block text-2xl font-bold mb-4" style="color: #455A64;">Observaciones (Opcional)</label>
                <textarea
                    v-model="registro.observaciones"
                    rows="3"
                    placeholder="Escribe cualquier observación..."
                    class="w-full px-6 py-4 text-xl border-0 rounded-2xl focus:ring-4 focus:ring-blue-500"
                    style="background: linear-gradient(145deg, #f5f9fc, #e3f2fd); box-shadow: inset 8px 8px 16px #d0dfe8, inset -8px -8px 16px #ffffff; color: #263238;"
                ></textarea>
            </div>

            <!-- Botones de Acción Grandes -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
                <button
                    @click="guardarRegistro"
                    :disabled="!puedeGuardar"
                    class="py-8 text-white rounded-3xl font-black text-3xl transition-all active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed"
                    style="background: linear-gradient(145deg, #2E7D32, #4CAF50); box-shadow: 12px 12px 24px #b3d4f1, -12px -12px 24px #f3ffff;"
                    @mouseover="e => !e.target.disabled && (e.target.style.boxShadow='8px 8px 16px #b3d4f1, -8px -8px 16px #f3ffff')"
                    @mouseout="e => e.target.style.boxShadow='12px 12px 24px #b3d4f1, -12px -12px 24px #f3ffff'"
                >
                    ✓ GUARDAR REGISTRO
                </button>
                
                <button
                    @click="reportarParo"
                    class="py-8 text-white rounded-3xl font-black text-3xl transition-all active:scale-95"
                    style="background: linear-gradient(145deg, #F57C00, #FF9800); box-shadow: 12px 12px 24px #b3d4f1, -12px -12px 24px #f3ffff;"
                    @mouseover="e => e.target.style.boxShadow='8px 8px 16px #b3d4f1, -8px -8px 16px #f3ffff'"
                    @mouseout="e => e.target.style.boxShadow='12px 12px 24px #b3d4f1, -12px -12px 24px #f3ffff'"
                >
                    ⚠️ REPORTAR PARO
                </button>
            </div>
        </div>

        <!-- Estado: Sin Orden Seleccionada -->
        <div v-else class="text-center py-20">
            <div class="w-40 h-40 mx-auto mb-8 rounded-full flex items-center justify-center" style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); box-shadow: 20px 20px 40px #b3d4f1, -20px -20px 40px #f3ffff;">
                <span class="text-8xl">📋</span>
            </div>
            <p class="text-3xl font-bold" style="color: #607D8B;">Selecciona una orden de producción para comenzar</p>
        </div>

        <!-- Historial del Día -->
        <div v-if="registrosHoy.length > 0" class="mt-8 p-6 rounded-3xl" style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); box-shadow: 12px 12px 24px #b3d4f1, -12px -12px 24px #f3ffff;">
            <h3 class="text-2xl font-black mb-6" style="color: #263238;">Registros de Hoy</h3>
            <div class="space-y-4">
                <div
                    v-for="reg in registrosHoy"
                    :key="reg.id"
                    class="flex items-center justify-between p-6 rounded-2xl"
                    style="background: linear-gradient(145deg, #f5f9fc, #e3f2fd); box-shadow: 5px 5px 10px #d0dfe8, -5px -5px 10px #ffffff;"
                >
                    <div class="flex items-center space-x-6">
                        <div class="text-3xl font-black" style="color: #455A64;">{{ reg.hora }}:00</div>
                        <div>
                            <div class="text-xl font-bold" style="color: #263238;">{{ reg.orden?.codigo_orden }}</div>
                            <div class="text-lg font-semibold" style="color: #607D8B;">{{ reg.orden?.producto?.nombre }}</div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-8">
                        <div class="text-center">
                            <div class="text-sm font-bold" style="color: #607D8B;">Producidas</div>
                            <div class="text-3xl font-black" style="color: #2E7D32;">{{ reg.cantidad_producida }}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-sm font-bold" style="color: #607D8B;">Defectuosas</div>
                            <div class="text-3xl font-black" style="color: #D32F2F;">{{ reg.cantidad_defectuosa }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import axios from 'axios';

// Estado
const horaActual = ref('');
const turnoActual = ref('Turno Diurno');
const operadorNombre = ref('Operador');
const ordenesActivas = ref([]);
const ordenSeleccionada = ref('');
const ordenActual = ref(null);
const registrosHoy = ref([]);
const registro = ref({
    cantidad_producida: 0,
    cantidad_defectuosa: 0,
    observaciones: ''
});

let intervalHora = null;

// Computed
const progreso = computed(() => {
    if (!ordenActual.value) return 0;
    const total = ordenActual.value.cantidad_requerida;
    const actual = ordenActual.value.cantidad_producida || 0;
    return total > 0 ? Math.round((actual / total) * 100) : 0;
});

const puedeGuardar = computed(() => {
    return ordenActual.value && registro.value.cantidad_producida >= 0;
});

// Métodos
const actualizarHora = () => {
    const now = new Date();
    horaActual.value = now.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
};

const cargarOrdenesActivas = async () => {
    try {
        const { data } = await axios.get('/api/ordenes-produccion', {
            params: {
                estado: 'en_proceso',
                incluir: 'producto,maquina'
            }
        });
        ordenesActivas.value = data.data || [];
    } catch (error) {
        console.error('Error cargando órdenes activas:', error);
    }
};

const cargarOrden = async () => {
    if (!ordenSeleccionada.value) {
        ordenActual.value = null;
        return;
    }
    
    try {
        const { data } = await axios.get(`/api/ordenes-produccion/${ordenSeleccionada.value}`, {
            params: { incluir: 'producto,maquina' }
        });
        ordenActual.value = data;
        
        // Reset formulario
        registro.value = {
            cantidad_producida: 0,
            cantidad_defectuosa: 0,
            observaciones: ''
        };
    } catch (error) {
        console.error('Error cargando orden:', error);
    }
};

const cargarRegistrosHoy = async () => {
    try {
        const hoy = new Date().toISOString().split('T')[0];
        const { data } = await axios.get('/api/registros-produccion', {
            params: {
                fecha: hoy,
                incluir: 'orden.producto'
            }
        });
        registrosHoy.value = data.data || [];
    } catch (error) {
        console.error('Error cargando registros:', error);
    }
};

const incrementar = (tipo) => {
    if (tipo === 'producidas') {
        registro.value.cantidad_producida++;
    } else {
        registro.value.cantidad_defectuosa++;
    }
};

const decrementar = (tipo) => {
    if (tipo === 'producidas' && registro.value.cantidad_producida > 0) {
        registro.value.cantidad_producida--;
    } else if (tipo === 'defectuosas' && registro.value.cantidad_defectuosa > 0) {
        registro.value.cantidad_defectuosa--;
    }
};

const guardarRegistro = async () => {
    if (!puedeGuardar.value) return;
    
    try {
        const payload = {
            orden_produccion_id: ordenActual.value.id,
            fecha_registro: new Date().toISOString().split('T')[0],
            hora_registro: new Date().getHours(),
            cantidad_producida: registro.value.cantidad_producida,
            cantidad_defectuosa: registro.value.cantidad_defectuosa,
            observaciones: registro.value.observaciones || null
        };
        
        await axios.post('/api/registros-produccion', payload);
        
        alert('✓ Registro guardado exitosamente');
        
        // Reset formulario
        registro.value = {
            cantidad_producida: 0,
            cantidad_defectuosa: 0,
            observaciones: ''
        };
        
        // Recargar datos
        await Promise.all([
            cargarOrden(),
            cargarRegistrosHoy()
        ]);
    } catch (error) {
        console.error('Error guardando registro:', error);
        alert('Error al guardar el registro. Por favor intenta nuevamente.');
    }
};

const reportarParo = async () => {
    if (!ordenActual.value) return;
    
    const motivo = prompt('¿Cuál es el motivo del paro?');
    if (!motivo) return;
    
    try {
        await axios.post('/api/alertas', {
            tipo: 'paro_maquina',
            prioridad: 'alta',
            titulo: `Paro reportado - ${ordenActual.value.maquina?.nombre_maquina || 'Máquina'}`,
            mensaje: motivo,
            origen: 'operador',
            origen_id: ordenActual.value.maquina?.id
        });
        
        alert('⚠️ Paro reportado. Un supervisor será notificado.');
    } catch (error) {
        console.error('Error reportando paro:', error);
        alert('Error al reportar el paro.');
    }
};

const reportarEmergencia = async () => {
    if (!confirm('¿CONFIRMAS QUE HAY UNA EMERGENCIA?')) return;
    
    try {
        await axios.post('/api/alertas', {
            tipo: 'emergencia',
            prioridad: 'critica',
            titulo: '🚨 EMERGENCIA REPORTADA',
            mensaje: `Emergencia reportada desde estación de ${operadorNombre.value}`,
            origen: 'operador'
        });
        
        alert('🚨 EMERGENCIA REPORTADA - Personal notificado');
    } catch (error) {
        console.error('Error reportando emergencia:', error);
    }
};

// Lifecycle
onMounted(async () => {
    actualizarHora();
    intervalHora = setInterval(actualizarHora, 1000);
    
    await Promise.all([
        cargarOrdenesActivas(),
        cargarRegistrosHoy()
    ]);
});

onUnmounted(() => {
    if (intervalHora) {
        clearInterval(intervalHora);
    }
});
</script>

<style scoped>
/* Estilos adicionales para interfaz táctil */
button {
    -webkit-tap-highlight-color: transparent;
    user-select: none;
}

input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

input[type="number"] {
    -moz-appearance: textfield;
}
</style>
