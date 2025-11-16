# Configuración de Pusher para Broadcasting en Tiempo Real

## 1. Crear cuenta en Pusher

1. Ve a [https://pusher.com/](https://pusher.com/) y crea una cuenta gratuita
2. Una vez registrado, ve al dashboard y crea una nueva aplicación (app)
3. Selecciona el plan gratuito (hasta 100 conexiones simultáneas)

## 2. Obtener credenciales de Pusher

Después de crear la aplicación, ve a la pestaña "App Keys" y copia:

- **App ID**: `PUSHER_APP_ID`
- **Key**: `PUSHER_APP_KEY`
- **Secret**: `PUSHER_APP_SECRET`
- **Cluster**: `PUSHER_APP_CLUSTER` (ej: `us2`, `eu`, `ap1`)

## 3. Configurar variables de entorno

Copia el archivo `.env.example` a `.env` y actualiza las siguientes variables:

```env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=123456
PUSHER_APP_KEY=abcdef123456
PUSHER_APP_SECRET=secret123456
PUSHER_APP_CLUSTER=us2
```

## 4. Instalar dependencias de frontend

```bash
npm install --save laravel-echo pusher-js
```

## 5. Configurar Laravel Echo

Crea el archivo `resources/js/bootstrap.js` o actualízalo:

```javascript
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    encrypted: true,
    auth: {
        headers: {
            Authorization: `Bearer ${localStorage.getItem('auth_token')}`
        }
    }
});
```

## 6. Configurar Vite para variables de entorno

En `vite.config.js`, agrega:

```javascript
export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
    ],
    define: {
        __VUE_OPTIONS_API__: true,
        __VUE_PROD_DEVTOOLS__: false,
    },
});
```

## 7. Variables de entorno en el frontend

En tu archivo `.env`, agrega las variables para el frontend:

```env
MIX_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
MIX_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

## 8. Probar la conexión

Ejecuta los siguientes comandos para probar:

```bash
# Limpiar cache
php artisan config:clear
php artisan cache:clear

# Verificar configuración
php artisan tinker
>>> config('broadcasting.default')
>>> config('broadcasting.connections.pusher')
```

## 9. Ejecutar el servidor

```bash
# Terminal 1: Servidor Laravel
php artisan serve

# Terminal 2: Queue worker (para broadcasting)
php artisan queue:work

# Terminal 3: Vite (desarrollo frontend)
npm run dev
```

## 10. Verificar broadcasting

Para probar que el broadcasting funciona:

1. Abre tu aplicación en el navegador
2. Abre las herramientas de desarrollo (F12)
3. Ve a la pestaña "Network" y filtra por "pusher"
4. Deberías ver conexiones WebSocket exitosas

## Solución de problemas

### Error: "Pusher : Could not connect to Pusher"

- Verifica que las credenciales de Pusher sean correctas
- Asegúrate de que el cluster sea correcto
- Verifica que no haya firewall bloqueando las conexiones

### Error: "Authentication failed"

- Verifica que el token de autenticación se esté enviando correctamente
- Asegúrate de que el usuario esté autenticado
- Revisa las rutas de canales en `routes/channels.php`

### Error: "No events are being received"

- Verifica que los eventos se estén disparando en el backend
- Asegúrate de que los nombres de canales coincidan
- Revisa la consola del navegador por errores de JavaScript

## Configuración de producción

Para producción, considera:

1. Usar Redis como driver de queue en lugar de database
2. Configurar un servidor de Redis dedicado
3. Usar HTTPS para todas las conexiones
4. Configurar límites de rate limiting apropiados
5. Monitorear el uso de Pusher para evitar exceder límites del plan gratuito