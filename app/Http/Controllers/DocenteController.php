<?php
//controlador para gestionar docentes
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\RolesModel;
use App\Models\Grupo;
use App\Models\Materia;
use App\Models\NotaModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DocenteController extends Controller
{
    // Mostrar lista de docentes
    private function puedeGestionar() {
        $u = Auth::user();
        if (!$u || !$u->roles_id) return false;
        $r = RolesModel::find($u->roles_id);
        return $r && (in_array($r->nombre, ['Admin','Rector']) || $r->tienePermiso('gestionar_docentes'));
    }

    public function index() {
        if (!$this->puedeGestionar()) return redirect('/dashboard')->with('error','No autorizado');
        $docentes = User::where('roles_id', RolesModel::where('nombre','Profesor')->first()->id)->get();
        return view('docentes.index', compact('docentes'));
    }

    // Mostrar formulario para crear un nuevo docente
    public function crear()
    {
        if (!$this->puedeGestionar()) return redirect('/dashboard')->with('error','No autorizado');
        return view('docentes.crear');
    }

    // Almacenar un nuevo docente en la base de datos
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'roles_id' => 5,
            'email_virified_at' => now()
        ]);

        return redirect()->route('docentes.index')->with('success', 'Docente creado exitosamente.');
    }

    // Verificar si el usuario es docente o puede gestionar
    private function esDocenteOPuedeGestionar() {
        $u = Auth::user();
        if (!$u || !$u->roles_id) return false;
        $r = RolesModel::find($u->roles_id);
        return $r && (in_array($r->nombre, ['Admin','Rector','Profesor']) || $r->tienePermiso('gestionar_docentes') || $r->tienePermiso('gestionar_notas'));
    }

    // Mostrar lista de grupos disponibles para docentes
    public function grupos() {
        if (!$this->esDocenteOPuedeGestionar()) {
            return redirect('/dashboard')->with('error', 'No autorizado');
        }
        
        $grupos = Grupo::orderBy('nombre')->get();
        return view('docentes.grupos', compact('grupos'));
    }

    // Ver detalles de un grupo específico y sus materias
    public function verGrupo($grupoId) {
        if (!$this->esDocenteOPuedeGestionar()) {
            return redirect('/dashboard')->with('error', 'No autorizado');
        }
        
        $grupo = Grupo::findOrFail($grupoId);
        $materias = Materia::orderBy('nombre')->get(); // Asumiendo que existe un modelo Materia
        $estudiantes = User::where('roles_id', RolesModel::where('nombre','Estudiante')->first()->id)
                          ->where('grupo_id', $grupoId) // Asumiendo que hay relación grupo-estudiante
                          ->orderBy('name')
                          ->get();
        
        return view('docentes.grupo', compact('grupo', 'materias', 'estudiantes'));
    }

    // Gestionar notas para una materia específica de un grupo
    public function gestionarNotas($grupoId, $materiaId) {
        if (!$this->esDocenteOPuedeGestionar()) {
            return redirect('/dashboard')->with('error', 'No autorizado');
        }
        
        $grupo = Grupo::findOrFail($grupoId);
        $materia = Materia::findOrFail($materiaId);
        
        // Obtener estudiantes del grupo
        $estudiantes = User::where('roles_id', RolesModel::where('nombre','Estudiante')->first()->id)
                          ->where('grupo_id', $grupoId)
                          ->orderBy('name')
                          ->get();
        
        // Obtener notas existentes para esta materia y grupo
        $notasExistentes = NotaModel::where('materia_id', $materiaId)
                                  ->whereIn('estudiante_id', $estudiantes->pluck('id'))
                                  ->get()
                                  ->keyBy('estudiante_id');
        
        return view('docentes.notas', compact('grupo', 'materia', 'estudiantes', 'notasExistentes'));
    }

    // Asignar/actualizar nota de un estudiante
    public function asignarNota(Request $request, $grupoId, $materiaId) {
        if (!$this->esDocenteOPuedeGestionar()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        
        $request->validate([
            'estudiante_id' => 'required|exists:users,id',
            'nota' => 'required|numeric|min:0|max:100',
            'periodo' => 'required|string|max:10'
        ]);
        
        // Verificar que el estudiante pertenece al grupo
        $estudiante = User::where('id', $request->estudiante_id)
                         ->where('grupo_id', $grupoId)
                         ->first();
        
        if (!$estudiante) {
            return response()->json(['error' => 'Estudiante no pertenece a este grupo'], 400);
        }
        
        // Crear o actualizar la nota
        NotaModel::updateOrCreate(
            [
                'estudiante_id' => $request->estudiante_id,
                'materia_id' => $materiaId,
                'periodo' => $request->periodo
            ],
            [
                'nota' => $request->nota
            ]
        );
        
        return response()->json(['success' => 'Nota asignada correctamente']);
    }
}