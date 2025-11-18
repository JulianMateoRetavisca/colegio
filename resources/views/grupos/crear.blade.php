@extends('layouts.app')

@section('title','Crear Grupo')

@section('content')
<section class="page-section">
    <div class="page-header">
        <div class="page-title">
            <h1 class="h4 mb-0"><i class="fas fa-layer-group me-2 text-primary"></i>Crear Grupo</h1>
            <p class="subtitle">Registrar un nuevo grupo académico</p>
        </div>
        <div class="action-bar"><a href="{{ route('grupos.index') }}" class="btn-pro outline"><i class="fas fa-arrow-left me-1"></i>Volver</a></div>
    </div>
    <x-form-card title="Datos del Grupo">
        <form method="POST" action="{{ route('grupos.guardar') }}" class="row g-3">
            @csrf
            <div class="col-12">
                <label class="form-label">Nombre</label>
                <input class="form-control" name="nombre" value="{{ old('nombre') }}" required />
            </div>
            <div class="col-12 d-flex justify-content-end gap-2">
                <a href="{{ route('grupos.index') }}" class="btn-pro outline">Cancelar</a>
                <button class="btn-pro primary" type="submit"><i class="fas fa-save me-1"></i>Crear</button>
            </div>
        </form>
    </x-form-card>
</section>
@endsection
