@php
use App\Models\RolesModel;
$usuario = Auth::user();
$rol = null;
if ($usuario && $usuario->roles_id) {
    $rol = RolesModel::find($usuario->roles_id);
}
@endphp

<style>
.stellar-sidebar {
    background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
    color: #fff;
    height: 100vh;
    overflow-y: auto;
    transition: transform 0.3s ease;
    width: 260px;
    position: fixed;
    left: 0;
    top: 0;
    z-index: 9998;
    transform: translateX(0);
    will-change: transform;
    box-shadow: 2px 0 10px rgba(0,0,0,0.1);
}

.stellar-sidebar.collapsed {
    transform: translateX(-100%);
    visibility: hidden;
    transition: transform 0.3s ease, visibility 0s linear 0.3s;
}

.stellar-sidebar .nav-link {
    color: #e5e7eb;
    padding: 0.75rem 1rem;
    margin: 0.25rem 0.5rem;
    border-radius: 8px;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    text-decoration: none;
}

.stellar-sidebar .nav-link:hover {
    background: rgba(52, 152, 219, 0.2);
    color: #3498db;
    transform: translateX(5px);
}

.stellar-sidebar .nav-link.active {
    background: linear-gradient(45deg, #3498db, #2980b9);
    color: white;
    box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
}

.stellar-sidebar .nav-link .label {
    transition: opacity 0.3s ease, margin-left 0.3s ease;
    font-weight: 500;
}

.stellar-sidebar.collapsed .nav-link .label {
    opacity: 0;
    width: 0;
    display: none;
}

.stellar-sidebar.collapsed nav, 
.stellar-sidebar.collapsed .stellar-sidebar-header {
    display: none;
}

.stellar-sidebar.collapsed .stellar-sidebar-title {
    display: none;
}

.stellar-sidebar-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.5rem 1rem 1rem;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    margin-bottom: 1rem;
}

.stellar-sidebar-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #ecf0f1;
    letter-spacing: 0.5px;
}

.stellar-sidebar-toggle-btn {
    background: rgba(255,255,255,0.1);
    border: none;
    color: #ecf0f1;
    cursor: pointer;
    width: 32px;
    height: 32px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.stellar-sidebar-toggle-btn:hover {
    background: rgba(255,255,255,0.2);
    transform: scale(1.1);
}

.stellar-sidebar-reset-btn {
    background: rgba(231, 76, 60, 0.1);
    border: none;
    color: #e74c3c;
    cursor: pointer;
    width: 28px;
    height: 28px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    transition: all 0.3s ease;
}

.stellar-sidebar-reset-btn:hover {
    background: rgba(231, 76, 60, 0.2);
    transform: scale(1.1);
}

.stellar-sidebar .nav-icon {
    width: 20px;
    text-align: center;
    margin-right: 12px;
    font-size: 1rem;
    transition: transform 0.3s ease;
}

.stellar-sidebar .nav-link:hover .nav-icon {
    transform: scale(1.1);
}

.stellar-sidebar-handle {
    position: fixed;
    left: 8px;
    top: 50%;
    transform: translateY(-50%);
    width: 44px;
    height: 44px;
    border-radius: 8px;
    background: linear-gradient(45deg, #3498db, #2980b9);
    color: #fff;
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 10000;
    box-shadow: 0 4px 12px rgba(52, 152, 219, 0.4);
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
}

.stellar-sidebar-handle:hover {
    transform: translateY(-50%) scale(1.1);
    box-shadow: 0 6px 16px rgba(52, 152, 219, 0.6);
}

.stellar-sidebar-handle:focus { 
    outline: 2px solid rgba(255,255,255,0.3); 
}

/* Scrollbar personalizado */
.stellar-sidebar::-webkit-scrollbar {
    width: 6px;
}

.stellar-sidebar::-webkit-scrollbar-track {
    background: rgba(255,255,255,0.1);
}

.stellar-sidebar::-webkit-scrollbar-thumb {
    background: rgba(255,255,255,0.3);
    border-radius: 3px;
}

.stellar-sidebar::-webkit-scrollbar-thumb:hover {
    background: rgba(255,255,255,0.5);
}

/* Grupos de menú */
.stellar-menu-group {
    margin: 1.5rem 0;
}

.stellar-menu-group-title {
    font-size: 0.8rem;
    text-transform: uppercase;
    color: #95a5a6;
    font-weight: 600;
    letter-spacing: 1px;
    padding: 0 1rem;
    margin-bottom: 0.5rem;
}

/* Indicador de página activa */
.stellar-nav-link.active::before {
    content: '';
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 4px;
    height: 20px;
    background: #3498db;
    border-radius: 0 4px 4px 0;
}
</style>

<div class="stellar-sidebar" id="appSidebar" role="navigation" aria-label="Menú principal">
    <!-- Header del Sidebar -->
    <div class="stellar-sidebar-header">
        <h6 class="stellar-sidebar-title">
            <span>Menú Principal</span>
        </h6>
        <div style="display: flex; gap: 6px; align-items: center;">
            <button id="sidebarReset" class="stellar-sidebar-reset-btn" aria-label="Restablecer menú" title="Restablecer menú">
                <i class="fas fa-sync-alt"></i>
            </button>
            <button id="sidebarToggle" class="stellar-sidebar-toggle-btn" aria-label="Alternar menú" title="Alternar menú">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </div>

    <!-- Navegación -->
    <nav class="nav flex-column" id="sidebarNav">
        <!-- Dashboard -->
        <a class="nav-link stellar-nav-link" href="{{ route('dashboard') }}">
            <i class="fas fa-tachometer-alt nav-icon"></i>
            <span class="label">Dashboard</span>
        </a>

        @if($rol)
        <!-- Gestión Académica -->
        <div class="stellar-menu-group">
            <div class="stellar-menu-group-title">Gestión Académica</div>
            
            @if($rol->tienePermiso('gestionar_estudiantes'))
            <a class="nav-link stellar-nav-link" href="{{ route('grupos.index') }}">
                <i class="fas fa-user-graduate nav-icon"></i>
                <span class="label">Estudiantes</span>
            </a>
            @endif

            @if($rol->tienePermiso('gestionar_docentes'))
            <a class="nav-link stellar-nav-link" href="{{ route('docentes.crear') }}">
                <i class="fas fa-chalkboard-teacher nav-icon"></i>
                <span class="label">Docentes</span>
            </a>
            @endif

            @if($rol->tienePermiso('matricular_estudiantes'))
            <a class="nav-link stellar-nav-link" href="#">
                <i class="fas fa-user-check nav-icon"></i>
                <span class="label">Matrículas</span>
            </a>
            @endif

            @if($rol->tienePermiso('gestionar_materias'))
            <a class="nav-link stellar-nav-link" href="#">
                <i class="fas fa-book-open nav-icon"></i>
                <span class="label">Materias</span>
            </a>
            @endif

            @if($rol->tienePermiso('gestionar_cursos'))
            <a class="nav-link stellar-nav-link" href="#">
                <i class="fas fa-layer-group nav-icon"></i>
                <span class="label">Cursos</span>
            </a>
            @endif
        </div>

        <!-- Administración -->
        <div class="stellar-menu-group">
            <div class="stellar-menu-group-title">Administración</div>
            
            @if($rol->tienePermiso('gestionar_usuarios'))
            <a class="nav-link stellar-nav-link" href="#">
                <i class="fas fa-users-cog nav-icon"></i>
                <span class="label">Usuarios</span>
            </a>
            @endif

            @if($rol->tienePermiso('gestionar_roles'))
            <a class="nav-link stellar-nav-link" href="{{ route('roles.index') }}">
                <i class="fas fa-user-shield nav-icon"></i>
                <span class="label">Roles</span>
            </a>
            @endif

            @if($rol->tienePermiso('gestionar_horarios'))
            <a class="nav-link stellar-nav-link" href="#">
                <i class="fas fa-calendar-alt nav-icon"></i>
                <span class="label">Horarios</span>
            </a>
            @endif

            @if($rol->tienePermiso('gestionar_disciplina'))
            <a class="nav-link stellar-nav-link" href="#">
                <i class="fas fa-gavel nav-icon"></i>
                <span class="label">Disciplina</span>
            </a>
            @endif
        </div>

        <!-- Reportes y Notas -->
        <div class="stellar-menu-group">
            <div class="stellar-menu-group-title">Reportes</div>
            
            @if($rol->tienePermiso('ver_reportes_generales'))
            <a class="nav-link stellar-nav-link" href="#">
                <i class="fas fa-chart-bar nav-icon"></i>
                <span class="label">Reportes</span>
            </a>
            @endif

            @if($rol->tienePermiso('gestionar_notas') || $rol->tienePermiso('registrar_notas') || $rol->tienePermiso('ver_notas'))
            <a class="nav-link stellar-nav-link" href="{{ route('notas.crear') }}">
                <i class="fas fa-money-bill-wave nav-icon"></i>
                <span class="label">Notas</span>
            </a>
            @endif
        </div>

        <!-- Sistema -->
        <div class="stellar-menu-group">
            <div class="stellar-menu-group-title">Sistema</div>
            
            @if($rol->tienePermiso('configurar_sistema'))
            <a class="nav-link stellar-nav-link" href="#">
                <i class="fas fa-cog nav-icon"></i>
                <span class="label">Configuración</span>
            </a>
            @endif
        </div>
        @endif
    </nav>
</div>

<!-- Botón para abrir sidebar cuando está colapsado -->
<button id="sidebarHandle" class="stellar-sidebar-handle" aria-label="Abrir menú" title="Abrir menú">
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
        if (collapsed) {
            sidebar.classList.add('collapsed');
        } else {
            sidebar.classList.remove('collapsed');
        }
        try { 
            localStorage.setItem(storageKey, collapsed ? '1' : '0'); 
        } catch(e) {}
    }

    function updateHandles(collapsed) {
        if (toggle) toggle.style.display = collapsed ? 'none' : 'flex';
        if (handle) handle.style.display = collapsed ? 'flex' : 'none';
    }

    // Cargar estado inicial
    try {
        const stored = localStorage.getItem(storageKey);
        const collapsed = stored === '1';
        setCollapsed(collapsed);
        updateHandles(collapsed);
    } catch(e) {
        updateHandles(false);
    }

    // Gestos táctiles
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

    // Event listeners
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
            try { 
                localStorage.removeItem(storageKey); 
            } catch(e) {}
            setCollapsed(false);
            updateHandles(false);
        });
    }

    // Marcar enlace activo
    const currentUrl = window.location.href;
    const navLinks = document.querySelectorAll('.stellar-nav-link');
    navLinks.forEach(link => {
        if (link.href === currentUrl) {
            link.classList.add('active');
        }
    });
});
</script>