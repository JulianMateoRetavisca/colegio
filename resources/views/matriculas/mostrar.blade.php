@extends('layouts.app')

@section('title', 'Detalle Matrícula')

@section('content')
@php /** @var \App\Models\MatriculaAcudiente $matricula */ @endphp
<div class="container py-4">
    <h2 class="mb-3">Matrícula de {{ $matricula->nombres }} {{ $matricula->apellidos }}</h2>
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <strong>Curso:</strong> {{ optional($matricula->curso)->nombre }}
                </div>
                <div class="col-md-6">
                    <strong>Documento:</strong> {{ $matricula->documento_identidad }}
                </div>
                <div class="col-md-6">
                    <strong>Email:</strong> {{ $matricula->email ?? '—' }}
                </div>
                <div class="col-md-6">
                    <strong>Teléfono:</strong> {{ $matricula->telefono ?? '—' }}
                </div>
                <div class="col-md-6">
                    <strong>Estado:</strong> {{ ucfirst($matricula->estado) }}
                </div>
                <div class="col-12">
                    <strong>Documentos:</strong>
                    @if(is_array($matricula->documentos) && count($matricula->documentos))
                        <ul class="mt-2">
                        @foreach($matricula->documentos as $ruta)
                            <li>
                                <a href="{{ route('matriculas.descargar', urlencode($ruta)) }}">Descargar {{ basename($ruta) }}</a>
                            </li>
                        @endforeach
                        </ul>
                    @else
                        <div class="text-muted">Sin archivos adjuntos.</div>
                    @endif
                </div>
            </div>
            <div class="mt-3">
                <a href="{{ route('matriculas.index') }}" class="btn btn-outline-secondary">Volver</a>
            </div>
        </div>
    </div>
</div>
@endsection

