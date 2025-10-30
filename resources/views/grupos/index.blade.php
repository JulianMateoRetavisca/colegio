@extends('layouts.app')

@section('title', 'Grupos - Colegio')

@section('content')
@include('partials.sidebar')

<div class="stellar-main-container" id="mainContainer">
    <div class="stellar-form-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1 class="stellar-form-title">
                <i class="fas fa-layer-group me-2"></i>Grupos
            </h1>
            <a href="{{ route('grupos.crear') }}" class="stellar-btn-primary">
                <i class="fas fa-plus me-1"></i>Crear Grupo
            </a>
        </div>

        <div class="stellar-table-card">
            <table class="stellar-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($grupos as $g)
                    <tr>
                        <td>{{ $g->id }}</td>
                        <td>{{ $g->nombre }}</td>
                        <td>
                            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                <a href="{{ route('grupos.editar', $g->id) }}" class="stellar-btn" style="background: rgba(52, 152, 219, 0.1); color: #3498db; border: 1px solid rgba(52, 152, 219, 0.2);">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('grupos.asignar', $g->id) }}" class="stellar-btn" style="background: rgba(155, 89, 182, 0.1); color: #9b59b6; border: 1px solid rgba(155, 89, 182, 0.2);">
                                    <i class="fas fa-users"></i>
                                </a>
                                <form method="POST" action="{{ route('grupos.eliminar', $g->id) }}" style="display:inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="stellar-btn" style="background: rgba(231, 76, 60, 0.1); color: #e74c3c; border: 1px solid rgba(231, 76, 60, 0.2);" onclick="return confirm('¿Eliminar este grupo?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
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
    if (sidebar) observer.observe(sidebar, {attributes: true, attributeFilter: ['class']});
    updateLayout();
});
</script>

<style>
.stellar-table-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    border: 1px solid #ecf0f1;
    overflow: hidden;
}
.stellar-table {
    width: 100%;
    border-collapse: collapse;
}
.stellar-table thead {
    background: #f8f9fa;
}
.stellar-table th {
    color: #2c3e50;
    font-weight: 600;
    padding: 1rem;
    text-align: left;
    border-bottom: 2px solid #ecf0f1;
}
.stellar-table td {
    padding: 1rem;
    border-bottom: 1px solid #ecf0f1;
}
.stellar-table tbody tr {
    transition: all 0.3s ease;
}
.stellar-table tbody tr:hover {
    background: #f8f9fa;
}
.stellar-btn {
    padding: 0.5rem 0.75rem;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
}
.stellar-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
</style>
@endsection