@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-body p-5 text-center">
            <h1 class="fw-bold mb-4">Panel Principal</h1>
            <p class="text-muted mb-5">
                Bienvenido al sistema. Desde aquí puedes acceder a las diferentes secciones según tus permisos.
            </p>

            <div class="d-flex justify-content-center flex-wrap gap-3">
                <!-- Botón para gestión de roles -->
                <a href="{{ route('roles.index') }}" class="btn btn-primary btn-lg px-5 py-3 rounded-3 shadow-sm">
                    Gestión de Roles
                </a>

                <!-- Botón para ver notas -->
                <a href="{{ route('notas.mostrar') }}" class="btn btn-success btn-lg px-5 py-3 rounded-3 shadow-sm">
                    Ver Notas
                </a>

                <!-- Botón para crear usuarios -->
                <a href="{{ route('register') }}" class="btn btn-warning btn-lg px-5 py-3 rounded-3 shadow-sm text-white">
                    Crear Usuario
                </a>

                <!-- Botón para docentes -->
                <a href="{{ route('docentes.index') }}" class="btn btn-info btn-lg px-5 py-3 rounded-3 shadow-sm text-white">
                    Gestión de Docentes
                </a>

                <!-- Botón para cerrar sesión -->
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-lg px-5 py-3 rounded-3 shadow-sm">
                        Cerrar Sesión
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
