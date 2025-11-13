@extends('layouts.app')

@section('content')
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('dashboard') }}">
            <i class="fas fa-book me-2 text-primary" style="opacity:0.9;"></i>Colegio
        </a>    
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user me-1"></i>{{ Auth::user()->name }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-user-cog me-1"></i>Perfil
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-cog me-1"></i>Configuración
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('logout') }}" 
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-1"></i>Cerrar Sesión
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 p-0">
            @include('partials.sidebar')
        </div>
        <div class="col-md-9 col-lg-10">
            <div class="main-content p-4">
                <h1 class="mb-4">Editar Usuario</h1>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="{{ route('usuarios.actualizar', $usuario->id) }}">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label class="form-label">Nombre</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $usuario->name) }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $usuario->email) }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Rol</label>
                                <select name="roles_id" class="form-select">
                                    <option value="">-- Sin rol --</option>
                                    @foreach($roles as $rol)
                                        <option value="{{ $rol->id }}" @if(old('roles_id', $usuario->roles_id) == $rol->id) selected @endif>{{ $rol->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nueva contraseña (opcional)</label>
                                <input type="password" name="password" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Confirmar contraseña</label>
                                <input type="password" name="password_confirmation" class="form-control">
                            </div>

                            <div class="d-flex justify-content-end">
                                <a href="{{ route('usuarios.index') }}" class="btn btn-secondary me-2">Cancelar</a>
                                <button class="btn btn-primary">Guardar cambios</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
