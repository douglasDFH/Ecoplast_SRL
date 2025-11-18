import { createRouter, createWebHistory } from 'vue-router';
import Dashboard from './components/Dashboard/Dashboard.vue';
import OrdenesProduccionView from './components/Ordenes/OrdenesProduccionView.vue';
import RegistroProduccionView from './components/Produccion/RegistroProduccionView.vue';
import CalidadView from './components/Calidad/CalidadView.vue';
import InventarioView from './components/Inventario/InventarioView.vue';
import MantenimientoView from './components/Mantenimiento/MantenimientoView.vue';
import ProductosView from './components/Productos/ProductosView.vue';
import MaquinasView from './components/Maquinas/MaquinasView.vue';
import FormulacionesView from './components/Formulaciones/FormulacionesView.vue';
import UsuariosView from './components/Usuarios/UsuariosView.vue';
import RolesPermisosView from './components/RolesPermisos/RolesPermisosView.vue';
import InsumosView from './components/Insumos/InsumosView.vue';
import CategoriasInsumosView from './components/Insumos/CategoriasInsumosView.vue';
import TiposMaterialesView from './components/TiposMateriales/TiposMaterialesView.vue';
import ProveedoresView from './components/Proveedores/ProveedoresView.vue';
import PanelMaquinasView from './components/PanelMaquinas/PanelMaquinasView.vue';

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
    {
        path: '/productos',
        name: 'productos',
        component: ProductosView,
    },
    {
        path: '/maquinas',
        name: 'maquinas',
        component: MaquinasView,
    },
    {
        path: '/formulaciones',
        name: 'formulaciones',
        component: FormulacionesView,
    },
    {
        path: '/usuarios',
        name: 'usuarios',
        component: UsuariosView,
    },
    {
        path: '/roles-permisos',
        name: 'roles-permisos',
        component: RolesPermisosView,
    },
    {
        path: '/insumos',
        name: 'insumos',
        component: InsumosView,
    },
    {
        path: '/categorias-insumos',
        name: 'categorias-insumos',
        component: CategoriasInsumosView,
    },
    {
        path: '/tipos-materiales',
        name: 'tipos-materiales',
        component: TiposMaterialesView,
    },
    {
        path: '/proveedores',
        name: 'proveedores',
        component: ProveedoresView,
    },
    {
        path: '/panel-maquinas',
        name: 'panel-maquinas',
        component: PanelMaquinasView,
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
