@extends('layouts.app')

@section('title', 'Materias - Colegio')

@section('content')
<section class="page-section">
    <div class="page-header">
        <div class="page-title">
            <h1 class="h4 mb-0"><i class="fas fa-book me-2 text-primary"></i>Materias</h1>
            <p class="subtitle">Materias disponibles y sus horarios asociados</p>
        </div>
        <div class="action-bar">
            <a href="{{ route('materias.crear') }}" class="btn-pro primary"><i class="fas fa-plus me-1"></i>Nueva Materia</a>
            <span class="badge bg-primary align-self-center">Total: {{ $materias->count() }}</span>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-sm">{{ session('error') }}</div>
    @endif

    <div class="pro-card">
        <div class="pro-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h2 class="h6 mb-0">Listado de Materias</h2>
            <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Buscar materia..." style="max-width:260px;">
        </div>
        <div class="pro-table-wrapper">
            <table class="pro-table" id="materiasTable">
                <thead>
                    <tr><th>ID</th><th>Nombre</th><th>Descripción</th><th>Horarios</th><th style="width:170px">Acciones</th></tr>
                </thead>
                <tbody id="materiasTableBody">
                @forelse($materias as $materia)
                    <tr>
                        <td>{{ $materia->id }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-circle bg-primary text-white"><i class="fas fa-book"></i></div>
                                <strong>{{ $materia->nombre }}</strong>
                            </div>
                        </td>
                        <td>{{ Str::limit($materia->descripcion ?? 'Sin descripción', 50) }}</td>
                        <td><span class="badge bg-info">{{ $materia->horarios()->count() }} horarios</span></td>
                        <td>
                            <div class="table-actions d-flex flex-wrap gap-1">
                                <a href="{{ route('materias.editar', $materia) }}" class="btn-pro xs outline" title="Editar"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('materias.eliminar', $materia) }}" method="POST" onsubmit="return confirm('¿Eliminar esta materia?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-pro xs danger" title="Eliminar"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="empty-state">
                                <i class="fas fa-book-open fa-2x text-muted mb-3"></i>
                                <p class="text-muted mb-2">No hay materias registradas.</p>
                                <a href="{{ route('materias.crear') }}" class="btn-pro primary"><i class="fas fa-plus me-1"></i>Crear primera materia</a>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded',()=>{
    const searchInput=document.getElementById('searchInput');
    const tbody=document.getElementById('materiasTableBody');
    if(searchInput){
        searchInput.addEventListener('input',e=>{
            const q=e.target.value.toLowerCase();
            Array.from(tbody.querySelectorAll('tr')).forEach(tr=>{
                const visible=tr.textContent.toLowerCase().includes(q);
                tr.style.display=visible?'':'none';
            });
        });
    }
});
</script>
@endsection
