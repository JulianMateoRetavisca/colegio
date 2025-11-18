@extends('layouts.app')

@section('title', 'Crear Docente - Colegio')

@section('content')
<div class="container-fluid mt-3">
    <div class="px-4 py-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h1 class="h3 text-dark">
            <i class="fas fa-user-tie me-2 text-primary"></i>Crear Docente
          </h1>
          <p class="text-muted mb-0">Registra un nuevo docente en el sistema</p>
        </div>
      </div>

      <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
          @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
          @endif
          @if($errors->any())
            <div class="alert alert-danger">
              <ul class="mb-0">
                @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <form action="{{ route('docentes.store') }}" method="POST" class="needs-validation" novalidate>
            @csrf
            <div class="mb-3">
              <label class="form-label fw-semibold">Nombre</label>
              <input name="name" value="{{ old('name') }}" class="form-control rounded-3 shadow-sm" required>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Email</label>
              <input name="email" type="email" value="{{ old('email') }}" class="form-control rounded-3 shadow-sm" required>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Contraseña</label>
                <input name="password" type="password" class="form-control rounded-3 shadow-sm" required>
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Confirmar contraseña</label>
                <input name="password_confirmation" type="password" class="form-control rounded-3 shadow-sm" required>
              </div>
            </div>

            <div class="d-flex justify-content-end">
              <a class="btn btn-secondary me-2 px-4" href="{{ route('docentes.index') }}">
                <i class="fas fa-arrow-left me-1"></i>Cancelar
              </a>
              <button class="btn btn-primary px-4" type="submit">
                <i class="fas fa-save me-1"></i>Guardar
              </button>
            </div>
          </form>
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
    background: rgba(255, 255, 255, 0.8);
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

  .btn-secondary {
    background-color: #b6b7d8;
    border-color: #b6b7d8;
    color: #2e2e2e;
  }

  .btn-secondary:hover {
    background-color: #a2a3c7;
    border-color: #a2a3c7;
  }

  .form-control {
    border: 1px solid #cfd2ff;
  }

  .form-control:focus {
    border-color: #9ba2ff;
    box-shadow: 0 0 6px rgba(155,162,255,0.4);
  }
</style>
@endsection
