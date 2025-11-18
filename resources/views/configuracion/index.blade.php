@extends('layouts.app')

@section('title','Configuración del sistema')

@section('content')
<section class="page-section">
  <div class="page-header">
    <div class="page-title">
      <h1 class="h4 mb-0"><i class="fas fa-gear me-2 text-primary"></i>Configuración</h1>
      <p class="subtitle">Preferencias generales y parámetros operativos</p>
    </div>
    <div class="action-bar"></div>
  </div>

  @if(session('ok'))
    <div class="alert alert-success border-0 rounded-3">{{ session('ok') }}</div>
  @endif

  <div class="pro-card">
    <div class="pro-card-header">
      <h2 class="h6 mb-0"><i class="fas fa-sliders me-2"></i>Ajustes</h2>
    </div>
    <div class="pro-card-body">
      <form action="{{ route('configuracion.guardar') }}" method="POST">
        @csrf
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Tema</label>
            <select name="settings[theme]" class="form-select">
              <option value="light" {{ ($settings['theme'] ?? 'light')==='light' ? 'selected' : '' }}>Claro</option>
              <option value="dark" {{ ($settings['theme'] ?? 'light')==='dark' ? 'selected' : '' }}>Oscuro</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Densidad de tablas</label>
            <select name="settings[table_density]" class="form-select">
              <option value="comfortable" {{ ($settings['table_density'] ?? 'comfortable')==='comfortable' ? 'selected' : '' }}>Cómoda</option>
              <option value="compact" {{ ($settings['table_density'] ?? 'comfortable')==='compact' ? 'selected' : '' }}>Compacta</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Nota mínima aprobación</label>
            <input type="text" class="form-control" name="settings[min_grade]" value="{{ $settings['min_grade'] ?? '3.0' }}" placeholder="Ej. 3.0">
          </div>
          <div class="col-md-4">
            <label class="form-label">SMTP habilitado</label>
            <select name="settings[smtp_enabled]" class="form-select">
              <option value="0" {{ ($settings['smtp_enabled'] ?? '0')==='0' ? 'selected' : '' }}>No</option>
              <option value="1" {{ ($settings['smtp_enabled'] ?? '0')==='1' ? 'selected' : '' }}>Sí</option>
            </select>
          </div>
          <div class="col-12">
            <label class="form-label">Periodos académicos (JSON)</label>
            <textarea name="settings[periodos_json]" rows="4" class="form-control" placeholder='[{"nombre":"Periodo 1","inicio":"2025-01-20","fin":"2025-03-20"}]'>{{ $settings['periodos_json'] ?? '[]' }}</textarea>
            <small class="text-muted">Formato libre para pruebas. Luego podemos crear UI dedicada.</small>
          </div>
        </div>
        <div class="pro-card-footer d-flex justify-content-end gap-2 mt-3">
          <button type="submit" class="btn-pro success"><i class="fas fa-save me-1"></i>Guardar</button>
        </div>
      </form>
    </div>
  </div>
</section>
@endsection
