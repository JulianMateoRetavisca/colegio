@extends('layouts.app')
@section('content')
<section class="page-section">
    <div class="page-header">
        <div class="page-title">
            <h1 class="h4 mb-0"><i class="fas fa-user-md me-2 text-primary"></i>Citas de Orientación</h1>
            <p class="subtitle">Gestión y seguimiento de citas psicológicas</p>
        </div>
        @if($user->tienePermiso('solicitar_orientacion'))
        <div class="action-bar">
            <button class="btn-pro primary" data-bs-toggle="collapse" data-bs-target="#solicitudCita"><i class="fas fa-plus me-1"></i>Nueva Solicitud</button>
        </div>
        @endif
    </div>

    @if($user->tienePermiso('solicitar_orientacion'))
    <div class="collapse show mb-4" id="solicitudCita">
        <div class="pro-card">
            <div class="pro-card-header"><h2 class="h6 mb-0">Solicitar cita</h2></div>
            <form method="POST" action="{{ route('orientacion.citas.solicitar') }}" class="row g-2 mt-1">
                @csrf
                <div class="col-md-4">
                    <input type="date" name="fecha_solicitada" class="form-control form-control-sm" placeholder="Fecha opcional">
                </div>
                <div class="col-md-5">
                    <input type="text" name="motivo" required class="form-control form-control-sm" placeholder="Motivo breve">
                </div>
                <div class="col-md-3">
                    <button class="btn-pro primary w-100"><i class="fas fa-paper-plane me-1"></i>Solicitar</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <div class="pro-card">
        <div class="pro-card-header"><h2 class="h6 mb-0">Listado de Citas</h2></div>
        <div class="pro-table-wrapper">
            <table class="pro-table table-sm align-middle">
                <thead>
                    <tr><th>ID</th><th>Estudiante</th><th>Orientador</th><th>Estado</th><th>Fechas</th><th>Acciones</th><th>Historial</th></tr>
                </thead>
                <tbody>
                @forelse($citas as $c)
                    <tr>
                        <td>{{ $c->id }}</td>
                        <td>{{ $c->estudiante?->name }}</td>
                        <td>{{ $c->orientador?->name ?? '—' }}</td>
                        <td><span class="badge bg-secondary">{{ $c->estado }}</span></td>
                        <td class="small">
                            Solicitada: {{ $c->fecha_solicitada ?? '—' }}<br>
                            Asignada: {{ $c->fecha_asignada ? ($c->fecha_asignada.' '.$c->hora_asignada) : '—' }}<br>
                            Próxima: {{ $c->fecha_proxima ?? '—' }}<br>
                            Cerrada: {{ $c->cerrada_at ?? '—' }}
                        </td>
                        <td style="width:300px">
                            @php $esOrientador = strtolower($user->rol?->nombre ?? '') === 'orientador'; @endphp
                            @if($esOrientador)
                                <div class="d-flex flex-column gap-1">
                                @if($c->estado === 'solicitada')
                                    <form method="POST" action="{{ url('/orientacion/citas/'.$c->id.'/revisar') }}" class="d-inline">@csrf <button class="btn-pro xs warning">Revisar</button></form>
                                @endif
                                @if(in_array($c->estado,['revisada','reprogramada']))
                                    <form method="POST" action="{{ url('/orientacion/citas/'.$c->id.'/asignar') }}" class="row g-1">
                                        @csrf
                                        <div class="col-5"><input type="date" name="fecha_asignada" required class="form-control form-control-sm"></div>
                                        <div class="col-4"><input type="time" name="hora_asignada" required class="form-control form-control-sm"></div>
                                        <div class="col-3"><button class="btn-pro xs primary w-100">Asignar</button></div>
                                    </form>
                                @endif
                                @if(in_array($c->estado,['asignada','reprogramada']))
                                    <div class="d-flex gap-1">
                                        <form method="POST" action="{{ url('/orientacion/citas/'.$c->id.'/realizar') }}" class="d-inline">@csrf <button class="btn-pro xs success">Realizada</button></form>
                                        <form method="POST" action="{{ url('/orientacion/citas/'.$c->id.'/reprogramar') }}" class="row g-1 flex-grow-1">
                                            @csrf
                                            <div class="col-6"><input type="date" name="fecha_asignada" required class="form-control form-control-sm"></div>
                                            <div class="col-6"><input type="time" name="hora_asignada" required class="form-control form-control-sm"></div>
                                            <div class="col-12 mt-1"><button class="btn-pro xs outline w-100">Reprogramar</button></div>
                                        </form>
                                    </div>
                                @endif
                                @if($c->estado === 'realizada')
                                    <form method="POST" action="{{ url('/orientacion/citas/'.$c->id.'/registrar-observaciones') }}">@csrf
                                        <textarea name="observaciones" required class="form-control form-control-sm" rows="2" placeholder="Observaciones"></textarea>
                                        <button class="btn-pro xs info mt-1">Guardar Obs.</button>
                                    </form>
                                @endif
                                @if($c->estado === 'registrada')
                                    <form method="POST" action="{{ url('/orientacion/citas/'.$c->id.'/evaluar-seguimiento') }}" class="row g-1">@csrf
                                        <div class="col-6"><select name="seguimiento" class="form-select form-select-sm"><option value="0">Cerrar</option><option value="1">Seguimiento</option></select></div>
                                        <div class="col-6"><input type="date" name="fecha_proxima" class="form-control form-control-sm"></div>
                                        <div class="col-12"><button class="btn-pro xs dark w-100 mt-1">Evaluar</button></div>
                                    </form>
                                @endif
                                </div>
                            @endif
                        </td>
                        <td><a href="{{ url('/orientacion/citas/'.$c->id.'/historial') }}" target="_blank" class="btn-pro xs outline">Ver</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Sin citas aún</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection