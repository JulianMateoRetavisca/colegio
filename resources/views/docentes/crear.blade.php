@extends('layouts.app')

@section('content')
@include('partials.sidebar')

<div class="container-fluid">
  <div class="row">
    <div class="col-md-3 col-lg-2 p-10" style="background-color: #f7f7f7ff;">
    </div>
    <div class="col-md-9 col-lg-10" style="background-color: #f7f7f7ff;">
      <div class="main-content p-4">
    <h1>Crear Docente</h1>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
      <div class="alert alert-danger">
        <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
      </div>
    @endif

    <form action="{{ route('docentes.store') }}" method="POST">
      @csrf
      <div class="mb-3">
        <label class="form-label">Nombre</label>
        <input name="name" value="{{ old('name') }}" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input name="email" type="email" value="{{ old('email') }}" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Contraseña</label>
        <input name="password" type="password" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Confirmar contraseña</label>
        <input name="password_confirmation" type="password" class="form-control" required>
      </div>

      <button class="btn btn-success" type="submit">Crear docente</button>
      <a class="btn btn-secondary" href="{{ route('docentes.index') }}">Cancelar</a>
    </form>
    </div>
     </div>  
  </div>
</div>
@endsection