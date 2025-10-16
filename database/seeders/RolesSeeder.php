<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RolesModel;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'nombre' => 'Admin',
                'descripcion' => 'Administrador del Sistema con acceso total a todas las funcionalidades',
                'permisos' => [
                    'acceso_total',
                    'gestionar_usuarios',
                    'gestionar_docentes',
                    'gestionar_estudiantes',
                    'gestionar_roles',
                    'gestionar_permisos',
                    'configurar_sistema',
                    'ver_logs_sistema',
                    'hacer_respaldos',
                    'ver_reportes_generales',
                    'ver_reportes_financieros',
                    'generar_reportes',
                    'exportar_reportes'
                ]
            ],
            [
                'nombre' => 'Rector',
                'descripcion' => 'Máxima autoridad del colegio, responsable de la dirección y administración general',
                'permisos' => [
                    'gestionar_usuarios',
                    'gestionar_docentes',
                    'gestionar_estudiantes',
                    'ver_usuarios',
                    'ver_docentes',
                    'ver_estudiantes',
                    'matricular_estudiantes',
                    'asignar_materias',
                    'aprobar_notas',
                    'gestionar_horarios',
                    'gestionar_periodos',
                    'aprobar_sanciones',
                    'enviar_comunicados',
                    'ver_reportes_generales',
                    'ver_reportes_academicos',
                    'ver_reportes_financieros',
                    'ver_reportes_disciplinarios',
                    'generar_reportes',
                    'exportar_reportes',
                    'gestionar_pagos',
                    'configurar_pensiones',
                    'gestionar_materias',
                    'gestionar_cursos'
                ]
            ],
            [
                'nombre' => 'CoordinadorDisciplina',
                'descripcion' => 'Encargado de coordinar actividades disciplinarias y convivencia escolar',
                'permisos' => [
                    'ver_estudiantes',
                    'gestionar_disciplina',
                    'ver_reportes_disciplinarios',
                    'aprobar_sanciones',
                    'justificar_inasistencias',
                    'gestionar_asistencia',
                    'enviar_comunicados',
                    'comunicarse_acudientes',
                    'ver_reportes_academicos',
                    'generar_reportes'
                ]
            ],
            [
                'nombre' => 'CoordinadorAcademico',
                'descripcion' => 'Encargado de coordinar actividades académicas y pedagógicas',
                'permisos' => [
                    'ver_estudiantes',
                    'ver_docentes',
                    'asignar_materias',
                    'gestionar_horarios',
                    'gestionar_periodos',
                    'aprobar_notas',
                    'ver_notas',
                    'ver_historial_academico',
                    'ver_reportes_academicos',
                    'generar_reportes',
                    'exportar_reportes',
                    'enviar_comunicados',
                    'gestionar_materias',
                    'gestionar_cursos'
                ]
            ],
            [
                'nombre' => 'Profesor',
                'descripcion' => 'Docente encargado de impartir clases y evaluar estudiantes',
                'permisos' => [
                    'ver_estudiantes',
                    'registrar_notas',
                    'ver_notas',
                    'crear_actividades',
                    'ver_horarios',
                    'gestionar_asistencia',
                    'comunicarse_acudientes',
                    'enviar_comunicados',
                    'ver_reportes_academicos',
                    'generar_reportes',
                    'ver_perfil_propio',
                    'editar_perfil_propio',
                    'cambiar_contrasena',
                    'ver_notificaciones'
                ]
            ],
            [
                'nombre' => 'Estudiante',
                'descripcion' => 'Estudiante del colegio',
                'permisos' => [
                    'ver_notas',
                    'ver_horarios',
                    'ver_actividades',
                    'ver_comunicados',
                    'ver_perfil_propio',
                    'editar_perfil_propio',
                    'cambiar_contrasena',
                    'ver_notificaciones'
                ]
            ],
            [
                'nombre' => 'Acudiente',
                'descripcion' => 'Padre, madre o acudiente responsable del estudiante',
                'permisos' => [
                    'ver_notas',
                    'ver_horarios',
                    'ver_estudiantes',
                    'ver_historial_academico',
                    'comunicarse_docentes',
                    'ver_reportes_academicos',
                    'ver_reportes_disciplinarios',
                    'justificar_inasistencias',
                    'ver_comunicados',
                    'ver_pagos',
                    'ver_perfil_propio',
                    'editar_perfil_propio',
                    'cambiar_contrasena',
                    'ver_notificaciones'
                ]
            ]
        ];

        foreach ($roles as $rol) {
            RolesModel::updateOrCreate(
                ['nombre' => $rol['nombre']],
                $rol
            );
        }
    }
}
