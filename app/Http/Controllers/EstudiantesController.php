<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class EstudiantesController extends Controller
{
    // Formulario para mostrar a los estudiantes
    public function MostrarEstudiante(){
        // Opción 1: Si no tienes el método usuarioPuedeGestionarRoles, usa esta validación simple
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Debes iniciar sesión.');
        }

        // Opción 2: Validación básica de permisos (ajusta según tu lógica)
        // if (!auth()->user()->can('gestionar_estudiantes')) {
        //     return redirect('/dashboard')->with('error', 'No tienes permisos para ver esta información.');
        // }

        // Obtener estudiantes
        $MostrarEstudiante = User::where('roles_id', 6)
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();

        return view('estudiantes.mostrar', compact('MostrarEstudiante'));
    }

    // Método auxiliar para obtener iniciales (opcional)
    private function obtenerIniciales($nombre)
    {
        $nombres = explode(' ', $nombre);
        $iniciales = '';
        
        if (count($nombres) >= 2) {
            $iniciales = substr($nombres[0], 0, 1) . substr($nombres[1], 0, 1);
        } else {
            $iniciales = substr($nombres[0], 0, 2);
        }
        
        return strtoupper($iniciales);
    }
}