<?php

namespace App\Observers;

use App\Models\Alerta;
use App\Events\AlertaGenerada;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

/**
 * Observer para Alerta
 *
 * Funciones:
 * - Broadcasting de alertas en tiempo real
 * - Envío de notificaciones push
 * - Log de alertas críticas
 * - Asignación automática de destinatarios
 */
class AlertaObserver
{
    /**
     * Handle the Alerta "created" event.
     */
    public function created(Alerta $alerta): void
    {
        // 1. Log de alertas críticas
        if ($alerta->severidad === 'critico') {
            Log::critical('Alerta crítica generada', [
                'id' => $alerta->id,
                'tipo' => $alerta->tipo_alerta,
                'mensaje' => $alerta->mensaje,
                'entidad' => "{$alerta->entidad_tipo}:{$alerta->entidad_id}",
            ]);
        }

        // 2. Asignar destinatario automáticamente si no tiene
        if (!$alerta->usuario_destino_id) {
            $this->asignarDestinatario($alerta);
        }

        // 3. Broadcasting en tiempo real
        broadcast(new AlertaGenerada($alerta))->toOthers();

        // 4. Enviar notificación push (si es crítica)
        if ($alerta->severidad === 'critico') {
            $this->enviarNotificacionPush($alerta);
        }
    }

    /**
     * Asigna automáticamente un destinatario según el tipo de alerta.
     */
    protected function asignarDestinatario(Alerta $alerta): void
    {
        $destinatario = null;

        switch ($alerta->tipo_alerta) {
            case 'stock_bajo':
                // Buscar admin de planta o supervisor de inventario
                $destinatario = \App\Models\User::whereHas('rol', function($q) {
                    $q->where('nombre_rol', 'Administrador de Planta');
                })->first();
                break;

            case 'maquina_parada':
            case 'parametros_proceso':
                // Buscar técnico de mantenimiento
                $destinatario = \App\Models\User::whereHas('rol', function($q) {
                    $q->where('nombre_rol', 'Técnico de Mantenimiento');
                })->first();
                break;

            case 'calidad_deficiente':
                // Buscar inspector de calidad
                $destinatario = \App\Models\User::whereHas('rol', function($q) {
                    $q->where('nombre_rol', 'Inspector de Calidad');
                })->first();
                break;

            case 'mantenimiento_vencido':
                // Buscar técnico de mantenimiento
                $destinatario = \App\Models\User::whereHas('rol', function($q) {
                    $q->where('nombre_rol', 'Técnico de Mantenimiento');
                })->first();
                break;

            case 'meta_no_cumplida':
                // Buscar gerencia
                $destinatario = \App\Models\User::whereHas('rol', function($q) {
                    $q->where('nombre_rol', 'Gerencia');
                })->first();
                break;

            default:
                // Por defecto, admin de planta
                $destinatario = \App\Models\User::whereHas('rol', function($q) {
                    $q->where('nombre_rol', 'Administrador de Planta');
                })->first();
                break;
        }

        if ($destinatario) {
            $alerta->usuario_destino_id = $destinatario->id;
            $alerta->saveQuietly(); // No disparar observer nuevamente
        }
    }

    /**
     * Envía notificación push al usuario destinatario.
     */
    protected function enviarNotificacionPush(Alerta $alerta): void
    {
        if (!$alerta->usuario_destino_id) {
            return;
        }

        $usuario = $alerta->usuario;

        if (!$usuario) {
            return;
        }

        // Aquí se integraría con sistema de notificaciones push
        // Por ejemplo: Firebase Cloud Messaging, Pusher Beams, etc.

        // Por ahora solo log
        Log::info('Notificación push enviada', [
            'usuario' => $usuario->email,
            'alerta_id' => $alerta->id,
            'titulo' => $alerta->titulo,
        ]);

        // Ejemplo con Laravel Notifications (si está configurado):
        // $usuario->notify(new \App\Notifications\AlertaCritica($alerta));
    }

    /**
     * Handle the Alerta "updated" event.
     */
    public function updated(Alerta $alerta): void
    {
        // Si la alerta fue marcada como leída, registrar
        if ($alerta->wasChanged('leida') && $alerta->leida) {
            Log::info('Alerta marcada como leída', [
                'id' => $alerta->id,
                'tipo' => $alerta->tipo_alerta,
                'leida_por' => Auth::user()?->email ?? 'sistema',
            ]);
        }
    }

    /**
     * Handle the Alerta "deleted" event.
     */
    public function deleted(Alerta $alerta): void
    {
        Log::info('Alerta eliminada', [
            'id' => $alerta->id,
            'tipo' => $alerta->tipo_alerta,
            'fue_atendida' => $alerta->leida,
        ]);
    }
}
