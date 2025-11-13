@extends('layouts.app')

@section('title', 'Editar Materia')

@section('content')
@php
    $usuario = Auth::user();
@endphp

<div class="container-fluid">
    <div class="row g-0">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 p-0">
            @include('partials.sidebar')
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 px-4 py-4">
            <!-- Hero / Header -->
            <div class="mb-4 rounded-4 p-4" style="background: linear-gradient(180deg, #f1eafe 0%, #efe7ff 100%);">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <span class="bg-white rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center" style="width:56px;height:56px;">
                            <i class="fa fa-edit text-primary fa-lg"></i>
                        </span>
                    </div>
                    <div>
                        <h2 class="h4 mb-1 text-dark">Editar Materia</h2>
                        <p class="mb-0 text-muted small">Actualiza la información de la materia</p>
                    </div>
                </div>
            </div>

            <!-- Form Card -->
            <div class="mx-auto" style="max-width:800px;">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('materias.actualizar', $materia) }}">
                            @csrf
                            @method('PUT')
                            
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Nombre de la Materia <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           name="nombre" 
                                           class="form-control @error('nombre') is-invalid @enderror" 
                                           value="{{ old('nombre', $materia->nombre) }}" 
                                           placeholder="Ej: Matemáticas, Ciencias Naturales, etc."
                                           required>
                                    @error('nombre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Descripción</label>
                                    <textarea name="descripcion" 
                                              class="form-control @error('descripcion') is-invalid @enderror" 
                                              rows="4"
                                              placeholder="Descripción breve de la materia (opcional)">{{ old('descripcion', $materia->descripcion) }}</textarea>
                                    @error('descripcion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Esta materia tiene <strong>{{ $materia->horarios()->count() }}</strong> horario(s) asignado(s).
                                    </div>
                                </div>

                                <div class="col-12">
                                    <hr class="my-3">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('materias.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left me-1"></i>Cancelar
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-1"></i>Actualizar Materia
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
