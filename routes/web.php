<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

// Mostrar login o redirigir al dashboard si ya hay sesión
Route::get('/', function () {
    if (Auth::check()) {
        return match (Auth::user()->type) {
            'administrador' => redirect()->route('admin.inicio'),
            'moderador' => redirect()->route('moderador.inicio'),
            default => redirect()->route('login.show'),
        };
    }

    return view('auth.login');
})->name('login.show');

// Ruta para procesar el login
Route::post('/', [AuthController::class, 'login'])->name('login');

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Panel administrador protegido
Route::middleware('rol:administrador')->group(function () {
    Route::get('/admin/inicio', [AdminController::class, 'inicio'])->name('admin.inicio');
    Route::post('/admin/registrar-plantel', [AdminController::class, 'registrarPlantel'])->name('admin.registrar_plantel');
    Route::post('/admin/registrar-moderador', [AdminController::class, 'registrarModerador'])->name('admin.registrar_moderador');
    Route::delete('/admin/eliminar-plantel/{id}', [AdminController::class, 'eliminarPlantel'])->name('admin.eliminar_plantel');
    Route::put('/admin/actualizar-plantel/{id}', [AdminController::class, 'actualizarPlantel'])->name('admin.actualizar_plantel');
    Route::delete('/admin/eliminar-moderador/{id}', [AdminController::class, 'eliminarModerador'])->name('admin.eliminar_moderador');
    Route::put('/admin/actualizar-moderador/{id}', [AdminController::class, 'actualizarModerador'])->name('admin.actualizar_moderador');
});

// Moderator Routes
Route::middleware('rol:moderador')->group(function () {
    Route::get('/moderador/inicio', fn () => view('moderador.inicio'))->name('moderador.inicio');
});

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
Route::get('/nada', function () {
    return view('nada');
})->name('nada');


Route::get('/debug-logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
});