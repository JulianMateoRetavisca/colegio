<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use App\Models\RolesModel;
use App\Models\User;
use App\Models\Grupo;
use App\Models\Horario;

class HorarioController extends Controller
{
    /**
     * Mostrar la vista principal de horarios
     */
    public function index()
    {
        $user = auth()->user();
        $rol = $user && $user->roles_id ? RolesModel::find($user->roles_id) : null;
        $nombreRol = $rol?->nombre ?? '';
        $nombreRolLower = strtolower($nombreRol);
        $esProfesor = $nombreRolLower === 'profesor';
        $rolAlto = in_array($nombreRolLower, ['administrador','rector','coordinador']);

        // Relacionales auxiliares
        $grupos = class_exists(Grupo::class) ? Grupo::orderBy('nombre')->get() : collect();
        $materias = class_exists(\App\Models\Materia::class) ? \App\Models\Materia::orderBy('nombre')->get() : collect();

        // Listado de docentes para filtro (solo visible para roles altos)
        $profesorRoleId = RolesModel::where('nombre', 'Profesor')->value('id');
        $docentes = $profesorRoleId ? User::where('roles_id', $profesorRoleId)->orderBy('name')->get() : collect();

        // Construcción de query de horarios si existe la tabla
        $horarios = collect();
        if (Schema::hasTable('horarios')) {
            $with = ['grupo', 'docente'];
            if (class_exists(\App\Models\Materia::class)) { $with[] = 'materia'; }
            $query = Horario::with($with)->orderBy('dia')->orderBy('hora_inicio');

            if ($esProfesor) {
                $query->where('docente_id', $user->id);
            } elseif ($rolAlto) {
                // opcional filtro por docente via ?docente_id=
                if (request()->filled('docente_id')) {
                    $query->where('docente_id', request('docente_id'));
                }
            } else {
                // Si no es profesor ni rol alto, restringir si no tiene permiso explicito
                if (!$rol || !$rol->tienePermiso('gestionar_horarios')) {
                    // Mostrar vacío para evitar fuga de información
                    return view('horarios.index', [
                        'grupos' => $grupos,
                        'materias' => $materias,
                        'docentes' => collect(),
                        'horarios' => collect(),
                        'esProfesor' => false,
                        'rolAlto' => false,
                    ])->with('error', 'No tienes autorización para ver horarios.');
                }
            }
            $horarios = $query->get();
        } else {
            // Fallback session (temporal) si la migración aún no corre
            $horarios = collect(session('horarios', []));
        }

        return view('horarios.index', [
            'grupos' => $grupos,
            'materias' => $materias,
            'docentes' => $docentes,
            'horarios' => $horarios,
            'esProfesor' => $esProfesor,
            'rolAlto' => $rolAlto,
        ]);
    }

    /**
     * Store a newly created horario (graceful fallback if DB table doesn't exist).
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'grupo_id' => ['nullable','integer'],
            'materia_id' => ['nullable','integer'],
            'docente_id' => ['nullable','integer'],
            'dia' => ['required','string'],
            'hora_inicio' => ['nullable','date_format:H:i'],
            'hora_fin' => ['nullable','date_format:H:i'],
            'observaciones' => ['nullable','string','max:255'],
        ]);

        // Si la tabla 'horarios' existe y el modelo Horario está disponible, usarlo; si sólo la tabla existe usar DB; sino session fallback
        if (Schema::hasTable('horarios')) {
            if (class_exists(Horario::class)) {
                // usar Eloquent para crear (maneja timestamps automáticamente)
                Horario::create($data);
            } else {
                $data['created_at'] = now();
                $data['updated_at'] = now();
                DB::table('horarios')->insert($data);
            }
            return Redirect::back()->with('success', 'Horario creado correctamente.');
        }

        // Si no existe la tabla, guardamos en session como fallback (temporal)
        $horarios = session('horarios', []);
        $horarios[] = array_merge($data, ['id' => Str::random(8), 'created_at' => now()->toDateTimeString()]);
        session(['horarios' => $horarios]);

        return Redirect::back()->with('warning', 'La tabla `horarios` no existe aún. El horario se guardó temporalmente en la sesión. Crea la migración para persistirlo.');
    }
}
