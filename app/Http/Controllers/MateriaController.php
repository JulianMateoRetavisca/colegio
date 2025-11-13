<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Materia;
use App\Models\Grupo;

class MateriaController extends Controller
{
    /**
     * Mostrar lista de materias
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Debes iniciar sesión.');
        }

        $materias = Materia::orderBy('nombre')->get();
        
        return view('materias.index', compact('materias'));
    }

    /**
     * Mostrar formulario para crear materia
     */
    public function crear()
    {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Debes iniciar sesión.');
        }

        return view('materias.crear');
    }

    /**
     * Guardar nueva materia
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:materias,nombre',
            'descripcion' => 'nullable|string|max:1000'
        ]);

        Materia::create($validated);

        return redirect()->route('materias.index')->with('success', 'Materia creada correctamente.');
    }

    /**
     * Mostrar formulario de edición
     */
    public function editar(Materia $materia)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Debes iniciar sesión.');
        }

        return view('materias.editar', compact('materia'));
    }

    /**
     * Actualizar materia
     */
    public function actualizar(Request $request, Materia $materia)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:materias,nombre,' . $materia->id,
            'descripcion' => 'nullable|string|max:1000'
        ]);

        $materia->update($validated);

        return redirect()->route('materias.index')->with('success', 'Materia actualizada correctamente.');
    }

    /**
     * Eliminar materia
     */
    public function eliminar(Materia $materia)
    {
        // Verificar si tiene horarios asociados
        if ($materia->horarios()->count() > 0) {
            return redirect()->route('materias.index')->with('error', 'No se puede eliminar la materia porque tiene horarios asociados.');
        }

        $materia->delete();

        return redirect()->route('materias.index')->with('success', 'Materia eliminada correctamente.');
    }

    /**
     * Mostrar vista para asignar materias a grupos
     */
    public function asignar()
    {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Debes iniciar sesión.');
        }

        $materias = Materia::orderBy('nombre')->get();
        $grupos = Grupo::orderBy('nombre')->get();

        return view('materias.asignar', compact('materias', 'grupos'));
    }
}
