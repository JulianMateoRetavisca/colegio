<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RolesModel;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AcudienteSeeder extends Seeder
{
    public function run(): void
    {
        // Permisos específicos para el rol Acudiente
        $permisos = [
            // Estudiantes
            'ver_estudiantes',
            'ver_historial_academico',
            // Académico
            'ver_notas',
            // Disciplina
            'ver_reportes_disciplinarios',
            'justificar_inasistencias',
            // Comunicación
            'comunicarse_docentes',
            'ver_comunicados',
            // Reportes
            'ver_reportes_academicos',
            // Finanzas
            'ver_pagos',
            // Personales
            'ver_perfil_propio',
            'editar_perfil_propio',
            'cambiar_contrasena',
            'ver_notificaciones',
            // Matrícula
            'cargar_documentos_matricula',
        ];

        // Asegurar que el rol Acudiente exista
        $rol = RolesModel::firstOrCreate(
            ['nombre' => 'Acudiente'],
            [
                'descripcion' => 'Padre, madre o acudiente responsable del estudiante',
                'permisos' => $permisos,
            ]
        );

        // Actualizar permisos del rol (por si ya existía con otros)
        $rol->permisos = $permisos;
        $rol->save();

        // Crear usuario acudiente por defecto
        $user = User::updateOrCreate(
            ['email' => 'acudiente@colegio.edu.co'],
            [
                'name' => 'Acudiente Demo',
                'password' => Hash::make('acudiente123'),
                'roles_id' => $rol->id,
                'email_verified_at' => now(),
            ]
        );

        $this->command?->info('Usuario acudiente creado/actualizado: acudiente@colegio.edu.co / acudiente123');
    }
}

