@extends('layouts.app')

@section('title','Crear Grupo')

@section('content')
<div class="container py-4">
    <h3>Crear Grupo</h3>
    <form method="POST" action="{{ route('grupos.guardar') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input class="form-control" name="nombre" value="{{ old('nombre') }}" required />
        </div>
        <button class="btn btn-primary">Crear</button>
        <a href="{{ route('grupos.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
