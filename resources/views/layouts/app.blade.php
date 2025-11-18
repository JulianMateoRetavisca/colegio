<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Colegio')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        /* 🎨 Variables globales de color */
        :root {
            --color-primario: #667eea;
            --color-secundario: #764ba2;
            --color-acento: #3498db;
            --color-fondo: #f8f9fa;
            --color-sidebar: #2c3e50;
            --color-hover: #34495e;
            --color-texto: #2c3e50;
            --color-blanco: #ffffff;
            --navbar-height: 56px; /* altura navbar fija */
            --navbar-h: 56px; /* alias para compatibilidad */
        }

        /* 🧩 Estilos generales */
        body {
            background: linear-gradient(135deg,#dfe5ff 0%,#ebe6ff 100%);
            color: var(--color-texto);
            font-family: "Segoe UI", sans-serif;
            min-height: 100vh;
        }

        .navbar-brand {
            font-weight: bold;
            color: var(--color-texto) !important;
        }

        .login-container {
            min-height: 100vh;
            background: linear-gradient(135deg, var(--color-primario) 0%, var(--color-secundario) 100%);
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        .sidebar {
            background: var(--color-sidebar);
            min-height: calc(100vh - 56px);
        }

        .sidebar .nav-link {
            color: #ecf0f1;
            padding: 12px 20px;
            border-radius: 5px;
            margin: 2px 0;
        }

        .sidebar .nav-link:hover {
            background: var(--color-hover);
            color: var(--color-blanco);
        }

        .sidebar .nav-link.active {
            background: var(--color-acento);
            color: var(--color-blanco);
        }

        .main-content {
            background: var(--color-fondo);
            min-height: calc(100vh - 56px);
        }
        .app-main { padding-top: var(--navbar-height); }
    </style>
    
    <link rel="stylesheet" href="{{ asset('css/admin-theme.css') }}">
    @stack('styles')
    @stack('head')
</head>
<body>
    @auth
        @unless(View::hasSection('hide_navbar'))
            @include('partials.navbar')
        @endunless
        @unless(View::hasSection('hide_sidebar'))
            @include('partials.sidebar')
        @endunless
    @endauth
    <main class="app-main @auth {{ View::hasSection('hide_sidebar') ? '' : 'with-sidebar' }} @endauth">
        @yield('content')
    </main>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <style>
        .app-main.with-sidebar { padding-left:260px; }
        /* Mantener padding también en móviles para no solapar la sidebar fija */
        @media (max-width: 992px) { .app-main.with-sidebar { padding-left:260px; } }
        /* Cuando la sidebar está colapsada, el contenido ocupa todo el ancho */
        body.sidebar-collapsed .app-main.with-sidebar { padding-left: 0; }
        @media (max-width: 992px) { body.sidebar-collapsed .app-main.with-sidebar { padding-left: 0; } }
    </style>
    @stack('scripts')
</body>
</html>
