@extends('layouts.app')

@section('title', 'Dashboard - Colegio')

@section('content')
<!-- Estilos específicos para el dashboard Stellar -->
<style>
    .stellar-dashboard {
        background: #f8f9fa;
        min-height: 100vh;
        font-family: 'Source Sans Pro', Helvetica, sans-serif;
    }

    /* Navbar Stellar */
    .stellar-navbar {
        background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%) !important;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        padding: 1rem 0;
    }

    .stellar-navbar .navbar-brand {
        font-weight: 700;
        font-size: 1.5rem;
        color: white !important;
    }

    .stellar-navbar .navbar-brand i {
        color: #9b59b6 !important;
    }

    .stellar-navbar .nav-link {
        color: rgba(255,255,255,0.9) !important;
        font-weight: 500;
        transition: color 0.3s ease;
    }

    .stellar-navbar .nav-link:hover {
        color: white !important;
    }

    .stellar-navbar .dropdown-menu {
        border: none;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        border-radius: 8px;
    }

    /* Main Content */
    .stellar-main-content {
        background: white;
        border-radius: 12px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        margin: 2rem 0;
        padding: 2rem;
        min-height: 70vh;
    }

    /* Welcome Section */
    .stellar-welcome {
        text-align: center;
        padding: 3rem 0;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        color: white;
        margin-bottom: 3rem;
    }

    .stellar-welcome h1 {
        font-weight: 700;
        margin-bottom: 0.5rem;
        font-size: 2.5rem;
    }

    .stellar-welcome p {
        font-size: 1.2rem;
        opacity: 0.9;
        margin-bottom: 0;
    }

    /* Quick Actions */
    .stellar-actions-section {
        margin-bottom: 3rem;
    }

    .stellar-section-title {
        color: #2c3e50;
        font-weight: 700;
        margin-bottom: 2rem;
        text-align: center;
        font-size: 1.8rem;
    }

    .stellar-action-card {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        text-align: center;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        border: 1px solid #ecf0f1;
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .stellar-action-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }

    .stellar-action-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.5rem;
        font-size: 2rem;
        background: linear-gradient(45deg, #3498db, #2980b9);
        color: white;
    }

    .stellar-action-icon.secondary {
        background: linear-gradient(45deg, #9b59b6, #8e44ad);
    }

    .stellar-action-icon.success {
        background: linear-gradient(45deg, #27ae60, #2ecc71);
    }

    .stellar-action-title {
        font-size: 1.3rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 1rem;
    }

    .stellar-action-description {
        color: #7f8c8d;
        margin-bottom: 1.5rem;
        flex-grow: 1;
    }

    .stellar-action-btn {
        background: linear-gradient(45deg, #3498db, #2980b9);
        border: none;
        border-radius: 8px;
        color: white;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-block;
    }

    .stellar-action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(52, 152, 219, 0.4);
        color: white;
        text-decoration: none;
    }

    .stellar-action-btn.outline {
        background: transparent;
        border: 2px solid #3498db;
        color: #3498db;
    }

    .stellar-action-btn.outline:hover {
        background: #3498db;
        color: white;
    }

    /* Recent Activity */
    .stellar-activity-section {
        margin-top: 3rem;
    }

    .stellar-activity-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        border: 1px solid #ecf0f1;
    }

    .stellar-card-header {
        background: linear-gradient(135deg, #3498db, #9b59b6);
        color: white;
        padding: 1.5rem 2rem;
        border-radius: 12px 12px 0 0;
    }

    .stellar-card-header h5 {
        margin: 0;
        font-weight: 600;
    }

    .stellar-card-body {
        padding: 2rem;
    }

    .stellar-empty-state {
        text-align: center;
        padding: 3rem 2rem;
        color: #7f8c8d;
    }

    .stellar-empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    /* Sidebar Container */
    .stellar-sidebar-container {
        background: #e9ebeeff;
        min-height: calc(100vh - 76px);
        padding: 0;
    }
</style>

<div class="stellar-dashboard">
    <!-- Navbar Stellar -->
    <nav class="navbar navbar-expand-lg navbar-dark stellar-navbar">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="fas fa-graduation-cap me-2"></i>Colegio
            </a>    
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i>{{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-user-cog me-1"></i>Perfil
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-cog me-1"></i>Configuración
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="{{ route('logout') }}" 
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt me-1"></i>Cerrar Sesión
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        @php
            $usuario = Auth::user();
            $rol = App\Models\RolesModel::find($usuario->roles_id);
        @endphp
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 p-0 stellar-sidebar-container">
                @include('partials.sidebar')
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <div class="stellar-main-content">
                    <!-- Welcome Banner -->
                    <div class="stellar-welcome">
                        <h1>¡Bienvenido, {{ Auth::user()->name }}!</h1>
                        <p>Gestiona tu colegio de manera eficiente</p>
                    </div>

                    <!-- Quick Actions -->
                    <div class="stellar-actions-section">
                        <h2 class="stellar-section-title">Acciones Rápidas</h2>
                        <div class="row">
                            @if($rol && $rol->tienePermiso('gestionar_estudiantes'))
                            <div class="col-md-4 mb-4">
                                <div class="stellar-action-card">
                                    <div class="stellar-action-icon">
                                        <i class="fas fa-user-plus"></i>
                                    </div>
                                    <h3 class="stellar-action-title">Gestión de Estudiantes</h3>
                                    <p class="stellar-action-description">
                                        Administra la información de los estudiantes, crea nuevos registros y actualiza datos existentes.
                                    </p>
                                    <a href="#" class="stellar-action-btn">
                                        <i class="fas fa-users me-2"></i>Gestionar Estudiantes
                                    </a>
                                </div>
                            </div>
                            @endif

                            @if($rol && $rol->tienePermiso('gestionar_docentes'))
                            <div class="col-md-4 mb-4">
                                <div class="stellar-action-card">
                                    <div class="stellar-action-icon secondary">
                                        <i class="fas fa-chalkboard-teacher"></i>
                                    </div>
                                    <h3 class="stellar-action-title">Gestión de Docentes</h3>
                                    <p class="stellar-action-description">
                                        Administra el personal docente, asigna materias y gestiona la información del profesorado.
                                    </p>
                                    <a href="{{ route('docentes.crear') }}" class="stellar-action-btn outline">
                                        <i class="fas fa-user-tie me-2"></i>Gestionar Docentes
                                    </a>
                                </div>
                            </div>
                            @endif

                            @if($rol && $rol->tienePermiso('ver_reportes_generales'))
                            <div class="col-md-4 mb-4">
                                <div class="stellar-action-card">
                                    <div class="stellar-action-icon success">
                                        <i class="fas fa-chart-line"></i>
                                    </div>
                                    <h3 class="stellar-action-title">Reportes y Análisis</h3>
                                    <p class="stellar-action-description">
                                        Genera reportes detallados y visualiza estadísticas importantes del colegio.
                                    </p>
                                    <a href="#" class="stellar-action-btn outline">
                                        <i class="fas fa-chart-bar me-2"></i>Ver Reportes
                                    </a>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="stellar-activity-section">
                        <div class="stellar-activity-card">
                            <div class="stellar-card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-history me-2"></i>Actividad Reciente
                                </h5>
                            </div>
                            <div class="stellar-card-body">
                                <div class="stellar-empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <h4>No hay actividad reciente</h4>
                                    <p>Las acciones que realices aparecerán aquí</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Logout Form -->
<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>
@endsection