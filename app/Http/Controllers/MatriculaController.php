<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Matricula;
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
}
