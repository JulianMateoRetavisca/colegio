@extends('layouts.app')

@section('content')
<section class="page-section">
    <div class="page-header">
        <div class="page-title">
            <h1 class="h4 mb-0"><i class="fas fa-graduation-cap me-2 text-primary"></i>Crear Nota</h1>
            <p class="subtitle">Estado inicial: borrador</p>
        </div>
        <div class="action-bar">
            <a href="{{ route('notas.mostrar') }}" class="btn-pro outline"><i class="fas fa-list me-1"></i>Ver Notas</a>
        </div>
    </div>
    <x-form-card title="Datos de la Nota">
        @if($errors->any())
            <div class="alert alert-danger alert-sm mb-3">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('notas.guardar') }}" method="POST" class="row g-3">
            @csrf
            <div class="col-md-4">
                <label for="group_id" class="form-label">Grupo</label>
                <select name="group_id" id="group_id" class="form-select form-select-sm">
                    <option value="">-- Seleccione grupo --</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="estudiante_id" class="form-label">Estudiante</label>
                <select name="estudiante_id" id="estudiante_id" class="form-select form-select-sm" required>
                    <option value="">-- Seleccione estudiante --</option>
                    @foreach($estudiantes as $est)
                        <option value="{{ $est->id }}">{{ $est->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="materia_id" class="form-label">Materia</label>
                <select name="materia_id" id="materia_id" class="form-select form-select-sm" required>
                    <option value="">-- Seleccione --</option>
                    @foreach($materias as $id => $nombre)
                        <option value="{{ $id }}">{{ $nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label for="nota" class="form-label">Nota (0-100)</label>
                <input type="number" name="nota" id="nota" class="form-control form-control-sm" min="0" max="100" step="0.01" required>
            </div>
            <div class="col-md-6">
                <label for="periodo" class="form-label">Periodo</label>
                <input type="text" name="periodo" id="periodo" class="form-control form-control-sm" maxlength="4" placeholder="Ej: 2025" required>
            </div>
            <div class="col-12 d-flex justify-content-end gap-2 mt-2">
                <a href="{{ route('notas.mostrar') }}" class="btn-pro outline">Cancelar</a>
                <button type="submit" class="btn-pro primary"><i class="fas fa-save me-1"></i>Guardar (Borrador)</button>
            </div>
        </form>
        <script>
                    document.addEventListener('DOMContentLoaded', function(){
                        const groupSelect = document.getElementById('group_id');
                        const studentSelect = document.getElementById('estudiante_id');

                        // Cargar grupos via endpoint
                        fetch('{{ route('notas.grupos') }}', { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
                            .then(r => {
                                if (!r.ok) throw new Error('HTTP ' + r.status);
                                return r.json();
                            })
                            .then(data => {
                                if (!data || data.length === 0) {
                                    // no hay grupos: dejar el listado de estudiantes como viene del servidor
                                    return;
                                }
                                // limpiar opciones actuales
                                groupSelect.innerHTML = '<option value="">-- Seleccione grupo --</option>';
                                data.forEach(g => {
                                    const opt = document.createElement('option');
                                    opt.value = g.id; opt.textContent = g.nombre || ('Grupo ' + g.id);
                                    groupSelect.appendChild(opt);
                                });

                                // cuando cambie el grupo, solicitar estudiantes
                                groupSelect.addEventListener('change', () => {
                                    const gid = groupSelect.value;
                                    // limpiar estudiantes
                                    studentSelect.innerHTML = '<option value="">-- Seleccione estudiante --</option>';
                                    if (!gid) return;
                                    fetch('{{ url('/notas/grupo') }}' + '/' + gid + '/estudiantes', { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
                                        .then(r => {
                                            if (!r.ok) throw new Error('HTTP ' + r.status);
                                            return r.json();
                                        })
                                        .then(list => {
                                            if (!list || list.length === 0) {
                                                const opt = document.createElement('option');
                                                opt.value = ''; opt.textContent = 'No hay estudiantes en este grupo';
                                                studentSelect.appendChild(opt);
                                                return;
                                            }
                                            list.forEach(s => {
                                                const opt = document.createElement('option');
                                                opt.value = s.id; opt.textContent = s.name;
                                                studentSelect.appendChild(opt);
                                            });
                                        })
                                        .catch(err => {
                                            console.error(err);
                                        });
                                });

                                // auto seleccionar primer grupo si existe
                                if (groupSelect.options.length > 1) {
                                    groupSelect.selectedIndex = 1;
                                    groupSelect.dispatchEvent(new Event('change'));
                                }
                            })
                            .catch(err => console.error(err));
                    });
                </script>
    </x-form-card>
</section>
@endsection
