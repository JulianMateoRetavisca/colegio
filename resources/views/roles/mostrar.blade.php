@extends('layouts.app')

@section('title', 'Detalles del Rol')

@section('content')
@php
    $usuario = Auth::user();
    $rolUsuario = App\Models\RolesModel::find($usuario->roles_id);
@endphp

<div class="dashboard-container py-4">
    <!-- Sidebar -->
    <div class="col-md-3 col-lg-2 p-0">
        @include('partials.sidebar') 
    </div>

    <div class="container-fluid">
        <!-- Encabezado -->
        <div class="welcome-card p-4 mb-4">
            <h2 class="fw-bold text-dark mb-1">Rol: <span class="text-primary">{{ $rol->nombre }}</span></h2>
            <p class="text-secondary mb-0">Información detallada de permisos y usuarios asignados</p>
        </div>

        <!-- Descripción -->
        <div class="action-card p-4 mb-4">
            <label class="form-label fw-semibold">Descripción:</label>
            <p class="text-secondary mb-0">{{ $rol->descripcion }}</p>
        </div>

        <!-- Permisos -->
        <div class="recent-card p-4 mb-5">
            <h5 class="fw-semibold text-dark mb-3"><i class="fas fa-user-shield me-2 text-primary"></i>Permisos por módulo</h5>
            <div class="row g-4">
                @foreach($gruposPermisos as $modulo => $permisos)
                    @php
                        $permisosSeleccionados = array_intersect(array_keys($permisos), $rol->permisos ?? []);
                    @endphp
                    <div class="col-lg-6">
                        <div class="stat-card p-3 h-100">
                            <h6 class="fw-bold text-dark">
                                {{ $modulo }}
                                <span class="text-secondary small">
                                    ({{ count($permisosSeleccionados) }}/{{ count($permisos) }})
                                </span>
                            </h6>

                            @if(count($permisosSeleccionados))
                                <ul class="mb-0 ps-3">
                                    @foreach($permisosSeleccionados as $permiso)
                                        <li class="text-secondary">{{ $permisosDisponibles[$permiso] ?? $permiso }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <span class="text-secondary small">Sin permisos asignados</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Usuarios asignados -->
        <div class="recent-card p-4 mb-4">
            <h5 class="fw-semibold text-dark mb-3"><i class="fas fa-users me-2 text-primary"></i>Usuarios con este rol</h5>

            <div class="table-responsive rounded">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nombre</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($usuarios as $u)
                            <tr>
                                <td class="text-dark">{{ $u->name }}</td>
                                <td class="text-secondary">{{ $u->email }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $usuarios->links() }}
            </div>
        </div>

        <!-- Acciones -->
        <div class="d-flex gap-3">
            <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary px-4">
                <i class="fas fa-arrow-left me-2"></i>Volver
            </a>
            <a href="{{ route('roles.editar', $rol->id) }}" class="btn btn-gradient px-4">
                <i class="fas fa-edit me-2"></i>Editar Rol
            </a>
        </div>
    </div>
</div>

{{-- ===================== ESTILOS ===================== --}}
<style>
    body {
        background: linear-gradient(135deg, #dce3ff 0%, #e6e0ff 100%);
        font-family: 'Poppins', sans-serif;
    }

    .dashboard-container {
        min-height: 100vh;
        padding-left: 260px; /* sidebar */
    }

    .welcome-card,
    .action-card,
    .recent-card,
    .stat-card {
        background: rgba(255, 255, 255, 0.65);
        border-radius: 18px;
        box-shadow: 0 6px 15px rgba(0,0,0,0.08);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.35);
    }

    .stat-card:hover {
        transform: translateY(-3px);
        transition: 0.3s ease;
    }

    .btn-gradient {
        background: linear-gradient(135deg, #6b73ff, #a06bff);
        color: #fff !important;
        border: none;
        border-radius: 12px;
        font-weight: 500;
    }

    .btn-outline-secondary {
        border-radius: 12px;
        font-weight: 500;
        color: #2b2b2b !important;
        border: 2px solid #b1b1b1;
    }

    .text-dark {
        color: #2b2b2b !important;
    }

    .text-secondary {
        color: #5a5a5a !important;
    }
</style>
@endsection
