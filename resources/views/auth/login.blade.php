@extends('layouts.app')

@section('title', 'Iniciar Sesión - Colegio')

@section('content')
<style>
    /* Fondo con degradado y animación suave */
    body {
        background: linear-gradient(135deg, #5f2c82, #49a09d);
        background-size: 200% 200%;
        animation: gradientShift 8s ease infinite;
        min-height: 100vh;
    }

    @keyframes gradientShift {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    /* Tarjeta con efecto glassmorphism */
    .login-card {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(12px);
        border-radius: 20px;
        box-shadow: 0 8px 32px rgba(31, 38, 135, 0.3);
        color: #fff;
        transition: all 0.3s ease;
    }

    .login-card:hover {
        box-shadow: 0 12px 40px rgba(31, 38, 135, 0.45);
    }

    .form-control {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        border-radius: 12px;
        color: #fff;
    }

    .form-control::placeholder {
        color: rgba(255, 255, 255, 0.6);
    }

    .form-label {
        color: #e0e0e0;
        font-weight: 500;
    }

    .btn-primary {
        background: linear-gradient(90deg, #6a11cb, #2575fc);
        border: none;
        border-radius: 12px;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background: linear-gradient(90deg, #2575fc, #6a11cb);
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(100, 100, 255, 0.3);
    }

    .text-muted a {
        color: #a9baff !important;
    }

    .login-icon {
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, #6a11cb, #2575fc);
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        color: white;
        font-size: 2rem;
        margin: 0 auto 1rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
    }

    .alert-danger {
        background: rgba(255, 0, 60, 0.2);
        color: #ffe1e1;
        border: none;
        border-radius: 10px;
    }

    label small {
        color: #ccc;
    }
</style>

<div class="d-flex align-items-center justify-content-center min-vh-100">
    <div class="login-card p-5" style="width: 100%; max-width: 420px;">
        <div class="text-center mb-4">
            <div class="login-icon">
                <i class="fas fa-book"></i>
            </div>
            <h3 class="fw-bold mb-1 text-white">Colegio</h3>
            <p class="text-light opacity-75">Inicia sesión en tu cuenta</p>
        </div>

        {{-- Errores --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0 small">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Formulario --}}
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">
                    <i class="fas fa-envelope me-1"></i> Correo electrónico
                </label>
                <input type="email" 
                       class="form-control @error('email') is-invalid @enderror"
                       id="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       required 
                       autocomplete="email" 
                       autofocus
                       placeholder="ejemplo@correo.com">
                @error('email')
                    <div class="invalid-feedback text-light small">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">
                    <i class="fas fa-lock me-1"></i> Contraseña
                </label>
                <div class="input-group">
                    <input type="password"
                           class="form-control @error('password') is-invalid @enderror"
                           id="password" 
                           name="password" 
                           required 
                           autocomplete="current-password"
                           placeholder="••••••••">
                    <button class="btn btn-outline-light" type="button" id="togglePassword" style="border-radius: 0 12px 12px 0;">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                @error('password')
                    <div class="invalid-feedback text-light small">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label text-light small" for="remember">Recordarme</label>
                </div>
                <a href="#" class="small text-decoration-none text-light">¿Olvidaste tu contraseña?</a>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-sign-in-alt me-2"></i> Iniciar Sesión
                </button>
            </div>
        </form>

        <div class="text-center mt-4">
            <small class="text-light">
                ¿No tienes una cuenta? 
                <a href="{{ route('register') }}" class="fw-semibold text-decoration-none">Regístrate aquí</a>
            </small>
        </div>
    </div>
</div>

{{-- Script mostrar/ocultar contraseña --}}
@push('scripts')
<script>
document.getElementById('togglePassword').addEventListener('click', function () {
    const input = document.getElementById('password');
    const icon = this.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
});
</script>
@endpush
@endsection
