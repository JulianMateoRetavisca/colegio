@extends('layouts.app')

@section('title','Editar Grupo')

@section('content')
<section class="page-section">
    <div class="page-header">
        <div class="page-title">
            <h1 class="h4 mb-0"><i class="fas fa-edit me-2 text-primary"></i>Editar Grupo</h1>
            <p class="subtitle">Actualizar información del grupo</p>
        </div>
        <div class="action-bar"><a href="{{ route('grupos.index') }}" class="btn-pro outline"><i class="fas fa-arrow-left me-1"></i>Volver</a></div>
    </div>
    <x-form-card title="Información">
        <form method="POST" action="{{ route('grupos.actualizar', $grupo->id) }}" class="row g-3">
            @csrf
            @method('PUT')
            <div class="col-12">
                <label class="form-label">Nombre</label>
                <input class="form-control" name="nombre" value="{{ old('nombre', $grupo->nombre) }}" required />
            </div>
            <div class="col-12 d-flex justify-content-end gap-2">
                <a href="{{ route('grupos.index') }}" class="btn-pro outline">Cancelar</a>
                <button class="btn-pro primary" type="submit"><i class="fas fa-save me-1"></i>Guardar</button>
            </div>
        </form>
    </x-form-card>
</section>
@endsection
