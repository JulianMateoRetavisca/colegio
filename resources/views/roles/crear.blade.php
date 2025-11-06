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
            @include('partials.sidebar')
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10" style="background-color: #f7f7f7;">
            <div class="main-content p-4">

                <h1 class="mb-4">Crear nuevo rol</h1>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('roles.guardar') }}" method="POST" class="bg-white p-4 rounded shadow-sm">
                    @csrf
                    <div class="mb-3">
                        <label for="nombre" class="form-label fw-semibold">Nombre del rol</label>
                        <input type="text" name="nombre" id="nombre" class="form-control" required value="{{ old('nombre') }}">
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label fw-semibold">Descripción</label>
                        <textarea name="descripcion" id="descripcion" class="form-control" rows="2" required>{{ old('descripcion') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                            <h5 class="fw-semibold mb-0">Permisos por módulo</h5>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-primary btn-sm" id="marcar_todo">
                                    <i class="fas fa-check-double me-1"></i> Marcar todo
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="desmarcar_todo">
                                    <i class="fas fa-times me-1"></i> Desmarcar todo
                                </button>
                                <button type="button" class="btn btn-outline-success btn-sm" id="expandir_todo">
                                    <i class="fas fa-expand me-1"></i> Expandir
                                </button>
                                <button type="button" class="btn btn-outline-dark btn-sm" id="contraer_todo">
                                    <i class="fas fa-compress me-1"></i> Contraer
                                </button>
                            </div>
                        </div>

                        <div class="row g-3">
                            @foreach($gruposPermisos as $modulo => $permisos)
                                <div class="col-lg-6">
                                    <div class="card h-100 shadow-sm">
                                        <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                                            <span class="fw-semibold">
                                                {{ $modulo }}
                                                <small class="ms-2 text-light conteo-modulo" data-modulo="{{ Str::slug($modulo) }}" data-total="{{ count($permisos) }}">
                                                    (<span class="seleccionados">0</span>/{{ count($permisos) }})
                                                </small>
                                            </span>
                                            <div class="form-check m-0">
                                                <input class="form-check-input marcar-modulo" type="checkbox" id="marcar_modulo_{{ Str::slug($modulo) }}" data-modulo="{{ Str::slug($modulo) }}">
                                                <label class="form-check-label text-white small" for="marcar_modulo_{{ Str::slug($modulo) }}">Seleccionar todo</label>
                                            </div>
                                        </div>

                                        <div class="card-body contenedor-permisos" data-modulo="{{ Str::slug($modulo) }}">
                                            <input type="text" class="form-control form-control-sm mb-3 buscador-modulo" placeholder="Buscar permisos..." data-modulo="{{ Str::slug($modulo) }}">
                                            <div class="row">
                                                @foreach($permisos as $permiso => $desc)
                                                    <div class="col-12 permiso-row" data-etiqueta="{{ Str::lower($desc) }}" data-modulo="{{ Str::slug($modulo) }}">
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

                    <div class="mt-4 d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-success px-4">
                            <i class="fas fa-save me-2"></i>Guardar rol
                        </button>
                        <a href="{{ route('roles.index') }}" class="btn btn-secondary px-4">
                            <i class="fas fa-arrow-left me-2"></i>Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
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

    function setModulosVisibles(visible) {
        document.querySelectorAll('.contenedor-permisos').forEach(body => {
            body.classList.toggle('d-none', !visible);
        });
    }

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
