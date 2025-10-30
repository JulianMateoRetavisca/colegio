@extends('layouts.app')

@section('title', 'Iniciar Sesión - Colegio')

@section('content')
<!-- Estilos específicos para el login Stellar -->
<style>
    .stellar-login {
        background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Source Sans Pro', Helvetica, sans-serif;
    }

    .stellar-wrapper {
        width: 100%;
        max-width: 400px;
        margin: 0 auto;
    }

    .stellar-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .stellar-logo {
        width: 80px;
        height: 80px;
        background: linear-gradient(45deg, #3498db, #9b59b6);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

    .stellar-logo i {
        font-size: 2rem;
        color: white;
    }

    .stellar-header h1 {
        color: white;
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    }

    .stellar-header p {
        color: rgba(255,255,255,0.8);
        font-size: 1.1rem;
        margin-bottom: 0;
    }

    .stellar-card {
        background: white;
        border-radius: 12px;
        padding: 2.5rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        backdrop-filter: blur(10px);
    }

    .stellar-input {
        border: 2px solid #e8e8e8;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        font-size: 1rem;
        transition: all 0.3s ease;
        margin-bottom: 1.5rem;
    }

    .stellar-input:focus {
        border-color: #3498db;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        outline: none;
    }

    .stellar-label {
        color: #2c3e50;
        font-weight: 600;
        margin-bottom: 0.5rem;
        display: block;
    }

    .stellar-btn {
        background: linear-gradient(45deg, #3498db, #2980b9);
        border: none;
        border-radius: 8px;
        color: white;
        padding: 1rem 2rem;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        width: 100%;
        margin-bottom: 1rem;
    }

    .stellar-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(52, 152, 219, 0.4);
        background: linear-gradient(45deg, #2980b9, #3498db);
    }

    .stellar-alert {
        background: #e74c3c;
        color: white;
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        border-left: 4px solid #c0392b;
    }

    .stellar-alert ul {
        margin: 0;
        padding-left: 1rem;
    }

    .stellar-alert li {
        margin-bottom: 0.25rem;
    }

    .stellar-checkbox {
        display: flex;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .stellar-checkbox input {
        margin-right: 0.5rem;
        transform: scale(1.2);
    }

    .stellar-checkbox label {
        color: #2c3e50;
        font-weight: 500;
    }

    .stellar-footer {
        text-align: center;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid #ecf0f1;
    }

    .stellar-footer a {
        color: #3498db;
        text-decoration: none;
        font-weight: 600;
        transition: color 0.3s ease;
    }

    .stellar-footer a:hover {
        color: #2980b9;
    }

    .stellar-icon {
        color: #3498db;
        margin-right: 0.5rem;
    }

    .invalid-feedback {
        color: #e74c3c;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .is-invalid {
        border-color: #e74c3c !important;
    }
</style>

<div class="stellar-login">
    <div class="stellar-wrapper">
        <!-- Header al estilo Stellar -->
        <header class="stellar-header">
            <div class="stellar-logo">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <h1>Colegio</h1>
            <p>Inicia sesión en tu cuenta</p>
        </header>

        <!-- Card del formulario -->
        <div class="stellar-card">
            @if ($errors->any())
                <div class="stellar-alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <!-- Campo Email -->
                <div class="form-group">
                    <label for="email" class="stellar-label">
                        <i class="fas fa-envelope stellar-icon"></i>Correo electrónico
                    </label>
                    <input type="email" 
                           class="stellar-input @error('email') is-invalid @enderror" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           autocomplete="email" 
                           autofocus
                           placeholder="Ingresa tu correo">
                    @error('email')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Campo Contraseña -->
                <div class="form-group">
                    <label for="password" class="stellar-label">
                        <i class="fas fa-lock stellar-icon"></i>Contraseña
                    </label>
                    <input type="password" 
                           class="stellar-input @error('password') is-invalid @enderror" 
                           id="password" 
                           name="password" 
                           required 
                           autocomplete="current-password"
                           placeholder="Ingresa tu contraseña">
                    @error('password')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Checkbox Recordarme -->
                <div class="stellar-checkbox">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Recordarme</label>
                </div>

                <!-- Botón de envío -->
                <button type="submit" class="stellar-btn">
                    <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                </button>
            </form>

            <!-- Footer -->
            <div class="stellar-footer">
                <small>
                    ¿No tienes una cuenta? 
                    <a href="{{ route('register') }}">Regístrate aquí</a>
                </small>
            </div>
        </div>
    </div>
</div>
@endsection