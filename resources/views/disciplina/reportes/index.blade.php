@extends('layouts.app')

@section('title','Reportes disciplinarios')

@section('content')
<section class="page-section">
  <div class="page-header">
    <div class="page-title">
      <h1 class="h4 mb-0"><i class="fas fa-user-exclamation me-2 text-primary"></i>Reportes disciplinarios</h1>
      <p class="subtitle">Listado general para coordinación/administración</p>
    </div>
    <div class="action-bar">
      <a href="{{ route('disciplina.reportes.crear') }}" class="btn-pro primary"><i class="fas fa-flag me-1"></i>Reportar incidente</a>
      <a href="{{ route('disciplina.reportes.mis') }}" class="btn-pro outline"><i class="fas fa-user me-1"></i>Mis reportes</a>
    </div>
  </div>

  @if(session('ok'))
    <div class="alert alert-success border-0 rounded-3">{{ session('ok') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger border-0 rounded-3">{{ session('error') }}</div>
  @endif

  <div class="pro-card mb-3">
    <div class="pro-card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
      <h2 class="h6 mb-0"><i class="fas fa-filter me-1"></i>Filtros</h2>
    </div>
    <div class="pro-card-body">
      <form method="GET" action="{{ route('disciplina.reportes.index') }}" class="row g-3">
        <div class="col-sm-6 col-md-4">
          <label class="form-label">Estado</label>
          <select name="estado" class="form-select">
            <option value="">Todos</option>
            @foreach($estados as $k=>$v)
              <option value="{{ $k }}" {{ ($filtros['estado'] ?? '')===$k ? 'selected' : '' }}>{{ $v }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-sm-6 col-md-4">
          <label class="form-label">ID Estudiante</label>
          <input type="number" name="estudiante_id" class="form-control" value="{{ $filtros['estudiante_id'] ?? '' }}" placeholder="Opcional">
        </div>
        <div class="col-12 d-flex justify-content-end gap-2">
          <a href="{{ route('disciplina.reportes.index') }}" class="btn-pro outline"><i class="fas fa-eraser me-1"></i>Limpiar</a>
          <button class="btn-pro info" type="submit"><i class="fas fa-search me-1"></i>Filtrar</button>
        </div>
      </form>
    </div>
  </div>

  <div class="pro-card">
    <div class="pro-card-header d-flex justify-content-between align-items-center">
      <h2 class="h6 mb-0"><i class="fas fa-list me-1"></i>Resultados</h2>
      <span class="badge bg-primary">Total: {{ $reportes->total() }}</span>
    </div>
    <div class="pro-table-wrapper">
      <table class="pro-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Estudiante</th>
            <th>Docente</th>
            <th>Estado</th>
            <th>Fecha</th>
            <th class="text-center">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($reportes as $r)
            <tr>
              <td>#{{ $r->id }}</td>
              <td>{{ $r->estudiante->name ?? '—' }}</td>
              <td>{{ $r->docente->name ?? '—' }}</td>
              <td>
                <span class="badge bg-{{ $badgeMap[$r->estado] ?? 'secondary' }}">{{ str_replace('_',' ', ucfirst($r->estado)) }}</span>
              </td>
              <td>{{ optional($r->created_at)->format('Y-m-d H:i') }}</td>
              <td class="text-center">
                <div class="action-bar justify-content-center">
                  @if($puedeGestionar && $r->estado === \App\Models\ReporteDisciplina::ESTADO_REPORTADO)
                    <form method="POST" action="{{ route('disciplina.reportes.revisar', $r->id) }}" class="d-inline">
                      @csrf
                      <button type="submit" class="btn-pro xs info" title="Iniciar revisión"><i class="fas fa-clipboard-check"></i></button>
                    </form>
                  @endif
                  @if($puedeGestionar && $r->estado === \App\Models\ReporteDisciplina::ESTADO_EN_REVISION)
                    <button type="button" class="btn-pro xs warning" title="Asignar sanción" data-sanction="{{ route('disciplina.reportes.asignar_sancion', $r->id) }}" data-reporte="#{{ $r->id }}" onclick="openSancionModal(this)"><i class="fas fa-gavel"></i></button>
                  @endif
                  @if($puedeGestionar && $r->estado === \App\Models\ReporteDisciplina::ESTADO_SANCION_ASIGNADA)
                    <form method="POST" action="{{ route('disciplina.reportes.notificar', $r->id) }}" class="d-inline">
                      @csrf
                      <button type="submit" class="btn-pro xs primary" title="Notificar sanción"><i class="fas fa-bell"></i></button>
                    </form>
                  @endif
                  @if($puedeGestionar && in_array($r->estado, [\App\Models\ReporteDisciplina::ESTADO_NOTIFICADO, \App\Models\ReporteDisciplina::ESTADO_APELACION_ACEPTADA, \App\Models\ReporteDisciplina::ESTADO_APELACION_RECHAZADA]))
                    <form method="POST" action="{{ route('disciplina.reportes.archivar', $r->id) }}" class="d-inline" onsubmit="return confirm('¿Archivar este caso?')">
                      @csrf
                      <button type="submit" class="btn-pro xs dark" title="Archivar"><i class="fas fa-box-archive"></i></button>
                    </form>
                  @endif
                  <button type="button" class="btn-pro xs outline" title="Historial" data-historial="{{ route('disciplina.reportes.historial', $r->id) }}" onclick="openHistorial(this)">
                    <i class="fas fa-history"></i>
                  </button>
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center text-muted">No hay reportes</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="pro-card-footer d-flex justify-content-end">
      {{ $reportes->links() }}
    </div>
  </div>

  <!-- Modal Historial -->
  <div class="modal fade" id="historialModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-history me-2"></i>Historial del reporte</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <ul id="historialList" class="timeline list-unstyled m-0"></ul>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Asignar Sanción -->
  <div class="modal fade" id="sancionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-gavel me-2"></i>Asignar sanción <span id="sancionReporte"></span></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="sancionForm" method="POST">
          @csrf
          <div class="modal-body">
            <label class="form-label">Detalle de la sanción</label>
            <textarea name="sancion_text" class="form-control" rows="3" required placeholder="Describa la sanción..."></textarea>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Guardar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>
@endsection

@push('scripts')
<script>
  function openHistorial(btn){
    const url = btn.getAttribute('data-historial');
    const list = document.getElementById('historialList');
    list.innerHTML = '<li class="text-muted">Cargando...</li>';
    fetch(url, { headers: { 'Accept':'application/json' }, credentials: 'same-origin' })
      .then(r => r.ok ? r.json() : Promise.reject())
      .then(items => {
        if(!Array.isArray(items) || items.length === 0){ list.innerHTML = '<li class="text-muted">Sin eventos</li>'; return; }
        list.innerHTML = items.map(it => {
          const from = it.estado_from ? it.estado_from.replaceAll('_',' ') : '—';
          const to = it.estado_to ? it.estado_to.replaceAll('_',' ') : '—';
          const desc = it.descripcion || '';
          const fecha = it.created_at || '';
          return `<li class="mb-2"><div class="item-title fw-semibold">${from} → ${to}</div><div class="item-meta">${fecha}</div><div class="small text-muted">${desc}</div></li>`;
        }).join('');
      }).catch(()=>{ list.innerHTML = '<li class="text-danger">No se pudo cargar el historial</li>'; });
    const modal = new bootstrap.Modal(document.getElementById('historialModal'));
    modal.show();
  }

  function openSancionModal(btn){
    const action = btn.getAttribute('data-sanction');
    const rep = btn.getAttribute('data-reporte');
    const form = document.getElementById('sancionForm');
    form.setAttribute('action', action);
    document.getElementById('sancionReporte').textContent = rep;
    form.reset();
    const modal = new bootstrap.Modal(document.getElementById('sancionModal'));
    modal.show();
  }
</script>
@endpush
