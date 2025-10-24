<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CrearUsuario;
use App\Http\Controllers\RolController;
use App\Http\Controllers\UsuarioSinrollController;
use App\Http\Controllers\EstudiantesController;

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
        Route::get('/asignar-roles', [RolController::class, 'mostrarFormulario'])->name('asignar-roles.form');
        Route::put('/{rol}', [RolController::class, 'actualizar'])->name('actualizar');
        Route::delete('/{rol}', [RolController::class, 'eliminar'])->name('eliminar');
        
        // Rutas AJAX para gestión de roles
        Route::post('/asignar-rol', [RolController::class, 'asignarRol'])->name('asignar');
        Route::post('/remover-rol', [RolController::class, 'removerRol'])->name('remover');
        Route::get('/roles-sistema', [RolController::class, 'obtenerRolesSistema'])->name('roles-sistema');

        // Rutas para asignar notas a estudiantes
        Route::post('/asignar-notas', [RolController::class, 'actualizarNota'])->name('roles.asignar-notas');

        // Rutas para gestión de notas (mantenidas aquí para compatibilidad con AJAX de roles)
        // (La UI para notas se declara fuera del prefijo 'roles' más abajo)

    });
    
    // Rutas UI para notas (fuera del prefijo 'roles' para nombres 'notas.*')
    Route::prefix('notas')->name('notas.')->group(function () {
        Route::get('/crear', [App\Http\Controllers\NotasController::class, 'crear'])->name('crear');
        Route::post('/', [App\Http\Controllers\NotasController::class, 'ValidarNota'])->name('guardar');
        Route::get('/estudiante/{id}', [App\Http\Controllers\NotasController::class, 'porEstudiante'])->name('estudiante');
        Route::get('/lista/estudiantes', [App\Http\Controllers\NotasController::class, 'listaEstudiantes'])->name('lista.estudiantes');
        // Página de visualización de notas (tabla + selector) accesible a usuarios autenticados
        Route::get('/mostrar', function() {
            return view('notas.mostrar');
        })->name('mostrar');
    });
});
//rutas para ver estudiantes
//Route:Middleware(['auth'])->group(function () {
        Route::prefix('estudiantes')->name('estudiantes.')->middleware('auth')->group(function () {
            Route::get('/', [App\Http\Controllers\EstudiantesController::class, 'index'])->name('index');
            Route::get('/mostrar', [App\Http\Controllers\EstudiantesController::class,'MostrarEstudiante'])->name('mostrar');
            Route::post('/', [App\Http\Controllers\EstudiantesController::class, 'store'])->name('store');
        });
//});

//Rutas para docentes
Route::prefix('docentes')->name('docentes.')->middleware('auth')->group(function () {
    Route::get('/', [App\Http\Controllers\DocenteController::class, 'index'])->name('index');
    Route::get('/crear', [App\Http\Controllers\DocenteController::class, 'crear'])->name('crear');
    Route::post('/', [App\Http\Controllers\DocenteController::class, 'store'])->name('store');
});

//ruta para usuarios sin rol
Route::get('/sin', [RolController::class, 'usuariosSinRol'])->name('sin');


//rutas para estudiantes, solo visualizar notas
Route::prefix('estudiantes')->name('estudiantes.')->middleware('auth')->group(function () {
    Route::get('/', function() {
        $user = auth()->user();
        if (!$user) return redirect()->route('login');
        // Obtener rol del usuario
        $rol = App\Models\RolesModel::find($user->roles_id);
        if (!$rol || $rol->nombre !== 'Estudiante') {
            abort(403, 'Solo estudiantes pueden acceder aquí');
        }
        return redirect()->route('notas.mostrar');
    })->name('index');
});    
