import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Configurar Axios para autenticación basada en sesión (cookies)
window.axios.defaults.withCredentials = true;
window.axios.defaults.withXSRFToken = true;

// Obtener el token CSRF del meta tag
const csrfToken = document.querySelector('meta[name="csrf-token"]');
if (csrfToken) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken.content;
} else {
    console.error('CSRF token not found');
}

/**
 * Configuración de Laravel Echo para Broadcasting en Tiempo Real
 */
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    encrypted: true,
    // Usar autenticación basada en sesión
    authEndpoint: '/broadcasting/auth',
    auth: {
        headers: {
            'X-CSRF-TOKEN': csrfToken?.content
        }
    }
});

// Función helper para verificar conexión
window.Echo.connector.pusher.connection.bind('connected', () => {
    console.log('Connected to Pusher');
});

window.Echo.connector.pusher.connection.bind('disconnected', () => {
    console.log('Disconnected from Pusher');
});

window.Echo.connector.pusher.connection.bind('error', (error) => {
    console.error('Pusher connection error:', error);
});
