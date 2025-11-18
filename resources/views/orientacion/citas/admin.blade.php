@extends('layouts.app')
@section('content')
<div class="container">
    <h1>Supervisión de Citas de Orientación</h1>
    <p class="text-muted">Vista para administración/rectoría/coordinación. Sin acciones de flujo (solo el Orientador gestiona). Use filtros para localizar casos.</p>

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-2">
            <select name="estado" class="form-select form-select-sm">
                <option value="">-- Estado --</option>
                @foreach($estados as $e)
                    <option value="{{ $e }}" @selected(request('estado')===$e)>{{ ucfirst($e) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="estudiante_id" class="form-select form-select-sm">
                <option value="">-- Estudiante --</option>
                @foreach($estudiantes as $est)
                    @if($est)
                        <option value="{{ $est->id }}" @selected(request('estudiante_id')==$est->id)>{{ $est->name }}</option>
                    @endif
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="orientador_id" class="form-select form-select-sm">
                <option value="">-- Orientador --</option>
                @foreach($orientadores as $ori)
                    @if($ori)
                        <option value="{{ $ori->id }}" @selected(request('orientador_id')==$ori->id)>{{ $ori->name }}</option>
                    @endif
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-sm btn-primary w-100">Filtrar</button>
        </div>
        <div class="col-md-2">
            <a href="{{ route('orientacion.citas.admin') }}" class="btn btn-sm btn-secondary w-100">Limpiar</a>
        </div>
    </form>

    <table class="table table-sm table-bordered align-middle">
        <thead>
            <tr>
                <th>ID</th>
                <th>Estudiante</th>
                <th>Orientador</th>
                <th>Estado</th>
                <th>Motivo</th>
                <th>Fechas</th>
                <th>Historial</th>
            </tr>
        </thead>
        <tbody>
        @forelse($citas as $c)
            <tr>
                <td>{{ $c->id }}</td>
                <td>{{ $c->estudiante?->name }}</td>
                <td>{{ $c->orientador?->name ?? '—' }}</td>
                <td><span class="badge bg-dark">{{ $c->estado }}</span></td>
                <td style="max-width:180px; font-size:12px">{{ $c->motivo }}</td>
                <td style="font-size:12px">
                    Solicitada: {{ $c->fecha_solicitada ?? '—' }}<br>
                    Asignada: {{ $c->fecha_asignada ? ($c->fecha_asignada.' '.$c->hora_asignada) : '—' }}<br>
                    Próxima: {{ $c->fecha_proxima ?? '—' }}<br>
                    Cerrada: {{ $c->cerrada_at ?? '—' }}
                </td>
                <td><a class="btn btn-sm btn-outline-primary" target="_blank" href="{{ url('/orientacion/citas/'.$c->id+'/historial') }}">Ver</a></td>
            </tr>
        @empty
            <tr><td colspan="7" class="text-center text-muted">No hay citas que coincidan</td></tr>
        @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
        {{ $citas->links() }}
    </div>
</div>
@endsection