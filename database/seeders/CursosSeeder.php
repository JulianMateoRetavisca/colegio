<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Curso;

class CursosSeeder extends Seeder
{
    public function run(): void
    {
        $cursos = [
            'Preescolar',
            'Primero A',
            'Segundo A',
            'Tercero A',
            'Cuarto A',
            'Quinto A',
            'Sexto A',
            'Séptimo A',
            'Octavo A',
            'Noveno A',
            'Décimo A',
            'Undécimo A',
        ];

        foreach ($cursos as $nombre) {
            Curso::updateOrCreate(['nombre' => $nombre]);
        }
    }
}

