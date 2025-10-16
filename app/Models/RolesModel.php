<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolesModel extends Model
{
    use HasFactory; // Asegúrate de importar HasFactory si lo usas

    protected $table = 'roles';
    protected $fillable = ['nombre', 'descripcion', 'permisos'];
    protected $casts = [
        'permisos' => 'array',
    ];
    public $timestamps = true;
    

    public function usuarios()
    {
        return $this->hasMany(User::class, 'roles_id');
    }

    public function tienePermiso($permiso)
    {
        return in_array($permiso, $this->permisos ?? []);
    }

    public static function obtenerRolesSistema()
    {
        return [
            'Admin' => 'Administrador del Sistema',
            'Rector' => 'Rector del Colegio',
            'CoordinadorDisciplina' => 'Coordinador de Disciplina',
            'CoordinadorAcademico' => 'Coordinador Académico',
            'Acudiente' => 'Padre de Familia o Acudiente',
            'Estudiante' => 'Estudiante',
            'Profesor' => 'Profesor o Docente'
        ];
    }

}