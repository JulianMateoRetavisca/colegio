@extends('layouts.app')

@section('title', 'Editar Materia')

@section('content')
<section class="page-section">
    <div class="page-header">
        <div class="page-title">
            <h1 class="h4 mb-0"><i class="fas fa-edit me-2 text-primary"></i>Editar Materia</h1>
            <p class="subtitle">Actualizar información de la materia</p>
        </div>
        <div class="action-bar"><a href="{{ route('materias.index') }}" class="btn-pro outline"><i class="fas fa-arrow-left me-1"></i>Volver</a></div>
    </div>
    <x-form-card title="Información de la Materia">
        <form method="POST" action="{{ route('materias.actualizar', $materia) }}" class="row g-3">
            @csrf
            @method('PUT')
            <div class="col-12">
                <label class="form-label">Nombre de la Materia <span class="text-danger">*</span></label>
                <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $materia->nombre) }}" required>
                @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label">Descripción</label>
                <textarea name="descripcion" class="form-control @error('descripcion') is-invalid @enderror" rows="4">{{ old('descripcion', $materia->descripcion) }}</textarea>
                @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <div class="alert alert-info mb-0"><i class="fas fa-info-circle me-1"></i>Horarios asignados: <strong>{{ $materia->horarios()->count() }}</strong></div>
            </div>
            <div class="col-12 d-flex justify-content-end gap-2 mt-2">
                <a href="{{ route('materias.index') }}" class="btn-pro outline">Cancelar</a>
                <button type="submit" class="btn-pro primary"><i class="fas fa-save me-1"></i>Actualizar</button>
            </div>
        </form>
    </x-form-card>
</section>
@endsection
