@extends('layouts.app')

@section('title', 'Gestión de Matrículas - Colegio')

@section('content')
<div class="container-fluid mt-3">
    <div class="px-4 py-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h1 class="h3 text-dark">
            <i class="fas fa-clipboard-list me-2 text-primary"></i>Gestión de Matrículas Pendientes
          </h1>
          <p class="text-muted mb-0">Revisa, acepta o rechaza las matrículas registradas</p>
        </div>
      </div>

      <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
          @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              {{ session('success') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
          @endif

          @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              {{ session('error') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
          @endif

          @if($matriculas->isEmpty())
            <div class="text-center text-muted mt-4">
              <i class="fas fa-info-circle fa-2x mb-2"></i>
              <p class="mb-0">No hay matrículas pendientes por gestionar.</p>
            </div>
          @else
            <div class="table-responsive mt-3">
              <table class="table table-hover align-middle">
                <thead class="table-light">
                  <tr>
                    <th>Nombre del Estudiante</th>
                    <th>Grado</th>
                    <th>Correo</th>
                    <th>Teléfono</th>
                    <th>Dirección</th>
                    <th class="text-center">Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($matriculas as $m)
                  <tr>
                    <td>{{ $m->nombre_estudiante }}</td>
                    <td>{{ $m->grado }}</td>
                    <td>{{ $m->correo_contacto }}</td>
                    <td>{{ $m->telefono_contacto }}</td>
                    <td>{{ $m->direccion }}</td>
                    <td class="text-center">
                      <a href="{{ route('matricula.gestionar', ['id' => $m->id, 'accion' => 'aceptar']) }}" 
                         class="btn btn-success btn-sm me-2 px-3 rounded-pill">
                         <i class="fas fa-check"></i> Aceptar
                      </a>
                      <a href="{{ route('matricula.gestionar', ['id' => $m->id, 'accion' => 'rechazar']) }}" 
                         class="btn btn-danger btn-sm px-3 rounded-pill">
                         <i class="fas fa-times"></i> Rechazar
                      </a>
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>

<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
  @csrf
</form>

<!-- Estilos -->
<style>
  body {
    background: linear-gradient(135deg, #dce3ff 0%, #e6e0ff 100%);
    font-family: 'Poppins', sans-serif;
  }

  .navbar {
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  }

  .card {
    backdrop-filter: blur(12px);
    background: rgba(255, 255, 255, 0.9);
    border: 1px solid rgba(255, 255, 255, 0.3);
  }

  .btn-primary {
    background-color: #6a74e1;
    border-color: #6a74e1;
  }

  .btn-primary:hover {
    background-color: #5b65c5;
    border-color: #5b65c5;
  }

  .btn-success {
    background-color: #4caf50;
    border-color: #4caf50;
  }

  .btn-danger {
    background-color: #f44336;
    border-color: #f44336;
  }

  .btn-success:hover {
    background-color: #43a047;
  }

  .btn-danger:hover {
    background-color: #e53935;
  }

  .table thead {
    background-color: #eef1ff;
  }

  .table-hover tbody tr:hover {
    background-color: #f5f7ff;
  }

  .form-control:focus {
    border-color: #9ba2ff;
    box-shadow: 0 0 6px rgba(155,162,255,0.4);
  }
</style>
@endsection
