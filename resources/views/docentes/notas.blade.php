
@extends('layouts.app')

@section('title', 'Notas: ' . $materia->nombre . ' - ' . $grupo->nombre)

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2><i class="fas fa-clipboard-list"></i> Gestión de Notas</h2>
                    <p class="text-muted mb-0">
                        <strong>Materia:</strong> {{ $materia->nombre }} | 
                        <strong>Grupo:</strong> {{ $grupo->nombre }}
                    </p>
                </div>
                <a href="{{ route('docentes.grupos.ver', $grupo->id) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver al Grupo
                </a>
            </div>
        </div>
    </div>
    @if($estudiantes->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-list-ol"></i> Lista de Estudiantes y Notas
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Selector de Período -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="periodo" class="form-label">Período:</label>
                            <select id="periodo" class="form-select">
                                <option value="1" {{ (isset($periodoSeleccionado) && $periodoSeleccionado=='1') ? 'selected' : '' }}>Primer Período</option>
                                <option value="2" {{ (isset($periodoSeleccionado) && $periodoSeleccionado=='2') ? 'selected' : '' }}>Segundo Período</option>
                                <option value="3" {{ (isset($periodoSeleccionado) && $periodoSeleccionado=='3') ? 'selected' : '' }}>Tercer Período</option>
                                <option value="4" {{ (isset($periodoSeleccionado) && $periodoSeleccionado=='4') ? 'selected' : '' }}>Cuarto Período</option>
                            </select>
                        </div>
                        <div class="col-md-9">
                            <label class="form-label">&nbsp;</label>
                            <div>
                                <button type="button" class="btn btn-primary" onclick="cargarNotas()">
                                    <i class="fas fa-sync"></i> Cargar Notas
                                </button>
                                <button type="button" class="btn btn-success" onclick="guardarTodasNotas()">
                                    <i class="fas fa-save"></i> Guardar Todas
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de Estudiantes y Notas -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th width="50">#</th>
                                    <th>Estudiante</th>
                                    <th width="80">P1</th>
                                    <th width="80">P2</th>
                                    <th width="80">P3</th>
                                    <th width="80">P4</th>
                                    <th width="200">Nueva Nota</th>
                                    <th width="120">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($estudiantes as $index => $estudiante)
                                <tr id="estudiante-{{ $estudiante->id }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $estudiante->name }}</strong><br>
                                        <small class="text-muted">{{ $estudiante->email }}</small>
                                    </td>
                                    <td><span id="cell-1-{{ $estudiante->id }}" class="badge {{ isset($mapaNotas[$estudiante->id]['1']) ? 'bg-info' : 'bg-secondary' }}">{{ $mapaNotas[$estudiante->id]['1'] ?? '—' }}</span></td>
                                    <td><span id="cell-2-{{ $estudiante->id }}" class="badge {{ isset($mapaNotas[$estudiante->id]['2']) ? 'bg-info' : 'bg-secondary' }}">{{ $mapaNotas[$estudiante->id]['2'] ?? '—' }}</span></td>
                                    <td><span id="cell-3-{{ $estudiante->id }}" class="badge {{ isset($mapaNotas[$estudiante->id]['3']) ? 'bg-info' : 'bg-secondary' }}">{{ $mapaNotas[$estudiante->id]['3'] ?? '—' }}</span></td>
                                    <td><span id="cell-4-{{ $estudiante->id }}" class="badge {{ isset($mapaNotas[$estudiante->id]['4']) ? 'bg-info' : 'bg-secondary' }}">{{ $mapaNotas[$estudiante->id]['4'] ?? '—' }}</span></td>
                                    <td>
                                        <input type="number" 
                                               class="form-control nota-input" 
                                               id="nota-{{ $estudiante->id }}"
                                               data-estudiante-id="{{ $estudiante->id }}"
                                               min="0" 
                                               max="100" 
                                               step="0.1"
                                               placeholder="0.0">
                                    </td>
                                    <td>
                                        <button type="button" 
                                                class="btn btn-sm btn-success"
                                                onclick="guardarNota({{ $estudiante->id }})">
                                            <i class="fas fa-save"></i>
                                        </button>
                                        <button type="button" 
                                                class="btn btn-sm btn-warning"
                                                onclick="limpiarNota({{ $estudiante->id }})">
                                            <i class="fas fa-eraser"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-user-slash fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay estudiantes en este grupo</h5>
                    <p class="text-muted">Contacta al administrador para asignar estudiantes al grupo.</p>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Modal de Confirmación -->
<div class="modal fade" id="confirmarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Acción</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="mensaje-confirmacion"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn-confirmar">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<!-- Toast de guardado -->
<div id="saveToast" class="toast align-items-center text-bg-success border-0 position-fixed" style="bottom:20px;right:20px;z-index:1055;" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
        <div class="toast-body" id="saveToastBody">Nota guardada</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button>
    </div>
</div>
@endsection

@section('scripts')
<script>
const grupoId = {{ $grupo->id }};
const materiaId = {{ $materia->id }};

function cargarNotas() {
    const periodo = document.getElementById('periodo').value;
    const url = new URL(window.location.href);
    url.searchParams.set('periodo', periodo);
    window.location.href = url.toString();
}

function guardarNota(estudianteId) {
    const notaInput = document.getElementById(`nota-${estudianteId}`);
    const nota = notaInput.value;
    const periodo = document.getElementById('periodo').value;
    
    if (!nota || nota < 0 || nota > 100) {
        alert('Por favor, ingresa una nota válida (0-100)');
        return;
    }
    
    // Mostrar indicador de carga
    const btn = event.target.closest('button');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    btn.disabled = true;
    
    fetch(`{{ route('docentes.grupos.notas.asignar', [$grupo->id, $materia->id]) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            estudiante_id: estudianteId,
            nota: nota,
            periodo: periodo
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Actualizar la celda del período correspondiente
            const cell = document.getElementById(`cell-${periodo}-${estudianteId}`);
            if (cell) {
                cell.textContent = nota;
                cell.className = 'badge bg-success';
            }
            
            // Limpiar el input
            notaInput.value = '';
            
            // Mostrar mensaje de éxito
            showAlert('Nota guardada correctamente', 'success');
            showToast('Nota guardada correctamente');
        } else {
            showAlert(data.error || 'Error al guardar la nota', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error al guardar la nota', 'danger');
    })
    .finally(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}

function limpiarNota(estudianteId) {
    document.getElementById(`nota-${estudianteId}`).value = '';
}

function guardarTodasNotas() {
    const notasInputs = document.querySelectorAll('.nota-input');
    const notasAGuardar = [];
    
    notasInputs.forEach(input => {
        if (input.value && input.value >= 0 && input.value <= 100) {
            notasAGuardar.push({
                estudianteId: input.dataset.estudianteId,
                nota: input.value
            });
        }
    });
    
    if (notasAGuardar.length === 0) {
        alert('No hay notas válidas para guardar');
        return;
    }
    
    document.getElementById('mensaje-confirmacion').textContent = 
        `¿Estás seguro de que quieres guardar ${notasAGuardar.length} nota(s)?`;
    
    const modal = new bootstrap.Modal(document.getElementById('confirmarModal'));
    modal.show();
    
    document.getElementById('btn-confirmar').onclick = function() {
        modal.hide();
        
        const periodo = document.getElementById('periodo').value;
        let completadas = 0;
        
        notasAGuardar.forEach((nota, index) => {
            setTimeout(() => {
                fetch(`{{ route('docentes.grupos.notas.asignar', [$grupo->id, $materia->id]) }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        estudiante_id: nota.estudianteId,
                        nota: nota.nota,
                        periodo: periodo
                    })
                })
                .then(response => response.json())
                .then(data => {
                    completadas++;
                    if (data.success) {
                        const cell = document.getElementById(`cell-${periodo}-${nota.estudianteId}`);
                        if (cell) { cell.textContent = nota.nota; cell.className = 'badge bg-success'; }
                        document.getElementById(`nota-${nota.estudianteId}`).value = '';
                    }
                    
                    if (completadas === notasAGuardar.length) {
                        showAlert('Todas las notas han sido guardadas', 'success');
                        showToast('Todas las notas guardadas');
                    }
                });
            }, index * 500); // Retraso entre peticiones para evitar sobrecarga
        });
    };
}

function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.parentNode.removeChild(alertDiv);
        }
    }, 5000);
}

function showToast(message){
    const toastEl = document.getElementById('saveToast');
    const bodyEl = document.getElementById('saveToastBody');
    if (!toastEl || !bodyEl) return;
    bodyEl.textContent = message;
    // Instanciar bootstrap toast dinámicamente
    let toast = bootstrap.Toast.getInstance(toastEl);
    if (!toast) {
        toast = new bootstrap.Toast(toastEl, { delay: 2500 });
    }
    toast.show();
}
</script>
@endsection