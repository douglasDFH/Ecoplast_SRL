<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Canales para el sistema de gestión biodegradable Ecoplast SRL
Broadcast::channel('ordenes-produccion', function ($user) {
    // Solo usuarios autenticados pueden escuchar actualizaciones de órdenes
    return $user !== null;
});

Broadcast::channel('maquinaria', function ($user) {
    // Solo usuarios autenticados pueden escuchar actualizaciones de maquinaria
    return $user !== null;
});

Broadcast::channel('productos', function ($user) {
    // Solo usuarios autenticados pueden escuchar actualizaciones de productos
    return $user !== null;
});

Broadcast::channel('inventario', function ($user) {
    // Solo usuarios autenticados pueden escuchar actualizaciones de inventario
    return $user !== null;
});

Broadcast::channel('alertas', function ($user) {
    // Solo usuarios autenticados pueden escuchar alertas del sistema
    return $user !== null;
});