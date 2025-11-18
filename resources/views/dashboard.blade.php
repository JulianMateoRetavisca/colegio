@extends('layouts.app')


@section('title', 'Dashboard - Colegio')

@section('content')

<div class="dashboard-wrapper container-fluid py-4">
    <div class="dashboard-header mb-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div>
            <h2 class="dashboard-title mb-1">¡Bienvenido, Administrador!</h2>
            <p class="dashboard-subtitle mb-0">Resumen operacional y acciones rápidas</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-sm btn-outline-primary" id="refreshMetrics"><i class="fas fa-sync me-1"></i>Actualizar</button>
            <button class="btn btn-sm btn-outline-secondary" id="toggleDark"><i class="fas fa-moon me-1"></i>Modo oscuro</button>
        </div>
    </div>

    <!-- Grid métricas principales -->
    <div class="row g-3 mb-4" id="metricsGrid">
        <div class="col-6 col-xl-3">
            <div class="metric-tile">
                <div class="metric-icon bg-gradient-info"><i class="fas fa-users"></i></div>
                <div class="metric-body">
                    <span class="metric-label">Usuarios</span>
                    <span class="metric-value">{{ $usuarios ?? '--' }}</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="metric-tile">
                <div class="metric-icon bg-gradient-success"><i class="fas fa-user-graduate"></i></div>
                <div class="metric-body">
                    <span class="metric-label">Estudiantes</span>
                    <span class="metric-value">{{ $estudiantes ?? '--' }}</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="metric-tile">
                <div class="metric-icon bg-gradient-warning"><i class="fas fa-tasks"></i></div>
                <div class="metric-body">
                    <span class="metric-label">Pendientes</span>
                    <span class="metric-value">{{ $pendientes ?? '--' }}</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="metric-tile">
                <div class="metric-icon bg-gradient-primary"><i class="fas fa-comments"></i></div>
                <div class="metric-body">
                    <span class="metric-label">Citas Orientación</span>
                    <span class="metric-value">{{ $citas ?? '--' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones rápidas modernizadas -->
    <div class="quick-actions mb-4">
        <h5 class="section-title mb-3"><i class="fas fa-bolt me-2 text-primary"></i>Acciones Rápidas</h5>
        <div class="actions-grid">
            <button class="action-btn" data-action="nuevo-estudiante"><i class="fas fa-user-plus"></i><span>Nuevo Estudiante</span></button>
            <button class="action-btn" data-action="nuevo-docente"><i class="fas fa-chalkboard-teacher"></i><span>Nuevo Docente</span></button>
            <button class="action-btn" data-action="ver-reportes"><i class="fas fa-chart-line"></i><span>Ver Reportes</span></button>
            <button class="action-btn" data-action="ver-horarios"><i class="fas fa-calendar-alt"></i><span>Horarios</span></button>
        </div>
    </div>

    <!-- Social / integraciones estilo Uiverse ejemplo -->
    <div class="integration-card mb-4">
        <div class="uiverse-card" id="socialCard">
            <span>Social</span>
            <a class="social-link" href="#" title="YouTube"><i class="fab fa-youtube"></i></a>
            <a class="social-link" href="#" title="GitHub"><i class="fab fa-github"></i></a>
            <a class="social-link" href="#" title="Discord"><i class="fab fa-discord"></i></a>
            <a class="social-link" href="#" title="Docs"><i class="fas fa-book"></i></a>
        </div>
    </div>

    <!-- Actividad reciente -->
    <div class="recent-activity mb-4">
        <h5 class="section-title mb-3"><i class="fas fa-history me-2 text-primary"></i>Actividad Reciente</h5>
        <ul class="timeline" id="activityTimeline">
            <!-- Items llenados por JS o servidor -->
        </ul>
    </div>
</div>

@push('styles')
<style>
/* ===== Dashboard Redesign Styles (fase 1) ===== */
/* Fondo global ahora en layout */
.dashboard-title { font-weight:600; }
.dashboard-subtitle { color:#555; font-size:0.95rem; }

/* Métricas */
.metric-tile { display:flex; gap:12px; background:#fff; border-radius:14px; padding:14px 16px; box-shadow:0 4px 12px rgba(0,0,0,.06); position:relative; overflow:hidden; min-height:90px; }
.metric-tile:before { content:""; position:absolute; inset:0; background:linear-gradient(120deg,rgba(0,77,146,.08),rgba(0,77,146,0)); opacity:0; transition:.3s; }
.metric-tile:hover:before { opacity:1; }
.metric-icon { width:54px; height:54px; border-radius:12px; display:flex; align-items:center; justify-content:center; color:#fff; font-size:1.4rem; }
.bg-gradient-info { background:linear-gradient(135deg,#3b82f6,#06b6d4); }
.bg-gradient-success { background:linear-gradient(135deg,#16a34a,#22c55e); }
.bg-gradient-warning { background:linear-gradient(135deg,#f59e0b,#fbbf24); }
.bg-gradient-primary { background:linear-gradient(135deg,#004d92,#3775bb); }
.metric-body { display:flex; flex-direction:column; justify-content:center; }
.metric-label { font-size:.75rem; text-transform:uppercase; letter-spacing:.5px; color:#666; }
.metric-value { font-size:1.4rem; font-weight:600; color:#222; }

/* Acciones rápidas */
.quick-actions { background:#fff; border-radius:18px; padding:20px; box-shadow:0 5px 18px rgba(0,0,0,.07); }
.section-title { font-weight:600; }
.actions-grid { display:grid; grid-template-columns: repeat(auto-fit,minmax(160px,1fr)); gap:14px; }
.action-btn { display:flex; flex-direction:column; align-items:center; gap:8px; padding:18px 14px; background:#f4f6fa; border:none; border-radius:16px; font-size:.85rem; font-weight:500; color:#222; cursor:pointer; transition:.25s; position:relative; }
.action-btn i { font-size:1.4rem; color:#004d92; }
.action-btn:hover { background:#e7eef7; box-shadow:0 6px 16px rgba(0,0,0,.08); transform:translateY(-3px); }
.action-btn:active { transform:translateY(0); }

/* Uiverse social card adaptada */
.integration-card { display:flex; }
.uiverse-card { position:relative; display:flex; align-items:center; justify-content:center; background:#e7e7e7; box-shadow:0 1px 3px rgba(0,0,0,.12),0 1px 2px rgba(0,0,0,.24); transition:all .3s cubic-bezier(.25,.8,.25,1); overflow:hidden; height:60px; width:100%; max-width:420px; border-radius:14px; }
.uiverse-card::before,.uiverse-card::after { position:absolute; display:flex; align-items:center; width:50%; height:100%; transition:.25s linear; z-index:1; content:""; }
.uiverse-card::before { left:0; justify-content:flex-end; background:#4d60b6; }
.uiverse-card::after { right:0; justify-content:flex-start; background:#4453a6; }
.uiverse-card:hover { box-shadow:0 14px 28px rgba(0,0,0,.25),0 10px 10px rgba(0,0,0,.22); }
.uiverse-card:hover span { opacity:0; z-index:-3; }
.uiverse-card:hover::before { opacity:.5; transform:translateY(-100%); }
.uiverse-card:hover::after { opacity:.5; transform:translateY(100%); }
.uiverse-card span { position:absolute; display:flex; align-items:center; justify-content:center; width:100%; height:100%; color:#fff; font-size:20px; font-weight:600; opacity:1; transition:opacity .25s; z-index:2; }
.uiverse-card .social-link { position:relative; display:flex; align-items:center; justify-content:center; width:25%; height:100%; color:#fff; font-size:22px; text-decoration:none; transition:.25s; }
.uiverse-card .social-link:hover { background:rgba(249,244,255,.85); color:#222; animation:bounce_613 .4s linear; }
@keyframes bounce_613 { 40%{transform:scale(1.3);}60%{transform:scale(.85);}80%{transform:scale(1.15);}100%{transform:scale(1);} }

/* Timeline actividad */
.recent-activity { background:#fff; border-radius:18px; padding:20px; box-shadow:0 5px 18px rgba(0,0,0,.07); }
.timeline { list-style:none; margin:0; padding:0; display:flex; flex-direction:column; gap:14px; }
.timeline li { position:relative; padding-left:28px; }
.timeline li:before { content:""; position:absolute; left:8px; top:6px; width:10px; height:10px; background:#004d92; border-radius:50%; box-shadow:0 0 0 4px rgba(0,77,146,.2); }
.timeline .item-title { font-weight:500; }
.timeline .item-meta { font-size:.7rem; text-transform:uppercase; color:#666; letter-spacing:.5px; }

/* Dark mode (placeholder) */
body.dark .metric-tile, body.dark .quick-actions, body.dark .recent-activity { background:#1f2530; box-shadow:none; }
body.dark .dashboard-subtitle, body.dark .metric-label, body.dark .timeline .item-meta { color:#bbb; }
body.dark .metric-value, body.dark .dashboard-title { color:#f5f5f5; }

@media (max-width: 768px){ .dashboard-header { flex-direction:column; align-items:flex-start; } }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded',()=>{
  const toggleDark = document.getElementById('toggleDark');
  if(toggleDark){ toggleDark.addEventListener('click',()=>{ document.body.classList.toggle('dark'); }); }
});
</script>
@endpush
@endsection
