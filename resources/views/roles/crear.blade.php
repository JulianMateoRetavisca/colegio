@extends('layouts.app')

@section('content')
<section class="page-section">
    <div class="page-header">
        <div class="page-title">
            <h1 class="h4 mb-0"><i class="fas fa-user-shield me-2 text-primary"></i>Crear nuevo rol</h1>
            <p class="subtitle">Define los permisos y características del nuevo rol</p>
        </div>
        <div class="action-bar">
            <a href="{{ route('roles.index') }}" class="btn-pro outline"><i class="fas fa-arrow-left me-1"></i>Volver</a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger shadow-sm border-0 rounded-3">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <x-form-card :title="'Datos del rol'">
        <form action="{{ route('roles.guardar') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="nombre" class="form-label">Nombre del rol</label>
                    <input type="text" name="nombre" id="nombre" class="form-control" required value="{{ old('nombre') }}">
                </div>
                <div class="col-md-6">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <input type="text" name="descripcion" id="descripcion" class="form-control" required value="{{ old('descripcion') }}">
                </div>
            </div>

            <div class="mt-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><i class="fas fa-key me-2 text-primary"></i>Permisos por módulo</h5>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn-pro xs primary" id="marcar_todo"><i class="fas fa-check-double"></i> Marcar todo</button>
                        <button type="button" class="btn-pro xs outline" id="desmarcar_todo"><i class="fas fa-times"></i> Desmarcar</button>
                        <button type="button" class="btn-pro xs info" id="expandir_todo"><i class="fas fa-expand"></i> Expandir</button>
                        <button type="button" class="btn-pro xs dark" id="contraer_todo"><i class="fas fa-compress"></i> Contraer</button>
                    </div>
                </div>

                <div class="row g-3">
                    @foreach($gruposPermisos as $modulo => $permisos)
                        <div class="col-lg-6">
                            <div class="pro-card h-100">
                                <div class="pro-card-header d-flex justify-content-between align-items-center">
                                    <span class="fw-semibold">
                                        {{ $modulo }}
                                        <small class="ms-2 text-muted conteo-modulo" data-modulo="{{ Str::slug($modulo) }}" data-total="{{ count($permisos) }}">
                                            (<span class="seleccionados">0</span>/{{ count($permisos) }})
                                        </small>
                                    </span>
                                    <div class="form-check m-0">
                                        <input class="form-check-input marcar-modulo" type="checkbox" id="marcar_modulo_{{ Str::slug($modulo) }}" data-modulo="{{ Str::slug($modulo) }}">
                                        <label class="form-check-label small" for="marcar_modulo_{{ Str::slug($modulo) }}">Seleccionar todo</label>
                                    </div>
                                </div>
                                <div class="pro-card-body contenedor-permisos" data-modulo="{{ Str::slug($modulo) }}">
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

            <div class="pro-card-footer d-flex justify-content-end gap-2 mt-3">
                <a href="{{ route('roles.index') }}" class="btn-pro outline"><i class="fas fa-arrow-left me-1"></i>Cancelar</a>
                <button type="submit" class="btn-pro success"><i class="fas fa-save me-1"></i>Guardar rol</button>
            </div>
        </form>
    </x-form-card>
</section>
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
