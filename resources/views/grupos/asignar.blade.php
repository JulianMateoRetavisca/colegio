@extends('layouts.app')

@section('title', 'Asignar Estudiantes al Grupo - Colegio')

@section('content')
<style>
    .stellar-asignar-grupo {
        background: #f8f9fa;
        min-height: 100vh;
        font-family: 'Source Sans Pro', Helvetica, sans-serif;
    }

    .stellar-main-container {
        margin-left: 260px;
        transition: margin-left 0.3s ease;
    }

    .stellar-main-container.sidebar-collapsed {
        margin-left: 0;
    }

    .stellar-form-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        padding: 2.5rem;
        margin: 2rem;
    }

    .stellar-form-header {
        text-align: center;
        margin-bottom: 2.5rem;
        padding-bottom: 1.5rem;
        border-bottom: 2px solid #ecf0f1;
    }

    .stellar-form-title {
        color: #2c3e50;
        font-weight: 700;
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }

    .stellar-grupo-nombre {
        color: #3498db;
        font-weight: 600;
        font-size: 1.3rem;
    }

    .stellar-form-group {
        margin-bottom: 2rem;
    }

    .stellar-form-label {
        color: #2c3e50;
        font-weight: 600;
        margin-bottom: 1rem;
        display: block;
        font-size: 1.1rem;
    }

    .stellar-select-multiple {
        border: 2px solid #e8e8e8;
        border-radius: 8px;
        padding: 0.75rem;
        font-size: 1rem;
        transition: all 0.3s ease;
        width: 100%;
        height: 300px;
        background: white;
    }

    .stellar-select-multiple:focus {
        border-color: #3498db;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        outline: none;
    }

    .stellar-select-multiple option {
        padding: 0.5rem;
        border-bottom: 1px solid #ecf0f1;
        transition: background 0.3s ease;
    }

    .stellar-select-multiple option:checked {
        background: linear-gradient(45deg, #3498db, #2980b9);
        color: white;
    }

    .stellar-select-multiple option:hover {
        background: #f8f9fa;
    }

    .stellar-form-actions {
        display: flex;
        gap: 1rem;
        justify-content: flex-start;
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid #ecf0f1;
    }

    .stellar-btn-primary {
        background: linear-gradient(45deg, #3498db, #2980b9);
        border: none;
        border-radius: 8px;
        color: white;
        padding: 0.75rem 2rem;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .stellar-btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(52, 152, 219, 0.4);
        color: white;
        text-decoration: none;
    }

    .stellar-btn-secondary {
        background: transparent;
        border: 2px solid #95a5a6;
        border-radius: 8px;
        color: #95a5a6;
        padding: 0.75rem 2rem;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .stellar-btn-secondary:hover {
        background: #95a5a6;
        color: white;
        text-decoration: none;
        transform: translateY(-2px);
    }

    .stellar-selected-count {
        background: linear-gradient(45deg, #27ae60, #2ecc71);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.9rem;
        margin-left: 1rem;
    }

    .stellar-search-box {
        margin-bottom: 1rem;
    }

    .stellar-search-input {
        border: 2px solid #e8e8e8;
        border-radius: 8px;
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        width: 100%;
    }

    .stellar-search-input:focus {
        border-color: #3498db;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        outline: none;
    }

    @media (max-width: 768px) {
        .stellar-main-container {
            margin-left: 0 !important;
        }
        
        .stellar-form-card {
            margin: 1rem;
            padding: 1.5rem;
        }
        
        .stellar-form-actions {
            flex-direction: column;
        }
        
        .stellar-btn-primary,
        .stellar-btn-secondary {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="stellar-asignar-grupo">
    @include('partials.sidebar')
    
    <div class="stellar-main-container" id="mainContainer">
        <div class="stellar-form-card">
            <div class="stellar-form-header">
                <h1 class="stellar-form-title">
                    <i class="fas fa-users me-2"></i>
                    Asignar Estudiantes al Grupo
                </h1>
                <div class="stellar-grupo-nombre">
                    {{ $grupo->nombre }}
                </div>
            </div>

            <form method="POST" action="{{ route('grupos.asignar.guardar', $grupo->id) }}" id="asignarForm">
                @csrf
                
                <div class="stellar-form-group">
                    <label class="stellar-form-label">
                        <i class="fas fa-user-graduate me-2"></i>
                        Estudiantes - Selecciona los que pertenecerán al grupo
                        <span id="selectedCount" class="stellar-selected-count">0 seleccionados</span>
                    </label>
                    
                    <div class="stellar-search-box">
                        <input type="text" 
                               class="stellar-search-input" 
                               placeholder="Buscar estudiante..." 
                               id="searchEstudiantes">
                    </div>

                    <select name="students[]" 
                            class="stellar-select-multiple" 
                            multiple 
                            id="estudiantesSelect"
                            onchange="updateSelectedCount()">
                        @foreach($estudiantes as $e)
                            <option value="{{ $e->id }}" 
                                    {{ $e->grupo_id == $grupo->id ? 'selected' : '' }}
                                    data-name="{{ $e->name }}">
                                {{ $e->name }} (ID: {{ $e->id }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="stellar-form-actions">
                    <button type="submit" class="stellar-btn-primary">
                        <i class="fas fa-save me-1"></i>
                        Guardar Asignación
                    </button>
                    <a href="{{ route('grupos.index') }}" class="stellar-btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>
                        Volver a Grupos
                    </a>
                </div>
            </form>
        </div>
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
    if (sidebar) {
        observer.observe(sidebar, {
            attributes: true,
            attributeFilter: ['class']
        });
    }
    updateLayout();
    updateSelectedCount();
});

function updateSelectedCount() {
    const select = document.getElementById('estudiantesSelect');
    const selectedCount = select.selectedOptions.length;
    document.getElementById('selectedCount').textContent = selectedCount + ' seleccionados';
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