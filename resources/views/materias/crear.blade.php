@extends('layouts.app')

@section('title', 'Crear Materia')

@section('content')
<section class="page-section">
    <div class="page-header">
        <div class="page-title">
            <h1 class="h4 mb-0"><i class="fas fa-book me-2 text-primary"></i>Crear Materia</h1>
            <p class="subtitle">Registrar una nueva materia en el sistema</p>
        </div>
        <div class="action-bar"><a href="{{ route('materias.index') }}" class="btn-pro outline"><i class="fas fa-arrow-left me-1"></i>Volver</a></div>
    </div>
    <x-form-card title="Datos de la Materia">
        <form method="POST" action="{{ route('materias.store') }}" class="row g-3">
            @csrf
            <div class="col-12">
                <label class="form-label">Nombre de la Materia <span class="text-danger">*</span></label>
                <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" placeholder="Ej: Matemáticas" required>
                @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label">Descripción</label>
                <textarea name="descripcion" class="form-control @error('descripcion') is-invalid @enderror" rows="4" placeholder="Descripción opcional">{{ old('descripcion') }}</textarea>
                @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12 d-flex justify-content-end gap-2">
                <a href="{{ route('materias.index') }}" class="btn-pro outline">Cancelar</a>
                <button type="submit" class="btn-pro primary"><i class="fas fa-save me-1"></i>Guardar</button>
            </div>
        </form>
    </x-form-card>
</section>
@endsection
