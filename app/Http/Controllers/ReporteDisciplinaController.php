<?php

namespace App\Http\Controllers;

use App\Models\ReporteDisciplina;
use App\Models\ReporteDisciplinaHistorial;
use App\Models\Grupo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReporteDisciplinaController extends Controller
{
    // Listado supervisor (coordinadores / admin / rector)
    public function index(Request $request)
    {
        $user = Auth::user();
        if(!$user) return $this->err('No autenticado',401);
        $puedeVer = $user->tienePermiso('ver_disciplina') || $user->tienePermiso('gestionar_disciplina');
        if(!$puedeVer) return $this->err('No autorizado',403);

        $q = ReporteDisciplina::with(['estudiante','docente','coordinador'])->orderByDesc('id');
        if($request->filled('estado')) $q->where('estado',$request->estado);
        if($request->filled('estudiante_id')) $q->where('estudiante_id',$request->estudiante_id);

        if ($request->wantsJson()) {
            return response()->json($q->paginate(25));
        }

        $reportes = $q->paginate(15)->appends($request->query());
        $estados = [
            ReporteDisciplina::ESTADO_REPORTADO => 'Reportado',
            ReporteDisciplina::ESTADO_EN_REVISION => 'En revisión',
            ReporteDisciplina::ESTADO_SANCION_ASIGNADA => 'Sanción asignada',
            ReporteDisciplina::ESTADO_NOTIFICADO => 'Notificado',
            ReporteDisciplina::ESTADO_APELACION_SOLICITADA => 'Apelación solicitada',
            ReporteDisciplina::ESTADO_APELACION_EN_REVISION => 'Apelación en revisión',
            ReporteDisciplina::ESTADO_APELACION_ACEPTADA => 'Apelación aceptada',
            ReporteDisciplina::ESTADO_APELACION_RECHAZADA => 'Apelación rechazada',
            ReporteDisciplina::ESTADO_ARCHIVADO => 'Archivado',
        ];
        $badgeMap = [
            'reportado'=>'secondary', 'en_revision'=>'info', 'sancion_asignada'=>'warning',
            'notificado'=>'primary', 'apelacion_solicitada'=>'dark', 'apelacion_en_revision'=>'info',
            'apelacion_aceptada'=>'success','apelacion_rechazada'=>'danger','archivado'=>'secondary'
        ];
        $puedeGestionar = $puedeVer && $user->tienePermiso('gestionar_disciplina');
        return view('disciplina.reportes.index', [
            'reportes' => $reportes,
            'filtros' => [
                'estado' => $request->get('estado'),
                'estudiante_id' => $request->get('estudiante_id'),
            ],
            'estados' => $estados,
            'badgeMap' => $badgeMap,
            'puedeGestionar' => $puedeGestionar,
        ]);
    }

    // Mis reportes (estudiante / acudiente / docente)
    public function mis()
    {
        $user = Auth::user();
        if(!$user) return $this->err('No autenticado',401);
        $nombreRol = strtolower($user->rol?->nombre ?? '');
        $q = ReporteDisciplina::with(['estudiante','docente','coordinador'])->orderByDesc('id');
        if(in_array($nombreRol,['estudiante','acudiente'])){
            $q->where('estudiante_id',$user->id);
        } elseif($nombreRol === 'profesor') {
            $q->where('docente_id',$user->id);
        }
        return view('disciplina.reportes.mis', ['reportes'=>$q->limit(100)->get(),'user'=>$user]);
    }

    // Formulario crear (docente)
    public function crear()
    {
        $user = Auth::user();
        if(!$user || !$user->tienePermiso('reportar_incidente')) return $this->err('No autorizado',403);
        $grupos = Grupo::orderBy('nombre')->get(['id','nombre']);
        return view('disciplina.reportes.crear',compact('grupos'));
    }

    // Guardar reporte
    public function store(Request $request)
    {
        $user = Auth::user();
        if(!$user || !$user->tienePermiso('reportar_incidente')) return $this->err('No autorizado',403);
        $request->validate([
            'estudiante_id' => 'required|exists:users,id',
            'descripcion_incidente' => 'required|string',
            'gravedad' => 'nullable|string|in:leve,moderada,grave'
        ]);
        $rep = ReporteDisciplina::create([
            'estudiante_id'=>$request->estudiante_id,
            'docente_id'=>$user->id,
            'estado'=>ReporteDisciplina::ESTADO_REPORTADO,
            'descripcion_incidente'=>$request->descripcion_incidente,
            'gravedad'=>$request->gravedad
        ]);
        $this->log($rep,null,$rep->estado,'Reporte creado');
        return redirect()->route('disciplina.reportes.mis')->with('ok','Reporte registrado');
    }

    // Revisar (coordinador)
    public function revisar($id)
    {
        $rep = ReporteDisciplina::find($id); if(!$rep) return $this->err('No encontrado',404);
        $user = Auth::user(); if(!$user || !$user->tienePermiso('gestionar_disciplina')) return $this->err('No autorizado',403);
        if($rep->estado !== ReporteDisciplina::ESTADO_REPORTADO) return $this->err('Estado inválido',409);
        $prev = $rep->estado;
        $rep->estado = ReporteDisciplina::ESTADO_EN_REVISION;
        $rep->coordinador_id = $user->id;
        $rep->save();
        $this->log($rep,$prev,$rep->estado,'Revisión iniciada');
        return back()->with('ok','Revisión iniciada');
    }

    // Asignar sanción
    public function asignarSancion(Request $request,$id)
    {
        $rep = ReporteDisciplina::find($id); if(!$rep) return $this->err('No encontrado',404);
        $user = Auth::user(); if(!$user || !$user->tienePermiso('gestionar_disciplina')) return $this->err('No autorizado',403);
        if(!in_array($rep->estado,[ReporteDisciplina::ESTADO_EN_REVISION])) return $this->err('Estado inválido',409);
        $request->validate(['sancion_text'=>'required|string']);
        $prev = $rep->estado;
        $rep->estado = ReporteDisciplina::ESTADO_SANCION_ASIGNADA;
        $rep->sancion_text = $request->sancion_text;
        $rep->sancion_activa = true;
        $rep->sancion_asignada_at = now();
        $rep->save();
        $this->log($rep,$prev,$rep->estado,'Sanción asignada');
        return back()->with('ok','Sanción asignada');
    }

    // Notificar sanción
    public function notificar($id)
    {
        $rep = ReporteDisciplina::find($id); if(!$rep) return $this->err('No encontrado',404);
        $user = Auth::user(); if(!$user || !$user->tienePermiso('gestionar_disciplina')) return $this->err('No autorizado',403);
        if($rep->estado !== ReporteDisciplina::ESTADO_SANCION_ASIGNADA) return $this->err('Estado inválido',409);
        $prev = $rep->estado;
        $rep->estado = ReporteDisciplina::ESTADO_NOTIFICADO;
        $rep->notificado_at = now();
        $rep->save();
        $this->log($rep,$prev,$rep->estado,'Sanción notificada');
        return back()->with('ok','Notificado');
    }

    // Solicitar apelación (acudiente)
    public function solicitarApelacion(Request $request,$id)
    {
        $rep = ReporteDisciplina::find($id); if(!$rep) return $this->err('No encontrado',404);
        $user = Auth::user(); if(!$user || !$user->tienePermiso('apelar_sancion')) return $this->err('No autorizado',403);
        if(!in_array($rep->estado,[ReporteDisciplina::ESTADO_SANCION_ASIGNADA,ReporteDisciplina::ESTADO_NOTIFICADO])) return $this->err('Estado inválido',409);
        $request->validate(['apelacion_motivo'=>'required|string']);
        $prev = $rep->estado;
        $rep->estado = ReporteDisciplina::ESTADO_APELACION_SOLICITADA;
        $rep->apelacion_motivo = $request->apelacion_motivo;
        $rep->save();
        $this->log($rep,$prev,$rep->estado,'Apelación solicitada');
        return back()->with('ok','Apelación solicitada');
    }

    // Revisar apelación (coordinador)
    public function revisarApelacion($id)
    {
        $rep = ReporteDisciplina::find($id); if(!$rep) return $this->err('No encontrado',404);
        $user = Auth::user(); if(!$user || !$user->tienePermiso('gestionar_disciplina')) return $this->err('No autorizado',403);
        if($rep->estado !== ReporteDisciplina::ESTADO_APELACION_SOLICITADA) return $this->err('Estado inválido',409);
        $prev = $rep->estado;
        $rep->estado = ReporteDisciplina::ESTADO_APELACION_EN_REVISION;
        $rep->save();
        $this->log($rep,$prev,$rep->estado,'Apelación en revisión');
        return back()->with('ok','Apelación en revisión');
    }

    // Resolver apelación (aceptar o rechazar). Si aceptada puede modificar/eliminar sanción.
    public function resolverApelacion(Request $request,$id)
    {
        $rep = ReporteDisciplina::find($id); if(!$rep) return $this->err('No encontrado',404);
        $user = Auth::user(); if(!$user || !$user->tienePermiso('gestionar_disciplina')) return $this->err('No autorizado',403);
        if($rep->estado !== ReporteDisciplina::ESTADO_APELACION_EN_REVISION) return $this->err('Estado inválido',409);
        $request->validate([
            'resultado' => 'required|string|in:aceptada,rechazada',
            'nueva_sancion_text' => 'nullable|string',
            'eliminar_sancion' => 'nullable|boolean'
        ]);
        $prev = $rep->estado;
        if($request->resultado === 'aceptada'){
            $rep->estado = ReporteDisciplina::ESTADO_APELACION_ACEPTADA;
            if($request->boolean('eliminar_sancion')){
                $rep->sancion_activa = false;
                $rep->sancion_eliminada_at = now();
            } elseif($request->filled('nueva_sancion_text')) {
                $rep->sancion_text = $request->nueva_sancion_text;
                $rep->sancion_modificada_at = now();
            }
        } else {
            $rep->estado = ReporteDisciplina::ESTADO_APELACION_RECHAZADA;
        }
        $rep->apelacion_result = $request->resultado;
        $rep->apelacion_resuelta_at = now();
        $rep->save();
        $this->log($rep,$prev,$rep->estado,'Apelación '.$request->resultado);
        return back()->with('ok','Apelación resuelta');
    }

    // Archivar caso
    public function archivar($id)
    {
        $rep = ReporteDisciplina::find($id); if(!$rep) return $this->err('No encontrado',404);
        $user = Auth::user(); if(!$user || !$user->tienePermiso('gestionar_disciplina')) return $this->err('No autorizado',403);
        if(!in_array($rep->estado,[ReporteDisciplina::ESTADO_NOTIFICADO,ReporteDisciplina::ESTADO_APELACION_ACEPTADA,ReporteDisciplina::ESTADO_APELACION_RECHAZADA])) return $this->err('Estado inválido',409);
        $prev = $rep->estado;
        $rep->estado = ReporteDisciplina::ESTADO_ARCHIVADO;
        $rep->archivado_at = now();
        $rep->save();
        $this->log($rep,$prev,$rep->estado,'Caso archivado');
        return back()->with('ok','Archivado');
    }

    // Historial JSON
    public function historial($id)
    {
        $rep = ReporteDisciplina::find($id); if(!$rep) return $this->err('No encontrado',404);
        $user = Auth::user(); if(!$user) return $this->err('No autenticado',401);
        $puede = $user->tienePermiso('ver_disciplina') || $rep->estudiante_id === $user->id || $rep->docente_id === $user->id;
        if(!$puede) return $this->err('No autorizado',403);
        return response()->json($rep->historial()->get());
    }

    private function log(ReporteDisciplina $rep,$from,$to,$descripcion=null)
    {
        ReporteDisciplinaHistorial::create([
            'reporte_id'=>$rep->id,
            'user_id'=>Auth::id(),
            'estado_from'=>$from,
            'estado_to'=>$to,
            'descripcion'=>$descripcion
        ]);
    }

    // Listado de grupos (para formulario)
    public function grupos()
    {
        $user = Auth::user(); if(!$user) return $this->err('No autenticado',401);
        if(!$user->tienePermiso('reportar_incidente')) return $this->err('No autorizado',403);
        return response()->json(Grupo::orderBy('nombre')->get(['id','nombre']));
    }
    // Estudiantes por grupo
    public function estudiantesGrupo($id)
    {
        $user = Auth::user(); if(!$user) return $this->err('No autenticado',401);
        if(!$user->tienePermiso('reportar_incidente')) return $this->err('No autorizado',403);
        $grupo = Grupo::find($id); if(!$grupo) return $this->err('Grupo no encontrado',404);
        $estudiantes = $grupo->estudiantes()->orderBy('name')->get(['id','name','grupo_id']);
        return response()->json($estudiantes);
    }

    private function err($msg,$code=400){ return response()->json(['message'=>$msg],$code); }
}
