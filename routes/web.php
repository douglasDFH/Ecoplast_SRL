<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

// Ruta principal redirige al login
Route::get('/', function () {
    return redirect()->route('login');
});

// Rutas de autenticación
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

// Dashboard y rutas SPA (protegido por autenticación)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Rutas para la SPA de Vue
    Route::get('/ordenes', function () {
        return view('dashboard');
    });

    Route::get('/produccion', function () {
        return view('dashboard');
    });

    Route::get('/calidad', function () {
        return view('dashboard');
    });

    Route::get('/inventario', function () {
        return view('dashboard');
    });

    Route::get('/mantenimiento', function () {
        return view('dashboard');
    });

    Route::get('/productos', function () {
        return view('dashboard');
    });

    Route::get('/maquinas', function () {
        return view('dashboard');
    });

    Route::get('/formulaciones', function () {
        return view('dashboard');
    });

    Route::get('/usuarios', function () {
        return view('dashboard');
    });

    Route::get('/insumos', function () {
        return view('dashboard');
    });

    Route::get('/categorias-insumos', function () {
        return view('dashboard');
    });

    Route::get('/tipos-materiales', function () {
        return view('dashboard');
    });

    Route::get('/proveedores', function () {
        return view('dashboard');
    });
});

