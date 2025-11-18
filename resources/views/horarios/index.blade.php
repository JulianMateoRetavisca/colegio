@extends('layouts.app')

@section('title', 'Horarios')

@section('content')
<section class="page-section">
    <div class="page-header">
        <div class="page-title">
            <h1 class="h4 mb-0"><i class="fa fa-calendar-alt me-2 text-primary"></i>Horarios</h1>
            <p class="subtitle">Visualización y gestión de horarios académicos</p>
        </div>
        @if($rolAlto)
        <div class="action-bar">
            <a href="#form-horario" class="btn-pro primary"><i class="fas fa-plus me-1"></i>Nuevo Horario</a>
        </div>
        @endif
    </div>

    @if(session('success'))<div class="alert alert-success alert-sm">{{ session('success') }}</div>@endif
    @if(session('warning'))<div class="alert alert-warning alert-sm">{{ session('warning') }}</div>@endif

    @if($rolAlto)
    <div class="pro-card mb-4">
        <form method="GET" action="{{ route('horarios.index') }}" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label mb-1">Filtrar por docente</label>
                <select name="docente_id" class="form-select form-select-sm">
                    <option value="">-- Todos --</option>
                    @foreach($docentes as $d)
                        <option value="{{ $d->id }}" @selected(request('docente_id') == $d->id)>{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn-pro primary w-100">Aplicar</button>
            </div>
            @if(request()->filled('docente_id'))
            <div class="col-md-2">
                <a href="{{ route('horarios.index') }}" class="btn-pro outline w-100">Quitar filtro</a>
            </div>
            @endif
        </form>
    </div>
    @endif

    <div class="pro-card">
        <div class="pro-card-header">
            <h2 class="h6 mb-0">Horarios registrados
                @if($esProfesor) <small class="text-muted">(tus horarios)</small>
                @elseif($rolAlto && request('docente_id')) <small class="text-muted">(filtrado)</small>@endif
            </h2>
        </div>
        <div class="pro-table-wrapper">
            <table class="pro-table table-sm">
                <thead>
                    <tr><th>Grupo</th><th>Materia</th><th>Docente</th><th>Día</th><th>Inicio</th><th>Fin</th><th>Observaciones</th></tr>
                </thead>
                <tbody>
                @if(count($horarios) === 0)
                    <tr><td colspan="7" class="text-center text-muted py-4">No hay horarios registrados.</td></tr>
                @else
                    @foreach($horarios as $h)
                        @php
                            $gid = is_array($h) ? ($h['grupo_id'] ?? null) : ($h->grupo_id ?? null);
                            $mid = is_array($h) ? ($h['materia_id'] ?? null) : ($h->materia_id ?? null);
                            $did = is_array($h) ? ($h['docente_id'] ?? null) : ($h->docente_id ?? null);
                            $dia = is_array($h) ? ($h['dia'] ?? null) : ($h->dia ?? null);
                            $hi = is_array($h) ? ($h['hora_inicio'] ?? null) : ($h->hora_inicio ?? null);
                            $hf = is_array($h) ? ($h['hora_fin'] ?? null) : ($h->hora_fin ?? null);
                            $obs = is_array($h) ? ($h['observaciones'] ?? null) : ($h->observaciones ?? null);
                            $gName = $gid ? (\App\Models\Grupo::find($gid)->nombre ?? '—') : '—';
                            $mName = $mid ? (class_exists(\App\Models\Materia::class) ? (\App\Models\Materia::find($mid)->nombre ?? '—') : '—') : '—';
                            $dName = $did ? (\App\Models\User::find($did)->name ?? '—') : '—';
                        @endphp
                        <tr>
                            <td>{{ $gName }}</td>
                            <td>{{ $mName }}</td>
                            <td>{{ $dName }}</td>
                            <td>{{ $dia }}</td>
                            <td>{{ $hi }}</td>
                            <td>{{ $hf }}</td>
                            <td>{{ $obs }}</td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
        </div>
    </div>

    @if(!$esProfesor && !$rolAlto)
        <div class="alert alert-info mt-4">No tienes permisos para crear horarios, sólo visualizarlos.</div>
    @endif

    @if($rolAlto)
    <div class="pro-card mt-4" id="form-horario">
        <div class="pro-card-header"><h2 class="h6 mb-0">Crear Horario</h2></div>
        <form method="POST" action="{{ route('horarios.store', []) }}" class="mt-3">
            @csrf
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">Grupo</label>
                    <select name="grupo_id" class="form-select form-select-sm">
                        <option value="">-- Seleccione un grupo --</option>
                        @foreach($grupos as $g)
                            <option value="{{ $g->id }}">{{ $g->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Materia</label>
                    <select name="materia_id" class="form-select form-select-sm">
                        <option value="">-- Seleccione una materia --</option>
                        @foreach($materias as $m)
                            <option value="{{ $m->id }}">{{ $m->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Docente</label>
                    <select name="docente_id" class="form-select form-select-sm" @if($esProfesor) disabled @endif>
                        <option value="">-- Seleccione un docente --</option>
                        @foreach($docentes as $doc)
                            <option value="{{ $doc->id }}">{{ $doc->name }} <{{ $doc->email }}></option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Día</label>
                    <select name="dia" class="form-select form-select-sm">
                        <option value="Lunes">Lunes</option>
                        <option value="Martes">Martes</option>
                        <option value="Miércoles">Miércoles</option>
                        <option value="Jueves">Jueves</option>
                        <option value="Viernes">Viernes</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Hora inicio</label>
                    <input type="time" name="hora_inicio" class="form-control form-control-sm" />
                </div>
                <div class="col-md-4">
                    <label class="form-label">Hora fin</label>
                    <input type="time" name="hora_fin" class="form-control form-control-sm" />
                </div>
                <div class="col-12">
                    <label class="form-label">Observaciones (opcional)</label>
                    <input type="text" name="observaciones" class="form-control form-control-sm" placeholder="Sala, notas, etc." />
                </div>
                <div class="col-12 d-flex justify-content-end">
                    <button type="submit" class="btn-pro primary"><i class="fas fa-save me-1"></i>Guardar</button>
                </div>
            </div>
        </form>
    </div>
    @endif
</section>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    console.debug('Horarios: formulario listo');
    // Aquí puedes añadir JS para validaciones, select2, o AJAX de guardado
});
</script>
@endsection

