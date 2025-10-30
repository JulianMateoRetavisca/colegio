@extends('layouts.app')

@section('title', 'Crear Docente - Colegio')

@section('content')
<style>
    .stellar-docentes-create {
        background: #f8f9fa;
        min-height: 100vh;
        font-family: 'Source Sans Pro', Helvetica, sans-serif;
    }

    .stellar-main-container {
        margin-left: 260px;
        transition: margin-left 0.3s ease;
    }

    .stellar-main-container.sidebar-collapsed {
        margin-left: 0;
    }

    .stellar-form-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        padding: 2.5rem;
        margin: 2rem;
    }

    .stellar-form-header {
        text-align: center;
        margin-bottom: 2.5rem;
        padding-bottom: 1.5rem;
        border-bottom: 2px solid #ecf0f1;
    }

    .stellar-form-title {
        color: #2c3e50;
        font-weight: 700;
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }

    .stellar-form-subtitle {
        color: #7f8c8d;
        font-size: 1.1rem;
    }

    .stellar-form-group {
        margin-bottom: 1.5rem;
    }

    .stellar-form-label {
        color: #2c3e50;
        font-weight: 600;
        margin-bottom: 0.5rem;
        display: block;
    }

    .stellar-form-input {
        border: 2px solid #e8e8e8;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        font-size: 1rem;
        transition: all 0.3s ease;
        width: 100%;
    }

    .stellar-form-input:focus {
        border-color: #3498db;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        outline: none;
    }

    .stellar-form-input.error {
        border-color: #e74c3c;
        box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1);
    }

    .stellar-alert-success {
        background: linear-gradient(45deg, #27ae60, #2ecc71);
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        margin-bottom: 2rem;
        border: none;
        box-shadow: 0 4px 12px rgba(39, 174, 96, 0.3);
    }

    .stellar-alert-danger {
        background: linear-gradient(45deg, #e74c3c, #c0392b);
        color: white;
        padding: 1.5rem;
        border-radius: 8px;
        margin-bottom: 2rem;
        border: none;
        box-shadow: 0 4px 12px rgba(231, 76, 60, 0.3);
    }

    .stellar-alert-danger ul {
        margin: 0;
        padding-left: 1rem;
    }

    .stellar-alert-danger li {
        margin-bottom: 0.25rem;
    }

    .stellar-btn-primary {
        background: linear-gradient(45deg, #3498db, #2980b9);
        border: none;
        border-radius: 8px;
        color: white;
        padding: 0.75rem 2rem;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .stellar-btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(52, 152, 219, 0.4);
        color: white;
        text-decoration: none;
    }

    .stellar-btn-secondary {
        background: transparent;
        border: 2px solid #95a5a6;
        border-radius: 8px;
        color: #95a5a6;
        padding: 0.75rem 2rem;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .stellar-btn-secondary:hover {
        background: #95a5a6;
        color: white;
        text-decoration: none;
        transform: translateY(-2px);
    }

    .stellar-form-actions {
        display: flex;
        gap: 1rem;
        justify-content: flex-start;
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid #ecf0f1;
    }

    .stellar-input-icon {
        position: relative;
    }

    .stellar-input-icon i {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #7f8c8d;
        z-index: 2;
    }

    .stellar-input-icon .stellar-form-input {
        padding-left: 3rem;
    }

    .stellar-password-strength {
        margin-top: 0.5rem;
        font-size: 0.875rem;
    }

    .stellar-password-weak { color: #e74c3c; }
    .stellar-password-medium { color: #f39c12; }
    .stellar-password-strong { color: #27ae60; }

    @media (max-width: 768px) {
        .stellar-main-container {
            margin-left: 0 !important;
        }
        
        .stellar-form-card {
            margin: 1rem;
            padding: 1.5rem;
        }
        
        .stellar-form-actions {
            flex-direction: column;
        }
        
        .stellar-btn-primary,
        .stellar-btn-secondary {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="stellar-docentes-create">
    @include('partials.sidebar')
    
    <div class="stellar-main-container" id="mainContainer">
        <div class="stellar-form-card">
            <!-- Header del formulario -->
            <div class="stellar-form-header">
                <h1 class="stellar-form-title">
                    <i class="fas fa-chalkboard-teacher me-2"></i>
                    Crear Nuevo Docente
                </h1>
                <p class="stellar-form-subtitle">
                    Complete la información del nuevo docente del sistema
                </p>
            </div>

            <!-- Mensajes de alerta -->
            @if(session('success'))
                <div class="stellar-alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="stellar-alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Por favor, corrige los siguientes errores:</strong>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Formulario -->
            <form action="{{ route('docentes.store') }}" method="POST" id="docenteForm">
                @csrf
                
                <!-- Campo Nombre -->
                <div class="stellar-form-group">
                    <label class="stellar-form-label">
                        <i class="fas fa-user me-1"></i>
                        Nombre Completo
                    </label>
                    <div class="stellar-input-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" 
                               name="name" 
                               value="{{ old('name') }}" 
                               class="stellar-form-input @error('name') error @enderror" 
                               placeholder="Ingrese el nombre completo del docente"
                               required>
                    </div>
                    @error('name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Campo Email -->
                <div class="stellar-form-group">
                    <label class="stellar-form-label">
                        <i class="fas fa-envelope me-1"></i>
                        Correo Electrónico
                    </label>
                    <div class="stellar-input-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               class="stellar-form-input @error('email') error @enderror" 
                               placeholder="ejemplo@colegio.com"
                               required>
                    </div>
                    @error('email')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Campo Contraseña -->
                <div class="stellar-form-group">
                    <label class="stellar-form-label">
                        <i class="fas fa-lock me-1"></i>
                        Contraseña
                    </label>
                    <div class="stellar-input-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" 
                               name="password" 
                               id="password"
                               class="stellar-form-input @error('password') error @enderror" 
                               placeholder="Ingrese una contraseña segura"
                               required
                               oninput="checkPasswordStrength(this.value)">
                    </div>
                    <div id="passwordStrength" class="stellar-password-strength"></div>
                    @error('password')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Campo Confirmar Contraseña -->
                <div class="stellar-form-group">
                    <label class="stellar-form-label">
                        <i class="fas fa-lock me-1"></i>
                        Confirmar Contraseña
                    </label>
                    <div class="stellar-input-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" 
                               name="password_confirmation" 
                               id="password_confirmation"
                               class="stellar-form-input" 
                               placeholder="Confirme la contraseña"
                               required
                               oninput="checkPasswordMatch()">
                    </div>
                    <div id="passwordMatch" class="stellar-password-strength"></div>
                </div>

                <!-- Acciones del formulario -->
                <div class="stellar-form-actions">
                    <button type="submit" class="stellar-btn-primary">
                        <i class="fas fa-save me-1"></i>
                        Crear Docente
                    </button>
                    <a href="{{ route('docentes.index') }}" class="stellar-btn-secondary">
                        <i class="fas fa-times me-1"></i>
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const mainContainer = document.getElementById('mainContainer');
    const sidebar = document.getElementById('appSidebar');

    function updateLayout() {
        if (sidebar && mainContainer) {
            const isCollapsed = sidebar.classList.contains('collapsed');
            mainContainer.classList.toggle('sidebar-collapsed', isCollapsed);
        }
    }

    const observer = new MutationObserver(updateLayout);
    if (sidebar) {
        observer.observe(sidebar, {
            attributes: true,
            attributeFilter: ['class']
        });
    }
    updateLayout();
});

function checkPasswordStrength(password) {
    const strengthElement = document.getElementById('passwordStrength');
    let strength = '';
    let strengthClass = '';

    if (password.length === 0) {
        strength = '';
    } else if (password.length < 6) {
        strength = 'Contraseña débil';
        strengthClass = 'stellar-password-weak';
    } else if (password.length < 8) {
        strength = 'Contraseña media';
        strengthClass = 'stellar-password-medium';
    } else {
        strength = 'Contraseña fuerte';
        strengthClass = 'stellar-password-strong';
    }

    strengthElement.innerHTML = strength;
    strengthElement.className = `stellar-password-strength ${strengthClass}`;
}

function checkPasswordMatch() {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('password_confirmation').value;
    const matchElement = document.getElementById('passwordMatch');

    if (confirmPassword.length === 0) {
        matchElement.innerHTML = '';
    } else if (password === confirmPassword) {
        matchElement.innerHTML = 'Las contraseñas coinciden';
        matchElement.className = 'stellar-password-strength stellar-password-strong';
    } else {
        matchElement.innerHTML = 'Las contraseñas no coinciden';
        matchElement.className = 'stellar-password-strength stellar-password-weak';
    }
}
</script>
@endsection