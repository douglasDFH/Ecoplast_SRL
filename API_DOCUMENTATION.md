# API Documentation - Ecoplast SRL Biodegradable Plastics Management

## Base URL
```
http://localhost:8000/api
```

## Authentication
All API endpoints require authentication using Laravel Sanctum.

### Login
```http
POST /api/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password"
}
```

### Logout
```http
POST /api/logout
Authorization: Bearer {token}
```

## Endpoints

### 🔄 Insumos Biodegradables (Raw Materials)

#### List Insumos
```http
GET /api/insumos
Authorization: Bearer {token}
```

**Query Parameters:**
- `categoria_id` - Filter by category ID
- `tipo_material` - Filter by material type (PLA, PHA, PBS, PBAT, Almidon)
- `certificacion` - Filter by certification
- `activo` - Filter by active status (true/false)
- `stock_bajo` - Show only low stock items (true)
- `page` - Page number
- `per_page` - Items per page

**Response:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "codigo_insumo": "PLA-001",
        "nombre_insumo": "PLA Ingeo 4043D",
        "categoria": {
          "id": 1,
          "nombre_categoria": "Polímeros Biodegradables"
        },
        "tipo_material": "PLA",
        "certificacion_biodegradable": "ASTM D6400",
        "stock_actual": 2500,
        "stock_minimo": 500,
        "precio_unitario": 3.5,
        "activo": true,
        "alertas_stock": false
      }
    ],
    "total": 5
  }
}
```

#### Get Insumo by ID
```http
GET /api/insumos/{id}
Authorization: Bearer {token}
```

#### Create Insumo
```http
POST /api/insumos
Authorization: Bearer {token}
Content-Type: application/json

{
  "codigo_insumo": "PLA-002",
  "nombre_insumo": "PLA Ingeo 4060D",
  "categoria_id": 1,
  "tipo_material": "PLA",
  "unidad_medida": "kg",
  "certificacion_biodegradable": "ASTM D6400",
  "proveedor": "NatureWorks",
  "precio_unitario": 3.8,
  "stock_minimo": 500,
  "stock_actual": 3000,
  "activo": true
}
```

#### Update Insumo
```http
PUT /api/insumos/{id}
Authorization: Bearer {token}
```

#### Delete Insumo
```http
DELETE /api/insumos/{id}
Authorization: Bearer {token}
```

#### Get Estadísticas de Insumos
```http
GET /api/insumos/estadisticas/general
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "total_insumos": 5,
    "insumos_activos": 5,
    "insumos_biodegradables": 5,
    "insumos_certificados": 5,
    "por_material": {
      "PLA": 1,
      "PHA": 1,
      "PBS": 1,
      "PBAT": 1,
      "Almidon": 1
    },
    "stock_bajo": 0,
    "valor_total_inventario": 42500
  }
}
```

#### Get Insumos por Categoría
```http
GET /api/insumos/por-categoria/{categoria_id}
Authorization: Bearer {token}
```

#### Get Insumos con Stock Bajo
```http
GET /api/insumos/stock-bajo
Authorization: Bearer {token}
```

#### Get Insumos Biodegradables
```http
GET /api/insumos/biodegradables
Authorization: Bearer {token}
```

### 🏭 Productos Terminados Biodegradables

#### List Productos
```http
GET /api/productos-terminados
Authorization: Bearer {token}
```

**Query Parameters:**
- `categoria_producto_id` - Filter by category ID
- `material_principal` - Filter by main material
- `certificacion_compostable` - Filter by compost certification
- `tiempo_compostaje_min/max` - Filter by composting time range
- `activo` - Filter by active status
- `sort_by` - Sort field (codigo_producto, nombre_producto, precio_venta)
- `sort_direction` - Sort direction (asc, desc)

#### Get Producto by ID
```http
GET /api/productos-terminados/{id}
Authorization: Bearer {token}
```

**Response includes:**
- `es_biodegradable` - Boolean indicating if product is biodegradable
- `nivel_sostenibilidad` - Sustainability level (excelente, muy_bueno, bueno, regular)
- `margen_ganancia` - Profit margin percentage
- `ordenes_pendientes` - Number of pending orders

#### Create Producto
```http
POST /api/productos-terminados
Authorization: Bearer {token}
Content-Type: application/json

{
  "codigo_producto": "BOLSAS-PLA-001",
  "nombre_producto": "Bolsas Biodegradables PLA",
  "categoria_producto_id": 1,
  "descripcion": "Bolsas compostables de PLA",
  "material_principal": "PLA",
  "certificacion_compostable": "EN 13432",
  "tiempo_compostaje_dias": 90,
  "peso_unitario_gramos": 5.2,
  "precio_venta": 0.15,
  "unidad_venta": "unidad",
  "unidades_por_paquete": 100,
  "stock_minimo": 1000,
  "stock_actual": 5000,
  "activo": true
}
```

#### Get Estadísticas de Productos
```http
GET /api/productos-terminados/estadisticas/general
Authorization: Bearer {token}
```

#### Get Productos por Sostenibilidad
```http
GET /api/productos-terminados/por-sostenibilidad
Authorization: Bearer {token}
```

### ⚙️ Maquinaria

#### List Maquinaria
```http
GET /api/maquinaria
Authorization: Bearer {token}
```

**Query Parameters:**
- `tipo_maquina_id` - Filter by machine type
- `estado_actual` - Filter by current status (operativa, mantenimiento, parada, averia)
- `marca` - Filter by brand
- `ubicacion` - Filter by location
- `activo` - Filter by active status

#### Get Máquina by ID
```http
GET /api/maquinaria/{id}
Authorization: Bearer {token}
```

**Response includes:**
- `oee_actual` - Current Overall Equipment Effectiveness
- `necesita_mantenimiento` - Boolean indicating maintenance needed
- `eficiencia_energetica` - Energy efficiency ratio
- `ordenes_activas` - Number of active orders

#### Create Máquina
```http
POST /api/maquinaria
Authorization: Bearer {token}
Content-Type: application/json

{
  "codigo_maquina": "INYECCION-001",
  "nombre_maquina": "Máquina Inyección Engel 150T",
  "tipo_maquina_id": 1,
  "marca": "Engel",
  "modelo": "Victory 150/45",
  "año_fabricacion": 2020,
  "capacidad_produccion": 120,
  "unidad_capacidad": "unidades/h",
  "consumo_energia_kwh": 25,
  "temp_min_operacion": 150,
  "temp_max_operacion": 280,
  "estado_actual": "operativa",
  "ubicacion": "Planta Principal - Línea 1",
  "activo": true
}
```

#### Update Estado de Máquina
```http
PATCH /api/maquinaria/{id}/estado
Authorization: Bearer {token}
Content-Type: application/json

{
  "estado_actual": "mantenimiento",
  "observaciones": "Mantenimiento preventivo programado"
}
```

#### Get Estadísticas de Maquinaria
```http
GET /api/maquinaria/estadisticas/general
Authorization: Bearer {token}
```

#### Get Máquinas por Estado
```http
GET /api/maquinaria/por-estado
Authorization: Bearer {token}
```

#### Get Máquinas que Necesitan Mantenimiento
```http
GET /api/maquinaria/necesitan-mantenimiento
Authorization: Bearer {token}
```

### 📋 Órdenes de Producción

#### List Órdenes
```http
GET /api/ordenes-produccion
Authorization: Bearer {token}
```

**Query Parameters:**
- `producto_id` - Filter by product ID
- `maquina_id` - Filter by machine ID
- `estado` - Filter by status (pendiente, en_proceso, pausada, completada, cancelada)
- `prioridad` - Filter by priority (baja, normal, alta, urgente)
- `fecha_desde/hasta` - Filter by date range
- `material_principal` - Filter by main material

#### Get Orden by ID
```http
GET /api/ordenes-produccion/{id}
Authorization: Bearer {token}
```

**Response includes:**
- `eficiencia_produccion` - Production efficiency percentage
- `tiempo_restante` - Remaining time in hours
- `progreso_estimado` - Estimated progress percentage
- `material_biodegradable` - Main material
- `certificado_compostable` - Compost certification
- `tiempo_compostaje` - Composting time in days

#### Create Orden de Producción
```http
POST /api/ordenes-produccion
Authorization: Bearer {token}
Content-Type: application/json

{
  "numero_orden": "OP-20251115-001",
  "producto_id": 1,
  "cantidad_planificada": 10000,
  "formulacion_id": 1,
  "maquina_id": 1,
  "turno_id": 1,
  "fecha_programada": "2025-11-20 08:00:00",
  "prioridad": "normal",
  "operador_id": 2,
  "supervisor_id": 3,
  "notas_produccion": "Primera orden del mes"
}
```

#### Update Orden
```http
PUT /api/ordenes-produccion/{id}
Authorization: Bearer {token}
```

#### Iniciar Producción
```http
PATCH /api/ordenes-produccion/{id}/iniciar
Authorization: Bearer {token}
```

#### Finalizar Producción
```http
PATCH /api/ordenes-produccion/{id}/finalizar
Authorization: Bearer {token}
Content-Type: application/json

{
  "cantidad_producida": 9800,
  "cantidad_conforme": 9600,
  "cantidad_defectuosa": 200,
  "observaciones_calidad": "Defectos menores en sellado"
}
```

#### Get Estadísticas de Órdenes
```http
GET /api/ordenes-produccion/estadisticas/general
Authorization: Bearer {token}
```

#### Get Órdenes por Estado
```http
GET /api/ordenes-produccion/por-estado
Authorization: Bearer {token}
```

## Real-time Dashboard Integration

### WebSocket Events (Pusher/Laravel Broadcasting)

#### Production Updates
```javascript
// Listen for production updates
Echo.channel('produccion')
    .listen('OrdenProduccionActualizada', (e) => {
        console.log('Orden actualizada:', e.orden);
        // Update dashboard with real-time data
    });

// Listen for machine status changes
Echo.channel('maquinaria')
    .listen('MaquinaEstadoCambiado', (e) => {
        console.log('Máquina actualizada:', e.maquina);
        // Update machine status in dashboard
    });

// Listen for inventory alerts
Echo.channel('inventario')
    .listen('AlertaStock', (e) => {
        console.log('Alerta de stock:', e.alerta);
        // Show notification and update inventory display
    });
```

### Dashboard KPIs

#### Production Metrics
- **OEE Global**: Average Overall Equipment Effectiveness
- **Eficiencia de Producción**: Production efficiency percentage
- **Órdenes Completadas**: Completed orders this month
- **Tiempo de Producción**: Average production time vs planned

#### Quality Metrics
- **Tasa de Conformidad**: Conforming products percentage
- **Defectos por Tipo**: Defects categorized by type
- **Inspecciones Realizadas**: Quality inspections completed

#### Inventory Metrics
- **Stock de Insumos**: Raw materials inventory levels
- **Productos en Stock**: Finished products availability
- **Alertas de Stock**: Low stock warnings
- **Valor de Inventario**: Total inventory value

#### Sustainability Metrics
- **Materiales Biodegradables**: Percentage of biodegradable materials used
- **Certificaciones Activas**: Active compost certifications
- **Tiempo Promedio de Compostaje**: Average composting time
- **Huella de Carbono**: Carbon footprint reduction

### Frontend Integration Example (Vue.js + Pinia)

```javascript
// stores/production.js
import { defineStore } from 'pinia'
import axios from 'axios'

export const useProductionStore = defineStore('production', {
  state: () => ({
    ordenes: [],
    estadisticas: {},
    loading: false
  }),

  actions: {
    async fetchOrdenes() {
      this.loading = true
      try {
        const response = await axios.get('/api/ordenes-produccion')
        this.ordenes = response.data.data.data
      } catch (error) {
        console.error('Error fetching orders:', error)
      } finally {
        this.loading = false
      }
    },

    async fetchEstadisticas() {
      try {
        const response = await axios.get('/api/ordenes-produccion/estadisticas/general')
        this.estadisticas = response.data.data
      } catch (error) {
        console.error('Error fetching statistics:', error)
      }
    }
  }
})
```

### Real-time Updates Setup

```javascript
// bootstrap.js or main.js
import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

window.Pusher = Pusher

window.Echo = new Echo({
  broadcaster: 'pusher',
  key: process.env.VITE_PUSHER_APP_KEY,
  cluster: process.env.VITE_PUSHER_APP_CLUSTER,
  encrypted: true,
  auth: {
    headers: {
      Authorization: `Bearer ${localStorage.getItem('auth_token')}`
    }
  }
})
```

## Error Handling

All API responses follow this structure:
```json
{
  "success": true|false,
  "data": {...}|null,
  "message": "Description of the result",
  "errors": {...} // Only present when success is false
}
```

### Common HTTP Status Codes
- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `409` - Conflict (business logic error)
- `422` - Validation Error
- `500` - Internal Server Error

## Rate Limiting

API endpoints are rate limited to prevent abuse:
- **Authenticated requests**: 60 requests per minute
- **Login attempts**: 5 attempts per minute

## Data Validation

All endpoints validate input data according to business rules:
- Biodegradable materials must have valid certifications
- Production orders require biodegradable products only
- Machine status affects order creation
- Stock levels prevent negative inventory

## Security Features

- **CORS enabled** for frontend integration
- **Input sanitization** on all endpoints
- **SQL injection prevention** via Eloquent ORM
- **XSS protection** via Laravel's built-in features
- **Rate limiting** to prevent brute force attacks

---

# 🔴 Real-Time Broadcasting (WebSocket Integration)

The Ecoplast SRL API includes real-time broadcasting capabilities using Laravel Broadcasting with Pusher for live dashboard updates and notifications.

## Configuration

### Pusher Setup
```javascript
// Frontend configuration (Vue.js with Laravel Echo)
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.VUE_APP_PUSHER_APP_KEY,
    cluster: process.env.VUE_APP_PUSHER_APP_CLUSTER,
    encrypted: true,
    auth: {
        headers: {
            Authorization: `Bearer ${token}`
        }
    }
});
```

### Environment Variables
```env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your_pusher_app_id
PUSHER_APP_KEY=your_pusher_app_key
PUSHER_APP_SECRET=your_pusher_app_secret
PUSHER_APP_CLUSTER=your_pusher_cluster
```

## Broadcast Channels

### 📊 Production Orders Channel
**Channel:** `private:ordenes-produccion`

**Events:**
- `OrdenProduccionActualizada` - Production order status changes
- `AlertaCreada` - System alerts (low stock, machine issues, etc.)

```javascript
// Listen for production order updates
Echo.private('ordenes-produccion')
    .listen('.OrdenProduccionActualizada', (e) => {
        console.log('Order updated:', e.orden);
        console.log('Change type:', e.tipo_cambio);
        // Update dashboard with real-time data
        updateProductionDashboard(e.orden);
    });

// Listen for system alerts
Echo.private('ordenes-produccion')
    .listen('.AlertaCreada', (e) => {
        console.log('Alert created:', e.alerta);
        // Show notification to user
        showNotification(e.alerta);
    });
```

### ⚙️ Machinery Channel
**Channel:** `private:maquinaria`

**Events:**
- `MaquinariaActualizada` - Machine status, maintenance, or OEE changes

```javascript
// Listen for machinery updates
Echo.private('maquinaria')
    .listen('.MaquinariaActualizada', (e) => {
        console.log('Machine updated:', e.maquina);
        console.log('Current status:', e.estado_actual);
        console.log('OEE:', e.oee_actual);
        // Update machine monitoring dashboard
        updateMachineStatus(e.maquina);
    });
```

### 📦 Products Channel
**Channel:** `private:productos`

**Events:**
- `ProductoTerminadoActualizado` - Product stock, certification, or specification changes

```javascript
// Listen for product updates
Echo.private('productos')
    .listen('.ProductoTerminadoActualizado', (e) => {
        console.log('Product updated:', e.producto);
        console.log('Current stock:', e.stock_actual);
        console.log('Certification:', e.certificado_compostaje);
        // Update inventory dashboard
        updateProductInventory(e.producto);
    });
```

### 📈 Inventory Channel
**Channel:** `private:inventario`

**Events:**
- `InventarioActualizado` - Raw material stock level changes

```javascript
// Listen for inventory updates
Echo.private('inventario')
    .listen('.InventarioActualizado', (e) => {
        console.log('Inventory updated:', e.inventario);
        console.log('Current quantity:', e.cantidad_actual);
        console.log('Stock status:', e.estado_stock);
        // Update inventory alerts
        updateInventoryAlerts(e.inventario);
    });
```

### 🚨 Alerts Channel
**Channel:** `private:alertas`

**Events:**
- `AlertaCreada` - General system alerts and notifications

```javascript
// Listen for all system alerts
Echo.private('alertas')
    .listen('.AlertaCreada', (e) => {
        console.log('System alert:', e.alerta);
        // Handle different alert types
        switch(e.alerta.tipo) {
            case 'stock_bajo':
                handleLowStockAlert(e.alerta);
                break;
            case 'maquina_falla':
                handleMachineFailureAlert(e.alerta);
                break;
            case 'orden_atrasada':
                handleDelayedOrderAlert(e.alerta);
                break;
        }
    });
```

## Event Data Structures

### OrdenProduccionActualizada
```json
{
  "orden": {
    "id": 1,
    "numero_orden": "OP-2024-001",
    "estado": "en_proceso",
    "cantidad_planificada": 1000,
    "cantidad_producida": 750,
    "eficiencia": 85.5,
    "producto": {
      "id": 1,
      "nombre_producto": "Bolsa Biodegradable 5L"
    },
    "maquina": {
      "id": 1,
      "nombre_maquina": "Inyectora 1"
    }
  },
  "tipo_cambio": "actualizada",
  "timestamp": "2024-01-15T10:30:00.000000Z"
}
```

### MaquinariaActualizada
```json
{
  "maquina": {
    "id": 1,
    "codigo_maquina": "INJ-001",
    "nombre_maquina": "Inyectora Principal",
    "estado_actual": "operativa",
    "tipo": {
      "id": 1,
      "nombre_tipo": "Inyectora"
    }
  },
  "tipo_cambio": "actualizada",
  "estado_actual": "operativa",
  "oee_actual": 87.5,
  "timestamp": "2024-01-15T10:30:00.000000Z"
}
```

### ProductoTerminadoActualizado
```json
{
  "producto": {
    "id": 1,
    "codigo_producto": "BOL-5L",
    "nombre_producto": "Bolsa Biodegradable 5L",
    "stock_actual": 2500,
    "certificado_compostaje": "EN 13432",
    "categoria": {
      "id": 1,
      "nombre_categoria": "Bolsas"
    }
  },
  "tipo_cambio": "actualizada",
  "stock_actual": 2500,
  "certificado_compostaje": "EN 13432",
  "fecha_vencimiento": "2025-12-31",
  "timestamp": "2024-01-15T10:30:00.000000Z"
}
```

### InventarioActualizado
```json
{
  "insumo": {
    "id": 1,
    "codigo_insumo": "PLA-001",
    "nombre_insumo": "PLA Granulado",
    "stock_actual": 150.50,
    "stock_minimo": 100.00,
    "categoria": {
      "id": 1,
      "nombre_categoria": "Polímeros Biodegradables"
    }
  },
  "tipo_cambio": "actualizada",
  "stock_actual": 150.50,
  "stock_minimo": 100.00,
  "estado_stock": "normal",
  "timestamp": "2024-01-15T10:30:00.000000Z"
}
```

### AlertaCreada
```json
{
  "alerta": {
    "id": 1,
    "tipo": "stock_bajo",
    "titulo": "Stock Bajo de Materia Prima",
    "mensaje": "El insumo PLA Granulado tiene stock por debajo del mínimo",
    "prioridad": "alta",
    "entidad_afectada": "insumo",
    "entidad_id": 5,
    "datos_adicionales": {
      "insumo_id": 5,
      "stock_actual": 50,
      "stock_minimo": 100
    }
  },
  "timestamp": "2024-01-15T10:30:00.000000Z"
}
```

## Frontend Integration Example

```javascript
// Vue.js Composition API example
import { ref, onMounted, onUnmounted } from 'vue';
import Echo from 'laravel-echo';

export default {
  setup() {
    const productionOrders = ref([]);
    const machines = ref([]);
    const inventory = ref([]);
    const alerts = ref([]);

    let echoChannels = [];

    onMounted(() => {
      // Connect to production orders channel
      const orderChannel = Echo.private('ordenes-produccion')
        .listen('.OrdenProduccionActualizada', (e) => {
          updateOrderInList(e.orden);
        });

      // Connect to machinery channel
      const machineChannel = Echo.private('maquinaria')
        .listen('.MaquinariaActualizada', (e) => {
          updateMachineInList(e.maquina);
        });

      // Connect to inventory channel
      const inventoryChannel = Echo.private('inventario')
        .listen('.InventarioActualizado', (e) => {
          updateInventoryItem(e.insumo);
        });

      // Connect to alerts channel
      const alertChannel = Echo.private('alertas')
        .listen('.AlertaCreada', (e) => {
          alerts.value.unshift(e.alerta);
        });

      echoChannels = [orderChannel, machineChannel, inventoryChannel, alertChannel];
    });

    onUnmounted(() => {
      // Clean up channels
      echoChannels.forEach(channel => channel.stopListening());
    });

    const updateOrderInList = (updatedOrder) => {
      const index = productionOrders.value.findIndex(order => order.id === updatedOrder.id);
      if (index !== -1) {
        productionOrders.value[index] = { ...productionOrders.value[index], ...updatedOrder };
      }
    };

    const updateMachineInList = (updatedMachine) => {
      const index = machines.value.findIndex(machine => machine.id === updatedMachine.id);
      if (index !== -1) {
        machines.value[index] = { ...machines.value[index], ...updatedMachine };
      }
    };

    const updateInventoryItem = (updatedInsumo) => {
      const index = inventory.value.findIndex(item => item.id === updatedInsumo.id);
      if (index !== -1) {
        inventory.value[index] = { ...inventory.value[index], ...updatedInsumo };
      }
    };

    return {
      productionOrders,
      machines,
      inventory,
      alerts
    };
  }
};
```

## Best Practices

1. **Authentication**: Always include Bearer token in Echo auth headers
2. **Channel Authorization**: Use private channels for sensitive data
3. **Error Handling**: Implement connection retry logic for WebSocket disconnections
4. **Performance**: Unsubscribe from channels when components unmount
5. **Data Synchronization**: Use events to update local state, not replace API calls
6. **Alert Management**: Implement alert dismissal and prioritization in frontend

### Testing Broadcasting

Para probar el sistema de broadcasting en tiempo real:

1. **Configurar Pusher** siguiendo las instrucciones en `PUSHER_SETUP.md`
2. **Iniciar servicios**:
   ```bash
   # Terminal 1: Laravel server
   php artisan serve

   # Terminal 2: Queue worker
   php artisan queue:work

   # Terminal 3: Vite dev server
   npm run dev
   ```
3. **Usar componente de prueba**: Importa `BroadcastingTest.vue` para verificar conexiones
4. **Crear datos de prueba**: Usa los endpoints API para crear órdenes, máquinas, etc.
5. **Verificar eventos**: Los cambios deberían aparecer en tiempo real en el dashboard

### Troubleshooting

- **Eventos no llegan**: Verifica configuración de Pusher y tokens de auth
- **Conexión falla**: Revisa CORS y configuración de broadcasting
- **Auth errors**: Asegúrate de que el usuario esté autenticado
- **Performance**: Monitorea el uso de Pusher para evitar límites del plan gratuito