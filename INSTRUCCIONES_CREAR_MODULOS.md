# GUÍA COMPLETA: Crear Módulos Faltantes en Ecoplast SRL

## MÓDULOS A CREAR:
1. Productos
2. Máquinas
3. Usuarios
4. Formulaciones

---

## 1. ACTUALIZAR SIDEBAR (AppLayout.vue)

Agregar DESPUÉS de la línea 88 (después del link de Mantenimiento):

```vue
<div class="mt-6 mb-3">
    <p class="px-5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Catálogos</p>
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
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
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
```

También actualizar en pageTitle (línea 175):

```javascript
const pageTitle = computed(() => {
    const titles = {
        '/': 'Dashboard Gerencia',
        '/ordenes': 'Órdenes de Producción',
        '/produccion': 'Registro de Producción',
        '/inventario': 'Gestión de Inventario',
        '/calidad': 'Control de Calidad',
        '/mantenimiento': 'Mantenimiento',
        '/productos': 'Productos Terminados',
        '/maquinas': 'Maquinaria',
        '/formulaciones': 'Formulaciones',
        '/usuarios': 'Usuarios y Roles',
    };
    return titles[route.path] || 'Ecoplast SRL';
});
```

Y en pageSubtitle (línea 186):

```javascript
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
    };
    return subtitles[route.path] || 'Bienvenido al sistema de gestión';
});
```

---

## 2. ACTUALIZAR ROUTER (router.js)

Agregar las importaciones al inicio:

```javascript
import ProductosView from './components/Productos/ProductosView.vue';
import MaquinasView from './components/Maquinas/MaquinasView.vue';
import FormulacionesView from './components/Formulaciones/FormulacionesView.vue';
import UsuariosView from './components/Usuarios/UsuariosView.vue';
```

Agregar las rutas al array routes:

```javascript
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
```

---

## 3. ACTUALIZAR WEB.PHP

Agregar después de la ruta de mantenimiento (línea ~42):

```php
Route::get('/productos', function () {
    return view('dashboard');
});

Route::get('/maquinas', function () {
    return view('dashboard');
});

Route::get('/formulaciones', function () {
    return view('dashboard');
});

Route::get('/usuarios', function () {
    return view('dashboard');
});
```

---

## 4. CREAR COMPONENTES VUE

Los archivos son demasiado largos para este documento.
Te los voy a crear individualmente en el siguiente paso.

POR FAVOR ESPERA A QUE TE CREE CADA COMPONENTE.
