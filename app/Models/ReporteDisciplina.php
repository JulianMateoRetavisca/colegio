<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReporteDisciplina extends Model
{
    use HasFactory;

    // Ajuste de nombre de tabla: la migración creó 'reportes_disciplina'
    // Laravel pluralizaría por defecto 'reporte_disciplinas', causando error 42S02.
    protected $table = 'reportes_disciplina';

    public const ESTADO_REPORTADO = 'reportado';
    public const ESTADO_EN_REVISION = 'en_revision';
    public const ESTADO_SANCION_ASIGNADA = 'sancion_asignada';
    public const ESTADO_NOTIFICADO = 'notificado';
    public const ESTADO_APELACION_SOLICITADA = 'apelacion_solicitada';
    public const ESTADO_APELACION_EN_REVISION = 'apelacion_en_revision';
    public const ESTADO_APELACION_ACEPTADA = 'apelacion_aceptada';
    public const ESTADO_APELACION_RECHAZADA = 'apelacion_rechazada';
    public const ESTADO_ARCHIVADO = 'archivado';

    protected $fillable = [
        'estudiante_id','docente_id','coordinador_id','estado','descripcion_incidente','gravedad','sancion_text','sancion_activa','sancion_asignada_at','notificado_at','apelacion_motivo','apelacion_result','apelacion_resuelta_at','sancion_modificada_at','sancion_eliminada_at','archivado_at'
    ];

    protected $casts = [
        'sancion_activa' => 'boolean',
        'sancion_asignada_at' => 'datetime',
        'notificado_at' => 'datetime',
        'apelacion_resuelta_at' => 'datetime',
        'sancion_modificada_at' => 'datetime',
        'sancion_eliminada_at' => 'datetime',
        'archivado_at' => 'datetime',
    ];

    public function estudiante(){ return $this->belongsTo(User::class,'estudiante_id'); }
    public function docente(){ return $this->belongsTo(User::class,'docente_id'); }
    public function coordinador(){ return $this->belongsTo(User::class,'coordinador_id'); }
    public function historial(){ return $this->hasMany(ReporteDisciplinaHistorial::class,'reporte_id')->orderBy('id'); }
}
