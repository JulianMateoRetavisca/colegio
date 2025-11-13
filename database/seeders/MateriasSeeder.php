<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Materia;

class MateriasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $materias = [
            [
                'nombre' => 'Matemáticas',
                'descripcion' => 'Álgebra, geometría, cálculo y estadística'
            ],
            [
                'nombre' => 'Ciencias Naturales',
                'descripcion' => 'Biología, química y física aplicada'
            ],
            [
                'nombre' => 'Lengua Castellana',
                'descripcion' => 'Gramática, literatura y comprensión lectora'
            ],
            [
                'nombre' => 'Inglés',
                'descripcion' => 'Idioma extranjero - Nivel básico a avanzado'
            ],
            [
                'nombre' => 'Ciencias Sociales',
                'descripcion' => 'Historia, geografía y educación cívica'
            ],
            [
                'nombre' => 'Educación Física',
                'descripcion' => 'Deportes, actividad física y salud'
            ],
            [
                'nombre' => 'Artes',
                'descripcion' => 'Música, dibujo y expresión artística'
            ],
            [
                'nombre' => 'Informática',
                'descripcion' => 'Computación, programación y tecnología'
            ],
            [
                'nombre' => 'Ética y Valores',
                'descripcion' => 'Formación en valores y convivencia'
            ],
            [
                'nombre' => 'Religión',
                'descripcion' => 'Educación religiosa y moral'
            ]
        ];

        foreach ($materias as $materia) {
            Materia::updateOrCreate(
                ['nombre' => $materia['nombre']],
                $materia
            );
        }
    }
}
