import axios from 'axios';

// Configuración base de la API
const apiClient = axios.create({
    baseURL: '/api',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
});

// Interceptor para agregar el token de autenticación
apiClient.interceptors.request.use(
    (config) => {
        const token = localStorage.getItem('auth_token');
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

// Interceptor para manejar errores de respuesta
apiClient.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response) {
            // Error de respuesta del servidor
            switch (error.response.status) {
                case 401:
                    // No autorizado - redirigir al login
                    localStorage.removeItem('auth_token');
                    window.location.href = '/login';
                    break;
                case 403:
                    console.error('Acceso denegado');
                    break;
                case 404:
                    console.error('Recurso no encontrado');
                    break;
                case 422:
                    // Error de validación
                    console.error('Error de validación:', error.response.data.errors);
                    break;
                case 500:
                    console.error('Error del servidor');
                    break;
            }
        }
        return Promise.reject(error);
    }
);

export default apiClient;
