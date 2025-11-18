@extends('layouts.app')

@section('title', 'Gestión de Grupos - Docentes')

@section('content')
<section class="page-section">
    <div class="page-header">
        <div class="page-title">
            <h1 class="h4 mb-0"><i class="fas fa-users me-2 text-primary"></i>Gestión de Grupos</h1>
            <p class="subtitle">Selecciona un grupo para administrar sus notas</p>
        </div>
        <div class="action-bar">
            <a href="{{ route('docentes.index') }}" class="btn-pro outline"><i class="fas fa-arrow-left me-1"></i>Volver</a>
        </div>
    </div>

    <div class="pro-card">
        <div class="pro-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h2 class="h6 mb-0"><i class="fas fa-list me-1"></i>Grupos asignados</h2>
            <span class="badge bg-primary">Total: {{ $grupos->count() }}</span>
        </div>
        <div class="pro-card-body">
            @if($grupos->count() > 0)
                <div class="row g-3">
                    @foreach($grupos as $grupo)
                        <div class="col-sm-6 col-lg-4">
                            <div class="pro-card mini h-100">
                                <div class="pro-card-body">
                                    <h6 class="mb-1 fw-semibold text-primary"><i class="fas fa-users me-1"></i>{{ $grupo->nombre }}</h6>
                                    <p class="text-muted small mb-2">Notas y calificaciones del grupo</p>
                                    <a href="{{ route('docentes.grupos.ver', $grupo->id) }}" class="btn-pro xs primary" title="Ver Grupo"><i class="fas fa-eye"></i> Ver</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state text-center py-5">
                    <i class="fas fa-users-slash fa-2x text-muted mb-3"></i>
                    <p class="text-muted mb-2">No hay grupos disponibles.</p>
                    <small class="text-muted">Contacta al administrador para crear grupos.</small>
                </div>
            @endif
        </div>
    </div>
</section>
@endsection

@section('scripts')
@endsection