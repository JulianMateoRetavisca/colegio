<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class CrearUsuario extends Controller
{
    public function showRegistrationForm()
    {
        return view('registro.registro');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), 
        ]);
        /*
            asignar rol por defecto 
        */
        $user->roles_id = 6;
        $user->save();
        
        Auth::login($user);

        return redirect('/dashboard');
    }
}
