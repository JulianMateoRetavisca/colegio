@extends('layouts.app')

@section('title','Asignar Estudiantes al Grupo')

@section('content')
<div class="container py-4">
    <h3>Asignar Estudiantes a: {{ $grupo->nombre }}</h3>

    <form method="POST" action="{{ route('grupos.asignar.guardar', $grupo->id) }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Estudiantes (selecciona los que pertenecerán al grupo)</label>
            <select name="students[]" class="form-select" multiple size="10">
                @foreach($estudiantes as $e)
                    <option value="{{ $e->id }}" {{ $e->grupo_id == $grupo->id ? 'selected' : '' }}>{{ $e->name }} (ID: {{ $e->id }})</option>
                @endforeach
            </select>
        </div>
        <button class="btn btn-primary">Guardar asignación</button>
        <a href="{{ route('grupos.index') }}" class="btn btn-secondary">Volver</a>
    </form>
</div>
@endsection
