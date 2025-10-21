@extends('layouts.app')

@section('content')
@include('partials.sidebar')

<div class="container-fluid">
  <div class="main-content p-4">
    <h1>Docentes</h1>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('docentes.crear') }}" class="btn btn-primary mb-3">Nuevo docente</a>

    <table class="table table-bordered">
      <thead><tr><th>Nombre</th><th>Email</th></tr></thead>
      <tbody>
        @foreach($docentes as $d)
          <tr>
            <td>{{ $d->name }}</td>
            <td>{{ $d->email }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection