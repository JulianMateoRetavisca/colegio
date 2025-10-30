<?php
namespace App\Http\Controllers;

use App\Models\NotaModel;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use App\Models\RolesModel;

class NotasController extends Controller
{
    public function index()
    {
        // Permitir a cualquier usuario (incluso no autenticado) ver todas las notas
        $notas = NotaModel::with('estudiante')->get();
        return response()->json($notas);
    }
    
    /**
     * Mostrar formulario para crear una nota desde la UI
     */
    public function crear()
    {
        // Solo usuarios con permiso de modificar/registrar notas pueden ver este formulario
        if (!Auth::check() || !Auth::user()->tienePermiso('modificar_notas')) {
            abort(403, 'No autorizado para acceder a crear notas');
        }

        $estudiantes = User::orderBy('name')
            ->where('roles_id', 6)
            ->get(['id', 'name']);
        $materias = [ 
            1 => 'Matemáticas',
            2 => 'Lenguaje',
            3 => 'Ciencias',
        ];

        return view('notas.crear', compact('estudiantes', 'materias'));
    }
    public function ValidarNota(Request $request)
    {
        // Solo usuarios autorizados pueden crear notas
        if (!Auth::check() || !Auth::user()->tienePermiso('modificar_notas')) {
            return response()->json(['message' => 'No autorizado'], 403);
        }
        $request->validate([
            'estudiante_id' => 'required|exists:users,id',
            'materia_id' => 'required|integer',
            'nota' => 'required|numeric|min:0|max:100',
            'periodo' => 'required|string|max:4',
        ]);

        $nota = NotaModel::create($request->only(['estudiante_id','materia_id','nota','periodo']));
        // Si la petición espera JSON (AJAX/API), devolver JSON. Si es un formulario web, redirigir a la vista de notas.
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($nota, 201);
        }

        // Redirigir a la vista de lista de notas con mensaje de éxito
        return redirect()->route('notas.mostrar')->with('success', 'Nota guardada correctamente');
    }
    public function MostrarNota($id)
    {
        $nota = NotaModel::with('estudiante')->find($id);
        if (!$nota) {
            return response()->json(['message' => 'Nota no encontrada'], 404);
        }
        // Permitir lectura pública de notas
        return response()->json($nota);
    }

    /**
     * Retornar todas las notas de un estudiante (JSON) — usado por la UI/admin
     */
    public function porEstudiante($id)
    {
        // Permitir acceso público: retornar notas del estudiante solicitado
        $notas = NotaModel::with('estudiante')->where('estudiante_id', $id)->get();
        return response()->json($notas);
    }

    /**
     * Retornar notas/trabajos de un grupo — accesible para docentes/usuarios con permiso
     * La función intenta detectar si la tabla `users` tiene columna `grupo_id` o `grupo`.
     * Si no existe ninguna, retorna un mensaje explicativo para que se agregue la columna o se adapte la lógica.
     */
    public function porGrupo($grupoId)
    {
        // Permitir acceso público: retornar notas de usuarios asociados al grupo

        // Detectar columna de grupo en la tabla users
        if (Schema::hasColumn('users', 'grupo_id')) {
            $usuarios = User::where('grupo_id', $grupoId)->get(['id']);
        } elseif (Schema::hasColumn('users', 'grupo')) {
            $usuarios = User::where('grupo', $grupoId)->get(['id']);
        } else {
            return response()->json(['message' => 'No hay columna "grupo" ni "grupo_id" en la tabla users. Añade la columna o ajusta la lógica.'], 400);
        }

        if ($usuarios->isEmpty()) {
            return response()->json([], 200);
        }

        $ids = $usuarios->pluck('id')->toArray();
        $notas = NotaModel::with('estudiante')->whereIn('estudiante_id', $ids)->get();
        return response()->json($notas);
    }

    /**
     * Lista simple de grupos disponibles (para poblar el selector en la UI).
     * Intentará detectar si la información de grupo está en la tabla `users` (columna 'grupo' o 'grupo_id').
     * Si existe una tabla `grupos` en el proyecto la usará si está disponible.
     */
    public function listaGrupos()
    {
        // Permitir acceso público a la lista de grupos
        // Preferimos usar una tabla 'grupos' si existe
        if (Schema::hasTable('grupos')) {
            $grupos = \DB::table('grupos')->select('id', 'nombre')->orderBy('nombre')->get();
            return response()->json($grupos);
        }

        // Si no hay tabla 'grupos', intentar inferir grupos desde columna en users
        if (Schema::hasColumn('users', 'grupo_id')) {
            $grupos = \DB::table('users')->select('grupo_id as id')->distinct()->whereNotNull('grupo_id')->get()->map(function($r){ return ['id' => $r->id, 'nombre' => 'Grupo '.$r->id]; });
            return response()->json($grupos);
        } elseif (Schema::hasColumn('users', 'grupo')) {
            $grupos = \DB::table('users')->select('grupo')->distinct()->whereNotNull('grupo')->get()->map(function($r){ return ['id' => $r->grupo, 'nombre' => 'Grupo '.$r->grupo]; });
            return response()->json($grupos);
        }

        return response()->json([], 200);
    }

    /**
     * Devuelve estudiantes pertenecientes a un grupo (id simple + name) — usado por el selector de la UI.
     */
    public function estudiantesPorGrupo($grupoId)
    {
        // Permitir acceso público a la lista de estudiantes por grupo
        if (Schema::hasColumn('users', 'grupo_id')) {
            $estudiantes = User::where('grupo_id', $grupoId)->where('roles_id', RolesModel::where('nombre','Estudiante')->first()->id ?? 6)->orderBy('name')->get(['id','name']);
            return response()->json($estudiantes);
        } elseif (Schema::hasColumn('users', 'grupo')) {
            $estudiantes = User::where('grupo', $grupoId)->where('roles_id', RolesModel::where('nombre','Estudiante')->first()->id ?? 6)->orderBy('name')->get(['id','name']);
            return response()->json($estudiantes);
        }

        return response()->json([], 200);
    }

    /**
     * Retornar lista simple de estudiantes (id, name) para poblar selects en la UI
     */
    public function listaEstudiantes()
    {
        // Permitir acceso público a la lista de estudiantes
        $estudiantes = User::where('roles_id', RolesModel::where('nombre','Estudiante')->first()->id ?? 6)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($estudiantes);
    }

    /**
     * Filtrar notas por grupo y/o materia. Si el usuario es administrador (acceso_total or role 'Admin')
     * puede ver todas las notas; si no, solo las permitidas por permisos o el propio estudiante.
     * Query params: grupo (id), materia (id)
     */
    public function filtrar(Request $request)
    {
    $grupo = $request->query('grupo');
    $materia = $request->query('materia');
    // Soporta filtro por usuario (estudiante)
    $usuarioFiltro = $request->query('usuario');
        $usuario = Auth::user();

        // Detectar si es admin/permiso de acceso total
    // Si no hay usuario autenticado, permitir la vista pública completa
    $rolNombre = $usuario->rol->nombre ?? '';
    $esAdmin = !$usuario ? true : ($rolNombre === 'Admin' || in_array('acceso_total', $usuario->rol->permisos ?? []));

        $query = NotaModel::with('estudiante');

        // Filtrar por materia si se especifica
        if ($materia) {
            $query->where('materia_id', $materia);
        }

        // Filtrar por usuario/estudiante si se especifica
        if ($usuarioFiltro) {
            $query->where('estudiante_id', $usuarioFiltro);
        }

        // Si se especifica grupo, filtrar por estudiantes de ese grupo
        if ($grupo) {
            if (Schema::hasColumn('users', 'grupo_id')) {
                $userIds = User::where('grupo_id', $grupo)->pluck('id')->toArray();
            } elseif (Schema::hasColumn('users', 'grupo')) {
                $userIds = User::where('grupo', $grupo)->pluck('id')->toArray();
            } else {
                $userIds = [];
            }
            if (!empty($userIds)) {
                $query->whereIn('estudiante_id', $userIds);
            } else {
                // no hay usuarios en ese grupo => devolver vacío
                return response()->json([], 200);
            }
        }

        // Si no es admin y no tiene permiso de ver notas, devolver solo sus propias notas
        if (!$esAdmin && !$usuario->tienePermiso('ver_notas') && !$usuario->tienePermiso('gestionar_notas')) {
            $query->where('estudiante_id', $usuario->id);
        }

        $notas = $query->orderBy('periodo', 'desc')->get();
        return response()->json($notas);
    }

    /**
     * Renderiza la vista pública de notas con un volcado inicial de notas
     * para que la página muestre contenido incluso si JavaScript falla.
     */
    public function vistaPublica()
    {
        // Esta vista ahora acepta filtros por query string: grupo, materia
        $request = request();
        $grupo = $request->query('grupo');
        $materia = $request->query('materia');

        // lista de materias (coincide con la UI)
        $materias = [
            1 => 'Matemáticas',
            2 => 'Lenguaje',
            3 => 'Ciencias',
        ];

        // Obtener grupos disponibles (preferir tabla 'grupos')
        if (Schema::hasTable('grupos')) {
            $grupos = \DB::table('grupos')->select('id','nombre')->orderBy('nombre')->get();
        } else {
            if (Schema::hasColumn('users','grupo_id')) {
                $grupos = \DB::table('users')->select('grupo_id as id')->distinct()->whereNotNull('grupo_id')->get()->map(function($r){ return (object)['id' => $r->id, 'nombre' => 'Grupo '.$r->id]; });
            } elseif (Schema::hasColumn('users','grupo')) {
                $grupos = \DB::table('users')->select('grupo')->distinct()->whereNotNull('grupo')->get()->map(function($r){ return (object)['id' => $r->grupo, 'nombre' => 'Grupo '.$r->grupo]; });
            } else {
                $grupos = collect([]);
            }
        }

        $query = NotaModel::with('estudiante');
        if ($materia) $query->where('materia_id', $materia);
        if ($grupo) {
            if (Schema::hasColumn('users','grupo_id')) {
                $userIds = User::where('grupo_id', $grupo)->pluck('id')->toArray();
            } elseif (Schema::hasColumn('users','grupo')) {
                $userIds = User::where('grupo', $grupo)->pluck('id')->toArray();
            } else {
                $userIds = [];
            }
            if (!empty($userIds)) {
                $query->whereIn('estudiante_id', $userIds);
            } else {
                // no hay usuarios en ese grupo -> devolver vacío
                $query->whereRaw('0 = 1');
            }
        }

        $notas = $query->orderBy('periodo', 'desc')->get();

        // Determinar si el usuario actual es administrador (para mostrar controles server-side)
        $isAdmin = false;
        if (Auth::check()) {
            $rol = Auth::user()->rol;
            $isAdmin = ($rol->nombre ?? '') === 'Admin' || in_array('acceso_total', $rol->permisos ?? []);
        }

        // Detectar si es estudiante autenticado
        $isStudent = false;
        $currentUserId = null;
        if (Auth::check()) {
            $currentUserId = Auth::id();
            $isStudent = (Auth::user()->rol->nombre ?? '') === 'Estudiante';
        }

        $selectedGrupo = $grupo;
        $selectedMateria = $materia;

        return view('notas.mostrar', compact('notas','grupos','materias','selectedGrupo','selectedMateria','isAdmin','isStudent','currentUserId'));
    }

    public function ActualizarNota(Request $request, $id)
    {
        $nota = NotaModel::find($id);
        if (!$nota) {
            return response()->json(['message' => 'Nota no encontrada'], 404);
        }

        // Solo usuarios autorizados pueden actualizar notas
        if (!Auth::check() || !Auth::user()->tienePermiso('modificar_notas')) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $request->validate([
            'estudiante_id' => 'sometimes|required|exists:users,id',
            'materia_id' => 'sometimes|required|integer',
            'nota' => 'sometimes|required|numeric|min:0|max:100',
            'periodo' => 'sometimes|required|string|max:4',
        ]);

        $nota->update($request->all());
        return response()->json($nota);
    }

}