@extends('layouts.app')

@section('content')
@php
    $usuario = Auth::user();
    $rolUsuario = App\Models\RolesModel::find($usuario->roles_id);
@endphp

<div class="container-fluid min-vh-100 dashboard-bg">
    <div class="row g-0">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 p-0 shadow-sm sidebar-bg">
            @include('partials.sidebar')
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 px-5 py-4">
            <div class="main-content">

                <div class="mb-4 border-bottom pb-2">
                    <h1 class="fw-bold text-primary">
                        <i class="fas fa-user-edit me-2"></i> Editar rol: {{ $rol->nombre }}
                    </h1>
                    <p class="text-muted mb-0">Modifica los permisos o la descripción del rol seleccionado.</p>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger shadow-sm rounded-3 glass-card">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('roles.actualizar', $rol->id) }}" method="POST"
                      class="glass-card p-4 rounded-4 border">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label for="nombre" class="form-label fw-semibold text-secondary">
                            Nombre del rol
                        </label>
                        <input type="text" name="nombre" id="nombre"
                               class="form-control shadow-sm rounded-3"
                               required value="{{ old('nombre', $rol->nombre) }}"
                               @if($esRolSistema) readonly @endif>
                    </div>

                    <div class="mb-4">
                        <label for="descripcion" class="form-label fw-semibold text-secondary">
                            Descripción
                        </label>
                        <textarea name="descripcion" id="descripcion"
                                  class="form-control shadow-sm rounded-3"
                                  rows="2" required
                                  @if($esRolSistema) readonly @endif>{{ old('descripcion', $rol->descripcion) }}</textarea>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold text-dark mb-0">
                                <i class="fas fa-key me-2 text-primary"></i> Permisos por módulo
                            </h5>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-primary btn-sm rounded-3 shadow-sm" id="marcar_todo">
                                    <i class="fas fa-check-double me-1"></i> Marcar todo
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-3 shadow-sm" id="desmarcar_todo">
                                    <i class="fas fa-times me-1"></i> Desmarcar todo
                                </button>
                                <button type="button" class="btn btn-outline-success btn-sm rounded-3 shadow-sm" id="expandir_todo">
                                    <i class="fas fa-expand me-1"></i> Expandir
                                </button>
                                <button type="button" class="btn btn-outline-dark btn-sm rounded-3 shadow-sm" id="contraer_todo">
                                    <i class="fas fa-compress me-1"></i> Contraer
                                </button>
                            </div>
                        </div>

                        <div class="row g-4">
                            @foreach($gruposPermisos as $modulo => $permisos)
                                <div class="col-lg-6">
                                    <div class="card h-100 border-0 shadow-sm rounded-4 glass-card">
                                        <div class="card-header d-flex justify-content-between align-items-center text-white"
                                             style="background: linear-gradient(90deg, #6b73ff, #a06bff); border-radius: 14px 14px 0 0;">
                                            <span class="fw-semibold">
                                                {{ $modulo }}
                                                <small class="ms-2 text-light conteo-modulo"
                                                       data-modulo="{{ Str::slug($modulo) }}"
                                                       data-total="{{ count($permisos) }}">
                                                    (<span class="seleccionados">0</span>/{{ count($permisos) }})
                                                </small>
                                            </span>
                                            <div class="form-check m-0">
                                                <input class="form-check-input marcar-modulo" type="checkbox"
                                                       id="marcar_modulo_{{ Str::slug($modulo) }}"
                                                       data-modulo="{{ Str::slug($modulo) }}">
                                                <label class="form-check-label text-white small"
                                                       for="marcar_modulo_{{ Str::slug($modulo) }}">Seleccionar todo</label>
                                            </div>
                                        </div>

                                        <div class="card-body contenedor-permisos bg-light"
                                             data-modulo="{{ Str::slug($modulo) }}">
                                            <input type="text" class="form-control form-control-sm mb-3 rounded-3 shadow-sm buscador-modulo"
                                                   placeholder="Buscar permisos..." data-modulo="{{ Str::slug($modulo) }}">
                                            <div class="row">
                                                @foreach($permisos as $permiso => $desc)
                                                    <div class="col-12 permiso-row"
                                                         data-etiqueta="{{ Str::lower($desc) }}"
                                                         data-modulo="{{ Str::slug($modulo) }}">
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input permiso-item permiso-{{ Str::slug($modulo) }}"
                                                                   type="checkbox" name="permisos[]"
                                                                   value="{{ $permiso }}"
                                                                   id="permiso_{{ $permiso }}"
                                                                   @if(in_array($permiso, $rol->permisos ?? [])) checked @endif>
                                                            <label class="form-check-label text-secondary"
                                                                   for="permiso_{{ $permiso }}">
                                                                {{ $desc }}
                                                            </label>
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

                    <div class="mt-4 d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-gradient px-4 rounded-3 shadow-sm">
                            <i class="fas fa-save me-2"></i> Actualizar rol
                        </button>
                        <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary px-4 rounded-3 shadow-sm">
                            <i class="fas fa-arrow-left me-2"></i> Cancelar
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<style>
body {
    background: linear-gradient(135deg, #dce3ff 0%, #e6e0ff 100%);
    font-family: 'Poppins', sans-serif;
}

.dashboard-bg {
    background: linear-gradient(135deg, #dce3ff 0%, #e6e0ff 100%);
}

.sidebar-bg {
    background-color: #1f2937;
}

.glass-card {
    background: rgba(255, 255, 255, 0.75);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.35);
}

.btn-gradient {
    background: linear-gradient(135deg, #6b73ff, #a06bff);
    border: none;
    color: white !important;
    font-weight: 500;
    transition: 0.3s ease;
}
.btn-gradient:hover {
    opacity: 0.9;
    transform: translateY(-2px);
}

.text-dark {
    color: #2b2b2b !important;
}
.text-secondary {
    color: #5a5a5a !important;
}
.card-header {
    border-bottom: none;
}
</style>

@endsection

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
        if (contenedor) contenedor.querySelector('.seleccionados').textContent = seleccionados;
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

    marcarTodo?.addEventListener('click', () => {
        document.querySelectorAll('.permiso-item').forEach(cb => cb.checked = true);
        document.querySelectorAll('.marcar-modulo').forEach(cb => cb.checked = true);
        actualizarTodosLosConteos();
    });

    desmarcarTodo?.addEventListener('click', () => {
        document.querySelectorAll('.permiso-item').forEach(cb => cb.checked = false);
        document.querySelectorAll('.marcar-modulo').forEach(cb => cb.checked = false);
        actualizarTodosLosConteos();
    });

    const setModulosVisibles = visible => {
        document.querySelectorAll('.contenedor-permisos')
            .forEach(body => body.classList.toggle('d-none', !visible));
    };

    expandirTodo?.addEventListener('click', () => setModulosVisibles(true));
    contraerTodo?.addEventListener('click', () => setModulosVisibles(false));

    document.querySelectorAll('.marcar-modulo').forEach(cb => {
        cb.addEventListener('change', e => {
            const modulo = e.target.dataset.modulo;
            document.querySelectorAll(`.permiso-${modulo}`).forEach(it => it.checked = e.target.checked);
            actualizarConteoModulo(modulo);
        });
    });

    document.querySelectorAll('.permiso-item').forEach(cb => {
        cb.addEventListener('change', e => {
            const modulo = e.target.className.match(/permiso-(\S+)/)[1];
            actualizarConteoModulo(modulo);
        });
    });

    document.querySelectorAll('.buscador-modulo').forEach(inp => {
        inp.addEventListener('input', e => {
            const q = e.target.value.trim().toLowerCase();
            const modulo = e.target.dataset.modulo;
            document.querySelectorAll(`.permiso-row[data-modulo="${modulo}"]`).forEach(row => {
                const visible = q === '' || row.dataset.etiqueta.includes(q);
                row.classList.toggle('d-none', !visible);
            });
        });
    });

    actualizarTodosLosConteos();
});
</script>
@endpush
