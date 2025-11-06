@extends('layouts.app')

@section('content')
@php
    $usuario = Auth::user();
    $rol = App\Models\RolesModel::find($usuario->roles_id);
@endphp

<div class="container-fluid min-vh-100" style="background: linear-gradient(135deg, #e0e7ff 0%, #ede9fe 50%, #faf5ff 100%);">
    <div class="row g-0">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 p-0 shadow-sm" style="background-color: #1f2937;">
            @include('partials.sidebar')
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 px-5 py-4">
            <div class="main-content bg-white bg-opacity-90 p-4 rounded-4 shadow-lg border-0">

                <div class="mb-4 border-bottom pb-2">
                    <h1 class="fw-bold" style="color: #4f46e5;">
                        <i class="fas fa-user-shield me-2 text-indigo-600"></i> Crear nuevo rol
                    </h1>
                    <p class="text-secondary mb-0">Define los permisos y características del nuevo rol.</p>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger shadow-sm rounded-4 border-0">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('roles.guardar') }}" method="POST" class="p-4 rounded-4 shadow-sm border-0"
                      style="background: linear-gradient(145deg, #ffffff, #f5f3ff);">
                    @csrf

                    <div class="mb-4">
                        <label for="nombre" class="form-label fw-semibold text-indigo-700">Nombre del rol</label>
                        <input type="text" name="nombre" id="nombre"
                               class="form-control shadow-sm rounded-3 border-0"
                               style="background-color: #eef2ff;"
                               required value="{{ old('nombre') }}">
                    </div>

                    <div class="mb-4">
                        <label for="descripcion" class="form-label fw-semibold text-indigo-700">Descripción</label>
                        <textarea name="descripcion" id="descripcion"
                                  class="form-control shadow-sm rounded-3 border-0"
                                  style="background-color: #eef2ff;" rows="2"
                                  required>{{ old('descripcion') }}</textarea>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold text-indigo-800 mb-0">
                                <i class="fas fa-key me-2 text-indigo-500"></i> Permisos por módulo
                            </h5>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm rounded-3 shadow-sm text-white" id="marcar_todo"
                                        style="background: linear-gradient(90deg, #6366f1, #8b5cf6); border: none;">
                                    <i class="fas fa-check-double me-1"></i> Marcar todo
                                </button>
                                <button type="button" class="btn btn-sm rounded-3 shadow-sm text-white" id="desmarcar_todo"
                                        style="background: linear-gradient(90deg, #94a3b8, #64748b); border: none;">
                                    <i class="fas fa-times me-1"></i> Desmarcar todo
                                </button>
                                <button type="button" class="btn btn-sm rounded-3 shadow-sm text-white" id="expandir_todo"
                                        style="background: linear-gradient(90deg, #60a5fa, #818cf8); border: none;">
                                    <i class="fas fa-expand me-1"></i> Expandir
                                </button>
                                <button type="button" class="btn btn-sm rounded-3 shadow-sm text-white" id="contraer_todo"
                                        style="background: linear-gradient(90deg, #4b5563, #1f2937); border: none;">
                                    <i class="fas fa-compress me-1"></i> Contraer
                                </button>
                            </div>
                        </div>

                        <div class="row g-4">
                            @foreach($gruposPermisos as $modulo => $permisos)
                                <div class="col-lg-6">
                                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden"
                                         style="background-color: #fafafa;">
                                        <div class="card-header d-flex justify-content-between align-items-center text-white"
                                             style="background: linear-gradient(90deg, #6366f1, #8b5cf6); border: none;">
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

                                        <div class="card-body contenedor-permisos p-3"
                                             style="background-color: #f9f9ff;"
                                             data-modulo="{{ Str::slug($modulo) }}">
                                            <input type="text" class="form-control form-control-sm mb-3 rounded-3 shadow-sm buscador-modulo border-0"
                                                   placeholder="Buscar permisos..." data-modulo="{{ Str::slug($modulo) }}"
                                                   style="background-color: #eef2ff;">
                                            <div class="row">
                                                @foreach($permisos as $permiso => $desc)
                                                    <div class="col-12 permiso-row"
                                                         data-etiqueta="{{ Str::lower($desc) }}"
                                                         data-modulo="{{ Str::slug($modulo) }}">
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input permiso-item permiso-{{ Str::slug($modulo) }}"
                                                                   type="checkbox" name="permisos[]"
                                                                   value="{{ $permiso }}"
                                                                   id="permiso_{{ $permiso }}">
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
                        <button type="submit" class="btn px-4 rounded-3 shadow-sm text-white"
                                style="background: linear-gradient(90deg, #4f46e5, #7c3aed); border: none;">
                            <i class="fas fa-save me-2"></i> Guardar rol
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
