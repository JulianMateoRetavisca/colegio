@extends('layouts.app')

@section('title', 'Iniciar Matrícula - Colegio')

@section('content')
<div class="container-fluid mt-3">
  <div class="main-content p-4">
    <div class="content-card p-5 mb-4">
      <h2 class="fw-bold text-dark mb-4">
        <i class="fas fa-user-graduate me-2 text-primary"></i>Inicio del Proceso de Matrícula
      </h2>

      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif

      @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
      @endif

      <form method="POST" action="{{ route('matricula.guardar') }}">
        @csrf
        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label fw-semibold text-dark">Nombre del Estudiante</label>
            <input type="text" name="nombre_estudiante" class="form-control form-control-lg" placeholder="Ejemplo: Juan Pérez" required>
          </div>

          <div class="col-md-6">
            <label class="form-label fw-semibold text-dark">Grado a Matricular</label>
            <input type="text" name="grado" class="form-control form-control-lg" placeholder="Ejemplo: 5° Primaria" required>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label fw-semibold text-dark">Teléfono de Contacto</label>
            <input type="text" name="telefono_contacto" class="form-control form-control-lg" placeholder="Ejemplo: 300 123 4567" required>
          </div>

          <div class="col-md-6">
            <label class="form-label fw-semibold text-dark">Correo Electrónico</label>
            <input type="email" name="correo_contacto" class="form-control form-control-lg" placeholder="Ejemplo: acudiente@email.com" required>
          </div>
        </div>

        <div class="mb-4">
          <label class="form-label fw-semibold text-dark">Dirección de Residencia</label>
          <input type="text" name="direccion" class="form-control form-control-lg" placeholder="Ejemplo: Calle 45 # 12 - 56" required>
        </div>

        <div class="text-end">
          <button type="submit" class="btn btn-gradient px-5 py-2">
            <i class="fas fa-paper-plane me-2"></i>Enviar Matrícula
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
  body {
    background: linear-gradient(135deg, #dce3ff 0%, #e6e0ff 100%);
    font-family: 'Poppins', sans-serif;
  }

  .navbar {
    position: sticky;
    top: 0;
    z-index: 1030;
    border-bottom: 1px solid rgba(0, 0, 0, 0.1);
  }

  .content-card {
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(10px);
    border-radius: 18px;
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.3);
  }

  .btn-gradient {
    background: linear-gradient(135deg, #6b73ff, #a06bff);
    border: none;
    color: white !important;
    border-radius: 12px;
    font-weight: 500;
    transition: 0.3s ease;
  }

  .btn-gradient:hover {
    opacity: 0.9;
    transform: translateY(-2px);
  }

  .form-control {
    border-radius: 10px;
    border: 1px solid #ddd;
    padding: 10px 15px;
    transition: 0.2s;
  }

  .form-control:focus {
    border-color: #6b73ff;
    box-shadow: 0 0 0 0.15rem rgba(107, 115, 255, 0.25);
  }

  .alert-success {
    background: rgba(0, 200, 83, 0.1);
    color: #2e7d32;
    border: none;
    border-radius: 10px;
  }

  .alert-danger {
    background: rgba(255, 0, 0, 0.1);
    color: #b71c1c;
    border: none;
    border-radius: 10px;
  }
</style>
@endsection
