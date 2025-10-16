
@extends('layouts.app')

@section('content')
@php
    $usuario = Auth::user();
    $rolUsuario = App\Models\RolesModel::find($usuario->roles_id);
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
                <h1>Editar rol: {{ $rol->nombre }}</h1>
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('roles.actualizar', $rol->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre del rol</label>
                        <input type="text" name="nombre" id="nombre" class="form-control" required value="{{ old('nombre', $rol->nombre) }}" @if($esRolSistema) readonly @endif>
                    </div>
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea name="descripcion" id="descripcion" class="form-control" required @if($esRolSistema) readonly @endif>{{ old('descripcion', $rol->descripcion) }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label d-block">Permisos por módulo</label>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <button type="button" class="btn btn-outline-primary btn-sm" id="marcar_todo">Marcar todo</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="desmarcar_todo">Desmarcar todo</button>
                            <button type="button" class="btn btn-outline-success btn-sm" id="expandir_todo">Expandir todo</button>
                            <button type="button" class="btn btn-outline-dark btn-sm" id="contraer_todo">Contraer todo</button>
                        </div>
                        <div class="row g-3">
                            @foreach($gruposPermisos as $modulo => $permisos)
                                <div class="col-lg-6">
                                    <div class="card h-100">
                                        <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
                                            <span>
                                                {{ $modulo }}
                                                <small class="ms-2 text-muted conteo-modulo" data-modulo="{{ Str::slug($modulo) }}" data-total="{{ count($permisos) }}">
                                                    Seleccionados: <span class="seleccionados">0</span>/{{ count($permisos) }}
                                                </small>
                                            </span>
                                            <div class="form-check form-check-inline m-0">
                                                <input class="form-check-input marcar-modulo" type="checkbox" id="marcar_modulo_{{ Str::slug($modulo) }}" data-modulo="{{ Str::slug($modulo) }}">
                                                <label class="form-check-label" for="marcar_modulo_{{ Str::slug($modulo) }}">Seleccionar todo</label>
                                            </div>
                                        </div>
                                        <div class="card-body contenedor-permisos" data-modulo="{{ Str::slug($modulo) }}">
                                            <div class="mb-3">
                                                <input type="text" class="form-control form-control-sm buscador-modulo" placeholder="Buscar permisos en {{ $modulo }}" data-modulo="{{ Str::slug($modulo) }}">
                                            </div>
                                            <div class="row">
                                                @foreach($permisos as $permiso => $desc)
                                                    <div class="col-md-12 permiso-row" data-etiqueta="{{ Str::lower($desc) }}" data-modulo="{{ Str::slug($modulo) }}">
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input permiso-item permiso-{{ Str::slug($modulo) }}" type="checkbox" name="permisos[]" value="{{ $permiso }}" id="permiso_{{ $permiso }}" @if(in_array($permiso, $rol->permisos ?? [])) checked @endif>
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
    const expandirTodo = document.getElementById('expandir_todo');
    const contraerTodo = document.getElementById('contraer_todo');
    function actualizarConteoModulo(modulo) {
        const items = document.querySelectorAll(`.permiso-${modulo}`);
        const seleccionados = Array.from(items).filter(it => it.checked).length;
        const contenedor = document.querySelector(`.conteo-modulo[data-modulo="${modulo}"]`);
        if (contenedor) {
            contenedor.querySelector('.seleccionados').textContent = seleccionados;
        }
        const toggle = document.getElementById(`marcar_modulo_${modulo}`);
        if (toggle) {
            const total = items.length;
            toggle.indeterminate = seleccionados > 0 && seleccionados < total;
            toggle.checked = seleccionados === total;
        }
    }
    function actualizarTodosLosConteos() {
        document.querySelectorAll('.conteo-modulo').forEach(el => {
            actualizarConteoModulo(el.getAttribute('data-modulo'));
        });
    }
    if (marcarTodo) marcarTodo.addEventListener('click', () => {
        document.querySelectorAll('.permiso-item').forEach(cb => cb.checked = true);
        document.querySelectorAll('.marcar-modulo').forEach(cb => cb.checked = true);
        actualizarTodosLosConteos();
    });
    if (desmarcarTodo) desmarcarTodo.addEventListener('click', () => {
        document.querySelectorAll('.permiso-item').forEach(cb => cb.checked = false);
        document.querySelectorAll('.marcar-modulo').forEach(cb => cb.checked = false);
        actualizarTodosLosConteos();
    });

    // Expandir/Contraer todo
    function setModulosVisibles(visible) {
        document.querySelectorAll('.contenedor-permisos').forEach(body => {
            if (visible) {
                body.classList.remove('d-none');
            } else {
                body.classList.add('d-none');
            }
        });
    }
    if (expandirTodo) expandirTodo.addEventListener('click', () => setModulosVisibles(true));
    if (contraerTodo) contraerTodo.addEventListener('click', () => setModulosVisibles(false));

    document.querySelectorAll('.marcar-modulo').forEach(cb => {
        cb.addEventListener('change', (e) => {
            const modulo = e.target.getAttribute('data-modulo');
            const items = document.querySelectorAll(`.permiso-${modulo}`);
            items.forEach(it => it.checked = e.target.checked);
            actualizarConteoModulo(modulo);
        });
    });

    // Cambios individuales actualizan conteos e indeterminate
    document.querySelectorAll('.permiso-item').forEach(cb => {
        cb.addEventListener('change', (e) => {
            const clases = Array.from(e.target.classList);
            const moduloClass = clases.find(c => c.startsWith('permiso-') && c !== 'permiso-item');
            if (moduloClass) {
                const modulo = moduloClass.replace('permiso-','');
                actualizarConteoModulo(modulo);
            }
        })
    });

    // Buscador por módulo
    document.querySelectorAll('.buscador-modulo').forEach(inp => {
        inp.addEventListener('input', (e) => {
            const q = e.target.value.trim().toLowerCase();
            const modulo = e.target.getAttribute('data-modulo');
            const rows = document.querySelectorAll(`.permiso-row[data-modulo="${modulo}"]`);
            rows.forEach(row => {
                const etiqueta = row.getAttribute('data-etiqueta');
                const visible = q === '' || etiqueta.includes(q);
                row.classList.toggle('d-none', !visible);
            });
        });
    });

    // Inicializar conteos con estado actual
    actualizarTodosLosConteos();
});
</script>
@endpush
                    <button type="submit" class="btn btn-primary">Actualizar rol</button>
                    <a href="{{ route('roles.index') }}" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
