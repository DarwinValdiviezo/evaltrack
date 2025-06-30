<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\EvaluacionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\RoleController;
use App\Http\Middleware\CheckRole;

Auth::routes();

Route::get('/', function() {
    return view('welcome');
});

// Ruta temporal para verificar rol del usuario
Route::get('/check-role', function() {
    $user = Auth::user();
    if ($user) {
        echo "Usuario: " . $user->email . "<br>";
        echo "Roles: " . $user->getRoleNames()->implode(', ') . "<br>";
        echo "Tiene rol Empleado: " . ($user->hasRole('Empleado') ? 'Sí' : 'No') . "<br>";
    } else {
        echo "No hay usuario autenticado";
    }
})->middleware('auth');

// Ruta de prueba simple
Route::get('/test', function() {
    return "Servidor funcionando correctamente";
});

// Ruta de prueba para verificar autenticación
Route::get('/test-auth', function() {
    if (Auth::check()) {
        return "Usuario autenticado: " . Auth::user()->email;
    } else {
        return "No hay usuario autenticado";
    }
});

// Ruta de prueba para el controlador de empleados
Route::get('/test-empleado', [EmpleadoController::class, 'miPerfil']);

// Rutas para empleados (sin middleware por ahora para probar)
Route::get('mis-asistencias',   [AsistenciaController::class,'misAsistencias'])
     ->name('mis-asistencias');
Route::get('asistencias/mis',   [AsistenciaController::class,'misAsistencias'])
     ->name('asistencias.mis');
Route::post('asistencias/{asistencia}/confirmar',
           [AsistenciaController::class,'confirmar'])
     ->name('asistencias.confirmar');
Route::get('asistencias/registrar', [AsistenciaController::class, 'mostrarRegistro'])
     ->name('asistencias.registrar');
Route::post('asistencias/registrar',
           [AsistenciaController::class,'registrarAsistencia'])
     ->name('asistencias.registrar');

Route::get('mis-evaluaciones',  [EvaluacionController::class,'misEvaluaciones'])
     ->name('mis-evaluaciones');
Route::get('evaluaciones/{evaluacion}/responder', [EvaluacionController::class, 'responder'])
     ->name('evaluaciones.responder');
Route::post('evaluaciones/{evaluacion}/guardar-respuesta', [EvaluacionController::class, 'guardarRespuesta'])
     ->name('evaluaciones.guardar-respuesta');

Route::get('mi-perfil', [EmpleadoController::class, 'miPerfil'])
     ->name('empleados.mi-perfil');
Route::put('mi-perfil', [EmpleadoController::class, 'actualizarMiPerfil'])
     ->name('empleados.actualizar-mi-perfil');

Route::get('/home', [HomeController::class,'index'])
     ->middleware('auth')
     ->name('home');

Route::middleware(['auth'])->group(function () {

    // Sólo Administrador
    Route::middleware([ CheckRole::class . ':Administrador' ])->group(function () {
        Route::resource('users', UserController::class)
             ->except(['show','destroy']);
        Route::resource('roles', RoleController::class);
    });

    // Administrador y Gestor de Talento Humano
    Route::middleware([ CheckRole::class . ':Administrador,Gestor de Talento Humano' ])->group(function () {
        Route::resource('empleados', EmpleadoController::class);
        
        // Solo Administrador puede crear, eliminar y activar/desactivar eventos
        Route::middleware([ CheckRole::class . ':Administrador' ])->group(function () {
            Route::resource('eventos', EventoController::class)->except(['index', 'show', 'edit', 'update']);
            Route::post('eventos/{evento}/activar', [EventoController::class, 'activar'])->name('eventos.activar');
            Route::post('eventos/{evento}/desactivar', [EventoController::class, 'desactivar'])->name('eventos.desactivar');
        });
        
        // Administrador y Gestor pueden editar eventos
        Route::resource('eventos', EventoController::class)->only(['edit', 'update']);
        
        Route::resource('asistencias', AsistenciaController::class)
             ->except(['show']);
        Route::resource('evaluaciones', EvaluacionController::class)
             ->except(['show'])
             ->parameters(['evaluaciones' => 'evaluacion']);
    });

});

Route::resource('eventos', EventoController::class)->only(['index', 'show']);

Route::get('evaluaciones/{evaluacion}/calificar', [EvaluacionController::class, 'calificar'])
     ->name('evaluaciones.calificar');
Route::post('evaluaciones/{evaluacion}/guardar-calificacion', [EvaluacionController::class, 'guardarCalificacion'])
     ->name('evaluaciones.guardar-calificacion');
