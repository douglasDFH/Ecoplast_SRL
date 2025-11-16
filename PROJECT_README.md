# Ecoplast SRL - Sistema de Gestión de Plásticos Biodegradables

Sistema completo de gestión para la producción de plásticos biodegradables con dashboard en tiempo real, desarrollado con Laravel 12, Vue.js 3 y Pusher.

## 🌟 Características

- **🏭 Gestión de Producción**: Órdenes de producción, máquinas y eficiencia OEE
- **📦 Control de Inventario**: Insumos biodegradables con alertas de stock
- **📊 Dashboard en Tiempo Real**: Broadcasting con Pusher para actualizaciones live
- **🚨 Sistema de Alertas**: Notificaciones automáticas para eventos críticos
- **🔐 Autenticación**: Laravel Sanctum para API segura
- **📱 Frontend Moderno**: Vue.js 3 con Pinia y Tailwind CSS

## 🚀 Inicio Rápido

### 1. Clonar y Instalar

```bash
# Clonar repositorio
git clone <repository-url>
cd ecoplast-srl

# Instalar dependencias PHP
composer install

# Instalar dependencias Node.js
npm install

# Copiar archivo de entorno
cp .env.example .env

# Generar key de aplicación
php artisan key:generate
```

### 2. Configurar Base de Datos

```bash
# Crear base de datos MySQL
# Configurar credenciales en .env

# Ejecutar migraciones
php artisan migrate

# Ejecutar seeders (datos de prueba)
php artisan db:seed
```

### 3. Configurar Pusher (Opcional - Para Broadcasting)

```bash
# Seguir instrucciones en PUSHER_SETUP.md
# Obtener credenciales de https://pusher.com
# Configurar variables en .env
```

### 4. Construir y Ejecutar

```bash
# Construir assets
npm run build
# o para desarrollo
npm run dev

# Iniciar servidor
php artisan serve

# Para desarrollo completo (servidor + queue + vite)
npm run start:dev
```

## 📋 Requisitos del Sistema

- **PHP**: 8.2 o superior
- **Node.js**: 18.x o superior
- **MySQL**: 8.0 o superior
- **Composer**: Última versión
- **Cuenta Pusher**: Para broadcasting (opcional)

## 🏗️ Arquitectura

### Backend (Laravel)
- **API RESTful** con recursos JSON
- **Broadcasting en Tiempo Real** con Laravel Echo
- **Autenticación** con Laravel Sanctum
- **Base de Datos** con Eloquent ORM
- **Queues** para procesamiento en background

### Frontend (Vue.js)
- **Componentes Reactivos** con Vue 3 Composition API
- **Gestión de Estado** con Pinia
- **UI Moderna** con Tailwind CSS
- **Gráficos** con Chart.js
- **Broadcasting** con Laravel Echo

## 📊 Dashboard en Tiempo Real

### KPIs Principales
- Órdenes de producción activas
- Estado de máquinas operativas
- Alertas de stock bajo
- Eficiencia promedio (OEE)

### Actualizaciones Live
- Cambios en órdenes de producción
- Estado de maquinaria
- Niveles de inventario
- Alertas del sistema

## 🔧 Configuración de Desarrollo

### Variables de Entorno (.env)

```env
# Base de datos
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecoplast_srl
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Broadcasting (Pusher)
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=your_cluster

# Queue
QUEUE_CONNECTION=database
```

### Comandos Útiles

```bash
# Limpiar cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Ejecutar tests
php artisan test

# Ver estado de migraciones
php artisan migrate:status

# Ver estado de queues
php artisan queue:status

# Reiniciar base de datos
php artisan migrate:fresh --seed
```

## 📚 Documentación

- **API Documentation**: `API_DOCUMENTATION.md`
- **Pusher Setup**: `PUSHER_SETUP.md`
- **Base de Datos**: Ver archivos de migración en `database/migrations/`

## 🧪 Testing

### Broadcasting
- Usar componente `BroadcastingTest.vue` para verificar conexiones
- Crear datos de prueba con seeders
- Verificar eventos en tiempo real

### API
```bash
# Ejecutar tests de API
php artisan test --filter=Api
```

## 🚀 Despliegue

### Producción
1. Configurar servidor web (Nginx/Apache)
2. Configurar base de datos MySQL
3. Configurar Pusher para producción
4. Configurar queues (Redis recomendado)
5. Ejecutar `npm run build`
6. Configurar variables de entorno

### Docker (Próximamente)
- Configuración Docker para desarrollo
- Docker Compose para servicios completos

## 🤝 Contribución

1. Fork el proyecto
2. Crear rama para feature (`git checkout -b feature/AmazingFeature`)
3. Commit cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abrir Pull Request

## 📝 Licencia

Este proyecto está bajo la Licencia MIT - ver el archivo [LICENSE](LICENSE) para más detalles.

## 👥 Equipo

- **Desarrollador**: GitHub Copilot
- **Framework**: Laravel 12 + Vue.js 3
- **Cliente**: Ecoplast SRL

## 📞 Soporte

Para soporte técnico o preguntas:
- Revisar documentación en `API_DOCUMENTATION.md`
- Verificar configuración en `PUSHER_SETUP.md`
- Abrir issue en el repositorio

---

**Ecoplast SRL** - Innovando en plásticos biodegradables con tecnología de vanguardia.