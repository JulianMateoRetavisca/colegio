<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Matricula;
use App\Models\User;

class MatriculaController extends Controller
{
    // Vista inicial para que el acudiente inicie el proceso de matrícula
    public function iniciarMatricula()
    {
        // Verificar si el usuario está autenticado
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Debes iniciar sesión para continuar.');
        }

        $user = Auth::user();

        // Validar que el usuario tenga rol de acudiente (roles_id = 5)
        if ($user->roles_id != 5) {
            return redirect('/dashboard')->with('error', 'No tienes permisos para iniciar la matrícula.');
        }

        // Retornar la vista donde el acudiente ingresará los datos de matrícula
        return view('matricula.iniciar');
    }

    // Guardar los datos de la matrícula en la base de datos
    public function guardarMatricula(Request $request)
    {
        // Verificar sesión
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Debes iniciar sesión para continuar.');
        }

        $user = Auth::user();

        // Solo los acudientes pueden registrar una matrícula
        if ($user->roles_id != 5) {
            return redirect('/dashboard')->with('error', 'No tienes permisos para realizar esta acción.');
        }

        // Validar los datos del formulario
        $request->validate([
            'nombre_estudiante' => 'required|string|max:100',
            'grado' => 'required|string|max:50',
            'telefono_contacto' => 'required|string|max:15',
            'direccion' => 'required|string|max:150',
            'correo_contacto' => 'required|email|max:100',
        ]);

        // Crear nueva matrícula
        $matricula = new Matricula();
        $matricula->acudiente_id = $user->id;
        $matricula->nombre_estudiante = $request->nombre_estudiante;
        $matricula->grado = $request->grado;
        $matricula->telefono_contacto = $request->telefono_contacto;
        $matricula->direccion = $request->direccion;
        $matricula->correo_contacto = $request->correo_contacto;
        $matricula->estado = 'pendiente';
        $matricula->save();

        // Retornar con mensaje de éxito
        return redirect()->route('matricula.iniciar')->with('success', 'Matrícula iniciada correctamente. Pronto recibirás confirmación.');
    }

    // (Opcional) Mostrar las matrículas registradas por el acudiente
    public function verMisMatriculas()
    {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Debes iniciar sesión.');
        }

        $user = Auth::user();

        if ($user->roles_id != 5) {
            return redirect('/dashboard')->with('error', 'No tienes permisos para ver esta información.');
        }

        $matriculas = Matricula::where('acudiente_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('matricula.mis_matriculas', compact('matriculas'));
    }
}
