@extends('layouts.app')


@section('title', 'Dashboard - Colegio')

@section('content')

<div class="dashboard-container py-4">
    <!-- Navbar & Sidebar ahora gestionados por layout -->
    <div class="container-fluid">
        <!-- Encabezado -->
        <div class="welcome-card p-4 mb-4">
            <h2 class="fw-bold text-dark mb-1">¡Bienvenido, Administrador del Sistema!</h2>
            <p class="text-secondary mb-0">Gestiona tu colegio de manera eficiente</p>
        </div>

        <!-- Tarjetas de resumen -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="stat-card">
                    <i class="fas fa-file-alt icon text-primary"></i>
                    <h5 class="text-dark fw-bold">Documentos</h5>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <i class="fas fa-check-circle icon text-success"></i>
                    <h5 class="text-dark fw-bold">Aprobados</h5>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <i class="fas fa-clock icon text-warning"></i>
                    <h5 class="text-dark fw-bold">Pendientes</h5>
                </div>
            </div>
        </div>

        <!-- Acciones rápidas -->
        <div class="action-card p-4 mb-4">
            <h5 class="fw-semibold text-dark mb-3"><i class="fas fa-bolt me-2 text-primary"></i>Acciones Rápidas</h5>
            <div class="d-flex flex-wrap gap-3">
                <button class="btn btn-gradient px-4 py-2"><i class="fas fa-user-plus me-2"></i>Nuevo Estudiante</button>
                <button class="btn btn-outline-secondary px-4 py-2"><i class="fas fa-chalkboard-teacher me-2"></i>Nuevo Docente</button>
                <button class="btn btn-outline-secondary px-4 py-2"><i class="fas fa-chart-line me-2"></i>Ver Reportes</button>
            </div>
        </div>

        <!-- Actividad reciente -->
        <div class="recent-card p-4">
            <h5 class="fw-semibold text-dark mb-3"><i class="fas fa-history me-2 text-primary"></i>Actividad Reciente</h5>
            <ul class="list-unstyled mb-0">
            </ul>
        </div>
    </div>
</div>

<style>
    body {
        background: linear-gradient(135deg, #dce3ff 0%, #e6e0ff 100%);
        font-family: 'Poppins', sans-serif;
    }

    .dashboard-container {
        min-height: 100vh;
        padding-left: 260px; /* para respetar la sidebar existente */
    }

    .welcome-card {
        background: rgba(255, 255, 255, 0.6);
        backdrop-filter: blur(12px);
        border-radius: 18px;
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .stat-card {
        background: rgba(255, 255, 255, 0.65);
        border-radius: 18px;
        padding: 2rem;
        text-align: center;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease-in-out;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    .icon {
        font-size: 2.5rem;
        margin-bottom: 0.5rem;
    }

    .action-card, .recent-card {
        background: rgba(255, 255, 255, 0.65);
        border-radius: 18px;
        backdrop-filter: blur(10px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .btn-gradient {
        background: linear-gradient(135deg, #6b73ff, #a06bff);
        border: none;
        color: white !important;
        border-radius: 12px;
        font-weight: 500;
        transition: 0.3s ease;
    }

    .btn-gradient:hover {
        opacity: 0.9;
        transform: translateY(-2px);
    }

    .btn-outline-secondary {
        background: transparent;
        border: 2px solid #a4a4a4;
        border-radius: 12px;
        color: #333;
        font-weight: 500;
    }

    .btn-outline-secondary:hover {
        background: rgba(164, 164, 164, 0.1);
    }

    .text-dark {
        color: #2b2b2b !important;
    }

    .text-secondary {
        color: #5a5a5a !important;
    }

</style>
@endsection
