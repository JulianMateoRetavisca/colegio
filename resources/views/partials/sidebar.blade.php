@php
use App\Models\RolesModel;
$usuario = Auth::user();
$rol = null;
if ($usuario && $usuario->roles_id) {
    $rol = RolesModel::find($usuario->roles_id);
}
@endphp

<style>
.sidebar {
    background: #1f2937;
    color: #fff;
    height: 100vh;
    overflow-y: auto;
    transition: transform 0.2s ease;
    width: 220px;
    position: fixed;
    left: 0;
    top: 0;
    z-index: 9998;
    transform: translateX(0);
    will-change: transform;
}
.sidebar.collapsed {
    transform: translateX(-100%);
    visibility: hidden;
    transition: transform 0.2s ease, visibility 0s linear 0.2s;
}
.sidebar .nav-link {
    color: #e5e7eb;
}
.sidebar .nav-link .label {
    transition: opacity 0.15s ease;
}
.sidebar.collapsed .nav-link .label {
    opacity: 0;
    width: 0;
    display: none;
}
.sidebar.collapsed nav, .sidebar.collapsed .sidebar-header {
    display: none;
}
.sidebar.collapsed .sidebar-title {
    display: none;
}
.sidebar .sidebar-header {
    display:flex;
    align-items:center;
    justify-content:space-between;
}
.sidebar-toggle-btn {
    background: transparent;
    border: none;
    color: #fff;
    cursor: pointer;
    z-index: 20;
}
.sidebar-reset-btn {
    background: transparent;
    border: none;
    color: #fff;
    cursor: pointer;
    font-size: 0.95rem;
    padding-left: 6px;
}
.sidebar .me-2 { width: 20px; }
.sidebar-handle {
    position: fixed;
    left: 8px;
    top: 50%;
    transform: translateY(-50%);
    width: 40px;
    height: 40px;
    border-radius: 6px;
    background: #111827;
    color: #fff;
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 10000;
    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
    border: none;
    cursor: pointer;
}
.sidebar-handle:focus { outline: 2px solid rgba(255,255,255,0.15); }
</style>

<div class="sidebar" id="appSidebar" role="navigation" aria-label="Menú principal">
    <div class="p-3 sidebar-header" style="position:relative;">
        <h6 class="text-white-50 text-uppercase mb-0">
            <span class="sidebar-title">Menú Principal</span>
        </h6>
        <div style="position:absolute; right:8px; top:8px; display:flex; gap:6px; align-items:center;">
            <button id="sidebarReset" class="sidebar-reset-btn" aria-label="Restablecer menú" title="Restablecer menú">
                <i class="fas fa-sync-alt"></i>
            </button>
            <button id="sidebarToggle" class="sidebar-toggle-btn" aria-label="Alternar menú" title="Alternar menú">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </div>
    <nav class="nav flex-column px-3" id="sidebarNav">
        <a class="nav-link" href="{{ route('dashboard') }}">
            <i class="fas fa-tachometer-alt me-2"></i>
            <span class="label">Dashboard</span>
        </a>
        @if($rol)
            @if($rol->tienePermiso('gestionar_usuarios'))
                <a class="nav-link" href="#">
                    <i class="fas fa-users-cog me-2"></i>
                    <span class="label">Gestión de Usuarios</span>
                </a>
            @endif
            @if($rol->tienePermiso('gestionar_estudiantes'))
                <a class="nav-link" href="#">
                    <i class="fas fa-user-graduate me-2"></i>
                    <span class="label">Estudiantes</span>
                </a>
            @endif
            @if($rol->tienePermiso('gestionar_docentes'))
                <a class="nav-link"  href="{{ route('docentes.crear') }}">
                    <i class="fas fa-chalkboard-teacher me-2"></i>
                    <span class="label">Docentes</span>
                </a>
            @endif
            @if($rol->tienePermiso('gestionar_roles'))
                <a class="nav-link" href="{{ route('roles.index') }}">
                    <i class="fas fa-user-shield me-2"></i>
                    <span class="label">Roles y Permisos</span>
                </a>
            @endif
            @if($rol->tienePermiso('matricular_estudiantes'))
                <a class="nav-link" href="#">
                    <i class="fas fa-user-check me-2"></i>
                    <span class="label">Matricular Estudiantes</span>
                </a>
            @endif
            @if($rol->tienePermiso('gestionar_materias'))
                <a class="nav-link" href="#">
                    <i class="fas fa-book-open me-2"></i>
                    <span class="label">Materias</span>
                </a>
            @endif
            @if($rol->tienePermiso('gestionar_cursos'))
                <a class="nav-link" href="#">
                    <i class="fas fa-layer-group me-2"></i>
                    <span class="label">Cursos</span>
                </a>
            @endif
            @if($rol->tienePermiso('gestionar_horarios'))
                <a class="nav-link" href="#">
                    <i class="fas fa-calendar-alt me-2"></i>
                    <span class="label">Horarios</span>
                </a>
            @endif
            @if($rol->tienePermiso('gestionar_disciplina'))
                <a class="nav-link" href="#">
                    <i class="fas fa-gavel me-2"></i>
                    <span class="label">Disciplina</span>
                </a>
            @endif
            @if($rol->tienePermiso('ver_reportes_generales'))
                <a class="nav-link" href="#">
                    <i class="fas fa-chart-bar me-2"></i>
                    <span class="label">Reportes</span>
                </a>
            @endif
            @if($rol->tienePermiso('gestionar_notas') || $rol->tienePermiso('registrar_notas') || $rol->tienePermiso('ver_notas'))
                <a class="nav-link" href="{{ route('notas.crear') }}">
                    <i class="fas fa-money-bill-wave me-2"></i>
                    <span class="label">Notas</span>
                </a>
            @endif
            @if($rol->tienePermiso('configurar_sistema'))
                <a class="nav-link" href="#">
                    <i class="fas fa-cog me-2"></i>
                    <span class="label">Configuración</span>
                </a>
            @endif
        @endif
    </nav>
</div>

<button id="sidebarHandle" class="sidebar-handle" aria-label="Abrir menú" title="Abrir menú">
    <i class="fas fa-bars"></i>
</button>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('appSidebar');
    const toggle = document.getElementById('sidebarToggle');
    const storageKey = 'app.sidebar.collapsed';
    const handle = document.getElementById('sidebarHandle');
    const resetBtn = document.getElementById('sidebarReset');

    function setCollapsed(collapsed) {
        if (collapsed) sidebar.classList.add('collapsed');
        else sidebar.classList.remove('collapsed');
        try { localStorage.setItem(storageKey, collapsed ? '1' : '0'); } catch(e) {}
    }

    function updateHandles(collapsed) {
        if (toggle) toggle.style.display = collapsed ? 'none' : 'inline-block';
        if (handle) handle.style.display = collapsed ? 'flex' : 'none';
    }

    try {
        const stored = localStorage.getItem(storageKey);
        const collapsed = stored === '1';
        setCollapsed(collapsed);
        updateHandles(collapsed);
    } catch(e) {
        updateHandles(false);
    }

    let touchStartX = null;
    window.addEventListener('touchstart', function (e) {
        if (e.touches && e.touches[0]) touchStartX = e.touches[0].clientX;
    }, {passive:true});
    window.addEventListener('touchend', function (e) {
        if (touchStartX !== null) {
            const touchEndX = (e.changedTouches && e.changedTouches[0]) ? e.changedTouches[0].clientX : null;
            if (touchEndX !== null) {
                const delta = touchEndX - touchStartX;
                if (touchStartX < 40 && delta > 80) {
                    setCollapsed(false);
                    updateHandles(false);
                }
            }
        }
        touchStartX = null;
    }, {passive:true});

    toggle.addEventListener('click', function () {
        const isCollapsed = sidebar.classList.contains('collapsed');
        const newState = !isCollapsed;
        setCollapsed(newState);
        updateHandles(newState);
    });

    handle.addEventListener('click', function () {
        setCollapsed(false);
        updateHandles(false);
    });

    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            try { localStorage.removeItem(storageKey); } catch(e) {}
            setCollapsed(false);
            updateHandles(false);
        });
    }
});
</script>
