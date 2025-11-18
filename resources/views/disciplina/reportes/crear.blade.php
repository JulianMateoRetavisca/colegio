@extends('layouts.app')
@section('content')
<section class="page-section">
    <div class="page-header">
        <div class="page-title">
            <h1 class="h4 mb-0"><i class="fas fa-exclamation-triangle me-2 text-danger"></i>Reportar Incidente</h1>
            <p class="subtitle">Registro de un nuevo reporte disciplinario</p>
        </div>
        <div class="action-bar"><a href="{{ url('/disciplina/reportes/mis') }}" class="btn-pro outline"><i class="fas fa-arrow-left me-1"></i>Mis reportes</a></div>
    </div>
    <x-form-card title="Datos del Reporte">
        <form method="POST" action="{{ route('disciplina.reportes.store') }}" id="formReporte" class="row g-3">
            @csrf
            <div class="col-md-4">
                <label class="form-label">Curso / Grupo</label>
                <select id="grupoSelect" class="form-select" required>
                    <option value="">-- Seleccionar --</option>
                    @foreach($grupos as $g)
                        <option value="{{ $g->id }}">{{ $g->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-8">
                <label class="form-label">Estudiante</label>
                <select name="estudiante_id" id="estudianteSelect" class="form-select" required disabled>
                    <option value="">Seleccione primero el grupo</option>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">Descripción del Incidente</label>
                <textarea name="descripcion_incidente" class="form-control" rows="4" required></textarea>
            </div>
            <div class="col-md-4">
                <label class="form-label">Gravedad</label>
                <select name="gravedad" class="form-select">
                    <option value="">-- Seleccionar --</option>
                    <option value="leve">Leve</option>
                    <option value="moderada">Moderada</option>
                    <option value="grave">Grave</option>
                </select>
            </div>
            <div class="col-12 d-flex justify-content-end gap-2">
                <button class="btn-pro primary" type="submit"><i class="fas fa-save me-1"></i>Guardar Reporte</button>
            </div>
        </form>
    </x-form-card>
</section>
<script>
document.addEventListener('DOMContentLoaded',()=>{
    const grupoSelect = document.getElementById('grupoSelect');
    const estudianteSelect = document.getElementById('estudianteSelect');
    grupoSelect.addEventListener('change', async ()=>{
        const id = grupoSelect.value;
        estudianteSelect.innerHTML = '<option value="">Cargando...</option>';
        estudianteSelect.disabled = true;
        if(!id){
            estudianteSelect.innerHTML = '<option value="">Seleccione primero el grupo</option>';
            return;
        }
        try{
            const resp = await fetch(`/disciplina/grupos/${id}/estudiantes`);
            if(!resp.ok){ throw new Error('Error al cargar'); }
            const data = await resp.json();
            if(!Array.isArray(data) || data.length===0){
                estudianteSelect.innerHTML = '<option value="">Sin estudiantes</option>';
            } else {
                estudianteSelect.innerHTML = '<option value="">-- Seleccionar estudiante --</option>' +
                    data.map(e=>`<option value="${e.id}">${e.name}</option>`).join('');
                estudianteSelect.disabled = false;
            }
        }catch(err){
            estudianteSelect.innerHTML = '<option value="">Error cargando estudiantes</option>';
        }
    });
});
</script>
@endsection