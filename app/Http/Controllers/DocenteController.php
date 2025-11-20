<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\RolesModel;
use App\Models\Grupo;
use App\Models\Materia;
use App\Models\NotaModel;
use Illuminate\Support\Facades\Auth;

class DocenteController extends Controller
{
    /* ============================================================
     * VALIDACIONES DE PERMISOS
     * ============================================================ */

    private function puedeGestionar() {
        $u = Auth::user();
        if (!$u || !$u->roles_id) return false;

        $r = RolesModel::find($u->roles_id);
        return $r && (
            in_array($r->nombre, ['Admin','Rector']) ||
            $r->tienePermiso('gestionar_docentes')
        );
    }

    private function esDocenteOPuedeGestionar() {
        $u = Auth::user();
        if (!$u || !$u->roles_id) return false;

        $r = RolesModel::find($u->roles_id);

        return $r &&
            (in_array($r->nombre, ['Admin','Rector','Profesor']) ||
            $r->tienePermiso('gestionar_docentes') ||
            $r->tienePermiso('gestionar_notas'));
    }

    /* ============================================================
     * CRUD BÁSICO DE DOCENTES
     * ============================================================ */

    public function index() {
        if (!$this->puedeGestionar()) return redirect('/dashboard')->with('error','No autorizado');

        $idProfesor = RolesModel::where('nombre','Profesor')->value('id');
        $docentes = User::where('roles_id', $idProfesor)->get();

        return view('docentes.index', compact('docentes'));
    }

    public function crear() {
        if (!$this->puedeGestionar()) return redirect('/dashboard')->with('error','No autorizado');
        return view('docentes.crear');
    }

    public function store(Request $request) {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name'               => $request->name,
            'email'              => $request->email,
            'password'           => bcrypt($request->password),
            'roles_id'           => 5, // Profesor
            'email_verified_at'  => now()
        ]);

        return redirect()->route('docentes.index')->with('success','Docente creado exitosamente.');
    }

    /* ============================================================
     * GRUPOS - LISTAR / VER
     * ============================================================ */

    public function grupos() {
        if (!$this->esDocenteOPuedeGestionar()) return redirect('/dashboard')->with('error','No autorizado');

        $grupos = Grupo::orderBy('nombre')->get();
        return view('docentes.grupos', compact('grupos'));
    }

    public function verGrupo($grupoId) {
        if (!$this->esDocenteOPuedeGestionar()) return redirect('/dashboard')->with('error','No autorizado');

        $grupo = Grupo::findOrFail($grupoId);
        $materias = Materia::orderBy('nombre')->get();

        $idEstudiante = RolesModel::where('nombre','Estudiante')->value('id');

        $estudiantes = User::where('roles_id', $idEstudiante)
            ->where('grupo_id', $grupoId)
            ->orderBy('name')
            ->get();

        return view('docentes.grupo', compact('grupo','materias','estudiantes'));
    }

    /* ============================================================
     * GESTIONAR NOTAS DE UN GRUPO Y MATERIA
     * ============================================================ */

    public function gestionarNotas(Request $request, $grupoId, $materiaId) {
        if (!$this->esDocenteOPuedeGestionar()) return redirect('/dashboard')->with('error','No autorizado');

        $grupo   = Grupo::findOrFail($grupoId);
        $materia = Materia::findOrFail($materiaId);

        $periodoSeleccionado = (string)($request->query('periodo', '1'));

        $idEstudiante = RolesModel::where('nombre','Estudiante')->value('id');

        $estudiantes = User::where('roles_id', $idEstudiante)
            ->where('grupo_id', $grupoId)
            ->orderBy('name')
            ->get();

        /** Notas del período seleccionado */
        $notasExistentes = NotaModel::where('materia_id',$materiaId)
            ->where('periodo',$periodoSeleccionado)
            ->whereIn('estudiante_id',$estudiantes->pluck('id'))
            ->get()
            ->keyBy('estudiante_id');

        /** Cargar notas de todos los períodos */
        $periodos  = ['1','2','3','4'];
        $todasNotas = NotaModel::where('materia_id',$materiaId)
            ->whereIn('estudiante_id',$estudiantes->pluck('id'))
            ->get(['estudiante_id','periodo','nota']);

        $mapaNotas = [];
        foreach ($todasNotas as $n) {
            $p = (string) intval($n->periodo);
            if (!in_array($p, ['1','2','3','4'])) continue;
            $mapaNotas[$n->estudiante_id][$p] = $n->nota;
        }

        return view('docentes.notas', compact(
            'grupo','materia','estudiantes','notasExistentes','periodoSeleccionado','mapaNotas'
        ));
    }

    /* ============================================================
     * ASIGNAR / ACTUALIZAR NOTA
     * ============================================================ */

    public function asignarNota(Request $request, $grupoId, $materiaId) {
        if (!$this->esDocenteOPuedeGestionar())
            return response()->json(['error'=>'No autorizado'],403);

        $request->validate([
            'estudiante_id' => 'required|exists:users,id',
            'nota'          => 'required|numeric|min:0|max:100',
            'periodo'       => 'required|in:1,2,3,4'
        ]);

        /** Validar estudiante del grupo */
        $est = User::where('id',$request->estudiante_id)
            ->where('grupo_id',$grupoId)
            ->first();

        if (!$est)
            return response()->json(['error'=>'Estudiante no pertenece al grupo'],400);

        /** Validar materia */
        $materia = Materia::find($materiaId);
        if (!$materia)
            return response()->json(['error'=>'Materia no encontrada'],404);

        $periodo = (string) intval($request->periodo);
        $valor   = round((float)$request->nota, 2);

        NotaModel::updateOrCreate(
            [
                'estudiante_id' => $request->estudiante_id,
                'materia_id'    => $materiaId,
                'periodo'       => $periodo
            ],
            ['nota' => $valor]
        );

        return response()->json(['success'=>'Nota asignada correctamente']);
    }

    /* ============================================================
     * RESUMEN DE NOTAS
     * ============================================================ */

    public function resumenNotas(Request $request) {
        if (!$this->esDocenteOPuedeGestionar()) return redirect('/dashboard')->with('error','No autorizado');

        $grupoSeleccionado   = $request->query('grupo');
        $materiaSeleccionada = $request->query('materia');

        $grupos   = Grupo::orderBy('nombre')->get();
        $materias = Materia::orderBy('nombre')->get();

        $estudiantes = collect();
        $mapaNotas   = [];
        $periodos    = ['1','2','3','4'];

        if ($grupoSeleccionado && $materiaSeleccionada) {
            $idEstudiante = RolesModel::where('nombre','Estudiante')->value('id');

            $estudiantes = User::where('roles_id',$idEstudiante)
                ->where('grupo_id',$grupoSeleccionado)
                ->orderBy('name')
                ->get();

            if ($estudiantes->isNotEmpty()) {
                $todasNotas = NotaModel::where('materia_id',$materiaSeleccionada)
                    ->whereIn('estudiante_id',$estudiantes->pluck('id'))
                    ->get(['estudiante_id','periodo','nota']);

                foreach ($todasNotas as $n) {
                    $p = (string) intval($n->periodo);
                    if (!in_array($p, ['1','2','3','4'])) continue;
                    $mapaNotas[$n->estudiante_id][$p] = $n->nota;
                }
            }
        }

        return view('docentes.notas_resumen', compact(
            'grupos','materias','grupoSeleccionado','materiaSeleccionada','estudiantes','mapaNotas','periodos'
        ));
    }
}
