@extends('layouts.app')

@section('title', 'Nueva Matrícula - Acudiente')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Matrícula de Estudiante (Acudiente)</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('matriculas.guardar') }}" enctype="multipart/form-data" class="card p-4 shadow-sm">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Nombres</label>
                <input type="text" name="nombres" class="form-control" value="{{ old('nombres') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Apellidos</label>
                <input type="text" name="apellidos" class="form-control" value="{{ old('apellidos') }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Documento de identidad</label>
                <input type="text" name="documento_identidad" class="form-control" value="{{ old('documento_identidad') }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Email (opcional)</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Teléfono (opcional)</label>
                <input type="text" name="telefono" class="form-control" value="{{ old('telefono') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Curso</label>
                <select name="curso_id" class="form-select" required>
                    <option value="">Seleccione un curso</option>
                    @foreach($cursos as $c)
                        <option value="{{ $c->id }}" @selected(old('curso_id') == $c->id)>{{ $c->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">Documentos (PDF/JPG/PNG, máx 5MB c/u)</label>
                <input type="file" name="documentos[]" class="form-control" multiple>
                <small class="text-muted">Puedes seleccionar varios archivos.</small>
            </div>
        </div>
        <div class="mt-3 d-flex gap-2">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Enviar matrícula</button>
        </div>
    </form>
</div>
@endsection

