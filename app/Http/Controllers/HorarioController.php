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
        // Preparar datos para la vista: grupos, materias (si existen), docentes (rol Profesor), y horarios existentes.
        // Grupos
        $grupos = [];
        if (class_exists(Grupo::class)) {
            $grupos = Grupo::orderBy('nombre')->get();
        }

        // Materias (opcional)
        $materias = [];
        if (class_exists(\App\Models\Materia::class)) {
            $materias = \App\Models\Materia::orderBy('nombre')->get();
        }

        // Docentes: buscar role id para 'Profesor'
        $profesorRoleId = RolesModel::where('nombre', 'Profesor')->value('id');
        $docentes = [];
        if ($profesorRoleId) {
            $docentes = User::where('roles_id', $profesorRoleId)->orderBy('name')->get();
        } else {
            // Fallback: cualquiera con roles_id no nulo
            $docentes = User::whereNotNull('roles_id')->orderBy('name')->get();
        }

        // Horarios: preferir modelo Horario (si existe y tabla migrada), sino tabla directa o session
        $horarios = [];
        if (class_exists(Horario::class) && Schema::hasTable('horarios')) {
            // eager load only the relations that exist to avoid errors when Materia model is missing
            $with = ['grupo', 'docente'];
            if (class_exists(\App\Models\Materia::class)) {
                $with[] = 'materia';
            }
            // eager load relations to avoid N+1
            $horarios = Horario::with($with)->orderBy('created_at', 'desc')->get();
        } elseif (Schema::hasTable('horarios')) {
            $horarios = DB::table('horarios')->orderBy('created_at', 'desc')->get();
        } else {
            $horarios = session('horarios', []);
        }

        return view('horarios.index', compact('grupos', 'materias', 'docentes', 'horarios'));
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
