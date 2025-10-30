<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CrearUsuario;
use App\Http\Controllers\RolController;
use App\Http\Controllers\UsuarioController;
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
    // Dentro del middleware 'auth' solo mantenemos las rutas de creación/guardado (protegidas)
    Route::prefix('notas')->name('notas.')->group(function () {
        Route::get('/crear', [App\Http\Controllers\NotasController::class, 'crear'])->name('crear');
        Route::post('/', [App\Http\Controllers\NotasController::class, 'ValidarNota'])->name('guardar');
        // Ruta para actualizar una nota (protegida)
        Route::put('/{id}', [App\Http\Controllers\NotasController::class, 'ActualizarNota'])->name('actualizar');
    });

    // Rutas para gestionar grupos (CRUD + asignar estudiantes)
    Route::prefix('grupos')->name('grupos.')->group(function () {
        Route::get('/', [App\Http\Controllers\GrupoController::class, 'index'])->name('index');
        Route::get('/crear', [App\Http\Controllers\GrupoController::class, 'crear'])->name('crear');
        Route::post('/', [App\Http\Controllers\GrupoController::class, 'guardar'])->name('guardar');
        Route::get('/{grupo}/editar', [App\Http\Controllers\GrupoController::class, 'editar'])->name('editar');
        Route::put('/{grupo}', [App\Http\Controllers\GrupoController::class, 'actualizar'])->name('actualizar');
        Route::delete('/{grupo}', [App\Http\Controllers\GrupoController::class, 'eliminar'])->name('eliminar');

        // Asignar estudiantes
        Route::get('/{grupo}/asignar', [App\Http\Controllers\GrupoController::class, 'asignar'])->name('asignar');
        Route::post('/{grupo}/asignar', [App\Http\Controllers\GrupoController::class, 'asignarGuardar'])->name('asignar.guardar');
    });

    // Rutas para gestionar usuarios (listar, editar, actualizar, eliminar)
    Route::prefix('usuarios')->name('usuarios.')->group(function () {
        Route::get('/', [UsuarioController::class, 'index'])->name('index');
        Route::get('/{usuario}/editar', [UsuarioController::class, 'editar'])->name('editar');
        Route::put('/{usuario}', [UsuarioController::class, 'actualizar'])->name('actualizar');
        Route::delete('/{usuario}', [UsuarioController::class, 'eliminar'])->name('eliminar');
    });
});

// Exponer la vista pública de notas fuera del middleware 'auth' para que cualquiera pueda verla
Route::get('/notas/mostrar', [App\Http\Controllers\NotasController::class, 'vistaPublica'])->name('notas.mostrar');

// Rutas públicas (lectura) para notas — permiten que la vista pública consulte datos JSON
Route::prefix('notas')->name('notas.')->group(function () {
    // Endpoint para obtener todas las notas (JSON) — usado por la UI cuando se quiere ver "Todos"
    Route::get('/', [App\Http\Controllers\NotasController::class, 'index'])->name('index');
    Route::get('/estudiante/{id}', [App\Http\Controllers\NotasController::class, 'porEstudiante'])->name('estudiante');
    Route::get('/lista/estudiantes', [App\Http\Controllers\NotasController::class, 'listaEstudiantes'])->name('lista.estudiantes');
    // Ruta para que docentes/visitors vean trabajos/notas de un grupo específico
    Route::get('/grupo/{grupoId}', [App\Http\Controllers\NotasController::class, 'porGrupo'])->name('grupo');
    // Endpoints auxiliares para la UI: listar grupos y obtener estudiantes de un grupo
    Route::get('/grupos', [App\Http\Controllers\NotasController::class, 'listaGrupos'])->name('grupos');
    Route::get('/grupo/{grupoId}/estudiantes', [App\Http\Controllers\NotasController::class, 'estudiantesPorGrupo'])->name('grupo.estudiantes');
    // Endpoint para filtrar notas por grupo y/o materia (ej: /notas/filtros?grupo=1&materia=2)
    Route::get('/filtros', [App\Http\Controllers\NotasController::class, 'filtrar'])->name('filtros');
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
