@extends('layouts.app')

@section('title', 'Lista de Roles - Colegio')

@section('content')
@php
    $usuario = Auth::user();
    $rol = App\Models\RolesModel::find($usuario->roles_id);
@endphp


<div class="page-section">
    <div class="page-header">
        <div>
            <h2 class="page-header-title"><i class="fas fa-user-shield me-2 text-primary"></i>Lista de Roles</h2>
            <p class="page-header-sub">Administración de roles del sistema</p>
        </div>
        <div class="action-bar">
            <a href="{{ route('roles.crear') }}" class="btn-pro primary"><i class="fas fa-plus-circle"></i>Nuevo rol</a>
            <a href="{{ route('sin') }}" class="btn-pro outline"><i class="fas fa-user-slash"></i>Sin rol</a>
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

            <div class="pro-card">
                <div class="pro-card-header">
                    <h5 class="m-0"><i class="fas fa-layer-group me-2"></i>Roles del Sistema</h5>
                </div>
                <div class="pro-table-wrapper">
                    <table class="pro-table">
                        <thead>
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
                                        <span class="badge-soft">
                                            {{ $rol->usuarios_count }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="action-bar justify-content-center">
                                            <a href="{{ route('roles.mostrar', $rol->id) }}" class="btn-pro outline" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('roles.editar', $rol->id) }}" class="btn-pro info" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('roles.eliminar', $rol->id) }}" method="POST" class="d-inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-pro danger" title="Eliminar" onclick="return confirm('¿Seguro que deseas eliminar este rol?')">
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

{{-- Form moved to navbar partial --}}

<!-- Estilos específicos (si se necesita) -->

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
