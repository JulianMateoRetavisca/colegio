@extends('layouts.app')

@section('title', 'Lista de Roles - Colegio')

@section('content')
@php
    $usuario = Auth::user();
    $rol = App\Models\RolesModel::find($usuario->roles_id);
@endphp

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
                    <ul class="dropdown-menu dropdown-menu-end">
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
    <div class="row g-0">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 p-0">
            @include('partials.sidebar')
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 px-4 py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 text-dark">
                        <i class="fas fa-user-shield me-2 text-primary"></i>Lista de Roles
                    </h1>
                    <p class="text-muted mb-0">Administración de roles del sistema</p>
                </div>
            </div>

            <!-- Banner de actualizaciones en vivo -->
            <div id="rolesLiveBanner" 
                 class="alert alert-info d-none text-center shadow-sm border-0 rounded-3"
                 role="alert"
                 style="position:fixed; left:50%; transform:translateX(-50%); top:12px; z-index:1050; max-width:900px;">
                <span id="rolesLiveMsg">Se detectaron cambios en roles por otros usuarios.</span>
                <button id="rolesLiveReload" class="btn btn-sm btn-primary ms-2 px-3">Actualizar</button>
            </div>

            @if(session('success'))
                <div class="alert alert-success shadow-sm border-0 rounded-3">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger shadow-sm border-0 rounded-3">{{ session('error') }}</div>
            @endif

            <!-- Botones principales -->
            <div class="d-flex justify-content-end gap-2 mb-4">
                <a href="{{ route('roles.crear') }}" class="btn btn-primary px-3">
                    <i class="fas fa-plus-circle me-2"></i>Crear nuevo rol
                </a>
                <a href="{{ route('sin') }}" class="btn btn-secondary px-3">
                    <i class="fas fa-user-slash me-2"></i>Ver usuarios sin rol
                </a>
            </div>

            <!-- Tabla de roles -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Usuarios asignados</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($roles as $rol)
                                <tr>
                                    <td><strong>{{ $rol->nombre }}</strong></td>
                                    <td>{{ $rol->descripcion }}</td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary px-3 py-2">
                                            {{ $rol->usuarios_count }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="{{ route('roles.mostrar', $rol->id) }}" class="btn btn-sm btn-outline-info" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('roles.editar', $rol->id) }}" class="btn btn-sm btn-outline-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('roles.eliminar', $rol->id) }}" method="POST" class="d-inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar" onclick="return confirm('¿Seguro que deseas eliminar este rol?')">
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
                </div>
            </div>

        </div>
    </div>
</div>

<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>

<!-- Estilos -->
<style>
body {
    background: linear-gradient(135deg, #dce3ff 0%, #e6e0ff 100%);
    font-family: 'Poppins', sans-serif;
}

.navbar {
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.card {
    backdrop-filter: blur(12px);
    background: rgba(255, 255, 255, 0.8);
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.table > thead > tr {
    background-color: rgba(240, 240, 255, 0.8);
}

.btn-outline-warning {
    color: #d18f00;
    border-color: #d18f00;
}
.btn-outline-warning:hover {
    background-color: #d18f00;
    color: #fff;
}

.btn-primary {
    background-color: #6a74e1;
    border-color: #6a74e1;
}
.btn-primary:hover {
    background-color: #5b65c5;
    border-color: #5b65c5;
}

.btn-secondary {
    background-color: #b6b7d8;
    border-color: #b6b7d8;
    color: #2e2e2e;
}
.btn-secondary:hover {
    background-color: #a2a3c7;
    border-color: #a2a3c7;
}
</style>

@endsection

@section('scripts')
<script>
(() => {
    const updatesUrl = "{{ route('roles.updates') }}";
    let last = @json($roles->max('updated_at') ? $roles->max('updated_at')->toIsoString() : now()->toIsoString());
    const banner = document.getElementById('rolesLiveBanner');
    const reloadBtn = document.getElementById('rolesLiveReload');

    function showBanner(count) {
        if (!banner) return;
        banner.classList.remove('d-none');
        document.getElementById('rolesLiveMsg').textContent = `Se detectaron ${count} cambios en roles.`;
    }
    reloadBtn && reloadBtn.addEventListener('click', () => location.reload());

    async function poll() {
        try {
            const res = await fetch(updatesUrl + '?since=' + encodeURIComponent(last), { credentials: 'same-origin', headers: { 'Accept': 'application/json' } });
            if (!res.ok) return;
            const data = await res.json();
            if (data && data.exito) {
                if (Array.isArray(data.datos) && data.datos.length > 0) {
                    showBanner(data.datos.length);
                }
                if (data.now) last = data.now;
            }
        } catch (e) {
            console.error('Polling roles error', e);
        }
    }

    setInterval(poll, 10000);
    setTimeout(poll, 1500);
})();
</script>
@endsection
