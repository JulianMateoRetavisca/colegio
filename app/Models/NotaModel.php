<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotaModel extends Model
{
    use HasFactory;

    protected $table = 'notas';
    protected $fillable = ['estudiante_id', 'materia_id', 'nota', 'periodo'];
    public $timestamps = true;

    public function estudiante()
    {
        return $this->belongsTo(User::class, 'estudiante_id');
    }
}   