<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\MatriculaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CrearUsuario;
use App\Http\Controllers\RolController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\UsuarioSinrollController;
use App\Http\Controllers\EstudiantesController;
use App\Http\Controllers\SettingsController;

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
        Route::get('/asignar-roles', [RolController::class, 'mostrarFormulario'])->name('asignar-roles');
        Route::put('/{rol}', [RolController::class, 'actualizar'])->name('actualizar');
        Route::delete('/{rol}', [RolController::class, 'eliminar'])->name('eliminar');
        
        // Rutas AJAX para gestión de roles
        Route::post('/asignar-rol', [RolController::class, 'asignarRol'])->name('asignar');
        Route::post('/remover-rol', [RolController::class, 'removerRol'])->name('remover');
        Route::get('/roles-sistema', [RolController::class, 'obtenerRolesSistema'])->name('roles-sistema');
    // Endpoint para detectar cambios recientes en roles
    Route::get('/updates', [RolController::class, 'updates'])->name('updates');

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
        // Transiciones de estado
        Route::post('/{id}/publicar', [App\Http\Controllers\NotasController::class, 'publicar'])->name('publicar');
        Route::post('/{id}/revisar', [App\Http\Controllers\NotasController::class, 'revisar'])->name('revisar');
        Route::post('/{id}/bloquear', [App\Http\Controllers\NotasController::class, 'bloquear'])->name('bloquear');
        Route::post('/{id}/revertir', [App\Http\Controllers\NotasController::class, 'revertir'])->name('revertir');
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

    // Rutas para gestionar horarios (vista básica)
    Route::prefix('horarios')->name('horarios.')->group(function () {
        Route::get('/', [App\Http\Controllers\HorarioController::class, 'index'])->name('index');
        // ruta para guardar un nuevo horario (formulario en la vista apunta a 'horarios.store')
        Route::post('/', [App\Http\Controllers\HorarioController::class, 'store'])->name('store');
    });

    // Rutas para gestionar usuarios (listar, editar, actualizar, eliminar)
    Route::prefix('usuarios')->name('usuarios.')->group(function () {
        Route::get('/', [UsuarioController::class, 'index'])->name('index');
        // Endpoint para detectar cambios recientes en usuarios (polling)
        Route::get('/updates', [UsuarioController::class, 'updates'])->name('updates');
        Route::get('/{usuario}/editar', [UsuarioController::class, 'editar'])->name('editar');
        Route::put('/{usuario}', [UsuarioController::class, 'actualizar'])->name('actualizar');
        Route::delete('/{usuario}', [UsuarioController::class, 'eliminar'])->name('eliminar');
    });

    // Rutas para gestionar materias (CRUD + asignar a grupos)
    Route::prefix('materias')->name('materias.')->group(function () {
        Route::get('/', [App\Http\Controllers\MateriaController::class, 'index'])->name('index');
        Route::get('/crear', [App\Http\Controllers\MateriaController::class, 'crear'])->name('crear');
        Route::post('/', [App\Http\Controllers\MateriaController::class, 'store'])->name('store');
        Route::get('/{materia}/editar', [App\Http\Controllers\MateriaController::class, 'editar'])->name('editar');
        Route::put('/{materia}', [App\Http\Controllers\MateriaController::class, 'actualizar'])->name('actualizar');
        Route::delete('/{materia}', [App\Http\Controllers\MateriaController::class, 'eliminar'])->name('eliminar');
        Route::get('/asignar', [App\Http\Controllers\MateriaController::class, 'asignar'])->name('asignar');
    });

    // Configuración del sistema
    Route::get('/configuracion', [SettingsController::class, 'index'])->name('configuracion.index');
    Route::post('/configuracion', [SettingsController::class, 'update'])->name('configuracion.guardar');
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
    Route::get('/index', [App\Http\Controllers\DocenteController::class, 'index'])->name('index');
    Route::get('/crear', [App\Http\Controllers\DocenteController::class, 'crear'])->name('crear');
    Route::post('/', [App\Http\Controllers\DocenteController::class, 'store'])->name('store');
    
    // Rutas para gestión de grupos y notas por parte de docentes
    Route::get('/grupos', [App\Http\Controllers\DocenteController::class, 'grupos'])->name('grupos');
    Route::get('/grupos/{grupo}', [App\Http\Controllers\DocenteController::class, 'verGrupo'])->name('grupos.ver');
    Route::get('/grupos/{grupo}/materia/{materia}', [App\Http\Controllers\DocenteController::class, 'gestionarNotas'])->name('grupos.notas');
    Route::post('/grupos/{grupo}/materia/{materia}/asignar', [App\Http\Controllers\DocenteController::class, 'asignarNota'])->name('grupos.notas.asignar');
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

Route::prefix('matricula')->name('matricula.')->middleware('auth')->group(function () {

    // Solo los acudientes (roles_id = 7)
    Route::get('/iniciar', function () {
        $user = Auth::user();
        if ($user->roles_id != 7) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }
        return app(MatriculaController::class)->iniciarMatricula();
    })->name('iniciar');

    Route::post('/guardar', function () {
        $user = Auth::user();
        if ($user->roles_id != 7) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }
        return app(MatriculaController::class)->guardarMatricula(request());
    })->name('guardar');


    // Solo los administradores (1) y rectores (2)
    Route::get('/aceptar', function () {
        $user = Auth::user();
        if (!in_array($user->roles_id, [1, 2])) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }
        return app(MatriculaController::class)->mostrarMatriculasPendientes();
    })->name('aceptar');

    Route::get('/gestionar/{id}/{accion}', function ($id, $accion) {
        $user = Auth::user();
        if (!in_array($user->roles_id, [1, 2])) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }
        return app(MatriculaController::class)->gestionarMatricula($id, $accion);
    })->name('gestionar');
});

// Flujo de orientación psicológica
Route::prefix('orientacion')->name('orientacion.')->middleware('auth')->group(function(){
    // Listado y filtros (orientador / admin)
    Route::get('/citas', [App\Http\Controllers\OrientacionCitaController::class,'index'])->name('citas.index');
    // Historial de una cita
    Route::get('/citas/{id}/historial', [App\Http\Controllers\OrientacionCitaController::class,'historial'])->name('citas.historial');
    // Vista HTML (listado)
    Route::get('/citas-ui', [App\Http\Controllers\OrientacionCitaController::class,'vistaListado'])->name('citas.vista');
    Route::get('/citas-admin-ui', [App\Http\Controllers\OrientacionCitaController::class,'vistaAdmin'])->name('citas.admin');
    // Estudiante solicita
    Route::post('/citas/solicitar', [App\Http\Controllers\OrientacionCitaController::class,'solicitar'])->name('citas.solicitar');
    // Transiciones (requieren permiso gestionar_orientacion)
    Route::post('/citas/{id}/revisar', [App\Http\Controllers\OrientacionCitaController::class,'revisar'])->name('citas.revisar');
    Route::post('/citas/{id}/asignar', [App\Http\Controllers\OrientacionCitaController::class,'asignar'])->name('citas.asignar');
    Route::post('/citas/{id}/reprogramar', [App\Http\Controllers\OrientacionCitaController::class,'reprogramar'])->name('citas.reprogramar');
    Route::post('/citas/{id}/realizar', [App\Http\Controllers\OrientacionCitaController::class,'realizar'])->name('citas.realizar');
    Route::post('/citas/{id}/observaciones', [App\Http\Controllers\OrientacionCitaController::class,'registrarObservaciones'])->name('citas.observaciones');
    Route::post('/citas/{id}/seguimiento', [App\Http\Controllers\OrientacionCitaController::class,'evaluarSeguimiento'])->name('citas.seguimiento');
});

// Flujo disciplinario
Route::prefix('disciplina')->name('disciplina.')->middleware('auth')->group(function(){
    Route::get('/reportes', [App\Http\Controllers\ReporteDisciplinaController::class,'index'])->name('reportes.index');
    Route::get('/mis-reportes', [App\Http\Controllers\ReporteDisciplinaController::class,'mis'])->name('reportes.mis');
    Route::get('/reportar', [App\Http\Controllers\ReporteDisciplinaController::class,'crear'])->name('reportes.crear');
    Route::post('/reportes', [App\Http\Controllers\ReporteDisciplinaController::class,'store'])->name('reportes.store');
    Route::post('/reportes/{id}/revisar', [App\Http\Controllers\ReporteDisciplinaController::class,'revisar'])->name('reportes.revisar');
    Route::post('/reportes/{id}/asignar-sancion', [App\Http\Controllers\ReporteDisciplinaController::class,'asignarSancion'])->name('reportes.asignar_sancion');
    Route::post('/reportes/{id}/notificar', [App\Http\Controllers\ReporteDisciplinaController::class,'notificar'])->name('reportes.notificar');
    Route::post('/reportes/{id}/apelacion', [App\Http\Controllers\ReporteDisciplinaController::class,'solicitarApelacion'])->name('reportes.apelacion.solicitar');
    Route::post('/reportes/{id}/apelacion-revisar', [App\Http\Controllers\ReporteDisciplinaController::class,'revisarApelacion'])->name('reportes.apelacion.revisar');
    Route::post('/reportes/{id}/apelacion-resolver', [App\Http\Controllers\ReporteDisciplinaController::class,'resolverApelacion'])->name('reportes.apelacion.resolver');
    Route::post('/reportes/{id}/archivar', [App\Http\Controllers\ReporteDisciplinaController::class,'archivar'])->name('reportes.archivar');
    Route::get('/reportes/{id}/historial', [App\Http\Controllers\ReporteDisciplinaController::class,'historial'])->name('reportes.historial');
    // Auxiliares para formulario
    Route::get('/grupos', [App\Http\Controllers\ReporteDisciplinaController::class,'grupos'])->name('grupos');
    Route::get('/grupos/{id}/estudiantes', [App\Http\Controllers\ReporteDisciplinaController::class,'estudiantesGrupo'])->name('grupos.estudiantes');
});

