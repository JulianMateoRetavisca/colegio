@extends('layouts.app')

@section('title', 'Horarios')

@section('content')
@php
    $usuario = Auth::user();
@endphp

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 p-0">
            @include('partials.sidebar')
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 px-4 py-4">

            <!-- Hero / Header (purple subtle gradient like template) -->
            <div class="mb-4 rounded-4 p-4" style="background: linear-gradient(180deg, #f1eafe 0%, #efe7ff 100%);">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <span class="bg-white rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center" style="width:56px;height:56px;">
                            <i class="fa fa-calendar-alt text-primary fa-lg"></i>
                        </span>
                    </div>
                    <div>
                        <h2 class="h4 mb-1 text-dark">Crear Horario</h2>
                        <p class="mb-0 text-muted small">Registra un nuevo horario en el sistema</p>
                    </div>
                </div>
            </div>

            <!-- Mensajes flash -->
            <div class="mx-auto mt-4" style="max-width:1100px;">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('warning'))
                    <div class="alert alert-warning">{{ session('warning') }}</div>
                @endif
            </div>

            <!-- Tabla de horarios existentes -->
            <div class="mx-auto mt-3" style="max-width:1100px;">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-3">
                        <h5 class="mb-3">Horarios registrados</h5>
                        <div class="table-responsive">
                            <table class="table table-sm align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Grupo</th>
                                        <th>Materia</th>
                                        <th>Docente</th>
                                        <th>Día</th>
                                        <th>Hora inicio</th>
                                        <th>Hora fin</th>
                                        <th>Observaciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($horarios) === 0)
                                        <tr>
                                            <td colspan="7" class="text-center text-secondary py-4">No hay horarios registrados.</td>
                                        </tr>
                                    @else
                                        @foreach($horarios as $h)
                                            @php
                                                // soportar tanto registros DB (obj) como session (array)
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
                </div>
            </div>

            <!-- Centered card with form (matching the create docente template) -->
            <div class="mx-auto" style="max-width:1100px;">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('horarios.store', []) }}">
                            @csrf
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Grupo</label>
                                    <select name="grupo_id" class="form-select">
                                        <option value="">-- Seleccione un grupo --</option>
                                        @foreach($grupos as $g)
                                            <option value="{{ $g->id }}">{{ $g->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Materia</label>
                                    <select name="materia_id" class="form-select">
                                        <option value="">-- Seleccione una materia --</option>
                                        @foreach($materias as $m)
                                            <option value="{{ $m->id }}">{{ $m->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Docente</label>
                                    <select name="docente_id" class="form-select">
                                        <option value="">-- Seleccione un docente --</option>
                                        @foreach($docentes as $doc)
                                            <option value="{{ $doc->id }}">{{ $doc->name }} &lt;{{ $doc->email }}&gt;</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Día</label>
                                    <select name="dia" class="form-select">
                                        <option value="Lunes">Lunes</option>
                                        <option value="Martes">Martes</option>
                                        <option value="Miércoles">Miércoles</option>
                                        <option value="Jueves">Jueves</option>
                                        <option value="Viernes">Viernes</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Hora inicio</label>
                                    <input type="time" name="hora_inicio" class="form-control" />
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Hora fin</label>
                                    <input type="time" name="hora_fin" class="form-control" />
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Observaciones (opcional)</label>
                                    <input type="text" name="observaciones" class="form-control" placeholder="Sala, notas, etc." />
                                </div>

                                <div class="col-12 d-flex justify-content-end">
                                    <a href="{{ url()->previous() }}" class="btn btn-secondary me-2">← Cancelar</a>
                                    <button type="submit" class="btn btn-primary">Guardar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    console.debug('Horarios: formulario listo');
    // Aquí puedes añadir JS para validaciones, select2, o AJAX de guardado
});
</script>
@endsection

