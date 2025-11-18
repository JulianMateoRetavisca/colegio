@extends('layouts.app')
@section('content')
<div class="container">
    <h1>Orientación Psicológica - Citas</h1>
    <p class="text-muted">Listado rápido (máx 100). Acciones según estado. </p>
    <div class="mb-4">
        @if($user->tienePermiso('solicitar_orientacion'))
        <form method="POST" action="{{ route('orientacion.citas.solicitar') }}" class="row g-2">
            @csrf
            <div class="col-md-4">
                <input type="date" name="fecha_solicitada" class="form-control" placeholder="Fecha solicitada opcional">
            </div>
            <div class="col-md-5">
                <input type="text" name="motivo" required class="form-control" placeholder="Motivo / descripción breve">
            </div>
            <div class="col-md-3">
                <button class="btn btn-primary w-100">Solicitar cita</button>
            </div>
        </form>
        @endif
    </div>

    <table class="table table-sm table-bordered align-middle">
        <thead>
            <tr>
                <th>ID</th>
                <th>Estudiante</th>
                <th>Orientador</th>
                <th>Estado</th>
                <th>Fechas</th>
                <th>Acciones</th>
                <th>Historial</th>
            </tr>
        </thead>
        <tbody>
            @forelse($citas as $c)
            <tr>
                <td>{{ $c->id }}</td>
                <td>{{ $c->estudiante?->name }}</td>
                <td>{{ $c->orientador?->name ?? '—' }}</td>
                <td><span class="badge bg-secondary">{{ $c->estado }}</span></td>
                <td>
                    <div style="font-size:12px">
                        Solicitada: {{ $c->fecha_solicitada ?? '—' }}<br>
                        Asignada: {{ $c->fecha_asignada ? ($c->fecha_asignada.' '.$c->hora_asignada) : '—' }}<br>
                        Próxima: {{ $c->fecha_proxima ?? '—' }}<br>
                        Cerrada: {{ $c->cerrada_at ?? '—' }}
                    </div>
                </td>
                <td style="width:320px">
                    @php $esOrientador = strtolower($user->rol?->nombre ?? '') === 'orientador'; @endphp
                    @if($esOrientador)
                        @if($c->estado === 'solicitada')
                            <form method="POST" action="{{ url('/orientacion/citas/'.$c->id.'/revisar') }}" class="d-inline">
                                @csrf
                                <button class="btn btn-sm btn-warning">Revisar</button>
                            </form>
                        @endif
                        @if(in_array($c->estado,['revisada','reprogramada']))
                            <form method="POST" action="{{ url('/orientacion/citas/'.$c->id.'/asignar') }}" class="row g-1 mt-1">
                                @csrf
                                <div class="col-5"><input type="date" name="fecha_asignada" required class="form-control form-control-sm"></div>
                                <div class="col-4"><input type="time" name="hora_asignada" required class="form-control form-control-sm"></div>
                                <div class="col-3"><button class="btn btn-sm btn-primary w-100">Asignar</button></div>
                            </form>
                        @endif
                        @if(in_array($c->estado,['asignada','reprogramada']))
                            <form method="POST" action="{{ url('/orientacion/citas/'.$c->id.'/realizar') }}" class="d-inline mt-1">
                                @csrf
                                <button class="btn btn-sm btn-success">Realizada</button>
                            </form>
                            <form method="POST" action="{{ url('/orientacion/citas/'.$c->id.'/reprogramar') }}" class="row g-1 mt-1">
                                @csrf
                                <div class="col-5"><input type="date" name="fecha_asignada" required class="form-control form-control-sm" placeholder="Nueva fecha"></div>
                                <div class="col-4"><input type="time" name="hora_asignada" required class="form-control form-control-sm"></div>
                                <div class="col-3"><button class="btn btn-sm btn-outline-secondary w-100">Reprogramar</button></div>
                            </form>
                        @endif
                        @if($c->estado === 'realizada')
                            <form method="POST" action="{{ url('/orientacion/citas/'.$c->id.'/registrar-observaciones') }}" class="mt-1">
                                @csrf
                                <textarea name="observaciones" required class="form-control form-control-sm" rows="2" placeholder="Observaciones"></textarea>
                                <button class="btn btn-sm btn-info mt-1">Registrar Observaciones</button>
                            </form>
                        @endif
                        @if($c->estado === 'registrada')
                            <form method="POST" action="{{ url('/orientacion/citas/'.$c->id.'/evaluar-seguimiento') }}" class="mt-1 row g-1">
                                @csrf
                                <div class="col-6">
                                    <select name="seguimiento" class="form-select form-select-sm">
                                        <option value="0">Cerrar</option>
                                        <option value="1">Requiere seguimiento</option>
                                    </select>
                                </div>
                                <div class="col-6"><input type="date" name="fecha_proxima" class="form-control form-control-sm" placeholder="Fecha próxima"></div>
                                <div class="col-12 mt-1"><button class="btn btn-sm btn-dark w-100">Evaluar</button></div>
                            </form>
                        @endif
                    @endif
                </td>
                <td>
                    <a href="{{ url('/orientacion/citas/'.$c->id.'/historial') }}" target="_blank" class="btn btn-sm btn-outline-primary">Ver</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center text-muted">Sin citas aún</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection