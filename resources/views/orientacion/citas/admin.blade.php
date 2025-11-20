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
                    {{-- Paso 1: reemplazo temporal para descartar bloque de fechas como origen --}}
                    @php
                        $debugData = [
                            'fecha_solicitada_type' => gettype($c->fecha_solicitada),
                            'fecha_asignada_type'   => gettype($c->fecha_asignada),
                            'hora_asignada_type'    => gettype($c->hora_asignada),
                            'fecha_proxima_type'    => gettype($c->fecha_proxima),
                            'cerrada_at_type'       => gettype($c->cerrada_at),
                        ];
                        echo '<div class="small text-muted">FD TYPES: '.e(json_encode($debugData)).'</div>';
                    @endphp
                    {{-- Mostrar solo IDs para verificar que el error persiste sin concatenaciones --}}
                    Solicitada: [omitida temporal]<br>
                    Asignada: [omitida temporal]<br>
                    Próxima: [omitida temporal]<br>
                    Cerrada: [omitida temporal]
                </td>
                    <td>
                        @php
                            $idType = gettype($c->id);
                            $historialUrl = url('/orientacion/citas').'/'.$c->id.'/historial';
                            echo '<div class="small text-muted">ID TYPE: '.e($idType).'</div>';
                        @endphp
                        <a class="btn btn-sm btn-outline-primary" target="_blank" href="{{ $historialUrl }}">Ver</a>
                    </td>
            </tr>
        @empty
            <tr><td colspan="7" class="text-center text-muted">No hay citas que coincidan</td></tr>
        @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
        @php
            // Paso 2: debug profundo de paginator
            $dbgCurrent = $citas->currentPage();
            $dbgPerPage = $citas->perPage();
            $dbgTotal = $citas->total();
            $dbgLast = $citas->lastPage();
            $dbgClass = get_class($citas);
            $rawPageParam = request()->query('page');
            echo '<div class="alert alert-info p-2 small">PG:['.e($dbgClass).'] current='.e($dbgCurrent).'('.gettype($dbgCurrent).') perPage='.e($dbgPerPage).'('.gettype($dbgPerPage).') total='.e($dbgTotal).'('.gettype($dbgTotal).') last='.e($dbgLast).' rawPageParam='.e(var_export($rawPageParam,true)).'</div>';
        @endphp
        @php
            try {
                if($citas->total() > $citas->perPage()) {
                    echo $citas->links();
                } else {
                    echo '<div class="small text-muted">Sin paginación (total <= perPage)</div>';
                }
            } catch(\Throwable $e) {
                echo '<pre class="text-danger">Paginator EXCEPTION: '.e($e->getMessage()).'\n'.e($e->getFile().':'.$e->getLine()).'</pre>';
            }
        @endphp
    </div>
</div>
@endsection