@extends('layouts.app')

@section('title','Grupos')

@section('content')
<section class="page-section">
    <div class="page-header">
        <div class="page-title">
            <h1 class="h4 mb-0">Grupos</h1>
            <p class="subtitle">Administración de grupos académicos registrados</p>
        </div>
        <div class="action-bar">
            <a href="{{ route('grupos.crear') }}" class="btn-pro primary"><i class="fas fa-plus me-1"></i>Nuevo Grupo</a>
        </div>
    </div>

    <div class="pro-card">
        <div class="pro-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h2 class="h6 mb-0">Listado de Grupos</h2>
            <div class="d-flex gap-2 align-items-center">
                <span class="badge bg-primary">Total: {{ $grupos->count() }}</span>
            </div>
        </div>
        <div class="pro-table-wrapper">
            <table class="pro-table">
                <thead>
                    <tr><th>ID</th><th>Nombre</th><th style="width:220px">Acciones</th></tr>
                </thead>
                <tbody>
                @forelse($grupos as $g)
                    <tr>
                        <td>{{ $g->id }}</td>
                        <td class="fw-semibold">{{ $g->nombre }}</td>
                        <td>
                            <div class="table-actions d-flex flex-wrap gap-1">
                                <a href="{{ route('grupos.editar', $g->id) }}" class="btn-pro xs outline"><i class="fas fa-edit"></i></a>
                                <a href="{{ route('grupos.asignar', $g->id) }}" class="btn-pro xs info"><i class="fas fa-user-plus"></i></a>
                                <form method="POST" action="{{ route('grupos.eliminar', $g->id) }}" onsubmit="return confirm('¿Eliminar este grupo?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn-pro xs danger" type="submit"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center py-5">
                            <div class="empty-state">
                                <i class="fas fa-layer-group fa-2x text-muted mb-3"></i>
                                <p class="mb-2 text-muted">No hay grupos registrados.</p>
                                <a href="{{ route('grupos.crear') }}" class="btn-pro primary"><i class="fas fa-plus me-1"></i>Crear primer grupo</a>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection
