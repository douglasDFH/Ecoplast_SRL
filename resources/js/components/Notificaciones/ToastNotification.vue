<template>
  <transition-group name="toast-fade" tag="div" class="fixed top-6 right-6 z-50 flex flex-col space-y-4">
    <div v-for="toast in toasts" :key="toast.id" class="rounded-3xl shadow-neumorphic px-6 py-4 min-w-[320px] flex items-center space-x-4"
         :style="getToastStyle(toast)">
      <div class="flex-shrink-0 text-2xl">
        <span v-if="toast.tipo === 'success'">✅</span>
        <span v-else-if="toast.tipo === 'error'">❌</span>
        <span v-else-if="toast.tipo === 'warning'">⚠️</span>
        <span v-else-if="toast.tipo === 'info'">ℹ️</span>
        <span v-else>🔔</span>
      </div>
      <div class="flex-1">
        <p class="font-bold text-lg mb-1" :style="{ color: '#263238' }">{{ toast.titulo }}</p>
        <p class="text-sm" :style="{ color: '#607D8B' }">{{ toast.mensaje }}</p>
      </div>
      <button @click="cerrarToast(toast.id)" class="ml-2 text-gray-400 hover:text-gray-600 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>
  </transition-group>
</template>

<script setup>
import { ref } from 'vue';

const toasts = ref([]);
let nextId = 1;

function mostrarToast({ titulo, mensaje, tipo = 'info', duracion = 4000 }) {
  const id = nextId++;
  toasts.value.push({ id, titulo, mensaje, tipo });
  if (tipo === 'error' || tipo === 'warning') {
    reproducirSonido(tipo);
  }
  setTimeout(() => cerrarToast(id), duracion);
}

function cerrarToast(id) {
  toasts.value = toasts.value.filter(t => t.id !== id);
}

function getToastStyle(toast) {
  if (toast.tipo === 'success') {
    return 'background: linear-gradient(145deg, #e8f5e9, #b9f6ca); box-shadow: 12px 12px 24px #b3d4f1, -12px -12px 24px #f3ffff;';
  }
  if (toast.tipo === 'error') {
    return 'background: linear-gradient(145deg, #ffebee, #ffcdd2); box-shadow: 12px 12px 24px #b3d4f1, -12px -12px 24px #f3ffff;';
  }
  if (toast.tipo === 'warning') {
    return 'background: linear-gradient(145deg, #fff8e1, #ffe082); box-shadow: 12px 12px 24px #b3d4f1, -12px -12px 24px #f3ffff;';
  }
  if (toast.tipo === 'info') {
    return 'background: linear-gradient(145deg, #e3f2fd, #bbdefb); box-shadow: 12px 12px 24px #b3d4f1, -12px -12px 24px #f3ffff;';
  }
  return 'background: linear-gradient(145deg, #e3f2fd, #bbdefb); box-shadow: 12px 12px 24px #b3d4f1, -12px -12px 24px #f3ffff;';
}

function reproducirSonido(tipo) {
  let audio;
  if (tipo === 'error') {
    audio = new Audio('/sounds/error.mp3');
  } else if (tipo === 'warning') {
    audio = new Audio('/sounds/warning.mp3');
  }
  if (audio) audio.play();
}

// Exponer función global para mostrar toasts
if (typeof window !== 'undefined') {
  window.mostrarToast = mostrarToast;
}
</script>

<style scoped>
.shadow-neumorphic {
  box-shadow: 12px 12px 24px #b3d4f1, -12px -12px 24px #f3ffff;
}
.toast-fade-enter-active, .toast-fade-leave-active {
  transition: all 0.4s cubic-bezier(.4,2,.6,1);
}
.toast-fade-enter-from, .toast-fade-leave-to {
  opacity: 0;
  transform: translateY(-30px) scale(0.95);
}
</style>
