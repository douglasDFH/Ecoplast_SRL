<template>
    <div class="min-h-screen" style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);">
        <div class="flex h-screen overflow-hidden">
            <!-- Sidebar -->
            <aside class="w-64 flex-shrink-0 p-4 overflow-y-auto h-screen scrollbar-custom">
                <!-- Logo -->
                <div class="p-6 mb-6 rounded-3xl" style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); box-shadow: 20px 20px 60px #b3d4f1, -20px -20px 60px #f3ffff;">
                    <div class="flex items-center justify-center">
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center mr-3" style="background: linear-gradient(145deg, #2E7D32, #4CAF50); box-shadow: 8px 8px 16px #b3d4f1, -8px -8px 16px #f3ffff;">
                            <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-lg font-bold" style="color: #263238;">ECOPLAST</div>
                            <div class="text-xs" style="color: #607D8B;">Sistema Industrial</div>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="space-y-3">
                    <router-link
                        to="/"
                        class="flex items-center px-5 py-3 rounded-2xl font-medium transition-all active:scale-95"
                        :class="$route.path === '/' ? 'nav-link-active' : 'nav-link'"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span>Dashboard</span>
                    </router-link>

                    <router-link
                        to="/ordenes"
                        class="flex items-center px-5 py-3 rounded-2xl font-medium transition-all active:scale-95"
                        :class="$route.path === '/ordenes' ? 'nav-link-active' : 'nav-link'"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Órdenes Producción</span>
                    </router-link>

                    <router-link
                        to="/panel-maquinas"
                        class="flex items-center px-5 py-3 rounded-2xl font-medium transition-all active:scale-95"
                        :class="$route.path === '/produccion' ? 'nav-link-active' : 'nav-link'"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <span>Registro Producción</span>
                    </router-link>

                    <router-link
                        to="/inventario"
                        class="flex items-center px-5 py-3 rounded-2xl font-medium transition-all active:scale-95"
                        :class="$route.path === '/inventario' ? 'nav-link-active' : 'nav-link'"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <span>Inventario</span>
                    </router-link>

                    <router-link
                        to="/calidad"
                        class="flex items-center px-5 py-3 rounded-2xl font-medium transition-all active:scale-95"
                        :class="$route.path === '/calidad' ? 'nav-link-active' : 'nav-link'"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Calidad</span>
                    </router-link>

                    <router-link
                        to="/mantenimiento"
                        class="flex items-center px-5 py-3 rounded-2xl font-medium transition-all active:scale-95"
                        :class="$route.path === '/mantenimiento' ? 'nav-link-active' : 'nav-link'"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>Mantenimiento</span>
                    </router-link>

                    <div class="mt-6 mb-3">
                        <p class="px-5 text-xs font-semibold uppercase tracking-wider" style="color: #607D8B;">Catálogos</p>
                    </div>

                    <router-link
                        to="/productos"
                        class="flex items-center px-5 py-3 rounded-2xl font-medium transition-all active:scale-95"
                        :class="$route.path === '/productos' ? 'nav-link-active' : 'nav-link'"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <span>Productos</span>
                    </router-link>

                    <router-link
                        to="/maquinas"
                        class="flex items-center px-5 py-3 rounded-2xl font-medium transition-all active:scale-95"
                        :class="$route.path === '/maquinas' ? 'nav-link-active' : 'nav-link'"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                        </svg>
                        <span>Máquinas</span>
                    </router-link>

                    <router-link
                        to="/formulaciones"
                        class="flex items-center px-5 py-3 rounded-2xl font-medium transition-all active:scale-95"
                        :class="$route.path === '/formulaciones' ? 'nav-link-active' : 'nav-link'"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                        </svg>
                        <span>Formulaciones</span>
                    </router-link>

                    <router-link
                        to="/usuarios"
                        class="flex items-center px-5 py-3 rounded-2xl font-medium transition-all active:scale-95"
                        :class="$route.path === '/usuarios' ? 'nav-link-active' : 'nav-link'"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <span>Usuarios</span>
                    </router-link>

                    <router-link
                        to="/roles-permisos"
                        class="flex items-center px-5 py-3 rounded-2xl font-medium transition-all active:scale-95"
                        :class="$route.path === '/roles-permisos' ? 'nav-link-active' : 'nav-link'"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <span>Roles y Permisos</span>
                    </router-link>

                    <div class="mt-6 mb-3">
                        <p class="px-5 text-xs font-semibold uppercase tracking-wider" style="color: #607D8B;">Inventario</p>
                    </div>

                    <router-link
                        to="/insumos"
                        class="flex items-center px-5 py-3 rounded-2xl font-medium transition-all active:scale-95"
                        :class="$route.path === '/insumos' ? 'nav-link-active' : 'nav-link'"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <span>Insumos</span>
                    </router-link>

                    <router-link
                        to="/categorias-insumos"
                        class="flex items-center px-5 py-3 rounded-2xl font-medium transition-all active:scale-95"
                        :class="$route.path === '/categorias-insumos' ? 'nav-link-active' : 'nav-link'"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        <span>Categorías de Insumos</span>
                    </router-link>

                    <router-link
                        to="/tipos-materiales"
                        class="flex items-center px-5 py-3 rounded-2xl font-medium transition-all active:scale-95"
                        :class="$route.path === '/tipos-materiales' ? 'nav-link-active' : 'nav-link'"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        <span>Tipos de Materiales</span>
                    </router-link>

                    <router-link
                        to="/proveedores"
                        class="flex items-center px-5 py-3 rounded-2xl font-medium transition-all active:scale-95"
                        :class="$route.path === '/proveedores' ? 'nav-link-active' : 'nav-link'"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span>Proveedores</span>
                    </router-link>
                </nav>
            </aside>

            <!-- Main Content -->
            <div class="flex-1 flex flex-col overflow-hidden">
                <!-- Top Navbar -->
                <header class="m-4 px-6 py-4 rounded-3xl" style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); box-shadow: 15px 15px 30px #b3d4f1, -15px -15px 30px #f3ffff;">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold" style="color: #263238;">{{ pageTitle }}</h1>
                            <p class="text-sm mt-1" style="color: #607D8B;">{{ pageSubtitle }}</p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <!-- Campana de notificaciones -->
                            <button class="relative focus:outline-none hover-button rounded-xl p-2" style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); box-shadow: 4px 4px 8px #b3d4f1, -4px -4px 8px #f3ffff;" @click="abrirDrawer">
                              <svg class="w-7 h-7 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                              </svg>
                              <span v-if="notificacionesNoLeidas > 0" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full px-2 py-0.5 shadow-neumorphic-inset">{{ notificacionesNoLeidas }}</span>
                            </button>
                            <!-- Usuario -->
                            <div class="flex items-center space-x-3 px-4 py-2 rounded-2xl" style="background: linear-gradient(145deg, #e3f2fd, #bbdefb); box-shadow: 8px 8px 16px #b3d4f1, -8px -8px 16px #f3ffff;">
                                <div class="text-right">
                                    <p class="text-sm font-semibold" style="color: #263238;">Admin</p>
                                    <p class="text-xs" style="color: #607D8B;">Administrador</p>
                                </div>
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: linear-gradient(145deg, #2E7D32, #4CAF50); box-shadow: 5px 5px 10px #b3d4f1, -5px -5px 10px #f3ffff;">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                            </div>
                            <!-- Logout -->
                            <form action="/logout" method="POST">
                                <button
                                    type="submit"
                                    class="flex items-center px-5 py-3 text-sm font-medium text-white rounded-2xl transition-all active:scale-95 logout-btn"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    <span>Cerrar Sesión</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto px-4 pb-4">
                    <router-view />
                    <!-- ToastNotification global -->
                    <ToastNotification />
                    <!-- Drawer de notificaciones -->
                    <NotificationDrawer :abierto="drawerAbierto" :historial="historialNotificaciones" @close="cerrarDrawer" @marcarLeidas="marcarTodasLeidas" />
                </main>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import { useNotificationsStore } from '../../store/notifications';
import ToastNotification from '../Notificaciones/ToastNotification.vue';
import NotificationDrawer from '../Notificaciones/NotificationDrawer.vue';

const route = useRoute();

const notifications = useNotificationsStore();
const notificacionesNoLeidas = computed(() => notifications.noLeidas);
const drawerAbierto = computed(() => notifications.drawerAbierto);
const historialNotificaciones = computed(() => notifications.historial);

function abrirDrawer() {
  notifications.abrirDrawer();
}
function cerrarDrawer() {
  notifications.cerrarDrawer();
}
function marcarTodasLeidas() {
  notifications.marcarTodasLeidas();
}

const pageTitle = computed(() => {
    const titles = {
        '/': 'Dashboard Gerencia',
        '/ordenes': 'Órdenes de Producción',
        '/produccion': 'Registro de Producción',
        '/panel-maquinas': 'Panel de control de máquinas en tiempo real',
        '/inventario': 'Gestión de Inventario',
        '/calidad': 'Control de Calidad',
        '/mantenimiento': 'Mantenimiento',
        '/productos': 'Productos Terminados',
        '/maquinas': 'Maquinaria',
        '/formulaciones': 'Formulaciones',
        '/usuarios': 'Usuarios y Roles',
        '/insumos': 'Gestión de Insumos',
        '/categorias-insumos': 'Categorías de Insumos',
        '/tipos-materiales': 'Tipos de Materiales',
        '/proveedores': 'Proveedores',
    };
    return titles[route.path] || 'Ecoplast SRL';
});

const pageSubtitle = computed(() => {
    const subtitles = {
        '/': 'Panel de control ejecutivo en tiempo real',
        '/ordenes': 'Gestión y seguimiento de órdenes de producción',
        '/produccion': 'Registro de producción para operadores',
        '/inventario': 'Control de stock de insumos y productos',
        '/calidad': 'Inspecciones y control de calidad',
        '/mantenimiento': 'Gestión de mantenimiento de maquinaria',
        '/productos': 'Catálogo de productos biodegradables',
        '/maquinas': 'Control y mantenimiento de maquinaria',
        '/formulaciones': 'Fórmulas de producción biodegradable',
        '/usuarios': 'Gestión de usuarios y permisos',
        '/insumos': 'Control de insumos biodegradables y materias primas',
        '/categorias-insumos': 'Clasificación de insumos por categoría',
        '/tipos-materiales': 'Gestión de tipos de materiales biodegradables',
        '/proveedores': 'Gestión de proveedores de insumos biodegradables',
        '/panel-maquinas': 'Panel de control de máquinas en tiempo real'
    };
    return subtitles[route.path] || 'Bienvenido al sistema de gestión';
});
</script>

<style scoped>
/* Estilos neomórficos para navegación */
.nav-link {
    background: linear-gradient(145deg, #e3f2fd, #bbdefb);
    box-shadow: 10px 10px 20px #b3d4f1, -10px -10px 20px #f3ffff;
    color: #455A64;
}

.nav-link:hover {
    box-shadow: 8px 8px 16px #b3d4f1, -8px -8px 16px #f3ffff;
}

.nav-link-active {
    background: linear-gradient(145deg, #e3f2fd, #bbdefb);
    box-shadow: inset 8px 8px 16px #b3d4f1, inset -8px -8px 16px #f3ffff;
    color: #1565C0;
    font-weight: 600;
}

/* Botón logout */
.logout-btn {
    background: linear-gradient(145deg, #455A64, #263238);
    box-shadow: 8px 8px 16px #b3d4f1, -8px -8px 16px #f3ffff;
}

.logout-btn:hover {
    box-shadow: 6px 6px 12px #b3d4f1, -6px -6px 12px #f3ffff;
}

/* Scrollbar personalizado */
.scrollbar-custom::-webkit-scrollbar {
    width: 8px;
}

.scrollbar-custom::-webkit-scrollbar-track {
    background: linear-gradient(145deg, #e3f2fd, #bbdefb);
    border-radius: 10px;
    margin: 10px 0;
}

.scrollbar-custom::-webkit-scrollbar-thumb {
    background: linear-gradient(145deg, #1565C0, #1976D2);
    border-radius: 10px;
    box-shadow: inset 2px 2px 5px rgba(0,0,0,0.1);
}

.scrollbar-custom::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(145deg, #1976D2, #2196F3);
}

/* Para Firefox */
.scrollbar-custom {
    scrollbar-width: thin;
    scrollbar-color: #1565C0 #e3f2fd;
}
</style>