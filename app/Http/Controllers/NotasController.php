<?php
namespace App\Http\Controllers;

use App\Models\NotaModel;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class NotasController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Si no está autenticado, devolver lista vacía
        if (!$user) {
            return response()->json([], 200);
        }

        // Si el usuario tiene permiso para ver/gestionar notas, retornar todas
        if ($user->tienePermiso('ver_notas') || $user->tienePermiso('gestionar_notas')) {
            $notas = NotaModel::with('estudiante')->get();
            return response()->json($notas);
        }

        // Por defecto (estudiante), retornar solo sus notas
        $notas = NotaModel::with('estudiante')->where('estudiante_id', $user->id)->get();
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
        return response()->json($nota, 201);
    }
    public function MostrarNota($id)
    {
        $nota = NotaModel::with('estudiante')->find($id);
        if (!$nota) {
            return response()->json(['message' => 'Nota no encontrada'], 404);
        }
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        // Usuarios con permiso pueden ver cualquier nota
        if ($user->tienePermiso('ver_notas') || $user->tienePermiso('gestionar_notas')) {
            return response()->json($nota);
        }

        // Estudiantes solo pueden ver sus propias notas
        if ($nota->estudiante_id === $user->id) {
            return response()->json($nota);
        }

        return response()->json(['message' => 'No autorizado'], 403);
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