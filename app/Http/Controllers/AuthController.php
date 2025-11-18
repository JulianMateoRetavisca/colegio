<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    // Mostrar formulario de login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Procesar el login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();
            // Redirecciones específicas por rol
            if ($user && $user->roles_id) {
                $rol = \App\Models\RolesModel::find($user->roles_id);
                if ($rol) {
                    if ($rol->nombre === 'Estudiante') {
                        return redirect()->route('estudiantes.index');
                    } elseif ($rol->nombre === 'Profesor') {
                        return redirect()->route('docentes.grupos');
                    }
                }
            }
            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    // Mostrar dashboard/menú principal
    public function dashboard()
    {
        // Métricas reales
        $usuarios = \App\Models\User::count();
        // Rol estudiante puede tener mayúsculas/minúsculas diferentes, normalizamos
        $estudiantes = \App\Models\User::whereHas('rol', function($q){
            $q->whereRaw('LOWER(nombre) = ?', ['estudiante']);
        })->count();

        // Citas de orientación activas (no cerradas)
        $citas = \App\Models\OrientacionCita::where('estado','!=', \App\Models\OrientacionCita::ESTADO_CERRADA)->count();

        // Reportes de disciplina abiertos (no archivados)
        $reportesAbiertos = \App\Models\ReporteDisciplina::where('estado','!=', \App\Models\ReporteDisciplina::ESTADO_ARCHIVADO)->count();

        // Pendientes: suma de activos (citas abiertas + reportes abiertos)
        $pendientes = $citas + $reportesAbiertos;

        return view('dashboard', compact('usuarios','estudiantes','citas','pendientes','reportesAbiertos'));
    }

    // Procesar logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login');
    }
}
