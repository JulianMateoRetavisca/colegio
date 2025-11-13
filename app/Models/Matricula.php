<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matricula extends Model
{
    use HasFactory;

    protected $table = 'matriculas';

    protected $fillable = [
        'acudiente_id',
        'nombre_estudiante',
        'grado',
        'telefono_contacto',
        'direccion',
        'correo_contacto',
        'estado',
    ];

    // Relación: una matrícula pertenece a un acudiente (usuario)
    public function acudiente()
    {
        return $this->belongsTo(User::class, 'acudiente_id');
    }
}
