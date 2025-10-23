<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatriculaAcudiente extends Model
{
    use HasFactory;

    protected $table = 'matriculas_acudientes';

    protected $fillable = [
        'user_id',
        'estudiante_id',
        'curso_id',
        'nombres',
        'apellidos',
        'documento_identidad',
        'email',
        'telefono',
        'documentos',
        'estado',
    ];

    protected $casts = [
        'documentos' => 'array',
    ];

    public function acudiente()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class, 'curso_id');
    }

    public function estudiante()
    {
        return $this->belongsTo(User::class, 'estudiante_id');
    }
}
