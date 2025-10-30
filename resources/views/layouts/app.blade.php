@extends('layouts.app')

@section('title', 'Mi Vista')

@section('content')
@include('partials.sidebar')

<div class="stellar-main-container" id="mainContainer">
    <div class="stellar-form-card">
        <h1 class="stellar-form-title">Título</h1>
        
        <input class="stellar-form-input" placeholder="Ejemplo">
        <select class="stellar-select-multiple" multiple>...</select>
        
        <button class="stellar-btn-primary">Guardar</button>
        <a class="stellar-btn-secondary">Cancelar</a>
    </div>
</div>
@endsection