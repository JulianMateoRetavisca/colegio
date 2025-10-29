@extends('layouts.app')

@section('title','Grupos')

@section('content')
<div class="container py-4">
    <h3>Grupos</h3>
    <a href="{{ route('grupos.crear') }}" class="btn btn-primary mb-3">Crear Grupo</a>
    <table class="table table-striped">
        <thead>
            <tr><th>Id</th><th>Nombre</th><th>Acciones</th></tr>
        </thead>
        <tbody>
            @foreach($grupos as $g)
            <tr>
                <td>{{ $g->id }}</td>
                <td>{{ $g->nombre }}</td>
                <td>
                    <a href="{{ route('grupos.editar', $g->id) }}" class="btn btn-sm btn-secondary">Editar</a>
                    <a href="{{ route('grupos.asignar', $g->id) }}" class="btn btn-sm btn-info">Asignar estudiantes</a>
                    <form method="POST" action="{{ route('grupos.eliminar', $g->id) }}" style="display:inline">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este grupo?')">Eliminar</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
