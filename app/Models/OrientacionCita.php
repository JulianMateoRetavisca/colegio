<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrientacionCita extends Model
{
    use HasFactory;

    // Estados del flujo de orientación
    public const ESTADO_SOLICITADA = 'solicitada';
    public const ESTADO_REVISADA = 'revisada';
    public const ESTADO_ASIGNADA = 'asignada';
    public const ESTADO_REPROGRAMADA = 'reprogramada';
    public const ESTADO_REALIZADA = 'realizada';
    public const ESTADO_REGISTRADA = 'registrada'; // observaciones registradas
    public const ESTADO_SEGUIMIENTO = 'seguimiento';
    public const ESTADO_CERRADA = 'cerrada';

    protected $fillable = [
        'estudiante_id','orientador_id','estado','fecha_solicitada','fecha_asignada','hora_asignada','motivo','observaciones','seguimiento_requerido','fecha_proxima','cerrada_at'
    ];

    protected $casts = [
        'seguimiento_requerido' => 'boolean',
        'fecha_solicitada' => 'date',
        'fecha_asignada' => 'date',
        'fecha_proxima' => 'date',
        'cerrada_at' => 'datetime',
    ];

    public function estudiante()
    {
        return $this->belongsTo(User::class,'estudiante_id');
    }
    public function orientador()
    {
        return $this->belongsTo(User::class,'orientador_id');
    }
    public function scopeEstado($q,$estado)
    {
        return $q->where('estado',$estado);
    }
    public function historial()
    {
        return $this->hasMany(OrientacionCitaHistorial::class,'cita_id')->orderBy('id');
    }
}
