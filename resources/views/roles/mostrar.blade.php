@extends('layouts.app')

@section('title', 'Detalles del Rol')

@section('content')
<section class="page-section">
    <div class="page-header">
        <div class="page-title">
            <h1 class="h4 mb-0"><i class="fas fa-user-shield me-2 text-primary"></i>Rol: {{ $rol->nombre }}</h1>
            <p class="subtitle">Información de permisos y usuarios asignados</p>
        </div>
        <div class="action-bar">
            <a href="{{ route('roles.index') }}" class="btn-pro outline"><i class="fas fa-arrow-left me-1"></i>Volver</a>
            <a href="{{ route('roles.editar', $rol->id) }}" class="btn-pro info"><i class="fas fa-edit me-1"></i>Editar</a>
        </div>
    </div>

    <div class="pro-card mb-3">
        <div class="pro-card-header"><h2 class="h6 mb-0">Descripción</h2></div>
        <div class="pro-card-body">
            <p class="mb-0 text-muted">{{ $rol->descripcion }}</p>
        </div>
    </div>

    <div class="pro-card mb-4">
        <div class="pro-card-header">
            <h2 class="h6 mb-0"><i class="fas fa-key me-2 text-primary"></i>Permisos por módulo</h2>
        </div>
        <div class="pro-card-body">
            <div class="row g-3">
                @foreach($gruposPermisos as $modulo => $permisos)
                    @php $permisosSeleccionados = array_intersect(array_keys($permisos), $rol->permisos ?? []); @endphp
                    <div class="col-lg-6">
                        <div class="pro-card mini h-100">
                            <div class="pro-card-body">
                                <h6 class="mb-2 fw-semibold">{{ $modulo }}
                                    <small class="text-muted">({{ count($permisosSeleccionados) }}/{{ count($permisos) }})</small>
                                </h6>
                                @if(count($permisosSeleccionados))
                                    <ul class="mb-0 ps-3">
                                        @foreach($permisosSeleccionados as $permiso)
                                            <li class="text-muted">{{ $permisosDisponibles[$permiso] ?? $permiso }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-muted small">Sin permisos asignados</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="pro-card">
        <div class="pro-card-header">
            <h2 class="h6 mb-0"><i class="fas fa-users me-2 text-primary"></i>Usuarios con este rol</h2>
        </div>
        <div class="pro-table-wrapper">
            <table class="pro-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($usuarios as $u)
                        <tr>
                            <td>{{ $u->name }}</td>
                            <td class="text-muted">{{ $u->email }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="pro-card-footer">
            {{ $usuarios->links() }}
        </div>
    </div>
</section>
@endsection
