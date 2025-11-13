@extends('layouts.app')

@section('title', 'Asignar Materias')

@section('content')
@php
    $usuario = Auth::user();
@endphp

<div class="container-fluid">
    <div class="row g-0">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 p-0">
            @include('partials.sidebar')
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 px-4 py-4">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 text-dark">
                        <i class="fas fa-link me-2 text-primary"></i>Asignar Materias a Grupos
                    </h1>
                    <p class="text-muted mb-0">Vincula materias con grupos/cursos</p>
                </div>
                <div>
                    <a href="{{ route('materias.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Volver a Materias
                    </a>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-filter me-2"></i>Filtros
                    </h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Filtrar por Grupo</label>
                            <select id="grupoFilter" class="form-select">
                                <option value="">Todos los grupos</option>
                                @foreach($grupos as $grupo)
                                    <option value="{{ $grupo->id }}">{{ $grupo->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Filtrar por Materia</label>
                            <select id="materiaFilter" class="form-select">
                                <option value="">Todas las materias</option>
                                @foreach($materias as $materia)
                                    <option value="{{ $materia->id }}">{{ $materia->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assignment Matrix -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white rounded-top-4">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-table me-2"></i>Matriz de Asignación
                    </h5>
                    <small class="text-muted">Visualiza las materias asignadas a cada grupo</small>
                </div>
                <div class="card-body">
                    @if($grupos->count() > 0 && $materias->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Grupo / Materia</th>
                                    @foreach($materias as $materia)
                                        <th class="text-center materia-col" data-materia-id="{{ $materia->id }}">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="fas fa-book mb-1"></i>
                                                <small>{{ $materia->nombre }}</small>
                                            </div>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody id="assignmentTableBody">
                                @foreach($grupos as $grupo)
                                    <tr class="grupo-row" data-grupo-id="{{ $grupo->id }}">
                                        <td><strong>{{ $grupo->nombre }}</strong></td>
                                        @foreach($materias as $materia)
                                            @php
                                                // Verificar si este grupo tiene horarios con esta materia
                                                $tieneAsignacion = \App\Models\Horario::where('grupo_id', $grupo->id)
                                                    ->where('materia_id', $materia->id)
                                                    ->exists();
                                            @endphp
                                            <td class="text-center materia-col" data-materia-id="{{ $materia->id }}">
                                                @if($tieneAsignacion)
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check"></i> Asignada
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">
                                                        <i class="fas fa-minus"></i> No asignada
                                                    </span>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="alert alert-info mt-4 mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Nota:</strong> Para asignar una materia a un grupo, ve a 
                        <a href="{{ route('horarios.index') }}" class="alert-link">Horarios</a> 
                        y crea un horario seleccionando el grupo y la materia correspondiente.
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                        <p class="text-muted">
                            @if($grupos->count() === 0 && $materias->count() === 0)
                                No hay grupos ni materias registrados.
                            @elseif($grupos->count() === 0)
                                No hay grupos registrados.
                            @else
                                No hay materias registradas.
                            @endif
                        </p>
                        <div class="d-flex gap-2 justify-content-center">
                            @if($grupos->count() === 0)
                                <a href="{{ route('grupos.crear') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i>Crear Grupo
                                </a>
                            @endif
                            @if($materias->count() === 0)
                                <a href="{{ route('materias.crear') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i>Crear Materia
                                </a>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const grupoFilter = document.getElementById('grupoFilter');
    const materiaFilter = document.getElementById('materiaFilter');
    const tableBody = document.getElementById('assignmentTableBody');
    
    if (grupoFilter && materiaFilter && tableBody) {
        // Filtro por grupo
        grupoFilter.addEventListener('change', function() {
            const selectedGrupo = this.value;
            const rows = tableBody.querySelectorAll('.grupo-row');
            
            rows.forEach(row => {
                if (selectedGrupo === '' || row.dataset.grupoId === selectedGrupo) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
        
        // Filtro por materia
        materiaFilter.addEventListener('change', function() {
            const selectedMateria = this.value;
            const materiaColumns = document.querySelectorAll('.materia-col');
            
            materiaColumns.forEach(col => {
                if (selectedMateria === '' || col.dataset.materiaId === selectedMateria) {
                    col.style.display = '';
                } else {
                    col.style.display = 'none';
                }
            });
        });
    }
});
</script>

<style>
.table th, .table td {
    vertical-align: middle;
}

.materia-col {
    min-width: 120px;
}
</style>
@endsection
