@extends('layouts.app')

@section('title', 'Mis Matrículas')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Mis Matrículas</h2>
        <a class="btn btn-primary" href="{{ route('matriculas.crear') }}">Nueva Matrícula</a>
    </div>

    @if ($matriculas->count() === 0)
        <div class="alert alert-info">Aún no has registrado matrículas.</div>
    @else
        <div class="list-group">
            @foreach ($matriculas as $m)
                <a href="{{ route('matriculas.mostrar', $m->id) }}" class="list-group-item list-group-item-action">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1">{{ $m->nombres }} {{ $m->apellidos }} - {{ optional($m->curso)->nombre }}</h5>
                        <small class="text-muted">{{ $m->created_at->format('Y-m-d H:i') }}</small>
                    </div>
                    <p class="mb-1">Estado: <strong>{{ ucfirst($m->estado) }}</strong></p>
                </a>
            @endforeach
        </div>

        <div class="mt-3">
            {{ $matriculas->links() }}
        </div>
    @endif
</div>
@endsection

