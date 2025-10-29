<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Grupo;
use App\Models\User;
use App\Models\RolesModel;
use Illuminate\Support\Facades\Auth;

class GrupoController extends Controller
{
    public function index()
    {
        $this->authorizeView();
        $grupos = Grupo::orderBy('nombre')->get();
        return view('grupos.index', compact('grupos'));
    }

    public function crear()
    {
        $this->authorizeView();
        return view('grupos.crear');
    }

    public function guardar(Request $request)
    {
        $this->authorizeView();
        $request->validate([ 'nombre' => 'required|string|max:255' ]);
        Grupo::create($request->only('nombre'));
        return redirect()->route('grupos.index');
    }

    public function editar($id)
    {
        $this->authorizeView();
        $grupo = Grupo::findOrFail($id);
        return view('grupos.editar', compact('grupo'));
    }

    public function actualizar(Request $request, $id)
    {
        $this->authorizeView();
        $grupo = Grupo::findOrFail($id);
        $request->validate([ 'nombre' => 'required|string|max:255' ]);
        $grupo->update($request->only('nombre'));
        return redirect()->route('grupos.index');
    }

    public function eliminar($id)
    {
        $this->authorizeView();
        $grupo = Grupo::findOrFail($id);
        // Opcional: quitar grupo a estudiantes antes de borrar
        User::where('grupo_id', $grupo->id)->update(['grupo_id' => null]);
        $grupo->delete();
        return redirect()->route('grupos.index');
    }

    /** Mostrar UI para asignar estudiantes al grupo */
    public function asignar($id)
    {
        $this->authorizeView();
        $grupo = Grupo::findOrFail($id);
        $estudiantesRolId = RolesModel::where('nombre', 'Estudiante')->first()->id ?? 6;
        $estudiantes = User::where('roles_id', $estudiantesRolId)->orderBy('name')->get();
        return view('grupos.asignar', compact('grupo','estudiantes'));
    }

    /** Guardar asignación: lista de student ids */
    public function asignarGuardar(Request $request, $id)
    {
        $this->authorizeView();
        $grupo = Grupo::findOrFail($id);
        $studentIds = $request->input('students', []);
        // Quitar grupo a todos los estudiantes que actualmente lo tienen
        User::where('grupo_id', $grupo->id)->update(['grupo_id' => null]);
        // Asignar grupo a seleccionados
        if (!empty($studentIds)) {
            User::whereIn('id', $studentIds)->update(['grupo_id' => $grupo->id]);
        }
        return redirect()->route('grupos.index');
    }

    protected function authorizeView()
    {
        $usuario = Auth::user();
        if (!$usuario) abort(403);
        // permitimos a administradores y profesores
        $esProfesor = ($usuario->rol->nombre ?? '') === 'Profesor';
        if (!$usuario->tienePermiso('gestionar_roles') && !$esProfesor && !$usuario->tienePermiso('acceso_total')) {
            abort(403);
        }
    }
}
