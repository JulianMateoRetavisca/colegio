@extends('layouts.app')

@section('content')
@php
    $usuario = Auth::user();
    $rolUsuario = App\Models\RolesModel::find($usuario->roles_id);
@endphp

<div class="container-fluid p-0">
    <div class="row g-0">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 p-0">
            <div class="sidebar">
                <div class="p-3">
                    <h6 class="text-white-50 text-uppercase">Menú Principal</h6>
                </div>
                <nav class="nav flex-column px-3">
                    <a class="nav-link" href="{{ route('dashboard') }}">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                    @if($rolUsuario)
                        @if($rolUsuario->tienePermiso('gestionar_usuarios'))
                            <a class="nav-link" href="#">
                                <i class="fas fa-users-cog me-2"></i>Gestión de Usuarios
                            </a>
                        @endif
                        @if($rolUsuario->tienePermiso('gestionar_estudiantes'))
                            <a class="nav-link" href="#">
                                <i class="fas fa-user-graduate me-2"></i>Estudiantes
                            </a>
                        @endif
                        @if($rolUsuario->tienePermiso('gestionar_docentes'))
                            <a class="nav-link" href="#">
                                <i class="fas fa-chalkboard-teacher me-2"></i>Docentes
                            </a>
                        @endif
                        @if($rolUsuario->tienePermiso('gestionar_roles'))
                            <a class="nav-link active" href="{{ route('roles.index') }}">
                                <i class="fas fa-user-shield me-2"></i>Roles y Permisos
                            </a>
                        @endif
                        @if($rolUsuario->tienePermiso('matricular_estudiantes'))
                            <a class="nav-link" href="#">
                                <i class="fas fa-user-check me-2"></i>Matricular Estudiantes
                            </a>
                        @endif
                        @if($rolUsuario->tienePermiso('gestionar_materias'))
                            <a class="nav-link" href="#">
                                <i class="fas fa-book-open me-2"></i>Materias
                            </a>
                        @endif
                        @if($rolUsuario->tienePermiso('gestionar_cursos'))
                            <a class="nav-link" href="#">
                                <i class="fas fa-layer-group me-2"></i>Cursos
                            </a>
                        @endif
                        @if($rolUsuario->tienePermiso('gestionar_horarios'))
                            <a class="nav-link" href="#">
                                <i class="fas fa-calendar-alt me-2"></i>Horarios
                            </a>
                        @endif
                        @if($rolUsuario->tienePermiso('gestionar_disciplina'))
                            <a class="nav-link" href="#">
                                <i class="fas fa-gavel me-2"></i>Disciplina
                            </a>
                        @endif
                        @if($rolUsuario->tienePermiso('ver_reportes_generales'))
                            <a class="nav-link" href="#">
                                <i class="fas fa-chart-bar me-2"></i>Reportes
                            </a>
                        @endif
                        @if($rolUsuario->tienePermiso('gestionar_pagos'))
                            <a class="nav-link" href="#">
                                <i class="fas fa-money-bill-wave me-2"></i>Pagos
                            </a>
                        @endif
                        @if($rolUsuario->tienePermiso('configurar_sistema'))
                            <a class="nav-link" href="#">
                                <i class="fas fa-cog me-2"></i>Configuración
                            </a>
                        @endif
                    @endif
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10">
            <div class="main-content p-4">
                <h1 class="mb-4">Detalles del rol: <span class="text-primary">{{ $rol->nombre }}</span></h1>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Descripción:</label>
                    <p class="bg-light border rounded p-3">{{ $rol->descripcion }}</p>
                </div>

                <div class="mb-5">
                    <label class="form-label d-block fw-semibold mb-3">Permisos por módulo</label>
                    <div class="row g-3">
                        @foreach($gruposPermisos as $modulo => $permisos)
                            @php
                                $permisosSeleccionados = array_intersect(array_keys($permisos), $rol->permisos ?? []);
                            @endphp
                            <div class="col-lg-6">
                                <div class="card h-100">
                                    <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
                                        <span>{{ $modulo }}</span>
                                        <small class="text-muted">{{ count($permisosSeleccionados) }}/{{ count($permisos) }} seleccionados</small>
                                    </div>
                                    <div class="card-body">
                                        @if(count($permisosSeleccionados))
                                            <ul class="mb-0">
                                                @foreach($permisosSeleccionados as $permiso)
                                                    <li>{{ $permisosDisponibles[$permiso] ?? $permiso }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="text-muted">Sin permisos seleccionados en este módulo</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <h3 class="mb-3 fw-semibold">Usuarios asignados a este rol</h3>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Nombre</th>
                                <th>Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($usuarios as $usuario)
                                <tr>
                                    <td>{{ $usuario->name }}</td>
                                    <td>{{ $usuario->email }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $usuarios->links() }}

                <div class="mt-4">
                    <a href="{{ route('roles.index') }}" class="btn btn-secondary me-2">
                        <i class="fas fa-arrow-left me-1"></i>Volver a la lista
                    </a>
                    <a href="{{ route('roles.editar', $rol->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-1"></i>Editar rol
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    body {
        background-color: #f8f9fa;
        font-family: 'Inter', sans-serif;
        margin: 0;
        padding: 0;
    }

    .sidebar {
        background-color: #1e1f26;
        color: #fff;
        min-height: 100vh;
        width: 230px;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 100;
        padding-top: 1rem;
        box-shadow: 3px 0 6px rgba(0, 0, 0, 0.2);
    }

    .sidebar .nav-link {
        color: #bfc0c3;
        font-weight: 500;
        padding: 10px 15px;
        margin-bottom: 3px;
        border-radius: 8px;
        transition: all 0.2s;
    }

    .sidebar .nav-link:hover,
    .sidebar .nav-link.active {
        background-color: #2e2f36;
        color: #ffffff;
    }

    .main-content {
        margin-left: 230px; /* coincide con el ancho de la sidebar */
        padding: 2rem;
        background-color: #ffffff;
        min-height: 100vh;
    }

    .main-content h1 {
        font-weight: 600;
        color: #333;
        margin-bottom: 1.5rem;
    }

    .card {
        border: none;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        border-radius: 12px;
    }

    .card-header {
        background-color: #f4f4f4;
        border-bottom: none;
    }

    .form-label {
        font-weight: 600;
        color: #333;
    }

    .btn {
        border-radius: 8px;
    }

    .btn-primary {
        background-color: #1e1f26;
        border-color: #1e1f26;
    }

    .btn-primary:hover {
        background-color: #2e2f36;
        border-color: #2e2f36;
    }

    .btn-outline-primary:hover {
        background-color: #1e1f26;
        border-color: #1e1f26;
        color: white;
    }

    /* Ajuste responsive */
    @media (max-width: 992px) {
        .sidebar {
            position: relative;
            width: 100%;
            height: auto;
            box-shadow: none;
        }

        .main-content {
            margin-left: 0;
        }
    }
</style>
@endpush
