<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MatriculaDocumento extends Model
{
    use HasFactory;

    protected $table = 'matricula_documentos';
    protected $fillable = [
        'matricula_id',
        'uploaded_by',
        'tipo',
        'original_name',
        'stored_name',
        'mime_type',
        'size_bytes'
    ];

    public function matricula()
    {
        return $this->belongsTo(Matricula::class, 'matricula_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getUrlAttribute()
    {
        return route('matricula.documentos.descargar', $this->id);
    }
}
