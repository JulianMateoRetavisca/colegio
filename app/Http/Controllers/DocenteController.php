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
     * HELPERS INTERNOS
     * ============================================================ */

    /**
     * Construye un mapa [estudiante_id => [periodo => nota]] para una materia dada
     * evitando duplicar lógica entre gestionarNotas y resumenNotas.
     */
    private function mapaNotasMateria($materiaId, $estudiantesIds) {
        if (empty($materiaId) || empty($estudiantesIds)) return [];

        $todas = NotaModel::where('materia_id', $materiaId)
            ->whereIn('estudiante_id', $estudiantesIds)
            ->get(['estudiante_id','periodo','nota','bloqueado']);

        $mapa = [];
        foreach ($todas as $n) {
            $p = (string) intval($n->periodo);
            if (!in_array($p, ['1','2','3','4'])) continue;
            $mapa[$n->estudiante_id][$p] = [
                'nota' => $n->nota,
                'bloqueado' => (bool)$n->bloqueado
            ];
        }
        return $mapa;
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
        if (!in_array($periodoSeleccionado, ['1','2','3','4'])) $periodoSeleccionado = '1';

        $idEstudiante = RolesModel::where('nombre','Estudiante')->value('id');

        $estudiantes = User::where('roles_id', $idEstudiante)
            ->where('grupo_id', $grupoId)
            ->orderBy('name')
            ->get();

        /** Notas del período seleccionado (solo ese periodo) */
        $notasExistentes = NotaModel::where('materia_id', $materiaId)
            ->where('periodo', $periodoSeleccionado)
            ->whereIn('estudiante_id', $estudiantes->pluck('id'))
            ->get()
            ->keyBy('estudiante_id');

        $periodos = ['1','2','3','4'];
        $mapaNotas = $this->mapaNotasMateria($materiaId, $estudiantes->pluck('id')->all());

        return view('docentes.notas', compact(
            'grupo','materia','estudiantes','notasExistentes','periodoSeleccionado','mapaNotas','periodos'
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

        /** Validar rol estudiante */
        $idEstudianteRol = RolesModel::where('nombre','Estudiante')->value('id');
        if ($est->roles_id != $idEstudianteRol) {
            return response()->json(['error'=>'El usuario indicado no es un estudiante'],400);
        }

        /** Validar materia */
        $materia = Materia::find($materiaId);
        if (!$materia)
            return response()->json(['error'=>'Materia no encontrada'],404);

        $periodo = (string) intval($request->periodo);
        $valor   = round((float)$request->nota, 2);

        $notaRegistro = NotaModel::where('estudiante_id', $request->estudiante_id)
            ->where('materia_id', $materiaId)
            ->where('periodo', $periodo)
            ->first();

        if ($notaRegistro && $notaRegistro->bloqueado) {
            return response()->json(['error'=>'La nota está bloqueada y no puede modificarse'],403);
        }

        $actual = NotaModel::updateOrCreate(
            [
                'estudiante_id' => $request->estudiante_id,
                'materia_id'    => $materiaId,
                'periodo'       => $periodo
            ],
            [
                'nota' => $valor
            ]
        );

        $notasEstudiante = NotaModel::where('materia_id',$materiaId)
            ->where('estudiante_id',$request->estudiante_id)
            ->get(['periodo','nota','bloqueado'])
            ->mapWithKeys(function($n){
                $p = (string) intval($n->periodo);
                return [$p => [
                    'nota' => $n->nota,
                    'bloqueado' => (bool)$n->bloqueado
                ]];
            });

        return response()->json([
            'success' => 'Nota asignada correctamente',
            'registro' => [
                'estudiante_id' => $actual->estudiante_id,
                'materia_id'    => $actual->materia_id,
                'periodo'       => $actual->periodo,
                'nota'          => $actual->nota
            ],
            'notas_periodos' => $notasEstudiante
        ]);
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
                $mapaNotas = $this->mapaNotasMateria($materiaSeleccionada, $estudiantes->pluck('id')->all());
            }
        }

        return view('docentes.notas_resumen', compact(
            'grupos','materias','grupoSeleccionado','materiaSeleccionada','estudiantes','mapaNotas','periodos'
        ));
    }
}
