import './bootstrap';

import { createApp } from 'vue';
import { createPinia } from 'pinia';
import router from './router';
import AppLayout from './components/Layout/AppLayout.vue';

// Crear la aplicación Vue con AppLayout como componente raíz
const app = createApp(AppLayout);

// Usar Pinia para el manejo de estado
const pinia = createPinia();
app.use(pinia);

// Usar Vue Router para el enrutamiento del frontend
app.use(router);

// Montar la aplicación en el elemento #app de nuestro layout
app.mount('#app');

console.log('✅ Vue App con AppLayout montada correctamente');


