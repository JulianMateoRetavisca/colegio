<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Auth') - Colegio</title>
    <link rel="stylesheet" href="/css/app.css">
    <link rel="stylesheet" href="/css/auth.css">
</head>
<body>
    <div class="auth-page d-flex align-items-center justify-content-center">
        <div class="auth-card">
            <div class="auth-brand text-center mb-3">
                <h3 class="mb-0">Colegio</h3>
                <small class="text-muted">Panel de acceso</small>
            </div>

            @yield('content')

            <div class="text-center mt-3 text-muted small">
                &copy; {{ date('Y') }} Colegio
            </div>
        </div>
    </div>
    <script src="/js/app.js"></script>
</body>
</html>