@extends('layouts.app')

@section('title', 'Materias - Colegio')

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
                        <i class="fas fa-book me-2 text-primary"></i>Lista de Materias
                    </h1>
                    <p class="text-muted mb-0">Materias registradas en el sistema</p>
                </div>
                <div>
                    <a href="{{ route('materias.crear') }}" class="btn btn-primary shadow-sm me-2">
                        <i class="fas fa-plus me-1"></i>Nueva Materia
                    </a>
                    <span class="badge bg-primary fs-6 shadow-sm">
                        Total: {{ $materias->count() }}
                    </span>
                </div>
            </div>

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Materias List -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center rounded-top-4">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>Materias Registradas
                    </h5>
                    <div class="d-flex">
                        <input type="text" class="form-control" placeholder="Buscar materia..." id="searchInput" style="max-width: 280px;">
                    </div>
                </div>
                <div class="card-body">
                    @if($materias->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Horarios</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="materiasTableBody">
                                @foreach($materias as $materia)
                                <tr>
                                    <td>{{ $materia->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle bg-primary text-white me-2">
                                                <i class="fas fa-book"></i>
                                            </div>
                                            <strong>{{ $materia->nombre }}</strong>
                                        </div>
                                    </td>
                                    <td>{{ Str::limit($materia->descripcion ?? 'Sin descripción', 50) }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $materia->horarios()->count() }} horarios</span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('materias.editar', $materia) }}" class="btn btn-outline-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('materias.eliminar', $materia) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar esta materia?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-book fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No hay materias registradas.</p>
                        <a href="{{ route('materias.crear') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>Crear Primera Materia
                        </a>
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
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    flex-shrink: 0;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Búsqueda en tiempo real
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.getElementById('materiasTableBody');
    
    if (searchInput && tableBody) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = tableBody.getElementsByTagName('tr');
            
            Array.from(rows).forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    }
});
</script>
@endsection
