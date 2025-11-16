<template>
  <transition name="drawer-fade">
    <div v-if="abierto" class="fixed inset-0 z-50 flex justify-end">
      <div class="bg-black bg-opacity-40 w-full h-full" @click="$emit('close')"></div>
      <aside class="w-full max-w-md h-full bg-white rounded-l-3xl shadow-neumorphic flex flex-col" style="background: linear-gradient(145deg, #e3f2fd, #bbdefb);">
        <div class="flex items-center justify-between p-6 border-b border-blue-200">
          <h2 class="text-2xl font-bold" style="color: #263238;">Notificaciones</h2>
          <button @click="$emit('close')" class="hover-button rounded-xl px-3 py-2 transition-all" style="color: #607D8B;">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        <div class="flex-1 overflow-y-auto p-6 space-y-4">
          <div v-if="historial.length === 0" class="text-center text-blue-400 text-lg py-12">
            No hay notificaciones recientes
          </div>
          <div v-else>
            <div v-for="toast in historial" :key="toast.id" class="rounded-2xl px-5 py-4 mb-3 shadow-neumorphic flex items-start space-x-3" :style="getToastStyle(toast)">
              <div class="text-2xl flex-shrink-0">
                <span v-if="toast.tipo === 'success'">✅</span>
                <span v-else-if="toast.tipo === 'error'">❌</span>
                <span v-else-if="toast.tipo === 'warning'">⚠️</span>
                <span v-else-if="toast.tipo === 'info'">ℹ️</span>
                <span v-else>🔔</span>
              </div>
              <div class="flex-1">
                <p class="font-bold text-base mb-1" :style="{ color: '#263238' }">{{ toast.titulo }}</p>
                <p class="text-sm mb-1" :style="{ color: '#607D8B' }">{{ toast.mensaje }}</p>
                <p class="text-xs" style="color: #90A4AE;">{{ formatearFecha(toast.fecha) }}</p>
              </div>
            </div>
          </div>
        </div>
        <div class="p-6 border-t border-blue-200 flex justify-end">
          <button @click="$emit('marcarLeidas')" class="px-6 py-3 rounded-2xl font-semibold transition-all hover-button" style="background: linear-gradient(145deg, #607D8B, #455A64); box-shadow: 12px 12px 24px #4a5a63, -12px -12px 24px #6e8495; color: white;">
            Marcar todas como leídas
          </button>
        </div>
      </aside>
    </div>
  </transition>
</template>

<script setup>
const props = defineProps({
  abierto: Boolean,
  historial: Array
});
const emit = defineEmits(['close', 'marcarLeidas']);

function getToastStyle(toast) {
  if (toast.tipo === 'success') {
    return 'background: linear-gradient(145deg, #e8f5e9, #b9f6ca); box-shadow: 8px 8px 16px #b3d4f1, -8px -8px 16px #f3ffff;';
  }
  if (toast.tipo === 'error') {
    return 'background: linear-gradient(145deg, #ffebee, #ffcdd2); box-shadow: 8px 8px 16px #b3d4f1, -8px -8px 16px #f3ffff;';
  }
  if (toast.tipo === 'warning') {
    return 'background: linear-gradient(145deg, #fff8e1, #ffe082); box-shadow: 8px 8px 16px #b3d4f1, -8px -8px 16px #f3ffff;';
  }
  if (toast.tipo === 'info') {
    return 'background: linear-gradient(145deg, #e3f2fd, #bbdefb); box-shadow: 8px 8px 16px #b3d4f1, -8px -8px 16px #f3ffff;';
  }
  return 'background: linear-gradient(145deg, #e3f2fd, #bbdefb); box-shadow: 8px 8px 16px #b3d4f1, -8px -8px 16px #f3ffff;';
}

function formatearFecha(fecha) {
  if (!fecha) return '';
  return new Date(fecha).toLocaleString('es-ES', { hour: '2-digit', minute: '2-digit', day: '2-digit', month: 'short' });
}
</script>

<style scoped>
.shadow-neumorphic {
  box-shadow: 8px 8px 16px #b3d4f1, -8px -8px 16px #f3ffff;
}
.drawer-fade-enter-active, .drawer-fade-leave-active {
  transition: all 0.3s cubic-bezier(.4,2,.6,1);
}
.drawer-fade-enter-from, .drawer-fade-leave-to {
  opacity: 0;
  transform: translateX(100px) scale(0.98);
}
</style>
