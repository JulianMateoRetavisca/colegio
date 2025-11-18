@extends('layouts.app')

@section('title', 'Gestión de Grupos - Docentes')

@section('content')
<div class="container py-4 mt-3">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-users"></i> Gestión de Grupos</h2>
                <a href="{{ route('docentes.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-list"></i> Seleccionar Grupo para Gestionar Notas
                    </h5>
                </div>
                <div class="card-body">
                    @if($grupos->count() > 0)
                        <div class="row">
                            @foreach($grupos as $grupo)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card h-100 border-left-primary">
                                        <div class="card-body">
                                            <h6 class="card-title text-primary">
                                                <i class="fas fa-users"></i> {{ $grupo->nombre }}
                                            </h6>
                                            <p class="card-text text-muted">
                                                Gestionar notas y calificaciones del grupo
                                            </p>
                                            <a href="{{ route('docentes.grupos.ver', $grupo->id) }}" 
                                               class="btn btn-primary btn-sm">
                                                <i class="fas fa-eye"></i> Ver Grupo
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No hay grupos disponibles</h5>
                            <p class="text-muted">Contacta al administrador para crear grupos.</p>
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
.border-left-primary {
    border-left: 4px solid #007bff !important;
}
.card-title {
    font-weight: 600;
}
</style>
@endsection