<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\RolesModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    private function puedeGestionar()
    {
        $usuario = Auth::user();
        if (!$usuario) return false;
        // Permiso explícito
        if ($usuario->tienePermiso('gestionar_usuarios')) return true;
        // O rol Admin / Rector
        $rol = $usuario->rol;
        return $rol && in_array($rol->nombre, ['Admin', 'Rector']);
    }

    public function index()
    {
        if (!$this->puedeGestionar()) {
            return redirect('/dashboard')->with('error', 'No tienes permisos para gestionar usuarios.');
        }

        $usuarios = User::with('rol')->orderBy('name')->get();
        return view('usuarios.index', compact('usuarios'));
    }

    public function editar(User $usuario)
    {
        if (!$this->puedeGestionar()) {
            return redirect('/dashboard')->with('error', 'No tienes permisos para editar usuarios.');
        }

        $roles = RolesModel::orderBy('nombre')->get();
        return view('usuarios.editar', compact('usuario', 'roles'));
    }

    public function actualizar(Request $request, User $usuario)
    {
        if (!$this->puedeGestionar()) {
            return redirect('/dashboard')->with('error', 'No tienes permisos para actualizar usuarios.');
        }

        $datos = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required','email','max:255', Rule::unique('users','email')->ignore($usuario->id)],
            'roles_id' => 'nullable|exists:roles,id',
            'password' => 'nullable|string|min:8|confirmed'
        ]);

        $usuario->name = $datos['name'];
        $usuario->email = $datos['email'];
        $usuario->roles_id = $datos['roles_id'] ?? null;

        if (!empty($datos['password'])) {
            $usuario->password = bcrypt($datos['password']);
        }

        $usuario->save();

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function eliminar(User $usuario)
    {
        if (!$this->puedeGestionar()) {
            return redirect('/dashboard')->with('error', 'No tienes permisos para eliminar usuarios.');
        }

        // Protegemos que un usuario se elimine a sí mismo
        if (Auth::id() === $usuario->id) {
            return redirect()->route('usuarios.index')->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $usuario->delete();
        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado correctamente.');
    }

    /**
     * Endpoint JSON para devolver usuarios modificados desde una marca de tiempo.
     * Parámetro GET: since (ISO datetime)
     */
    public function updates(\Illuminate\Http\Request $request)
    {
        if (!$this->puedeGestionar()) {
            return response()->json(['exito' => false, 'error' => 'No autorizado'], 403);
        }

        $since = $request->query('since');
        $now = now()->toIsoString();

        if (!$since) {
            // Si no se envía 'since', devolvemos vacío y la marca actual para inicializar al cliente
            return response()->json(['exito' => true, 'datos' => [], 'now' => $now]);
        }

        try {
            $sinceDt = \Carbon\Carbon::parse($since);
        } catch (\Exception $e) {
            return response()->json(['exito' => false, 'error' => 'Formato de fecha inválido'], 400);
        }

        $usuarios = User::with('rol')
            ->where('updated_at', '>', $sinceDt)
            ->get()
            ->map(function ($u) {
                return [
                    'id' => $u->id,
                    'name' => $u->name,
                    'email' => $u->email,
                    'roles_id' => $u->roles_id,
                    'rol' => $u->rol ? $u->rol->nombre : null,
                    'updated_at' => $u->updated_at->toIsoString(),
                ];
            });

        return response()->json(['exito' => true, 'datos' => $usuarios, 'now' => $now]);
    }
}
