<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrientacionCitaHistorial extends Model
{
    use HasFactory;

    protected $fillable = [
        'cita_id','user_id','estado_from','estado_to','descripcion'
    ];

    public function cita()
    {
        return $this->belongsTo(OrientacionCita::class,'cita_id');
    }
    public function usuario()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
