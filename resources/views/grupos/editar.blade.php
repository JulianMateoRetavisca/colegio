@extends('layouts.app')

@section('title', 'Editar Grupo - Colegio')

@section('content')
<style>
.stellar-main-container {
    margin-left: 260px;
    transition: margin-left 0.3s ease;
}
.stellar-main-container.sidebar-collapsed { margin-left: 0; }
.stellar-form-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    padding: 2.5rem;
    margin: 2rem;
}
.stellar-form-title {
    color: #2c3e50;
    font-weight: 700;
    margin-bottom: 2rem;
}
.stellar-form-input {
    border: 2px solid #e8e8e8;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    font-size: 1rem;
    transition: all 0.3s ease;
    width: 100%;
}
.stellar-form-input:focus {
    border-color: #3498db;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
    outline: none;
}
.stellar-btn-primary {
    background: linear-gradient(45deg, #3498db, #2980b9);
    border: none;
    border-radius: 8px;
    color: white;
    padding: 0.75rem 2rem;
    font-weight: 600;
    transition: all 0.3s ease;
}
.stellar-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(52, 152, 219, 0.4);
}
.stellar-btn-secondary {
    background: transparent;
    border: 2px solid #95a5a6;
    border-radius: 8px;
    color: #95a5a6;
    padding: 0.75rem 2rem;
    font-weight: 600;
    transition: all 0.3s ease;
}
.stellar-btn-secondary:hover {
    background: #95a5a6;
    color: white;
}
</style>

@include('partials.sidebar')

<div class="stellar-main-container" id="mainContainer">
    <div class="stellar-form-card">
        <h1 class="stellar-form-title">
            <i class="fas fa-edit me-2"></i>Editar Grupo
        </h1>
        
        <form method="POST" action="{{ route('grupos.actualizar', $grupo->id) }}">
            @csrf
            @method('PUT')
            <div style="margin-bottom: 1.5rem;">
                <label style="color: #2c3e50; font-weight: 600; margin-bottom: 0.5rem; display: block;">
                    Nombre del Grupo
                </label>
                <input class="stellar-form-input" 
                       name="nombre" 
                       value="{{ old('nombre', $grupo->nombre) }}" 
                       placeholder="Ingrese el nombre del grupo"
                       required />
            </div>
            
            <div style="display: flex; gap: 1rem;">
                <button type="submit" class="stellar-btn-primary">
                    <i class="fas fa-save me-1"></i>Guardar
                </button>
                <a href="{{ route('grupos.index') }}" class="stellar-btn-secondary">
                    <i class="fas fa-times me-1"></i>Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const mainContainer = document.getElementById('mainContainer');
    const sidebar = document.getElementById('appSidebar');
    
    function updateLayout() {
        if (sidebar && mainContainer) {
            const isCollapsed = sidebar.classList.contains('collapsed');
            mainContainer.classList.toggle('sidebar-collapsed', isCollapsed);
        }
    }
    
    const observer = new MutationObserver(updateLayout);
    if (sidebar) observer.observe(sidebar, {attributes: true, attributeFilter: ['class']});
    updateLayout();
});
</script>
@endsection