@extends('layouts.app')

@section('title', 'Asignar Estudiantes al Grupo - Colegio')

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
    margin-bottom: 1rem;
}
.stellar-grupo-nombre {
    color: #3498db;
    font-weight: 600;
    font-size: 1.2rem;
    text-align: center;
}
.stellar-form-label {
    color: #2c3e50;
    font-weight: 600;
    margin-bottom: 1rem;
    display: block;
}
.stellar-select-multiple {
    border: 2px solid #e8e8e8;
    border-radius: 8px;
    padding: 0.75rem;
    font-size: 1rem;
    width: 100%;
    height: 300px;
}
.stellar-select-multiple:focus {
    border-color: #3498db;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
    outline: none;
}
.stellar-select-multiple option:checked {
    background: linear-gradient(45deg, #3498db, #2980b9);
    color: white;
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
.stellar-selected-count {
    background: #27ae60;
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.8rem;
    margin-left: 0.5rem;
}
.stellar-search-input {
    border: 2px solid #e8e8e8;
    border-radius: 8px;
    padding: 0.5rem 1rem;
    width: 100%;
    margin-bottom: 1rem;
}
.stellar-search-input:focus {
    border-color: #3498db;
    outline: none;
}
</style>

@include('partials.sidebar')

<div class="stellar-main-container" id="mainContainer">
    <div class="stellar-form-card">
        <h1 class="stellar-form-title">
            <i class="fas fa-users me-2"></i>Asignar Estudiantes al Grupo
        </h1>
        <div class="stellar-grupo-nombre">{{ $grupo->nombre }}</div>

        <form method="POST" action="{{ route('grupos.asignar.guardar', $grupo->id) }}">
            @csrf
            
            <div style="margin-bottom: 2rem;">
                <label class="stellar-form-label">
                    Estudiantes 
                    <span id="selectedCount" class="stellar-selected-count">0</span>
                </label>
                
                <input type="text" 
                       class="stellar-search-input" 
                       placeholder="Buscar estudiante..." 
                       id="searchEstudiantes">

                <select name="students[]" 
                        class="stellar-select-multiple" 
                        multiple 
                        id="estudiantesSelect"
                        onchange="updateSelectedCount()">
                    @foreach($estudiantes as $e)
                        <option value="{{ $e->id }}" 
                                {{ $e->grupo_id == $grupo->id ? 'selected' : '' }}>
                            {{ $e->name }} (ID: {{ $e->id }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; gap: 1rem;">
                <button type="submit" class="stellar-btn-primary">
                    <i class="fas fa-save me-1"></i>Guardar
                </button>
                <a href="{{ route('grupos.index') }}" class="stellar-btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Volver
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
    updateSelectedCount();
});

function updateSelectedCount() {
    const select = document.getElementById('estudiantesSelect');
    const selectedCount = select.selectedOptions.length;
    document.getElementById('selectedCount').textContent = selectedCount;
}

document.getElementById('searchEstudiantes').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const options = document.getElementById('estudiantesSelect').options;
    
    for (let option of options) {
        const text = option.textContent.toLowerCase();
        option.style.display = text.includes(searchTerm) ? '' : 'none';
    }
});
</script>
@endsection