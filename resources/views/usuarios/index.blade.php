@extends('layouts.app')

@section('title', 'Gestión de Usuarios')

@section('content')

<div class="page-section">
    <div class="page-header">
        <div>
            <h2 class="page-header-title"><i class="fas fa-users me-2 text-primary"></i>Gestión de Usuarios</h2>
            <p class="page-header-sub">Administra los usuarios del sistema en tiempo real</p>
        </div>
        <div class="action-bar">
            <a href="{{ route('register') }}" class="btn-pro"><i class="fas fa-user-plus"></i>Nuevo Usuario</a>
        </div>
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

            <div class="pro-card">
                <div class="pro-card-header">
                    <h5 class="m-0"><i class="fas fa-list me-2"></i>Listado</h5>
                </div>
                <div class="pro-table-wrapper">
                    @if($usuarios->count())
                        <table class="pro-table">
                            <thead>
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
                                                <div class="action-bar">
                                                <a href="{{ route('usuarios.editar', $usuario->id) }}" class="btn-pro btn-pro-outline">
                                                    <i class="fas fa-edit"></i>Editar
                                                </a>
                                                <form action="{{ route('usuarios.eliminar', $usuario->id) }}" 
                                                      method="POST" class="d-inline-block ms-1">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-pro" style="background:#c1121f" onclick="return confirm('¿Eliminar este usuario?')">
                                                        <i class="fas fa-trash"></i>Eliminar
                                                    </button>
                                                </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="empty-state"><i class="fas fa-users"></i><p>No hay usuarios registrados.</p></div>
                    @endif
                </div>
            </div>
    </div>
</div>

<!-- Estilos -->
<style>.alert{border-radius:12px;font-weight:500;}</style>

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
