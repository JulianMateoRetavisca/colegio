@extends('layouts.app')

@section('title','Editar Grupo')

@section('content')
<div class="container py-4">
    <h3>Editar Grupo</h3>
    <form method="POST" action="{{ route('grupos.actualizar', $grupo->id) }}">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input class="form-control" name="nombre" value="{{ old('nombre', $grupo->nombre) }}" required />
        </div>
        <button class="btn btn-primary">Guardar</button>
        <a href="{{ route('grupos.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
