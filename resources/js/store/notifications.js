import { defineStore } from 'pinia';

export const useNotificationsStore = defineStore('notifications', {
  state: () => ({
    toasts: [], // toasts activos
    historial: [], // historial de notificaciones
    noLeidas: 0,
    drawerAbierto: false
  }),
  actions: {
    addToast(toast) {
      this.toasts.push(toast);
      this.historial.unshift({ ...toast, fecha: new Date() });
      if (this.historial.length > 20) this.historial.length = 20;
      this.noLeidas++;
    },
    removeToast(id) {
      this.toasts = this.toasts.filter(t => t.id !== id);
    },
    marcarTodasLeidas() {
      this.noLeidas = 0;
    },
    abrirDrawer() {
      this.drawerAbierto = true;
      this.marcarTodasLeidas();
    },
    cerrarDrawer() {
      this.drawerAbierto = false;
    }
  }
});
