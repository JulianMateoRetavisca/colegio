@extends('layouts.app')

@section('title', 'Docentes - Colegio')

@section('content')
<div class="container-fluid mt-3">
  <div class="main-content p-4">
    <div class="content-card p-4 mb-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-dark mb-0"><i class="fas fa-chalkboard-teacher me-2 text-primary"></i>Gestión de Docentes</h2>
        <div>
          <a href="{{ route('docentes.grupos') }}" class="btn btn-success px-4 py-2 me-2">
            <i class="fas fa-users me-2"></i>Gestionar Grupos y Notas
          </a>
          <a href="{{ route('docentes.crear') }}" class="btn btn-gradient px-4 py-2">
            <i class="fas fa-user-plus me-2"></i>Nuevo Docente
          </a>
        </div>
      </div>

      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif

      <div class="table-responsive">
        <table class="table table-hover align-middle text-center">
          <thead class="table-light">
            <tr>
              <th scope="col">Nombre</th>
              <th scope="col">Correo Electrónico</th>
            </tr>
          </thead>
          <tbody>
            @forelse($docentes as $d)
              <tr>
                <td class="fw-semibold">{{ $d->name }}</td>
                <td>{{ $d->email }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="2" class="text-muted">No hay docentes registrados.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
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

  .btn-success {
    background: linear-gradient(135deg, #28a745, #20c997);
    border: none;
    color: white !important;
    border-radius: 12px;
    font-weight: 500;
    transition: 0.3s ease;
  }

  .btn-success:hover {
    opacity: 0.9;
    transform: translateY(-2px);
    background: linear-gradient(135deg, #218838, #1ea891);
  }

  .table {
    border-radius: 12px;
    overflow: hidden;
  }

  .table thead {
    background: rgba(107, 115, 255, 0.1);
  }

  .table tbody tr:hover {
    background-color: rgba(160, 107, 255, 0.08);
  }

  .alert-success {
    background: rgba(0, 200, 83, 0.1);
    color: #2e7d32;
    border: none;
    border-radius: 10px;
  }

  .text-dark {
    color: #2b2b2b !important;
  }

  .text-secondary {
    color: #5a5a5a !important;
  }
</style>
@endsection
