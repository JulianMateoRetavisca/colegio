@extends('layouts.app')

@section('title', 'Gestión de Usuarios')

@section('content')
<div class="dashboard-container py-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 p-0">
            @include('partials.sidebar') <!-- SIN CAMBIOS -->
        </div>

        <!-- Contenido principal -->
        <div class="col-md-9 col-lg-10 px-4">
            <div class="welcome-card p-4 mb-4">
                <h2 class="fw-bold text-dark mb-1">Gestión de Usuarios</h2>
                <p class="text-secondary mb-0">Administra los usuarios del sistema en tiempo real</p>
            </div>

            <!-- Banner de actualizaciones en vivo -->
            <div id="liveUpdateBanner" class="alert alert-info d-none text-center shadow-lg" 
                role="alert" style="position:fixed; left:50%; transform:translateX(-50%); top:12px; 
                z-index:1050; max-width:900px;">
                <span id="liveUpdateMessage">Se detectaron cambios realizados por otros usuarios.</span>
                <button id="liveUpdateReload" class="btn btn-sm btn-gradient ms-2">Actualizar</button>
            </div>

            <!-- Alertas -->
            @if(session('success'))
                <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger shadow-sm">{{ session('error') }}</div>
            @endif

            <!-- Botón nuevo usuario -->
            <div class="d-flex justify-content-end mb-3">
                <a href="{{ route('register') }}" class="btn btn-gradient px-4 py-2">
                    <i class="fas fa-user-plus me-2"></i>Nuevo Usuario
                </a>
            </div>

            <!-- Tabla de usuarios -->
            <div class="card user-card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    @if($usuarios->count())
                        <div class="table-responsive">
                            <table class="table align-middle table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Email</th>
                                        <th>Rol</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($usuarios as $usuario)
                                        <tr>
                                            <td class="text-dark fw-medium">{{ $usuario->name }}</td>
                                            <td class="text-secondary">{{ $usuario->email }}</td>
                                            <td class="text-secondary">{{ $usuario->rol ? $usuario->rol->nombre : 'Sin rol' }}</td>
                                            <td>
                                                <a href="{{ route('usuarios.editar', $usuario->id) }}" 
                                                   class="btn btn-sm btn-outline-secondary px-3">
                                                    <i class="fas fa-edit me-1"></i>Editar
                                                </a>
                                                <form action="{{ route('usuarios.eliminar', $usuario->id) }}" 
                                                      method="POST" class="d-inline-block ms-1">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                        class="btn btn-sm btn-danger px-3" 
                                                        onclick="return confirm('¿Eliminar este usuario?')">
                                                        <i class="fas fa-trash me-1"></i>Eliminar
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4 text-secondary fw-medium">
                            No hay usuarios registrados.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estilos -->
<style>
    body {
        background: linear-gradient(135deg, #dce3ff 0%, #e6e0ff 100%);
        font-family: 'Poppins', sans-serif;
    }

    .dashboard-container {
        min-height: 100vh;
        padding-left: 0px; /* Mantiene espacio de la sidebar */
    }

    .welcome-card {
        background: rgba(255, 255, 255, 0.6);
        backdrop-filter: blur(12px);
        border-radius: 18px;
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .user-card {
        background: rgba(255, 255, 255, 0.65);
        border-radius: 18px;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .btn-gradient {
        background: linear-gradient(135deg, #6b73ff, #a06bff);
        border: none;
        color: white !important;
        border-radius: 12px;
        font-weight: 500;
        transition: 0.3s ease;
    }

    .btn-gradient:hover {
        opacity: 0.9;
        transform: translateY(-2px);
    }

    .btn-outline-secondary {
        background: transparent;
        border: 2px solid #a4a4a4;
        border-radius: 12px;
        color: #333;
        font-weight: 500;
    }

    .btn-outline-secondary:hover {
        background: rgba(164, 164, 164, 0.1);
    }

    .text-dark {
        color: #2b2b2b !important;
    }

    .text-secondary {
        color: #5a5a5a !important;
    }

    .table th {
        font-weight: 600;
    }

    .table td {
        vertical-align: middle;
    }

    .alert {
        border-radius: 12px;
        font-weight: 500;
    }
</style>

@endsection

@section('scripts')
<script>
(() => {
    const updatesUrl = "{{ route('usuarios.updates') }}";
    let last = @json($usuarios->max('updated_at') ? $usuarios->max('updated_at')->toIsoString() : now()->toIsoString());
    const banner = document.getElementById('liveUpdateBanner');
    const reloadBtn = document.getElementById('liveUpdateReload');

    function showBanner(count) {
        if (!banner) return;
        banner.classList.remove('d-none');
        document.getElementById('liveUpdateMessage').textContent = 
            `Se detectaron ${count} cambios por otros usuarios.`;
    }

    reloadBtn && reloadBtn.addEventListener('click', () => location.reload());

    async function poll() {
        try {
            const res = await fetch(updatesUrl + '?since=' + encodeURIComponent(last), {
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json' }
            });
            if (!res.ok) return;
            const data = await res.json();
            if (data && data.exito) {
                if (Array.isArray(data.datos) && data.datos.length > 0) {
                    showBanner(data.datos.length);
                }
                if (data.now) last = data.now;
            }
        } catch (e) {
            console.error('Polling error', e);
        }
    }

    setInterval(poll, 8000);
    setTimeout(poll, 2000);
})();
</script>
@endsection
