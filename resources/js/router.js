import { createRouter, createWebHistory } from 'vue-router';
import Dashboard from './components/Dashboard/Dashboard.vue';
import OrdenesProduccionView from './components/Ordenes/OrdenesProduccionView.vue';
import RegistroProduccionView from './components/Produccion/RegistroProduccionView.vue';
import CalidadView from './components/Calidad/CalidadView.vue';
import InventarioView from './components/Inventario/InventarioView.vue';
import MantenimientoView from './components/Mantenimiento/MantenimientoView.vue';

const routes = [
    {
        path: '/',
        name: 'dashboard',
        component: Dashboard,
    },
    {
        path: '/ordenes',
        name: 'ordenes',
        component: OrdenesProduccionView,
    },
    {
        path: '/produccion',
        name: 'produccion',
        component: RegistroProduccionView,
    },
    {
        path: '/calidad',
        name: 'calidad',
        component: CalidadView,
    },
    {
        path: '/inventario',
        name: 'inventario',
        component: InventarioView,
    },
    {
        path: '/mantenimiento',
        name: 'mantenimiento',
        component: MantenimientoView,
    },
    // Aquí se pueden añadir más rutas en el futuro
    // { path: '/mantenimiento', name: 'mantenimiento', component: MantenimientoComponent },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
