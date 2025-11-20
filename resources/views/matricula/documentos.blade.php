@extends('layouts.app')
@section('title','Documentos Matrícula')
@section('content')
<div class="container py-4">
  <h4 class="mb-3"><i class="fas fa-file-upload me-1"></i>Documentos para Matrícula #{{ $matricula->id }}</h4>
  @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
  @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

  <div class="card mb-4">
    <div class="card-header">Subir nuevos documentos</div>
    <div class="card-body">
      <form method="POST" action="{{ route('matricula.documentos.subir',$matricula->id) }}" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
          <label class="form-label">Tipo (opcional)</label>
          <input type="text" name="tipo" class="form-control" placeholder="certificado, cedula, foto" maxlength="50">
        </div>
        <div class="mb-3">
          <label class="form-label">Seleccionar archivo(s)</label>
          <input type="file" name="documentos[]" class="form-control" multiple required accept=".pdf,.jpg,.jpeg,.png">
          <small class="text-muted">Formatos permitidos: PDF/JPG/PNG. Máx 4MB cada uno.</small>
        </div>
        <button class="btn btn-primary"><i class="fas fa-cloud-upload-alt me-1"></i>Subir</button>
        <a href="{{ route('matricula.iniciar') }}" class="btn btn-secondary ms-2">Volver</a>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="card-header">Documentos cargados</div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped mb-0">
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
