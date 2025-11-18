@extends('layouts.app')

@section('content')

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar gestionada por layout -->

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10">
            <div class="main-content p-4">
                <h1 class="mb-4">Usuarios Sin Rol</h1>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <!-- Tabla de usuarios sin rol -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Usuarios sin rol asignado</h5>
                    </div>
                    <div class="card-body">
                        @if($usuarios->count() > 0)
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Usuario</th>
                                    <th>Email</th>
                                    <th>Rol Actual</th>
                                    <th>Asignar Rol</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($usuarios as $usuario)
                                <tr>
                                    <td>
                                        <strong>{{ $usuario->name }}</strong>
                                    </td>
                                    <td>{{ $usuario->email }}</td>
                                    <td>
                                        <span class="badge bg-secondary">Sin rol</span>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-primary dropdown-toggle" type="button" 
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                Asignar Rol
                                            </button>
                                            <ul class="dropdown-menu">
                                                <!-- Roles del 3 al 8 -->
                                                @foreach($roles as $rol)
                                                <li>
                                                    <form action="{{ route('roles.asignar') }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="usuario_id" value="{{ $usuario->id }}">
                                                        <input type="hidden" name="rol_id" value="{{ $rol->id }}">
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="fas fa-user me-2"></i>{{ $rol->nombre }}
                                                        </button>
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
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">No hay usuarios sin rol</h4>
                            <p class="text-muted">Todos los usuarios tienen un rol asignado.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection