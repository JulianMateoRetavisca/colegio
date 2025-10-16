
@extends('layouts.app')

@section('content')
@php
    $usuario = Auth::user();
    $rol = App\Models\RolesModel::find($usuario->roles_id);
@endphp
<div class="container-fluid">
    <div class="row">
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
                    @if($rol)
                        @if($rol->tienePermiso('gestionar_usuarios'))
                            <a class="nav-link" href="#">
                                <i class="fas fa-users-cog me-2"></i>Gestión de Usuarios
                            </a>
                        @endif
                        @if($rol->tienePermiso('gestionar_estudiantes'))
                            <a class="nav-link" href="#">
                                <i class="fas fa-user-graduate me-2"></i>Estudiantes
                            </a>
                        @endif
                        @if($rol->tienePermiso('gestionar_docentes'))
                            <a class="nav-link" href="#">
                                <i class="fas fa-chalkboard-teacher me-2"></i>Docentes
                            </a>
                        @endif
                        @if($rol->tienePermiso('gestionar_roles'))
                            <a class="nav-link active" href="{{ route('roles.index') }}">
                                <i class="fas fa-user-shield me-2"></i>Roles y Permisos
                            </a>
                        @endif
                        @if($rol->tienePermiso('matricular_estudiantes'))
                            <a class="nav-link" href="#">
                                <i class="fas fa-user-check me-2"></i>Matricular Estudiantes
                            </a>
                        @endif
                        @if($rol->tienePermiso('gestionar_materias'))
                            <a class="nav-link" href="#">
                                <i class="fas fa-book-open me-2"></i>Materias
                            </a>
                        @endif
                        @if($rol->tienePermiso('gestionar_cursos'))
                            <a class="nav-link" href="#">
                                <i class="fas fa-layer-group me-2"></i>Cursos
                            </a>
                        @endif
                        @if($rol->tienePermiso('gestionar_horarios'))
                            <a class="nav-link" href="#">
                                <i class="fas fa-calendar-alt me-2"></i>Horarios
                            </a>
                        @endif
                        @if($rol->tienePermiso('gestionar_disciplina'))
                            <a class="nav-link" href="#">
                                <i class="fas fa-gavel me-2"></i>Disciplina
                            </a>
                        @endif
                        @if($rol->tienePermiso('ver_reportes_generales'))
                            <a class="nav-link" href="#">
                                <i class="fas fa-chart-bar me-2"></i>Reportes
                            </a>
                        @endif
                        @if($rol->tienePermiso('gestionar_pagos'))
                            <a class="nav-link" href="#">
                                <i class="fas fa-money-bill-wave me-2"></i>Pagos
                            </a>
                        @endif
                        @if($rol->tienePermiso('configurar_sistema'))
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
                <h1>Crear nuevo rol</h1>
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('roles.guardar') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre del rol</label>
                        <input type="text" name="nombre" id="nombre" class="form-control" required value="{{ old('nombre') }}">
                    </div>
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea name="descripcion" id="descripcion" class="form-control" required>{{ old('descripcion') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label d-block">Permisos por módulo</label>
                        <div class="d-flex gap-2 mb-3">
                            <button type="button" class="btn btn-outline-primary btn-sm" id="marcar_todo">Marcar todo</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="desmarcar_todo">Desmarcar todo</button>
                        </div>
                        <div class="row g-3">
                            @foreach($gruposPermisos as $modulo => $permisos)
                                <div class="col-lg-6">
                                    <div class="card h-100">
                                        <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
                                            <span>{{ $modulo }}</span>
                                            <div class="form-check form-check-inline m-0">
                                                <input class="form-check-input marcar-modulo" type="checkbox" id="marcar_modulo_{{ Str::slug($modulo) }}" data-modulo="{{ Str::slug($modulo) }}">
                                                <label class="form-check-label" for="marcar_modulo_{{ Str::slug($modulo) }}">Seleccionar todo</label>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                @foreach($permisos as $permiso => $desc)
                                                    <div class="col-md-12">
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input permiso-item permiso-{{ Str::slug($modulo) }}" type="checkbox" name="permisos[]" value="{{ $permiso }}" id="permiso_{{ $permiso }}">
                                                            <label class="form-check-label" for="permiso_{{ $permiso }}">{{ $desc }}</label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const marcarTodo = document.getElementById('marcar_todo');
    const desmarcarTodo = document.getElementById('desmarcar_todo');
    if (marcarTodo) marcarTodo.addEventListener('click', () => {
        document.querySelectorAll('.permiso-item').forEach(cb => cb.checked = true);
        document.querySelectorAll('.marcar-modulo').forEach(cb => cb.checked = true);
    });
    if (desmarcarTodo) desmarcarTodo.addEventListener('click', () => {
        document.querySelectorAll('.permiso-item').forEach(cb => cb.checked = false);
        document.querySelectorAll('.marcar-modulo').forEach(cb => cb.checked = false);
    });

    document.querySelectorAll('.marcar-modulo').forEach(cb => {
        cb.addEventListener('change', (e) => {
            const modulo = e.target.getAttribute('data-modulo');
            const items = document.querySelectorAll(`.permiso-${modulo}`);
            items.forEach(it => it.checked = e.target.checked);
        });
    });
});
</script>
@endpush
                    <button type="submit" class="btn btn-success">Crear rol</button>
                    <a href="{{ route('roles.index') }}" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
