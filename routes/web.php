<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CrearUsuario;
use App\Http\Controllers\RolController;
use App\Http\Controllers\MatriculaAcudienteController;

// Ruta raíz redirige al login
Route::get('/', function () {
    return redirect('/login');
});

// Rutas de autenticación
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas Crear usuarios
Route::get('/register', [CrearUsuario::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [CrearUsuario::class, 'register']);


// Rutas protegidas por autenticación
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    
    // Rutas de gestión de roles
    Route::prefix('roles')->name('roles.')->group(function () {
        // Rutas web principales
        Route::get('/', [RolController::class, 'index'])->name('index');
        Route::get('/crear', [RolController::class, 'crear'])->name('crear');
        Route::post('/', [RolController::class, 'guardar'])->name('guardar');
        Route::get('/{rol}', [RolController::class, 'mostrar'])->name('mostrar');
        Route::get('/{rol}/editar', [RolController::class, 'editar'])->name('editar');
        Route::put('/{rol}', [RolController::class, 'actualizar'])->name('actualizar');
        Route::delete('/{rol}', [RolController::class, 'eliminar'])->name('eliminar');
        
        // Rutas AJAX para gestión de roles
        Route::post('/asignar-rol', [RolController::class, 'asignarRol'])->name('asignar');
        Route::post('/remover-rol', [RolController::class, 'removerRol'])->name('remover');
        Route::get('/usuarios-sin-rol', [RolController::class, 'obtenerUsuariosSinRol'])->name('usuarios-sin-rol');
        Route::get('/roles-sistema', [RolController::class, 'obtenerRolesSistema'])->name('roles-sistema');
    });

    // Rutas de matrículas por acudientes
    Route::prefix('matriculas')->name('matriculas.')->group(function () {
        Route::get('/', [MatriculaAcudienteController::class, 'listar'])->name('index');
        Route::get('/crear', [MatriculaAcudienteController::class, 'crear'])->name('crear');
        Route::post('/guardar', [MatriculaAcudienteController::class, 'guardar'])->name('guardar');
        Route::get('/{matricula}', [MatriculaAcudienteController::class, 'mostrar'])->name('mostrar');
        Route::get('/descargar/{ruta}', [MatriculaAcudienteController::class, 'descargarDocumento'])->name('descargar');
    });
});
