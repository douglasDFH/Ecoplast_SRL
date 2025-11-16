<template>
    <div class="max-w-4xl mx-auto p-6">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">🧪 Pruebas de Broadcasting</h1>

            <!-- Estado de Conexión -->
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-3">Estado de Conexión</h2>
                <div class="flex items-center space-x-4">
                    <div
                        class="flex items-center"
                        :class="connectionStatus.connected ? 'text-green-600' : 'text-red-600'"
                    >
                        <div
                            class="w-3 h-3 rounded-full mr-2"
                            :class="connectionStatus.connected ? 'bg-green-500' : 'bg-red-500'"
                        ></div>
                        <span>{{ connectionStatus.connected ? 'Conectado' : 'Desconectado' }}</span>
                    </div>
                    <button
                        @click="testConnection"
                        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
                    >
                        Probar Conexión
                    </button>
                </div>
                <p class="text-sm text-gray-600 mt-2">
                    Socket ID: {{ connectionStatus.socketId || 'N/A' }}
                </p>
            </div>

            <!-- Test de Eventos -->
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-3">Test de Eventos</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <button
                        @click="testOrderEvent"
                        class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600"
                    >
                        📋 Simular Orden de Producción
                    </button>
                    <button
                        @click="testMachineEvent"
                        class="px-4 py-2 bg-purple-500 text-white rounded hover:bg-purple-600"
                    >
                        ⚙️ Simular Evento de Máquina
                    </button>
                    <button
                        @click="testInventoryEvent"
                        class="px-4 py-2 bg-orange-500 text-white rounded hover:bg-orange-600"
                    >
                        📦 Simular Evento de Inventario
                    </button>
                    <button
                        @click="testAlertEvent"
                        class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600"
                    >
                        🚨 Simular Alerta
                    </button>
                </div>
            </div>

            <!-- Log de Eventos -->
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-3">Log de Eventos Recibidos</h2>
                <div class="bg-gray-100 rounded p-4 max-h-96 overflow-y-auto">
                    <div
                        v-for="(event, index) in eventLog"
                        :key="index"
                        class="mb-2 p-2 bg-white rounded text-sm"
                    >
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="font-medium text-blue-600">{{ event.event }}</span>
                                <span class="text-gray-500 ml-2">{{ formatTime(event.timestamp) }}</span>
                            </div>
                            <span class="text-xs text-gray-500">{{ event.channel }}</span>
                        </div>
                        <pre class="text-xs text-gray-700 mt-1 overflow-x-auto">{{ JSON.stringify(event.data, null, 2) }}</pre>
                    </div>
                    <div v-if="eventLog.length === 0" class="text-center text-gray-500 py-4">
                        No se han recibido eventos aún
                    </div>
                </div>
                <button
                    @click="clearLog"
                    class="mt-2 px-3 py-1 bg-gray-500 text-white rounded text-sm hover:bg-gray-600"
                >
                    Limpiar Log
                </button>
            </div>

            <!-- Información de Debug -->
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-3">Información de Debug</h2>
                <div class="bg-gray-100 rounded p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <strong>Echo disponible:</strong> {{ echoAvailable ? 'Sí' : 'No' }}
                        </div>
                        <div>
                            <strong>Pusher disponible:</strong> {{ pusherAvailable ? 'Sí' : 'No' }}
                        </div>
                        <div>
                            <strong>Token de auth:</strong> {{ authToken ? 'Presente' : 'Ausente' }}
                        </div>
                        <div>
                            <strong>Canales suscritos:</strong> {{ subscribedChannels.length }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const connectionStatus = ref({
    connected: false,
    socketId: null
});

const eventLog = ref([]);
const subscribedChannels = ref([]);
let channelSubscriptions = [];

// Verificar disponibilidad de librerías
const echoAvailable = ref(!!window.Echo);
const pusherAvailable = ref(!!window.Pusher);
const authToken = ref(!!localStorage.getItem('auth_token'));

const testConnection = () => {
    if (!window.Echo) {
        alert('Laravel Echo no está disponible');
        return;
    }

    // Verificar conexión
    const pusher = window.Echo.connector.pusher;
    connectionStatus.value.connected = pusher.connection.state === 'connected';
    connectionStatus.value.socketId = pusher.connection.socket_id;

    if (connectionStatus.value.connected) {
        alert('Conexión exitosa con Pusher');
    } else {
        alert('No hay conexión con Pusher. Estado: ' + pusher.connection.state);
    }
};

const testOrderEvent = () => {
    // Simular envío de evento (en producción esto vendría del backend)
    const mockEvent = {
        event: 'OrdenProduccionActualizada',
        channel: 'ordenes-produccion',
        data: {
            orden: {
                id: 1,
                numero_orden: 'OP-TEST-001',
                estado: 'en_proceso',
                cantidad_planificada: 1000,
                cantidad_producida: 500,
                eficiencia: 75.5
            },
            tipo_cambio: 'actualizada',
            timestamp: new Date().toISOString()
        },
        timestamp: new Date()
    };

    eventLog.value.unshift(mockEvent);
};

const testMachineEvent = () => {
    const mockEvent = {
        event: 'MaquinariaActualizada',
        channel: 'maquinaria',
        data: {
            maquina: {
                id: 1,
                nombre_maquina: 'Inyectora Test',
                estado_actual: 'operativa',
                oee_actual: 85.2
            },
            tipo_cambio: 'actualizada',
            timestamp: new Date().toISOString()
        },
        timestamp: new Date()
    };

    eventLog.value.unshift(mockEvent);
};

const testInventoryEvent = () => {
    const mockEvent = {
        event: 'InventarioActualizado',
        channel: 'inventario',
        data: {
            insumo: {
                id: 1,
                nombre_insumo: 'PLA Test',
                stock_actual: 75,
                stock_minimo: 100
            },
            tipo_cambio: 'actualizada',
            estado_stock: 'bajo',
            timestamp: new Date().toISOString()
        },
        timestamp: new Date()
    };

    eventLog.value.unshift(mockEvent);
};

const testAlertEvent = () => {
    const mockEvent = {
        event: 'AlertaCreada',
        channel: 'alertas',
        data: {
            alerta: {
                id: 1,
                tipo: 'stock_bajo',
                titulo: 'Alerta de Prueba',
                mensaje: 'Esta es una alerta de prueba del sistema de broadcasting',
                prioridad: 'media'
            },
            timestamp: new Date().toISOString()
        },
        timestamp: new Date()
    };

    eventLog.value.unshift(mockEvent);
};

const clearLog = () => {
    eventLog.value = [];
};

const formatTime = (date) => {
    return new Date(date).toLocaleTimeString('es-ES', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
};

// Configurar listeners de eventos reales
const setupEventListeners = () => {
    if (!window.Echo) return;

    // Listener para órdenes de producción
    const orderChannel = window.Echo.private('ordenes-produccion')
        .listen('.OrdenProduccionActualizada', (e) => {
            eventLog.value.unshift({
                event: 'OrdenProduccionActualizada',
                channel: 'ordenes-produccion',
                data: e,
                timestamp: new Date()
            });
        });

    // Listener para maquinaria
    const machineChannel = window.Echo.private('maquinaria')
        .listen('.MaquinariaActualizada', (e) => {
            eventLog.value.unshift({
                event: 'MaquinariaActualizada',
                channel: 'maquinaria',
                data: e,
                timestamp: new Date()
            });
        });

    // Listener para inventario
    const inventoryChannel = window.Echo.private('inventario')
        .listen('.InventarioActualizado', (e) => {
            eventLog.value.unshift({
                event: 'InventarioActualizado',
                channel: 'inventario',
                data: e,
                timestamp: new Date()
            });
        });

    // Listener para alertas
    const alertChannel = window.Echo.private('alertas')
        .listen('.AlertaCreada', (e) => {
            eventLog.value.unshift({
                event: 'AlertaCreada',
                channel: 'alertas',
                data: e,
                timestamp: new Date()
            });
        });

    channelSubscriptions = [orderChannel, machineChannel, inventoryChannel, alertChannel];
    subscribedChannels.value = ['ordenes-produccion', 'maquinaria', 'inventario', 'alertas'];
};

const cleanupEventListeners = () => {
    channelSubscriptions.forEach(channel => {
        try {
            channel.stopListening();
        } catch (error) {
            console.error('Error cleaning up channel:', error);
        }
    });
    channelSubscriptions = [];
    subscribedChannels.value = [];
};

onMounted(() => {
    setupEventListeners();
    testConnection();
});

onUnmounted(() => {
    cleanupEventListeners();
});
</script>