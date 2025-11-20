@php
use App\Models\RolesModel;
$usuario = Auth::user();
$rol = null;
if ($usuario && $usuario->roles_id) {
    $rol = RolesModel::find($usuario->roles_id);
}
$active = function ($patterns) {
    foreach ((array)$patterns as $p) {
        if (request()->routeIs($p) || request()->is($p)) return ' active';
    }
    return '';
};
@endphp

<style>
/* =============================================== */
/* Corrección: navbar height como variable global  */
/* =============================================== */
:root {
  --navbar-height: 56px;
}

/* Contenedor fijo para ubicar la tarjeta como sidebar */
.sidebar { /* conservado por compatibilidad */
  position: fixed;
  left: 0;
  top: var(--navbar-height);
  width: 260px;
  height: calc(100vh - var(--navbar-height));
  padding: 0;
  background: transparent;
  z-index: 9998;
}
.sidebar-fixed {
  position: fixed;
  left: 0;
  top: var(--navbar-height); /* justo debajo de la navbar */
  width: 260px;
  height: calc(100vh - var(--navbar-height));
  background: transparent;
  overflow: hidden; /* el card maneja el overflow */
  z-index: 9998;
}

/* Header y acciones dentro de la tarjeta */
.nav-card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 8px;
}
.nav-actions { display:flex; gap:6px; }
.nav-btn {
  background: transparent;
  color: #fff;
  border: 1px solid rgba(255,255,255,0.2);
  border-radius: 6px;
  padding: 4px 8px;
  cursor: pointer;
}
.nav-btn:hover { background: rgba(255,255,255,0.1); }

/* Estilos del componente tipo tarjeta (proporcionados) */
.card.nav-card {
  width: 100%;
  height: 100%;
  border-radius: 0px;
  background: rgb(27, 26, 26);
  color: white;
  font-weight: 600;
  font-size: 1.2em;
  padding: 15px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  box-shadow: -5px 5px 1px 0px #004d92;
  overflow: hidden;
}
.card.nav-card > span {
  display: block;
  margin-bottom: 8px;
}
.card__container {
  display: flex;
  flex-direction: column;
  gap: 2px;
}
.element {
  color: grey;
  font-size: .8em;
  padding: 6px 15px;
  border-left: 2px solid grey;
  cursor: pointer;
  text-decoration: none;
  display: block;
  border-radius: 6px 0 0 6px;
}
.element.active {
  background-color: #004d92;
  border-left: 2px solid #8cb4ff;
  color: azure;
}
.element:hover:not(.active) {
  color: #3775bb;
}

/* Mantener fija en móviles también */
@media (max-width: 992px) {
  .sidebar,
  .sidebar-fixed { 
    position: fixed; 
    left: 0; 
    top: var(--navbar-height); 
    width: 260px; 
    height: calc(100vh - var(--navbar-height)); 
  }
  .card.nav-card { width: 100%; height: 100%; border-radius: 0; }
}

/* Nota: padding/espaciado del contenido se maneja en el layout (.app-main) */

/* Estado colapsado: oculta la sidebar fuera de pantalla y muestra el handle */
body.sidebar-collapsed .sidebar-fixed { transform: translateX(-100%); }
.sidebar-handle {
  display: none;
  position: fixed;
  left: 10px;
  top: calc(var(--navbar-height) + 10px);
  z-index: 10000;
  background: #1b1a1a;
  color: #fff;
  border: 1px solid #004d92;
  box-shadow: -3px 3px 1px 0px #004d92;
  border-radius: 6px;
  padding: 6px 10px;
  cursor: pointer;
}
body.sidebar-collapsed .sidebar-handle { display: inline-flex; align-items:center; gap:6px; }
</style>

<div class="sidebar-fixed">
  <div class="card nav-card">
    <div class="nav-card-header">
      <span>Menú</span>
      <div class="nav-actions">
        <button id="sidebarCollapseBtn" class="nav-btn" title="Colapsar sidebar" aria-label="Colapsar sidebar">
          <i class="fas fa-angle-double-left"></i>
        </button>
      </div>
    </div>
    <div class="card__container">
      <a class="element{{ $active('dashboard') }}" href="{{ route('dashboard') }}">Dashboard</a>
      @if($rol)
        @if($rol->tienePermiso('gestionar_usuarios'))
          <a class="element{{ $active('usuarios.*') }}" href="{{ route('usuarios.index') }}">Gestión de Usuarios</a>
        @endif
        @if($rol->tienePermiso('gestionar_estudiantes'))
          <a class="element{{ $active('estudiantes.*') }}" href="{{ route('estudiantes.mostrar') }}">Estudiantes</a>
        @endif
        @if($rol->tienePermiso('gestionar_docentes'))
          <a class="element{{ $active('docentes.*') }}" href="{{ route('docentes.index') }}">Docentes</a>
        @endif
        @if($rol->tienePermiso('gestionar_roles'))
          <a class="element{{ $active('roles.*') }}" href="{{ route('roles.index') }}">Roles y Permisos</a>
        @endif

        @php $rolUsuario = auth()->user()->roles_id ?? null; @endphp
        @if($rolUsuario == 7)
          <a class="element{{ $active('matricula.iniciar') }}" href="{{ route('matricula.iniciar') }}">Matricular Estudiantes</a>
        @endif
        @if($rolUsuario == 1 || $rolUsuario == 2)
          <a class="element{{ $active('matricula.aceptar') }}" href="{{ route('matricula.aceptar') }}">Gestionar Matrículas</a>
        @endif

        @if($rol->tienePermiso('gestionar_materias'))
          <a class="element{{ $active('materias.*') }}" href="{{ route('materias.index') }}">Materias</a>
        @endif
        @if($rol->tienePermiso('gestionar_cursos'))
          <a class="element{{ $active('grupos.*') }}" href="{{ route('grupos.index') }}">Cursos</a>
        @endif

        @php
          $nombreRolLower = $rol && isset($rol->nombre) ? strtolower($rol->nombre) : '';
          $rolAlto = in_array($nombreRolLower, ['administrador','rector','coordinador']);
          $esProfesor = $nombreRolLower === 'profesor';
        @endphp
        @if($rol->tienePermiso('gestionar_horarios') || $esProfesor || $rolAlto)
          <a class="element{{ $active('horarios.*') }}" href="{{ route('horarios.index') }}">Horarios</a>
        @endif

        @if($rol->tienePermiso('gestionar_disciplina'))
          <a class="element{{ $active('disciplina.reportes.*') }}" href="{{ route('disciplina.reportes.index') }}">Disciplina</a>
        @endif
        @if($rol->tienePermiso('reportar_incidente'))
          <a class="element{{ $active('disciplina.reportes.crear') }}" href="{{ route('disciplina.reportes.crear') }}">Reportar Incidente</a>
        @endif
        @if($rol->tienePermiso('ver_disciplina') && !$rol->tienePermiso('gestionar_disciplina'))
          <a class="element{{ $active('disciplina.reportes.mis') }}" href="{{ route('disciplina.reportes.mis') }}">Mis Reportes</a>
        @endif

        @if($rol->tienePermiso('gestionar_orientacion') || $rol->tienePermiso('ver_orientacion') || $rol->tienePermiso('solicitar_orientacion'))
          @php
            $nombreRolLower = strtolower($rol->nombre ?? '');
            $rutaOrientacion = route('orientacion.citas.vista');
            if(in_array($nombreRolLower,['admin','administrador','rector','coordinadoracademico','coordinadordisciplina'])){
              $rutaOrientacion = route('orientacion.citas.admin');
            }
          @endphp
          <a class="element{{ $active('orientacion.*') }}" href="{{ $rutaOrientacion }}">Orientación</a>
        @endif

        @php $esEstudianteEstricto = $rol && strtolower($rol->nombre ?? '') === 'estudiante'; @endphp
        @if($esEstudianteEstricto)
          <a class="element{{ $active(['notas.*']) }}" href="{{ route('notas.mostrar') }}">Mis Notas</a>
        @endif

        @php $esProfesor = $rol && (isset($rol->nombre) ? strtolower($rol->nombre) === 'profesor' : false); @endphp
        @if($esProfesor || $rol->tienePermiso('gestionar_notas'))
          <a class="element{{ $active('docentes.grupos') }}" href="{{ route('docentes.grupos') }}">Grupos & Notas</a>
          <a class="element{{ $active('docentes.notas.resumen') }}" href="{{ route('docentes.notas.resumen') }}">Notas (Resumen)</a>
        @endif

        @if($rol->tienePermiso('configurar_sistema'))
          <a class="element{{ $active('configuracion.*') }}" href="{{ route('configuracion.index') }}">Configuración</a>
        @endif
      @endif
    </div>
  </div>
</div>

<button id="sidebarHandle" class="sidebar-handle" title="Mostrar menú" aria-label="Mostrar menú">
  <i class="fas fa-bars"></i> Menú
  <span class="visually-hidden">Abrir menú lateral</span>
</button>

<script>
document.addEventListener('DOMContentLoaded', function(){
  const storageKey = 'app.sidebar.collapsed';
  const body = document.body;
  const collapseBtn = document.getElementById('sidebarCollapseBtn');
  const handleBtn = document.getElementById('sidebarHandle');

  function setCollapsed(state){
    if(state){ body.classList.add('sidebar-collapsed'); }
    else { body.classList.remove('sidebar-collapsed'); }
    try{ localStorage.setItem(storageKey, state ? '1' : '0'); }catch(e){}
  }

  // Estado inicial desde almacenamiento
  try {
    const stored = localStorage.getItem(storageKey);
    if(stored === '1') { body.classList.add('sidebar-collapsed'); }
  } catch(e) {}

  if(collapseBtn){
    collapseBtn.addEventListener('click', function(){ setCollapsed(true); });
  }
  if(handleBtn){
    handleBtn.addEventListener('click', function(){ setCollapsed(false); });
  }
});
</script>
