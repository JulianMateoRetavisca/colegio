@extends('layouts.app')

@section('content')
@php
    $usuario = Auth::user();
    $rol = App\Models\RolesModel::find($usuario->roles_id);
@endphp

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 p-0">
            @include('partials.sidebar')
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10">
            <div class="main-content p-4">
                <h1 class="mb-4">Lista de Roles</h1>

                <!-- Banner de actualizaciones en vivo para roles -->
                <div id="rolesLiveBanner" class="alert alert-info d-none text-center" role="alert" style="position:fixed; left:50%; transform:translateX(-50%); top:12px; z-index:1050; max-width:900px;">
                    <span id="rolesLiveMsg">Se detectaron cambios en roles por otros usuarios.</span>
                    <button id="rolesLiveReload" class="btn btn-sm btn-primary ms-2">Actualizar</button>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <!-- Botones principales -->
                <div class="d-flex justify-content-end gap-2 mb-3">
                    <a href="{{ route('roles.crear') }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-2"></i>Crear nuevo rol
                    </a>
                    <a href="{{ route('sin') }}" class="btn btn-secondary">
                        <i class="fas fa-user-slash me-2"></i>Ver usuarios sin rol
                    </a>
                </div>

                <!-- Tabla de roles -->
                <table class="table table-bordered align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Usuarios asignados</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $rol)
                            <tr>
                                <td>{{ $rol->nombre }}</td>
                                <td>{{ $rol->descripcion }}</td>
                                <td>{{ $rol->usuarios_count }}</td>
                                <td>
                                    <a href="{{ route('roles.mostrar', $rol->id) }}" class="btn btn-info btn-sm">Ver</a>
                                    <a href="{{ route('roles.editar', $rol->id) }}" class="btn btn-warning btn-sm">Editar</a>
                                    <form action="{{ route('roles.eliminar', $rol->id) }}" method="POST" class="d-inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que deseas eliminar este rol?')">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div> 
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