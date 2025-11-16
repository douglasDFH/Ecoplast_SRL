import { defineStore } from 'pinia';
import { ref, reactive } from 'vue';
import {
    ordenProduccionService,
    inventarioService,
    alertaService,
    dashboardService,
} from '../services';

export const useDashboardStore = defineStore('dashboard', () => {
    // Estado reactivo
    const productionOrders = ref([]);
    const machines = ref([]);
    const inventory = ref([]);
    const products = ref([]);
    const alerts = ref([]);
    const dashboardData = ref(null);
    const kpis = reactive({
        totalOrders: 0,
        activeOrders: 0,
        totalMachines: 0,
        operationalMachines: 0,
        lowStockItems: 0,
        totalAlerts: 0,
        averageEfficiency: 0,
        todayProduction: 0
    });

    // Estado de conexión
    const isConnected = ref(false);
    const connectionStatus = ref('Desconectado');

    // Canales Echo
    let echoChannels = [];

    // Acciones
    const fetchInitialData = async () => {
        try {
            const dashResponse = await dashboardService.getDashboard();
            dashboardData.value = dashResponse.data;

            const [ordersRes, inventoryRes, alertsRes] = await Promise.all([
                ordenProduccionService.getOrdenes({ limit: 50 }),
                inventarioService.getInsumos({ limit: 100 }),
                alertaService.getActivas()
            ]);

            productionOrders.value = ordersRes.data.data || ordersRes.data || [];
            inventory.value = inventoryRes.data.data || inventoryRes.data || [];
            alerts.value = alertsRes.data.data || alertsRes.data || [];

            // Usar datos del dashboard para máquinas
            if (dashboardData.value?.maquinas) {
                machines.value = dashboardData.value.maquinas;
            }

            calculateKPIs();
        } catch (error) {
            console.error('Error fetching initial data:', error);
        }
    };

    const calculateKPIs = () => {
        kpis.totalOrders = productionOrders.value.length;
        kpis.activeOrders = productionOrders.value.filter(order =>
            ['pendiente', 'programada', 'en_proceso'].includes(order.estado)
        ).length;

        kpis.totalMachines = machines.value.length;
        kpis.operationalMachines = machines.value.filter(machine =>
            machine.estado_actual === 'operativa'
        ).length;

        kpis.lowStockItems = inventory.value.filter(item =>
            item.stock_actual <= item.stock_minimo
        ).length;

        kpis.totalAlerts = alerts.value.filter(alert =>
            alert.estado === 'activa'
        ).length;

        // Calcular eficiencia promedio
        const ordersWithEfficiency = productionOrders.value.filter(order => order.eficiencia);
        kpis.averageEfficiency = ordersWithEfficiency.length > 0
            ? ordersWithEfficiency.reduce((sum, order) => sum + order.eficiencia, 0) / ordersWithEfficiency.length
            : 0;

        // Calcular producción del día
        const today = new Date().toISOString().split('T')[0];
        kpis.todayProduction = productionOrders.value
            .filter(order => order.fecha_inicio?.startsWith(today) && order.cantidad_producida)
            .reduce((sum, order) => sum + order.cantidad_producida, 0);
    };

    const connectToBroadcasting = () => {
        if (!window.Echo) {
            console.error('Laravel Echo not available');
            return;
        }

        try {
            // Escuchar eventos de producción
            window.Echo.channel('produccion')
                .listen('.produccion.registrada', (e) => {
                    console.log('Producción registrada:', e);
                    fetchInitialData(); // Refrescar datos
                });

            // Escuchar eventos de alertas
            window.Echo.channel('alertas')
                .listen('.alerta.generada', (e) => {
                    console.log('Nueva alerta:', e);
                    addAlert(e.alerta);
                    calculateKPIs();
                });

            // Escuchar eventos de órdenes completadas
            window.Echo.channel('ordenes-produccion')
                .listen('.orden.completada', (e) => {
                    console.log('Orden completada:', e);
                    updateProductionOrder(e.orden);
                    calculateKPIs();
                });

            // Escuchar eventos de máquina parada
            window.Echo.channel('maquinaria')
                .listen('.maquina.parada', (e) => {
                    console.log('Máquina parada:', e);
                    updateMachine(e.maquina);
                    calculateKPIs();
                });

            // Escuchar eventos de defectos detectados
            window.Echo.channel('calidad')
                .listen('.defecto.detectado', (e) => {
                    console.log('Defecto detectado:', e);
                    // Generar alerta visual
                    addAlert({
                        tipo: 'calidad_defectos',
                        prioridad: e.inspeccion.resultado === 'rechazado' ? 'alta' : 'media',
                        titulo: 'Defecto detectado en producción',
                        mensaje: `Lote ${e.lote?.codigo_lote || 'N/A'}: ${e.inspeccion.cantidad_defectuosa} unidades defectuosas`,
                        estado: 'activa',
                        leida: false,
                    });
                });

            isConnected.value = true;
            connectionStatus.value = 'Conectado';

            console.log('Connected to broadcasting channels');

        } catch (error) {
            console.error('Error connecting to broadcasting:', error);
            connectionStatus.value = 'Error de conexión';
        }
    };

    const disconnectFromBroadcasting = () => {
        echoChannels.forEach(channel => {
            try {
                channel.stopListening();
            } catch (error) {
                console.error('Error stopping channel:', error);
            }
        });
        echoChannels = [];
        isConnected.value = false;
        connectionStatus.value = 'Desconectado';
    };

    // Funciones de actualización para broadcasting
    const updateProductionOrder = (updatedOrder) => {
        const index = productionOrders.value.findIndex(order => order.id === updatedOrder.id);
        if (index !== -1) {
            productionOrders.value[index] = { ...productionOrders.value[index], ...updatedOrder };
        } else {
            productionOrders.value.push(updatedOrder);
        }
    };

    const updateMachine = (updatedMachine) => {
        const index = machines.value.findIndex(machine => machine.id === updatedMachine.id);
        if (index !== -1) {
            machines.value[index] = { ...machines.value[index], ...updatedMachine };
        } else {
            machines.value.push(updatedMachine);
        }
    };

    const updateInventoryItem = (updatedInsumo) => {
        const index = inventory.value.findIndex(item => item.id === updatedInsumo.id);
        if (index !== -1) {
            inventory.value[index] = { ...inventory.value[index], ...updatedInsumo };
        } else {
            inventory.value.push(updatedInsumo);
        }
    };

    const updateProduct = (updatedProduct) => {
        const index = products.value.findIndex(product => product.id === updatedProduct.id);
        if (index !== -1) {
            products.value[index] = { ...products.value[index], ...updatedProduct };
        } else {
            products.value.push(updatedProduct);
        }
    };

    const addAlert = (newAlert) => {
        alerts.value.unshift(newAlert);
        // Mantener solo las últimas 50 alertas
        if (alerts.value.length > 50) {
            alerts.value = alerts.value.slice(0, 50);
        }
    };

    const markAlertAsRead = (alertId) => {
        const alert = alerts.value.find(a => a.id === alertId);
        if (alert) {
            alert.estado = 'leida';
        }
    };

    const clearOldAlerts = () => {
        const oneWeekAgo = new Date();
        oneWeekAgo.setDate(oneWeekAgo.getDate() - 7);

        alerts.value = alerts.value.filter(alert => {
            const alertDate = new Date(alert.created_at);
            return alertDate > oneWeekAgo || alert.estado === 'activa';
        });
    };

    return {
        // Estado
        productionOrders,
        machines,
        inventory,
        products,
        alerts,
        kpis,
        isConnected,
        connectionStatus,

        // Acciones
        fetchInitialData,
        calculateKPIs,
        connectToBroadcasting,
        disconnectFromBroadcasting,
        updateProductionOrder,
        updateMachine,
        updateInventoryItem,
        updateProduct,
        addAlert,
        markAlertAsRead,
        clearOldAlerts
    };
});