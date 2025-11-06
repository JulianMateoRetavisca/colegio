<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    use HasFactory;

    protected $table = 'horarios';

    protected $fillable = [
        'grupo_id',
        'materia_id',
        'docente_id',
        'dia',
        'hora_inicio',
        'hora_fin',
        'observaciones',
    ];

    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'grupo_id');
    }

    public function materia()
    {
        // Guardar la relación sólo si el modelo Materia existe para evitar errores en instalaciones incompletas
        if (class_exists(\App\Models\Materia::class)) {
            return $this->belongsTo(\App\Models\Materia::class, 'materia_id');
        }

        // Si no existe el modelo, devolver una relación nula simulada que evita errores al llamarla desde vistas.
        // Nota: esto no permite eager-loading de materias si el modelo no está presente.
        return null;
    }

    public function docente()
    {
        return $this->belongsTo(User::class, 'docente_id');
    }
}
