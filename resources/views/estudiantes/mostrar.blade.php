@extends('layouts.app')

@section('title', 'Estudiantes - Colegio')

@section('content')
<div class="container-fluid mt-3">
    @php
        $usuario = Auth::user();
        $rol = App\Models\RolesModel::find($usuario->roles_id);
    @endphp
    <div class="px-4 py-4"><!-- contenido principal -->
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 text-dark">
                        <i class="fas fa-users me-2 text-primary"></i>Lista de Estudiantes
                    </h1>
                    <p class="text-muted mb-0">Estudiantes registrados en el sistema</p>
                </div>
                <div>
                    <span class="badge bg-primary fs-6 shadow-sm">
                        Total: {{ $MostrarEstudiante->count() }}
                    </span>
                </div>
            </div>

            <!-- Students List -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center rounded-top-4">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>Estudiantes Registrados
                    </h5>
                    <div class="d-flex">
                        <input type="text" class="form-control" placeholder="Buscar estudiante..." id="searchInput" style="max-width: 280px;">
                    </div>
                </div>
                <div class="card-body">
                    @if($MostrarEstudiante->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre Completo</th>
                                    <th>Email</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($MostrarEstudiante as $estudiante)
                                <tr>
                                    <td>{{ $estudiante->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary rounded-circle text-white d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                <i class="fas fa-user"></i>
                                            </div>
                                            <div>
                                                <strong>{{ $estudiante->name }}</strong>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $estudiante->email }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-outline-primary" title="Ver información">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            @if($rol && $rol->tienePermiso('gestionar_estudiantes'))
                                            <button class="btn btn-sm btn-outline-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-users fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">No hay estudiantes registrados</h4>
                        <p class="text-muted">No se encontraron estudiantes con rol de estudiante.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Logout Form -->
<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>

<!-- Estilos -->
<style>
    body {
        background: linear-gradient(135deg, #dce3ff 0%, #e6e0ff 100%);
        font-family: 'Poppins', sans-serif;
    }

    .main-content {
        min-height: 100vh;
    }

    .card {
        backdrop-filter: blur(12px);
        background: rgba(255, 255, 255, 0.7);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .navbar {
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .btn-outline-warning {
        color: #d18f00;
        border-color: #d18f00;
    }

    .btn-outline-warning:hover {
        background-color: #d18f00;
        color: #fff;
    }
</style>

@endsection

@section('scripts')
<script>
document.getElementById('searchInput').addEventListener('keyup', function() {
    const searchText = this.value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchText) ? '' : 'none';
    });
});
</script>
@endsection
