@extends('layouts.app')
@section('title','Documentos Matrícula Admin')
@section('content')
<div class="container py-4">
  <h4 class="mb-3"><i class="fas fa-folder-open me-1"></i>Documentos Matrícula #{{ $matricula->id }} (Admin)</h4>
  @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
  @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

  <div class="alert alert-info">Estado: <strong>{{ $matricula->estado }}</strong></div>

  <div class="card">
    <div class="card-header">Listado de Documentos</div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="table-dark">
            <tr>
              <th>#</th>
              <th>Tipo</th>
              <th>Nombre Original</th>
              <th>MIME</th>
              <th>Tamaño</th>
              <th>Fecha</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
          @forelse($documentos as $i=>$doc)
            <tr>
              <td>{{ $i+1 }}</td>
              <td>{{ $doc->tipo ?? '—' }}</td>
              <td>{{ $doc->original_name }}</td>
              <td><small>{{ $doc->mime_type }}</small></td>
              <td>{{ number_format($doc->size_bytes/1024,1) }} KB</td>
              <td>{{ $doc->created_at->format('d/m/Y H:i') }}</td>
              <td>
                <a href="{{ route('matricula.documentos.descargar',$doc->id) }}" class="btn btn-sm btn-success" title="Descargar"><i class="fas fa-download"></i></a>
                <form method="POST" action="{{ route('matricula.documentos.eliminar',$doc->id) }}" class="d-inline" onsubmit="return confirm('¿Eliminar documento?');">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-danger" title="Eliminar"><i class="fas fa-trash"></i></button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="7" class="text-center py-3">No hay documentos cargados.</td></tr>
          @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
