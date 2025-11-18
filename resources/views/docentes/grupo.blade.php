@extends('layouts.app')

@section('title', 'Grupo: ' . $grupo->nombre)

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-users"></i> {{ $grupo->nombre }}</h2>
                <a href="{{ route('docentes.grupos') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver a Grupos
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Información del Grupo -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle"></i> Información del Grupo
                    </h5>
                </div>
                <div class="card-body">
                    <p><strong>Nombre:</strong> {{ $grupo->nombre }}</p>
                    <p><strong>Total Estudiantes:</strong> {{ $estudiantes->count() }}</p>
                    <hr>
                    <h6>Estudiantes:</h6>
                    @if($estudiantes->count() > 0)
                        <ul class="list-unstyled">
                            @foreach($estudiantes as $estudiante)
                                <li class="mb-1">
                                    <i class="fas fa-user text-muted"></i> {{ $estudiante->name }}
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">No hay estudiantes asignados a este grupo.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Materias Disponibles -->
        <div class="col-md-8 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-book"></i> Gestión de Notas por Materia
                    </h5>
                </div>
                <div class="card-body">
                    @if($materias->count() > 0)
                        <div class="row">
                            @foreach($materias as $materia)
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100 border-left-success">
                                        <div class="card-body">
                                            <h6 class="card-title text-success">
                                                <i class="fas fa-book-open"></i> {{ $materia->nombre }}
                                            </h6>
                                            <p class="card-text text-muted">
                                                {{ $materia->descripcion ?? 'Ver y gestionar notas de esta materia' }}
                                            </p>
                                            <a href="{{ route('docentes.grupos.notas', [$grupo->id, $materia->id]) }}" 
                                               class="btn btn-success btn-sm">
                                                <i class="fas fa-edit"></i> Gestionar Notas
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No hay materias disponibles</h5>
                            <p class="text-muted">Contacta al administrador para configurar las materias.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<style>
.border-left-success {
    border-left: 4px solid #28a745 !important;
}
.card-title {
    font-weight: 600;
}
</style>
@endsection