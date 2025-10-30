@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 p-0">
            @include('partials.sidebar')
        </div>
        <div class="col-md-9 col-lg-10">
            <div class="main-content p-4">
                <h1 class="mb-4">Gestión de Usuarios</h1>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="d-flex justify-content-end mb-3">
                    <a href="{{ route('register') }}" class="btn btn-primary">Nuevo Usuario</a>
                </div>

                <div class="card">
                    <div class="card-body">
                        @if($usuarios->count())
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
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
                                                <td>{{ $usuario->name }}</td>
                                                <td>{{ $usuario->email }}</td>
                                                <td>{{ $usuario->rol ? $usuario->rol->nombre : 'Sin rol' }}</td>
                                                <td>
                                                    <a href="{{ route('usuarios.editar', $usuario->id) }}" class="btn btn-sm btn-secondary">Editar</a>
                                                    <form action="{{ route('usuarios.eliminar', $usuario->id) }}" method="POST" class="d-inline-block ms-1">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este usuario?')">Eliminar</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4 text-muted">No hay usuarios registrados.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
