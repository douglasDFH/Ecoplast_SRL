import { ref } from 'vue';

export function useBroadcasting() {
    const connected = ref(false);
    const listeners = ref([]);

    // Verificar si Echo está disponible
    const isEchoReady = () => {
        return typeof window !== 'undefined' && window.Echo;
    };

    // Escuchar canal público
    const listenToChannel = (channelName, eventName, callback) => {
        if (!isEchoReady()) {
            console.error('Laravel Echo no está disponible');
            return null;
        }

        const listener = window.Echo.channel(channelName)
            .listen(eventName, callback);

        listeners.value.push({ channelName, eventName, listener });
        connected.value = true;

        return listener;
    };

    // Escuchar canal privado
    const listenToPrivateChannel = (channelName, eventName, callback) => {
        if (!isEchoReady()) {
            console.error('Laravel Echo no está disponible');
            return null;
        }

        const listener = window.Echo.private(channelName)
            .listen(eventName, callback);

        listeners.value.push({ channelName, eventName, listener });
        connected.value = true;

        return listener;
    };

    // Dejar de escuchar un canal
    const leaveChannel = (channelName) => {
        if (!isEchoReady()) return;

        window.Echo.leave(channelName);
        listeners.value = listeners.value.filter(l => l.channelName !== channelName);
    };

    // Escuchar eventos de producción
    const listenToProduccion = (callback) => {
        return listenToChannel('produccion', '.produccion.registrada', callback);
    };

    // Escuchar eventos de alertas
    const listenToAlertas = (callback) => {
        return listenToChannel('alertas', '.alerta.generada', callback);
    };

    // Escuchar eventos de órdenes de producción
    const listenToOrdenes = (callback) => {
        return listenToChannel('ordenes-produccion', '.orden.completada', callback);
    };

    // Escuchar eventos de maquinaria
    const listenToMaquinaria = (callback) => {
        return listenToChannel('maquinaria', '.maquina.parada', callback);
    };

    // Escuchar eventos de calidad
    const listenToCalidad = (callback) => {
        return listenToChannel('calidad', '.defecto.detectado', callback);
    };

    // Limpiar todos los listeners
    const cleanup = () => {
        if (!isEchoReady()) return;

        listeners.value.forEach(({ channelName }) => {
            window.Echo.leave(channelName);
        });
        listeners.value = [];
        connected.value = false;
    };

    return {
        connected,
        listeners,
        listenToChannel,
        listenToPrivateChannel,
        leaveChannel,
        listenToProduccion,
        listenToAlertas,
        listenToOrdenes,
        listenToMaquinaria,
        listenToCalidad,
        cleanup,
    };
}
