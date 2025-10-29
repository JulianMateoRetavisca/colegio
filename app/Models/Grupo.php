<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    use HasFactory;

    protected $table = 'grupos';
    protected $fillable = ['nombre'];

    public function estudiantes()
    {
        return $this->hasMany(User::class, 'grupo_id');
    }
}
