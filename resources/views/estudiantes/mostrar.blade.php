@extends('layouts.app')

@section('title', 'Estudiantes - Colegio')

@section('content')
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('dashboard') }}">
            <i class="fas fa-book me-2 text-primary" style="opacity:0.9;"></i>Colegio
        </a>    
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user me-1"></i>{{ Auth::user()->name }}
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-user-cog me-1"></i>Perfil
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-cog me-1"></i>Configuración
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('logout') }}" 
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-1"></i>Cerrar Sesión
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container-fluid">
    @php
        $usuario = Auth::user();
        $rol = App\Models\RolesModel::find($usuario->roles_id);
    @endphp
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 p-0">
            @include('partials.sidebar')
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10">
            <div class="main-content p-4">
                <!-- Header Section -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h1 class="h3 text-dark">
                                    <i class="fas fa-users me-2 text-primary"></i>Lista de Estudiantes
                                </h1>
                                <p class="text-muted">Estudiantes registrados en el sistema</p>
                            </div>
                            <div>
                                <span class="badge bg-primary fs-6">
                                    Total: {{ $MostrarEstudiante->count() }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Students List -->
                <div class="row">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-list me-2"></i>Estudiantes Registrados
                                </h5>
                                <div class="d-flex">
                                    <input type="text" class="form-control me-2" placeholder="Buscar estudiante..." id="searchInput">
                                </div>
                            </div>
                            <div class="card-body">
                                @if($MostrarEstudiante->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
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
        </div>
    </div>
</div>

<!-- Logout Form -->
<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>

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