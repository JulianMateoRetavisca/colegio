@extends('layouts.app')

@section('title','Asignar Estudiantes al Grupo')

@section('content')
<section class="page-section">
    <div class="page-header">
        <div class="page-title">
            <h1 class="h4 mb-0"><i class="fas fa-user-plus me-2 text-primary"></i>Asignar Estudiantes</h1>
            <p class="subtitle">Grupo: {{ $grupo->nombre }}</p>
        </div>
        <div class="action-bar"><a href="{{ route('grupos.index') }}" class="btn-pro outline"><i class="fas fa-arrow-left me-1"></i>Volver</a></div>
    </div>
    <x-form-card title="Seleccionar Estudiantes">
        <form method="POST" action="{{ route('grupos.asignar.guardar', $grupo->id) }}" class="row g-3">
            @csrf
            <div class="col-12">
                <label class="form-label">Estudiantes (selecciona los que pertenecerán al grupo)</label>
                <select name="students[]" class="form-select" multiple size="12">
                    @foreach($estudiantes as $e)
                        <option value="{{ $e->id }}" {{ $e->grupo_id == $grupo->id ? 'selected' : '' }}>{{ $e->name }} (ID: {{ $e->id }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 d-flex justify-content-end gap-2">
                <a href="{{ route('grupos.index') }}" class="btn-pro outline">Cancelar</a>
                <button class="btn-pro primary" type="submit"><i class="fas fa-save me-1"></i>Guardar asignación</button>
            </div>
        </form>
    </x-form-card>
</section>
@endsection
