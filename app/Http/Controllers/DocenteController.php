<?php
//controlador para gestionar docentes
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\RolesModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DocenteController extends Controller
{
    // Mostrar lista de docentes
    private function puedeGestionar() {
        $u = Auth::user();
        if (!$u || !$u->roles_id) return false;
        $r = RolesModel::find($u->roles_id);
        return $r && (in_array($r->nombre, ['Admin','Rector']) || $r->tienePermiso('gestionar_docentes'));
    }

    public function index() {
        if (!$this->puedeGestionar()) return redirect('/dashboard')->with('error','No autorizado');
        $docentes = User::where('roles_id', RolesModel::where('nombre','Profesor')->first()->id)->get();
        return view('docentes.index', compact('docentes'));
    }

    // Mostrar formulario para crear un nuevo docente
    public function crear()
    {
        if (!$this->puedeGestionar()) return redirect('/dashboard')->with('error','No autorizado');
        return view('docentes.crear');
    }

    // Almacenar un nuevo docente en la base de datos
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'roles_id' => 5,
            'email_virified_at' => now()
        ]);

        return redirect()->route('docentes.index')->with('success', 'Docente creado exitosamente.');
    }
}