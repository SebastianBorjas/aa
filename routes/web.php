<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ModController;
use App\Http\Controllers\MaestroController;
use App\Http\Controllers\AlumnoController; // Add AlumnoController
use App\Http\Controllers\EmpresaController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Mostrar login o redirigir al dashboard si ya hay sesión
Route::get('/', function () {
    if (Auth::check()) {
        return match (Auth::user()->type) {
            'administrador' => redirect()->route('admin.inicio'),
            'moderador' => redirect()->route('moderador.inicio'),
            'maestro' => redirect()->route('maestro.inicio'),
            'alumno' => redirect()->route('alumno.inicio'), // Add alumno redirect
            'empresa' => redirect()->route('empresa.inicio'), // Add empresa redirect
            default => redirect()->route('login.show'),
        };
    }

    return view('auth.login');
})->name('login.show');

// Ruta para procesar el login
Route::post('/', [AuthController::class, 'login'])->name('login');

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');


// Panel administrador protegido
Route::middleware(['rol:administrador', 'nocache'])->group(function () {
    Route::get('/admin/inicio', [AdminController::class, 'inicio'])->name('admin.inicio');
    Route::post('/admin/registrar-plantel', [AdminController::class, 'registrarPlantel'])->name('admin.registrar_plantel');
    Route::post('/admin/registrar-moderador', [AdminController::class, 'registrarModerador'])->name('admin.registrar_moderador');
    Route::delete('/admin/eliminar-plantel/{id}', [AdminController::class, 'eliminarPlantel'])->name('admin.eliminar_plantel');
    Route::put('/admin/actualizar-plantel/{id}', [AdminController::class, 'actualizarPlantel'])->name('admin.actualizar_plantel');
    Route::delete('/admin/eliminar-moderador/{id}', [AdminController::class, 'eliminarModerador'])->name('admin.eliminar_moderador');
    Route::put('/admin/actualizar-moderador/{id}', [AdminController::class, 'actualizarModerador'])->name('admin.actualizar_moderador');
});

// Panel moderador protegido
Route::middleware(['rol:moderador', 'nocache'])->group(function () {
    Route::get('/moderador/inicio', [ModController::class, 'inicio'])->name('moderador.inicio');
    Route::post('/moderador/empresas', [ModController::class, 'registerEmpresa'])->name('moderador.registerEmpresa');
    Route::put('/moderador/empresas/{empresa}', [ModController::class, 'updateEmpresa'])->name('moderador.updateEmpresa');
    Route::delete('/moderador/empresas/{empresa}', [ModController::class, 'deleteEmpresa'])->name('moderador.deleteEmpresa');
    Route::post('/moderador/instituciones', [ModController::class, 'registerInstitucion'])->name('moderador.registerInstitucion');
    Route::put('/moderador/instituciones/{institucion}', [ModController::class, 'updateInstitucion'])->name('moderador.updateInstitucion');
    Route::delete('/moderador/instituciones/{institucion}', [ModController::class, 'deleteInstitucion'])->name('moderador.deleteInstitucion');
    Route::post('/moderador/maestros', [ModController::class, 'registerMaestro'])->name('moderador.registerMaestro');
    Route::put('/moderador/maestros/{maestro}', [ModController::class, 'updateMaestro'])->name('moderador.updateMaestro');
    Route::delete('/moderador/maestros/{maestro}', [ModController::class, 'deleteMaestro'])->name('moderador.deleteMaestro');
    Route::post('/moderador/especialidades', [ModController::class, 'registerEspecialidad'])->name('moderador.registerEspecialidad');
    Route::put('/moderador/especialidades/{especialidad}', [ModController::class, 'updateEspecialidad'])->name('moderador.updateEspecialidad');
    Route::delete('/moderador/especialidades/{especialidad}', [ModController::class, 'deleteEspecialidad'])->name('moderador.deleteEspecialidad');
    Route::post('/moderador/alumnos', [ModController::class, 'registerAlumno'])->name('moderador.registerAlumno');
    Route::put('/moderador/alumnos/{alumno}', [ModController::class, 'updateAlumno'])->name('moderador.updateAlumno');
    Route::delete('/moderador/alumnos/{alumno}', [ModController::class, 'deleteAlumno'])->name('moderador.deleteAlumno');
    Route::post('/moderador/alumnos/{alumno}/lista', [ModController::class, 'guardarListaAlumno'])->name('moderador.guardarListaAlumno');
    Route::get('/moderador/maestros-por-institucion/{institucion}', [ModController::class, 'getMaestrosPorInstitucion'])->name('moderador.maestrosPorInstitucion');
    Route::get('/moderador/especialidades-por-institucion/{institucion}', [ModController::class, 'getEspecialidadesPorInstitucion'])->name('moderador.especialidadesPorInstitucion');
});

Route::middleware(['rol:maestro', 'nocache'])->group(function () {
    Route::get('/maestro/inicio', [MaestroController::class, 'inicio'])->name('maestro.inicio');
    Route::get('/maestro/planes/crear', [MaestroController::class, 'planesCrear'])->name('maestro.planes.crear');
    Route::post('/maestro/planes/crear', [MaestroController::class, 'crearPlan'])->name('maestro.planes.store');

    // Vistas detalladas de planes, temas y subtemas
    Route::get('/maestro/planes/{plan}', [MaestroController::class, 'verPlan'])->name('maestro.planes.ver');
    Route::get('/maestro/temas/{tema}', [MaestroController::class, 'verTema'])->name('maestro.temas.ver');
    Route::get('/maestro/subtemas/{subtema}', [MaestroController::class, 'verSubtema'])->name('maestro.subtemas.ver');
    Route::get('/maestro/temas/{tema}/subtemas/crear', [MaestroController::class, 'formCrearSubtema'])->name('maestro.subtemas.crear');
    // NUEVAS rutas para CRUD de planes, temas y subtemas
    Route::post('/maestro/planes/{id}/update', [MaestroController::class, 'actualizarPlan'])->name('maestro.planes.update');
    Route::post('/maestro/planes/{id}/delete', [MaestroController::class, 'eliminarPlan'])->name('maestro.planes.delete');

    Route::post('/maestro/temas/save', [MaestroController::class, 'guardarTema'])->name('maestro.temas.save');
    Route::post('/maestro/temas/{id}/delete', [MaestroController::class, 'eliminarTema'])->name('maestro.temas.delete');

    Route::post('/maestro/subtemas/save', [MaestroController::class, 'guardarSubtema'])->name('maestro.subtemas.save');
    Route::post('/maestro/subtemas/{id}/delete', [MaestroController::class, 'eliminarSubtema'])->name('maestro.subtemas.delete');
    Route::post('/maestro/subtemas/{id}/file', [MaestroController::class, 'subtemaAgregarArchivo'])->name('maestro.subtemas.addfile');
    Route::post('/maestro/subtemas/{id}/file-delete', [MaestroController::class, 'subtemaEliminarArchivo'])->name('maestro.subtemas.deletefile');

    // Asignar plan a alumnos
    Route::post('/maestro/asignar-plan', [MaestroController::class, 'asignarPlan'])->name('maestro.asignar_plan');

    Route::post('/maestro/entregas/{entrega}/verificar', [MaestroController::class, 'verificarEntrega'])->name('maestro.entregas.verificar');
    Route::post('/maestro/entregas/{entrega}/rechazar', [MaestroController::class, 'rechazarEntrega'])->name('maestro.entregas.rechazar');
});



// Panel alumno protegido
Route::middleware(['rol:alumno', 'nocache'])->group(function () {
    Route::get('/alumno/inicio', [AlumnoController::class, 'inicio'])->name('alumno.inicio');
    Route::get('/alumno/temas/{tema}', [AlumnoController::class, 'verTema'])->name('alumno.tema');
    Route::get('/alumno/subtemas/{subtema}', [AlumnoController::class, 'verSubtema'])->name('alumno.subtema');
    Route::post('/alumno/subtemas/{subtema}/entregar', [AlumnoController::class, 'entregarTarea'])->name('alumno.entregar_tarea');
});

Route::middleware(['rol:empresa', 'nocache'])->group(function () {
    Route::get('/empresa/inicio', [EmpresaController::class, 'inicio'])->name('empresa.inicio');
    Route::post('/empresa/lista/guardar', [EmpresaController::class, 'guardarLista'])->name('empresa.guardarLista');
    Route::post('/empresa/entregas/{entrega}/verificar', [EmpresaController::class, 'verificarEntrega'])->name('empresa.entregas.verificar');
    Route::post('/empresa/entregas/{entrega}/rechazar', [EmpresaController::class, 'rechazarEntrega'])->name('empresa.entregas.rechazar');
    Route::post('/empresa/alumnos/{alumno}/lista', [EmpresaController::class, 'guardarListaAlumno'])->name('empresa.guardarListaAlumno');
});

Route::get('/nada', function () {
    return view('nada');
})->name('nada');

Route::get('/debug-logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
});