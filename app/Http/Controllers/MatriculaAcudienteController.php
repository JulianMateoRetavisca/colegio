<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MatriculaAcudiente;
use App\Models\RolesModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MatriculaAcudienteController extends Controller
{
    // Mostrar formulario de nueva matrícula por acudiente
    public function crear()
    {
        $usuario = Auth::user();
        $rol = RolesModel::find($usuario->roles_id);
        if (!$rol || $rol->nombre !== 'Acudiente') {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para acceder a esta sección.');
        }

        $cursos = \DB::table('cursos')->orderBy('nombre')->get();
        return view('matriculas.crear', compact('cursos'));
    }

    // Guardar la matrícula y subir documentos al FTP
    public function guardar(Request $request)
    {
        $usuario = Auth::user();
        $rol = RolesModel::find($usuario->roles_id);
        if (!$rol || $rol->nombre !== 'Acudiente') {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para realizar esta acción.');
        }

        $request->validate([
            'curso_id' => 'required|exists:cursos,id',
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'documento_identidad' => 'required|string|max:100',
            'email' => 'nullable|email',
            'telefono' => 'nullable|string|max:50',
            'documentos.*' => 'file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $rutasDocumentos = [];
        $disk = config('filesystems.upload_disk', 'ftp');
        if ($request->hasFile('documentos')) {
            foreach ($request->file('documentos') as $file) {
                $nombre = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                $ext = $file->getClientOriginalExtension();
                $ruta = 'matriculas/' . date('Y/m') . '/' . $usuario->id . '/' . $nombre . '-' . time() . '.' . $ext;
                $saved = Storage::disk($disk)->put($ruta, fopen($file->getRealPath(), 'r+'));
                if ($saved) {
                    $rutasDocumentos[] = $ruta;
                }
            }
        }

        MatriculaAcudiente::create([
            'user_id' => $usuario->id,
            'curso_id' => $request->input('curso_id'),
            'nombres' => $request->input('nombres'),
            'apellidos' => $request->input('apellidos'),
            'documento_identidad' => $request->input('documento_identidad'),
            'email' => $request->input('email'),
            'telefono' => $request->input('telefono'),
            'documentos' => $rutasDocumentos,
            'estado' => 'pendiente',
        ]);

        return redirect()->route('matriculas.crear')->with('success', 'Matrícula enviada correctamente.');
    }

    // Listado de matrículas del acudiente
    public function listar()
    {
        $usuario = Auth::user();
        $matriculas = MatriculaAcudiente::where('user_id', $usuario->id)->orderByDesc('created_at')->paginate(10);
        return view('matriculas.index', compact('matriculas'));
    }

    // Mostrar detalle de una matrícula
    public function mostrar(MatriculaAcudiente $matricula)
    {
        $usuario = Auth::user();
        $rol = RolesModel::find($usuario->roles_id);
        if ($matricula->user_id !== $usuario->id && (!$rol || !$rol->tienePermiso('ver_matriculas_otros'))) {
            return redirect()->route('matriculas.index')->with('error', 'No autorizado.');
        }
        return view('matriculas.mostrar', compact('matricula'));
    }

    // Descargar un documento desde FTP
    public function descargarDocumento($ruta)
    {
        $ruta = urldecode($ruta);
        $disk = config('filesystems.upload_disk', 'ftp');
        if (Storage::disk($disk)->exists($ruta)) {
            $stream = Storage::disk($disk)->readStream($ruta);
            return response()->stream(function () use ($stream) {
                fpassthru($stream);
            }, 200, [
                'Content-Type' => Storage::disk($disk)->mimeType($ruta) ?? 'application/octet-stream',
                'Content-Disposition' => 'attachment; filename="' . basename($ruta) . '"',
            ]);
        }
        return redirect()->back()->with('error', 'Archivo no encontrado.');
    }
}
