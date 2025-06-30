<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión | EvalTrack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(120deg, #1e293b 0%, #2563eb 100%);
            display: flex;
            flex-direction: column;
        }
        .login-bg {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }
        .login-card {
            background: #fff;
            border-radius: 2rem;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.18);
            padding: 3rem 2.5rem 2.2rem 2.5rem;
            max-width: 420px;
            width: 100%;
            text-align: center;
            animation: fadeInUp 1.1s cubic-bezier(.39,.575,.565,1.000);
        }
        @keyframes fadeInUp {
            0% { opacity: 0; transform: translateY(40px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .login-logo {
            width: 70px;
            margin-bottom: 1.1rem;
            filter: drop-shadow(0 2px 8px #2563eb22);
            animation: fadeIn 1.2s;
        }
        .login-title {
            font-size: 2rem;
            font-weight: bold;
            color: #1e293b;
            margin-bottom: 0.3rem;
            letter-spacing: 0.5px;
        }
        .login-desc {
            color: #475569;
            margin-bottom: 2rem;
            font-size: 1.08rem;
        }
        .login-form .form-control {
            border-radius: 1.5rem;
            padding: 0.8rem 1.2rem;
            font-size: 1.08rem;
        }
        .login-form .form-label {
            font-weight: 500;
            color: #2563eb;
        }
        .btn-login {
            background: #2563eb;
            color: #fff;
            font-weight: 600;
            border-radius: 2rem;
            padding: 0.75rem 2.5rem;
            font-size: 1.1rem;
            margin-top: 0.7rem;
            margin-bottom: 0.5rem;
            transition: background 0.2s, transform 0.2s;
            box-shadow: 0 2px 8px 0 rgba(37,99,235,0.08);
        }
        .btn-login:hover {
            background: #1d4ed8;
            color: #fff;
            transform: translateY(-2px) scale(1.03);
            box-shadow: 0 4px 16px 0 rgba(37,99,235,0.13);
        }
        .login-footer-link {
            color: #2563eb;
            text-decoration: underline;
            font-size: 1rem;
        }
        .login-footer-link:hover {
            color: #1e293b;
        }
        .validation-message {
            font-size: 0.85rem;
            margin-top: 0.25rem;
        }
        .validation-message.valid {
            color: #198754;
        }
        .validation-message.invalid {
            color: #dc3545;
        }
        .form-control.is-valid {
            border-color: #198754;
            box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
        }
        .form-control.is-invalid {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
        footer {
            background: #1e293b;
            color: #cbd5e1;
            text-align: center;
            padding: 1.2rem 0 0.7rem 0;
            font-size: 1rem;
            letter-spacing: 0.5px;
        }
        @media (max-width: 600px) {
            .login-card {
                padding: 1.5rem 0.5rem 1.2rem 0.5rem;
                max-width: 98vw;
            }
            .login-title {
                font-size: 1.3rem;
            }
            .login-desc {
                font-size: 0.98rem;
            }
        }
    </style>
</head>
<body>
<div class="login-bg">
    <div class="login-card">
        <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Logo" class="login-logo">
        <div class="login-title">EvalTrack</div>
        <div class="login-desc">Inicia sesión para continuar</div>
        <form method="POST" action="{{ route('login') }}" class="login-form text-start" id="loginForm">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">Correo electrónico</label>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus pattern="[^@]+@evaltrack\.com$" placeholder="usuario@evaltrack.com">
                @error('email')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
                <div class="validation-message" id="emailValidation"></div>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
                @error('password')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
                <div class="validation-message" id="passwordValidation"></div>
            </div>
            <div class="mb-3 form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label" for="remember">Recordarme</label>
            </div>
            <button type="submit" class="btn btn-login w-100" id="submitBtn">Iniciar sesión</button>
            <div class="d-flex justify-content-between mt-2">
                <a class="login-footer-link" href="{{ route('register') }}">¿No tienes cuenta? Regístrate</a>
                <a class="login-footer-link" href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
            </div>
        </form>
    </div>
</div>
<footer>
    &copy; {{ date('Y') }} EvalTrack &mdash; Plataforma de Gestión de Talento Humano
</footer>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('loginForm');
    const submitBtn = document.getElementById('submitBtn');
    
    // Validaciones en tiempo real
    const validations = {
        email: {
            pattern: /^[^@]+@evaltrack\.com$/,
            message: 'Solo se permiten correos con dominio @evaltrack.com.'
        },
        password: {
            required: true,
            message: 'La contraseña es obligatoria.'
        }
    };

    // Función para mostrar mensaje de validación
    function showValidation(fieldId, isValid, message) {
        const field = document.getElementById(fieldId);
        const validationDiv = document.getElementById(fieldId + 'Validation');
        
        if (isValid) {
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
            validationDiv.textContent = '✓ ' + message;
            validationDiv.className = 'validation-message valid';
        } else {
            field.classList.remove('is-valid');
            field.classList.add('is-invalid');
            validationDiv.textContent = '✗ ' + message;
            validationDiv.className = 'validation-message invalid';
        }
    }

    // Función para validar campo
    function validateField(fieldId, value) {
        const validation = validations[fieldId];
        if (!validation) return true;

        if (validation.required && !value) {
            showValidation(fieldId, false, 'Este campo es obligatorio');
            return false;
        }

        if (validation.pattern && !validation.pattern.test(value)) {
            showValidation(fieldId, false, validation.message);
            return false;
        }

        showValidation(fieldId, true, 'Campo válido');
        return true;
    }

    // Agregar event listeners para validación en tiempo real
    Object.keys(validations).forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('input', function() {
                validateField(fieldId, this.value);
            });
            field.addEventListener('blur', function() {
                validateField(fieldId, this.value);
            });
        }
    });

    // Validar formulario antes de enviar
    form.addEventListener('submit', function(e) {
        let isValid = true;
        
        Object.keys(validations).forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field && !validateField(fieldId, field.value)) {
                isValid = false;
            }
        });

        if (!isValid) {
            e.preventDefault();
            alert('Por favor, corrige los errores en el formulario antes de continuar.');
        }
    });
});
</script>
</body>
</html>
