@extends('layouts.app')

@section('content')
<section class="page-section">
    <div class="page-header">
        <div class="page-title">
            <h1 class="h4 mb-0"><i class="fas fa-users me-2 text-primary"></i>Usuarios Sin Rol</h1>
            <p class="subtitle">Asignación de roles pendientes</p>
        </div>
        <div class="action-bar"><a href="{{ route('usuarios.index') }}" class="btn-pro outline"><i class="fas fa-arrow-left me-1"></i>Todos los usuarios</a></div>
    </div>
    @if(session('success'))<div class="alert alert-success alert-sm">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-danger alert-sm">{{ session('error') }}</div>@endif
    <div class="pro-card">
        <div class="pro-card-header"><h2 class="h6 mb-0">Listado</h2></div>
        <div class="pro-table-wrapper">
            @if($usuarios->count() > 0)
            <table class="pro-table">
                <thead><tr><th>Usuario</th><th>Email</th><th>Rol Actual</th><th>Asignar Rol</th></tr></thead>
                <tbody>
                @foreach($usuarios as $usuario)
                    <tr>
                        <td class="fw-semibold">{{ $usuario->name }}</td>
                        <td>{{ $usuario->email }}</td>
                        <td><span class="badge bg-secondary">Sin rol</span></td>
                        <td>
                            <div class="dropdown">
                                <button class="btn-pro xs primary dropdown-toggle" type="button" data-bs-toggle="dropdown">Asignar</button>
                                <ul class="dropdown-menu">
                                    @foreach($roles as $rol)
                                    <li>
                                        <form action="{{ route('roles.asignar') }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="usuario_id" value="{{ $usuario->id }}">
                                            <input type="hidden" name="rol_id" value="{{ $rol->id }}">
                                            <button type="submit" class="dropdown-item"><i class="fas fa-user me-1"></i>{{ $rol->nombre }}</button>
                                        </form>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            @else
            <div class="empty-state py-5 text-center">
                <i class="fas fa-users fa-2x text-muted mb-3"></i>
                <p class="text-muted mb-0">No hay usuarios sin rol.</p>
            </div>
            @endif
        </div>
    </div>
</section>
@endsection