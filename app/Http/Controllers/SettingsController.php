<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->tienePermiso('configurar_sistema')) {
            abort(403);
        }
        $settings = Setting::getMany([
            'theme' => 'light',
            'table_density' => 'comfortable',
            'min_grade' => '3.0',
            'smtp_enabled' => '0',
            'periodos_json' => '[]',
        ]);
        return view('configuracion.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->tienePermiso('configurar_sistema')) {
            abort(403);
        }
        $data = $request->input('settings', []);
        // Validaciones simples
        $request->validate([
            'settings.theme' => 'nullable|in:light,dark',
            'settings.table_density' => 'nullable|in:comfortable,compact',
            'settings.min_grade' => 'nullable',
            'settings.smtp_enabled' => 'nullable|in:0,1',
            'settings.periodos_json' => 'nullable|string',
        ]);

        foreach ($data as $key => $value) {
            Setting::setValue($key, is_null($value) ? '' : (string)$value, $user->id);
        }

        return redirect()->route('configuracion.index')->with('ok', 'Configuración guardada');
    }
}
