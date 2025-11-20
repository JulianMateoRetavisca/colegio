@extends('layouts.app')

@section('title','Resumen de Notas')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-0"><i class="fas fa-chart-bar me-2"></i>Resumen de Notas por Período</h2>
            <small class="text-muted">Selecciona un grupo y una materia para ver las notas cargadas en cada período.</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('docentes.grupos') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i>Volver a Grupos</a>
        </div>
    </div>

    <form method="GET" action="{{ route('docentes.notas.resumen') }}" class="row g-3 mb-3">
        <div class="col-md-4">
            <label class="form-label">Grupo</label>
            <select name="grupo" class="form-select" required>
                <option value="">-- Seleccione --</option>
                @foreach($grupos as $g)
                    <option value="{{ $g->id }}" {{ $grupoSeleccionado == $g->id ? 'selected' : '' }}>{{ $g->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Materia</label>
            <select name="materia" class="form-select" required>
                <option value="">-- Seleccione --</option>
                @foreach($materias as $m)
                    <option value="{{ $m->id }}" {{ $materiaSeleccionada == $m->id ? 'selected' : '' }}>{{ $m->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary"><i class="fas fa-search me-1"></i>Ver Resumen</button>
        </div>
    </form>

    @if($grupoSeleccionado && $materiaSeleccionada)
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <span><i class="fas fa-book me-1"></i>Notas de {{ optional($materias->firstWhere('id',$materiaSeleccionada))->nombre }} - {{ optional($grupos->firstWhere('id',$grupoSeleccionado))->nombre }}</span>
                <small class="text-white-50">Períodos 1 a 4</small>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0 align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Estudiante</th>
                                <th>P1</th>
                                <th>P2</th>
                                <th>P3</th>
                                <th>P4</th>
                                <th>Promedio</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($estudiantes as $i => $est)
                                @php
                                    $sum = 0; $count = 0; $vals = [];
                                    foreach($periodos as $p){
                                        $registro = $mapaNotas[$est->id][$p] ?? null;
                                        $v = $registro['nota'] ?? null;
                                        $vals[$p] = $registro; // guardamos arreglo completo para colores/estado
                                        if($v !== null){ $sum += (float)$v; $count++; }
                                    }
                                    $prom = $count>0 ? number_format($sum/$count,2) : '—';
                                @endphp
                                <tr>
                                    <td>{{ $i+1 }}</td>
                                    <td><strong>{{ $est->name }}</strong><br><small class="text-muted">{{ $est->email }}</small></td>
                                    @foreach($periodos as $p)
                                        @php $registro = $vals[$p]; $v = $registro['nota'] ?? null; $bloq = $registro['bloqueado'] ?? false; @endphp
                                        <td>
                                            @if($v !== null)
                                                <span class="badge {{ $bloq ? 'bg-secondary' : 'bg-info' }}" title="{{ $bloq ? 'Bloqueado' : 'Nota registrada' }}">{{ $v }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    <td class="fw-semibold">{{ $prom }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center py-4"><em>No hay estudiantes en este grupo.</em></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-info"><i class="fas fa-info-circle me-1"></i> Selecciona un grupo y materia para ver el resumen.</div>
    @endif
</div>
@endsection
