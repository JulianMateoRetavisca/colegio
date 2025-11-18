<?php

namespace App\Http\Controllers;

use App\Models\OrientacionCita;
use App\Models\OrientacionCitaHistorial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrientacionCitaController extends Controller
{
    // Listado filtrable por estado / orientador / estudiante
    public function index(Request $request)
    {
        $query = OrientacionCita::with(['estudiante','orientador'])->orderByDesc('id');
        if ($estado = $request->query('estado')) $query->where('estado',$estado);
        if ($request->query('estudiante_id')) $query->where('estudiante_id',$request->query('estudiante_id'));
        if ($request->query('orientador_id')) $query->where('orientador_id',$request->query('orientador_id'));
        return response()->json($query->paginate(25));
    }

    // Estudiante solicita cita
    public function solicitar(Request $request)
    {
        if (!Auth::check() || !Auth::user()->tienePermiso('solicitar_orientacion')) {
            return response()->json(['message'=>'No autorizado'],403);
        }
        $request->validate([
            'fecha_solicitada' => 'nullable|date',
            'motivo' => 'required|string|max:180'
        ]);
        $cita = OrientacionCita::create([
            'estudiante_id' => Auth::id(),
            'estado' => OrientacionCita::ESTADO_SOLICITADA,
            'fecha_solicitada' => $request->fecha_solicitada,
            'motivo' => $request->motivo
        ]);
        $this->logHistorial($cita,null,OrientacionCita::ESTADO_SOLICITADA,'Solicitud creada');
        return response()->json($cita,201);
    }

    // Orientador revisa solicitud
    public function revisar($id)
    {
        $cita = OrientacionCita::find($id);
        if(!$cita) return $this->err('Cita no encontrada',404);
        if(!Auth::check() || !Auth::user()->tienePermiso('gestionar_orientacion')) return $this->err('No autorizado',403);
        if($cita->estado !== OrientacionCita::ESTADO_SOLICITADA) return $this->err('Solo citas solicitadas pueden revisarse',409);
        $prev = $cita->estado;
        $cita->estado = OrientacionCita::ESTADO_REVISADA;
        $cita->orientador_id = Auth::id();
        $cita->save();
        $this->logHistorial($cita,$prev,$cita->estado,'Cita revisada');
        return response()->json(['message'=>'Cita revisada','cita'=>$cita]);
    }

    // Asignar fecha y hora (orientador)
    public function asignar(Request $request,$id)
    {
        $cita = OrientacionCita::find($id);
        if(!$cita) return $this->err('Cita no encontrada',404);
        if(!Auth::check() || !Auth::user()->tienePermiso('gestionar_orientacion')) return $this->err('No autorizado',403);
        if(!in_array($cita->estado,[OrientacionCita::ESTADO_REVISADA,OrientacionCita::ESTADO_REPROGRAMADA])) return $this->err('La cita debe estar revisada o reprogramada',409);
        $request->validate([
            'fecha_asignada' => 'required|date|after_or_equal:today',
            'hora_asignada' => 'required'
        ]);
        $prev = $cita->estado;
        $cita->fecha_asignada = $request->fecha_asignada;
        $cita->hora_asignada = $request->hora_asignada;
        $cita->estado = OrientacionCita::ESTADO_ASIGNADA;
        $cita->save();
        $this->logHistorial($cita,$prev,$cita->estado,'Fecha y hora asignadas');
        return response()->json(['message'=>'Cita asignada','cita'=>$cita]);
    }

    // Reprogramar (no disponible fecha/hora anterior)
    public function reprogramar(Request $request,$id)
    {
        $cita = OrientacionCita::find($id);
        if(!$cita) return $this->err('Cita no encontrada',404);
        if(!Auth::check() || !Auth::user()->tienePermiso('gestionar_orientacion')) return $this->err('No autorizado',403);
        if(!in_array($cita->estado,[OrientacionCita::ESTADO_ASIGNADA])) return $this->err('Solo citas asignadas pueden reprogramarse',409);
        $request->validate([
            'fecha_asignada' => 'required|date|after_or_equal:today',
            'hora_asignada' => 'required'
        ]);
        $prev = $cita->estado;
        $cita->fecha_asignada = $request->fecha_asignada;
        $cita->hora_asignada = $request->hora_asignada;
        $cita->estado = OrientacionCita::ESTADO_REPROGRAMADA;
        $cita->save();
        $this->logHistorial($cita,$prev,$cita->estado,'Cita reprogramada');
        return response()->json(['message'=>'Cita reprogramada','cita'=>$cita]);
    }

    // Marcar asistencia y realizar orientación
    public function realizar($id)
    {
        $cita = OrientacionCita::find($id);
        if(!$cita) return $this->err('Cita no encontrada',404);
        if(!Auth::check() || !Auth::user()->tienePermiso('gestionar_orientacion')) return $this->err('No autorizado',403);
        if(!in_array($cita->estado,[OrientacionCita::ESTADO_ASIGNADA,OrientacionCita::ESTADO_REPROGRAMADA])) return $this->err('La cita debe estar asignada',409);
        $prev = $cita->estado;
        $cita->estado = OrientacionCita::ESTADO_REALIZADA;
        $cita->save();
        $this->logHistorial($cita,$prev,$cita->estado,'Cita realizada');
        return response()->json(['message'=>'Cita realizada','cita'=>$cita]);
    }

    // Registrar observaciones luego de realizar
    public function registrarObservaciones(Request $request,$id)
    {
        $cita = OrientacionCita::find($id);
        if(!$cita) return $this->err('Cita no encontrada',404);
        if(!Auth::check() || !Auth::user()->tienePermiso('gestionar_orientacion')) return $this->err('No autorizado',403);
        if($cita->estado !== OrientacionCita::ESTADO_REALIZADA) return $this->err('Debe estar realizada para registrar observaciones',409);
        $request->validate(['observaciones'=>'required|string']);
        $prev = $cita->estado;
        $cita->observaciones = $request->observaciones;
        $cita->estado = OrientacionCita::ESTADO_REGISTRADA;
        $cita->save();
        $this->logHistorial($cita,$prev,$cita->estado,'Observaciones registradas');
        return response()->json(['message'=>'Observaciones registradas','cita'=>$cita]);
    }

    // Evaluar seguimiento y cerrar o programar próxima
    public function evaluarSeguimiento(Request $request,$id)
    {
        $cita = OrientacionCita::find($id);
        if(!$cita) return $this->err('Cita no encontrada',404);
        if(!Auth::check() || !Auth::user()->tienePermiso('gestionar_orientacion')) return $this->err('No autorizado',403);
        if($cita->estado !== OrientacionCita::ESTADO_REGISTRADA) return $this->err('Debe estar registrada para evaluar seguimiento',409);
        $request->validate([
            'seguimiento' => 'required|boolean',
            'fecha_proxima' => 'nullable|date|after:today'
        ]);
        $prev = $cita->estado;
        if($request->seguimiento){
            if(!$request->fecha_proxima) return $this->err('Fecha próxima requerida para seguimiento',422);
            $cita->seguimiento_requerido = true;
            $cita->fecha_proxima = $request->fecha_proxima;
            $cita->estado = OrientacionCita::ESTADO_SEGUIMIENTO;
        } else {
            $cita->seguimiento_requerido = false;
            $cita->cerrada_at = now();
            $cita->estado = OrientacionCita::ESTADO_CERRADA;
        }
        $cita->save();
        $this->logHistorial($cita,$prev,$cita->estado,'Evaluación seguimiento');
        return response()->json(['message'=>'Evaluación de seguimiento registrada','cita'=>$cita]);
    }

    // Listar historial
    public function historial($id)
    {
        $cita = OrientacionCita::find($id);
        if(!$cita) return $this->err('Cita no encontrada',404);
        $user = Auth::user();
        if(!$user) return $this->err('No autenticado',401);
        $puedeVerTodas = $user->tienePermiso('gestionar_orientacion') || $user->tienePermiso('ver_orientacion');
        if(!$puedeVerTodas && $cita->estudiante_id !== $user->id){
            return $this->err('No autorizado',403);
        }
        $historial = OrientacionCitaHistorial::where('cita_id',$cita->id)->orderBy('id')->get();
        return response()->json($historial);
    }

    // Vista HTML (listado básico con acciones según estado)
    public function vistaListado()
    {
        $user = Auth::user();
        if(!$user) return redirect()->route('login');
        $query = OrientacionCita::with(['estudiante','orientador'])->orderByDesc('id');
        // Si NO puede gestionar orientación, solo ve sus propias citas (estudiante o profesor mirando casos de sus estudiantes se podría ampliar luego)
        if(!$user->tienePermiso('gestionar_orientacion')){
            $query->where('estudiante_id',$user->id);
        }
        $citas = $query->limit(100)->get();
        return view('orientacion.citas.index',compact('citas','user'));
    }

    // Vista para supervisión (Admin / Rector / Coordinadores) con filtros, sin acciones de flujo
    public function vistaAdmin(Request $request)
    {
        $user = Auth::user();
        if(!$user) return redirect()->route('login');
        if(!$user->tienePermiso('ver_orientacion')) return $this->err('No autorizado',403);

        $query = OrientacionCita::with(['estudiante','orientador'])->orderByDesc('id');
        if($request->filled('estado')) $query->where('estado',$request->estado);
        if($request->filled('estudiante_id')) $query->where('estudiante_id',$request->estudiante_id);
        if($request->filled('orientador_id')) $query->where('orientador_id',$request->orientador_id);
        $citas = $query->paginate(25)->appends($request->query());

        // Listas auxiliares simples (podrían optimizarse luego)
        $estudiantes = OrientacionCita::select('estudiante_id')->distinct()->with('estudiante')->get()->pluck('estudiante');
        $orientadores = OrientacionCita::select('orientador_id')->whereNotNull('orientador_id')->distinct()->with('orientador')->get()->pluck('orientador');
        $estados = [
            OrientacionCita::ESTADO_SOLICITADA,
            OrientacionCita::ESTADO_REVISADA,
            OrientacionCita::ESTADO_ASIGNADA,
            OrientacionCita::ESTADO_REPROGRAMADA,
            OrientacionCita::ESTADO_REALIZADA,
            OrientacionCita::ESTADO_REGISTRADA,
            OrientacionCita::ESTADO_SEGUIMIENTO,
            OrientacionCita::ESTADO_CERRADA,
        ];
        return view('orientacion.citas.admin',compact('citas','user','estudiantes','orientadores','estados'));
    }

    private function logHistorial(OrientacionCita $cita,$from,$to,$descripcion=null)
    {
        OrientacionCitaHistorial::create([
            'cita_id' => $cita->id,
            'user_id' => Auth::id(),
            'estado_from' => $from,
            'estado_to' => $to,
            'descripcion' => $descripcion
        ]);
    }

    private function err($msg,$code=400){ return response()->json(['message'=>$msg],$code); }
}
