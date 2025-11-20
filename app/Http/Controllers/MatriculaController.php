<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Matricula;
use App\Models\MatriculaDocumento;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\User;

class MatriculaController extends Controller
{
    public function iniciarMatricula()
    {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Debes iniciar sesión para continuar.');
        }

        $user = Auth::user();

        if ($user->roles_id != 7) {
            return redirect('/dashboard')->with('error', 'No tienes permisos para iniciar la matrícula.');
        }

        return view('matricula.iniciar');
    }

    public function guardarMatricula(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Debes iniciar sesión para continuar.');
        }

        $user = Auth::user();

        if ($user->roles_id != 7) {
            return redirect('/dashboard')->with('error', 'No tienes permisos para realizar esta acción.');
        }

        $request->validate([
            'nombre_estudiante' => 'required|string|max:100',
            'grado' => 'required|string|max:50',
            'telefono_contacto' => 'required|string|max:15',
            'direccion' => 'required|string|max:150',
            'correo_contacto' => 'required|email|max:100',
        ]);

        $matricula = new Matricula();
        $matricula->acudiente_id = $user->id;
        $matricula->nombre_estudiante = $request->nombre_estudiante;
        $matricula->grado = $request->grado;
        $matricula->telefono_contacto = $request->telefono_contacto;
        $matricula->direccion = $request->direccion;
        $matricula->correo_contacto = $request->correo_contacto;
        $matricula->estado = 'pendiente';
        $matricula->save();

        return redirect()->route('matricula.iniciar')->with('success', 'Matrícula iniciada correctamente. Pronto recibirás confirmación.');
    }

    public function verMisMatriculas()
    {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Debes iniciar sesión.');
        }

        $user = Auth::user();

        if ($user->roles_id != 7) {
            return redirect('/dashboard')->with('error', 'No tienes permisos para ver esta información.');
        }

        $matriculas = Matricula::where('acudiente_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('matricula.mis_matriculas', compact('matriculas'));
    }

    // Nueva vista para que administradores/rectores gestionen matrículas pendientes
    public function mostrarMatriculasPendientes()
    {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Debes iniciar sesión.');
        }

        $user = Auth::user();

        if ($user->roles_id != 1 && $user->roles_id != 2) {
            return redirect('/dashboard')->with('error', 'No tienes permisos para ver esta página.');
        }

        $matriculas = Matricula::where('estado', 'pendiente')->get();

        return view('matricula.aceptar', compact('matriculas'));
    }

    // Aceptar o rechazar una matrícula
    public function gestionarMatricula($id, $accion)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Debes iniciar sesión.');
        }

        $user = Auth::user();

        if ($user->roles_id != 1 && $user->roles_id != 2) {
            return redirect('/dashboard')->with('error', 'No tienes permisos para realizar esta acción.');
        }

        $matricula = Matricula::find($id);
        if (!$matricula) {
            return redirect()->back()->with('error', 'Matrícula no encontrada.');
        }

        if ($accion == 'aceptar') {
            $matricula->estado = 'aceptada';

            // Crear usuario estudiante asociado
            $estudiante = new User();
            $estudiante->name = $matricula->nombre_estudiante;
            $estudiante->email = $matricula->correo_contacto;
            $estudiante->password = bcrypt('123456'); // Contraseña inicial
            $estudiante->roles_id = 6;
            $estudiante->save();
        } elseif ($accion == 'rechazar') {
            $matricula->estado = 'rechazada';
        } else {
            return redirect()->back()->with('error', 'Acción inválida.');
        }

        $matricula->save();

        // Redirige a la página de gestión de matrículas
        return redirect()->route('matricula.aceptar')->with('success', 'Matrícula gestionada correctamente.');
    }

    /* =============================================================
     * SUBIDA Y GESTIÓN DE DOCUMENTOS (ACUDIENTE / ADMIN)
     * ============================================================= */

    // Formulario para subir documentos asociados a una matrícula (acudiente)
    public function documentosForm($id)
    {
        $user = Auth::user();
        if (!$user || $user->roles_id != 7) {
            return redirect('/dashboard')->with('error','No autorizado');
        }

        $matricula = Matricula::where('id',$id)->where('acudiente_id',$user->id)->first();
        if (!$matricula) return redirect()->back()->with('error','Matrícula no encontrada');

        $documentos = MatriculaDocumento::where('matricula_id',$matricula->id)->orderBy('created_at','desc')->get();
        return view('matricula.documentos', compact('matricula','documentos'));
    }

    // Procesar subida de uno o varios documentos
    public function documentosSubir(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user || $user->roles_id != 7) {
            return redirect('/dashboard')->with('error','No autorizado');
        }

        $matricula = Matricula::where('id',$id)->where('acudiente_id',$user->id)->first();
        if (!$matricula) return redirect()->back()->with('error','Matrícula no encontrada');

        $request->validate([
            'documentos.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:4096',
            'tipo' => 'nullable|string|max:50'
        ]);

        if (!$request->hasFile('documentos')) {
            return redirect()->back()->with('error','No se recibió ningún archivo');
        }

        foreach ($request->file('documentos') as $file) {
            $storedName = Str::uuid()->toString().'_'.preg_replace('/[^A-Za-z0-9\.\-_]/','_', $file->getClientOriginalName());
            $path = $file->storeAs('matriculas/'.$matricula->id, $storedName, 'local');

            MatriculaDocumento::create([
                'matricula_id' => $matricula->id,
                'uploaded_by' => $user->id,
                'tipo' => $request->input('tipo'),
                'original_name' => $file->getClientOriginalName(),
                'stored_name' => $storedName,
                'mime_type' => $file->getClientMimeType(),
                'size_bytes' => $file->getSize()
            ]);
        }

        return redirect()->route('matricula.documentos.form', $matricula->id)->with('success','Documento(s) subidos correctamente');
    }

    // Vista para administradores/rectores ver documentos de una matrícula
    public function documentosAdmin($id)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->roles_id,[1,2])) {
            return redirect('/dashboard')->with('error','No autorizado');
        }

        $matricula = Matricula::find($id);
        if (!$matricula) return redirect()->back()->with('error','Matrícula no encontrada');

        $documentos = MatriculaDocumento::where('matricula_id',$matricula->id)->orderBy('created_at','desc')->get();
        return view('matricula.documentos_admin', compact('matricula','documentos'));
    }

    // Descargar documento (acudiente propietario o admin/rector)
    public function documentoDescargar($documentoId)
    {
        $user = Auth::user();
        if (!$user) return redirect('/login');

        $doc = MatriculaDocumento::find($documentoId);
        if (!$doc) return redirect()->back()->with('error','Documento no encontrado');

        $matricula = Matricula::find($doc->matricula_id);
        if (!$matricula) return redirect()->back()->with('error','Matrícula asociada no encontrada');

        $puede = ($user->roles_id == 7 && $matricula->acudiente_id == $user->id) || in_array($user->roles_id,[1,2]);
        if (!$puede) return redirect('/dashboard')->with('error','No autorizado');

        $filePath = 'matriculas/'.$matricula->id.'/'.$doc->stored_name;
        if (!Storage::disk('local')->exists($filePath)) {
            return redirect()->back()->with('error','Archivo no existe en el servidor');
        }
        return Storage::disk('local')->download($filePath, $doc->original_name);
    }

    // Eliminar documento (solo acudiente propietario mientras matrícula pendiente o admin/rector)
    public function documentoEliminar($documentoId)
    {
        $user = Auth::user();
        if (!$user) return redirect('/login');

        $doc = MatriculaDocumento::find($documentoId);
        if (!$doc) return redirect()->back()->with('error','Documento no encontrado');

        $matricula = Matricula::find($doc->matricula_id);
        if (!$matricula) return redirect()->back()->with('error','Matrícula asociada no encontrada');

        $puede = false;
        if ($user->roles_id == 7 && $matricula->acudiente_id == $user->id && $matricula->estado == 'pendiente') {
            $puede = true;
        }
        if (in_array($user->roles_id,[1,2])) {
            $puede = true;
        }
        if (!$puede) return redirect('/dashboard')->with('error','No autorizado');

        $filePath = 'matriculas/'.$matricula->id.'/'.$doc->stored_name;
        if (Storage::disk('local')->exists($filePath)) {
            Storage::disk('local')->delete($filePath);
        }
        $doc->delete();

        $redirectRoute = $user->roles_id == 7 ? 'matricula.documentos.form' : 'matricula.documentos.admin';
        return redirect()->route($redirectRoute, $matricula->id)->with('success','Documento eliminado');
    }
}
