<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReporteDisciplinaHistorial extends Model
{
    use HasFactory;

    // Ajuste de nombre de tabla: migración creó 'reportes_disciplina_historial'.
    protected $table = 'reportes_disciplina_historial';

    protected $fillable = [
        'reporte_id','user_id','estado_from','estado_to','descripcion'
    ];

    public function reporte(){ return $this->belongsTo(ReporteDisciplina::class,'reporte_id'); }
    public function usuario(){ return $this->belongsTo(User::class,'user_id'); }
}
