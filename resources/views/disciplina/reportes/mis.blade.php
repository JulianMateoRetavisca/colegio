@extends('layouts.app')
@section('content')
<section class="page-section">
    <div class="page-header">
        <div class="page-title">
            <h1 class="h4 mb-0"><i class="fas fa-gavel me-2 text-danger"></i>Mis Reportes Disciplinarios</h1>
            <p class="subtitle">Estado y acciones de los incidentes registrados</p>
        </div>
        <div class="action-bar">
            <a href="{{ url('/disciplina/reportes/crear') }}" class="btn-pro primary"><i class="fas fa-plus me-1"></i>Nuevo Reporte</a>
        </div>
    </div>
    <div class="pro-card">
        <div class="pro-card-header"><h2 class="h6 mb-0">Listado</h2></div>
        <div class="pro-table-wrapper">
            <table class="pro-table table-sm align-middle">
                <thead>
                    <tr><th>ID</th><th>Estudiante</th><th>Docente</th><th>Estado</th><th>Gravedad</th><th>Sanción</th><th style="width:300px">Acciones</th></tr>
                </thead>
                <tbody>
                @forelse($reportes as $r)
                    <tr>
                        <td>{{ $r->id }}</td>
                        <td>{{ $r->estudiante?->name }}</td>
                        <td>{{ $r->docente?->name }}</td>
                        <td><span class="badge bg-secondary">{{ $r->estado }}</span></td>
                        <td>{{ $r->gravedad ?? '—' }}</td>
                        <td class="small" style="max-width:160px">{{ $r->sancion_activa ? $r->sancion_text : '—' }}</td>
                        <td>
                            @php $rolNombre = strtolower($user->rol?->nombre ?? ''); @endphp
                            <div class="d-flex flex-column gap-1">
                            @if($rolNombre==='coordinadordisciplina' || $rolNombre==='coordinadoracademico' || $rolNombre==='admin' || $rolNombre==='administrador' || $rolNombre==='rector')
                                @if($r->estado==='reportado')
                                    <form method="POST" action="{{ url('/disciplina/reportes/'.$r->id.'/revisar') }}">@csrf <button class="btn-pro xs warning">Revisar</button></form>
                                @endif
                                @if($r->estado==='en_revision')
                                    <form method="POST" action="{{ url('/disciplina/reportes/'.$r->id.'/asignar-sancion') }}" class="">@csrf
                                        <textarea name="sancion_text" class="form-control form-control-sm mb-1" rows="2" required placeholder="Sanción"></textarea>
                                        <button class="btn-pro xs primary w-100">Asignar</button>
                                    </form>
                                @endif
                                @if($r->estado==='sancion_asignada')
                                    <form method="POST" action="{{ url('/disciplina/reportes/'.$r->id.'/notificar') }}">@csrf <button class="btn-pro xs success w-100">Notificar</button></form>
                                @endif
                                @if(in_array($r->estado,['notificado','apelacion_aceptada','apelacion_rechazada']))
                                    <form method="POST" action="{{ url('/disciplina/reportes/'.$r->id.'/archivar') }}">@csrf <button class="btn-pro xs dark w-100">Archivar</button></form>
                                @endif
                                @if($r->estado==='apelacion_solicitada')
                                    <form method="POST" action="{{ url('/disciplina/reportes/'.$r->id.'/apelacion-revisar') }}">@csrf <button class="btn-pro xs info w-100">Revisar Apelación</button></form>
                                @endif
                                @if($r->estado==='apelacion_en_revision')
                                    <form method="POST" action="{{ url('/disciplina/reportes/'.$r->id.'/apelacion-resolver') }}" class="">@csrf
                                        <select name="resultado" class="form-select form-select-sm mb-1" required>
                                            <option value="aceptada">Aceptar</option>
                                            <option value="rechazada">Rechazar</option>
                                        </select>
                                        <textarea name="nueva_sancion_text" class="form-control form-control-sm mb-1" rows="2" placeholder="Nueva sanción (opcional)"></textarea>
                                        <div class="form-check small mb-1">
                                          <input class="form-check-input" type="checkbox" name="eliminar_sancion" value="1" id="elim{{ $r->id }}">
                                          <label class="form-check-label" for="elim{{ $r->id }}">Eliminar sanción</label>
                                        </div>
                                        <button class="btn-pro xs outline w-100">Resolver</button>
                                    </form>
                                @endif
                            @endif
                            @if($user->tienePermiso('apelar_sancion') && in_array($r->estado,['sancion_asignada','notificado']))
                                <form method="POST" action="{{ url('/disciplina/reportes/'.$r->id.'/apelacion') }}">@csrf
                                    <textarea name="apelacion_motivo" class="form-control form-control-sm mb-1" rows="2" required placeholder="Motivo apelación"></textarea>
                                    <button class="btn-pro xs danger w-100">Apelar</button>
                                </form>
                            @endif
                            <a href="{{ url('/disciplina/reportes/'.$r->id.'/historial') }}" target="_blank" class="btn-pro xs outline w-100">Historial</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Sin reportes</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection