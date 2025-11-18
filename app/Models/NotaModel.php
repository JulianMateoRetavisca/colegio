<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotaModel extends Model
{
    use HasFactory;

    protected $table = 'notas';
    protected $fillable = [
        'estudiante_id',
        'materia_id',
        'nota',
        'periodo',
        'estado',
        'publicado_at',
        'revisado_at',
        'bloqueado'
    ];
    protected $casts = [
        'publicado_at' => 'datetime',
        'revisado_at' => 'datetime',
        'bloqueado' => 'boolean'
    ];
    public $timestamps = true;

    // Estados posibles del flujo de calificaciones
    public const ESTADO_BORRADOR = 'borrador';
    public const ESTADO_PUBLICADA = 'publicada';
    public const ESTADO_REVISADA = 'revisada';
    public const ESTADO_BLOQUEADA = 'bloqueada';

    public function estudiante()
    {
        return $this->belongsTo(User::class, 'estudiante_id');
    }

    public function scopeEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }
}   