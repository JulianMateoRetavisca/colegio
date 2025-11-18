@extends('layouts.app')

@section('content')
<section class="page-section">
    <div class="page-header">
        <div class="page-title">
            <h1 class="h4 mb-0"><i class="fas fa-user-edit me-2 text-primary"></i>Editar Usuario</h1>
            <p class="subtitle">Modificar datos y rol del usuario</p>
        </div>
        <div class="action-bar"><a href="{{ route('usuarios.index') }}" class="btn-pro outline"><i class="fas fa-arrow-left me-1"></i>Volver</a></div>
    </div>
    <x-form-card title="Información del Usuario">
        <form method="POST" action="{{ route('usuarios.actualizar', $usuario->id) }}" class="row g-3">
            @csrf
            @method('PUT')
            @if($errors->any())
                <div class="col-12">
                    <div class="alert alert-danger mb-0">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
            <div class="col-md-6">
                <label class="form-label">Nombre</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $usuario->name) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $usuario->email) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Rol</label>
                <select name="roles_id" class="form-select">
                    <option value="">-- Sin rol --</option>
                    @foreach($roles as $rol)
                        <option value="{{ $rol->id }}" @selected(old('roles_id', $usuario->roles_id) == $rol->id)>{{ $rol->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Nueva contraseña (opcional)</label>
                <input type="password" name="password" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">Confirmar contraseña</label>
                <input type="password" name="password_confirmation" class="form-control">
            </div>
            <div class="col-12 d-flex justify-content-end gap-2 mt-2">
                <a href="{{ route('usuarios.index') }}" class="btn-pro outline">Cancelar</a>
                <button class="btn-pro primary" type="submit"><i class="fas fa-save me-1"></i>Guardar cambios</button>
            </div>
        </form>
    </x-form-card>
</section>
@endsection
