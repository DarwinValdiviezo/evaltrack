<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse | EvalTrack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(120deg, #1e293b 0%, #2563eb 100%);
            display: flex;
            flex-direction: column;
        }
        .register-bg {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }
        .register-card {
            background: #fff;
            border-radius: 2rem;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.18);
            padding: 2.5rem 2.2rem 1.5rem 2.2rem;
            max-width: 520px;
            width: 100%;
            text-align: center;
            animation: fadeInUp 1.1s cubic-bezier(.39,.575,.565,1.000);
        }
        @keyframes fadeInUp {
            0% { opacity: 0; transform: translateY(40px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .register-logo {
            width: 70px;
            margin-bottom: 1.1rem;
            filter: drop-shadow(0 2px 8px #2563eb22);
            animation: fadeIn 1.2s;
        }
        .register-title {
            font-size: 2rem;
            font-weight: bold;
            color: #1e293b;
            margin-bottom: 0.3rem;
            letter-spacing: 0.5px;
        }
        .register-desc {
            color: #475569;
            margin-bottom: 2rem;
            font-size: 1.08rem;
        }
        .register-form .form-control,
        .register-form .form-select {
            border-radius: 1.5rem;
            padding: 0.8rem 1.2rem;
            font-size: 1.08rem;
        }
        .register-form .form-label {
            font-weight: 500;
            color: #2563eb;
        }
        .btn-register {
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
        .btn-register:hover {
            background: #1d4ed8;
            color: #fff;
            transform: translateY(-2px) scale(1.03);
            box-shadow: 0 4px 16px 0 rgba(37,99,235,0.13);
        }
        .register-footer-link {
            color: #2563eb;
            text-decoration: underline;
            font-size: 1rem;
        }
        .register-footer-link:hover {
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
            .register-card {
                padding: 1.5rem 0.5rem 1.2rem 0.5rem;
                max-width: 98vw;
            }
            .register-title {
                font-size: 1.3rem;
            }
            .register-desc {
                font-size: 0.98rem;
            }
        }
    </style>
</head>
<body>
<div class="register-bg">
    <div class="register-card">
        <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Logo" class="register-logo">
        <div class="register-title">EvalTrack</div>
        <div class="register-desc">Crea tu cuenta para comenzar</div>
        <form method="POST" action="{{ route('register') }}" class="register-form text-start" id="registerForm">
            @csrf
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input id="nombre" type="text" class="form-control @error('nombre') is-invalid @enderror" name="nombre" value="{{ old('nombre') }}" required autofocus pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+" maxlength="255">
                @error('nombre')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
                <div class="validation-message" id="nombreValidation"></div>
            </div>
            <div class="mb-3">
                <label for="apellido" class="form-label">Apellido</label>
                <input id="apellido" type="text" class="form-control @error('apellido') is-invalid @enderror" name="apellido" value="{{ old('apellido') }}" required pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+" maxlength="255">
                @error('apellido')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
                <div class="validation-message" id="apellidoValidation"></div>
            </div>
            <div class="mb-3">
                <label for="cedula" class="form-label">Cédula</label>
                <input id="cedula" type="text" class="form-control @error('cedula') is-invalid @enderror" name="cedula" value="{{ old('cedula') }}" required pattern="17[0-9]{8}" maxlength="10" placeholder="17XXXXXXXX">
                @error('cedula')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
                <div class="validation-message" id="cedulaValidation"></div>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Correo electrónico</label>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required pattern="[^@]+@evaltrack\.com$" placeholder="usuario@evaltrack.com">
                @error('email')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
                <div class="validation-message" id="emailValidation"></div>
            </div>
            <div class="mb-3">
                <label for="telefono" class="form-label">Teléfono</label>
                <input id="telefono" type="text" class="form-control @error('telefono') is-invalid @enderror" name="telefono" value="{{ old('telefono') }}" required pattern="09[0-9]{8}" maxlength="10" placeholder="09XXXXXXXX">
                @error('telefono')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
                <div class="validation-message" id="telefonoValidation"></div>
            </div>
            <div class="mb-3">
                <label for="fecha_nacimiento" class="form-label">Fecha de nacimiento</label>
                <input id="fecha_nacimiento" type="date" class="form-control @error('fecha_nacimiento') is-invalid @enderror" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}" required>
                @error('fecha_nacimiento')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
                <div class="validation-message" id="fechaValidation"></div>
            </div>
            <div class="mb-3">
                <label for="cargo" class="form-label">Cargo</label>
                <select id="cargo" class="form-select @error('cargo') is-invalid @enderror" name="cargo" required>
                    <option value="">Selecciona un cargo</option>
                    <option value="Desarrollador" {{ old('cargo') == 'Desarrollador' ? 'selected' : '' }}>Desarrollador</option>
                    <option value="Marketing" {{ old('cargo') == 'Marketing' ? 'selected' : '' }}>Marketing</option>
                    <option value="Finanzas" {{ old('cargo') == 'Finanzas' ? 'selected' : '' }}>Finanzas</option>
                    <option value="Analista" {{ old('cargo') == 'Analista' ? 'selected' : '' }}>Analista</option>
                </select>
                @error('cargo')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
                <div class="validation-message" id="cargoValidation"></div>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required minlength="8">
                @error('password')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
                <div class="validation-message" id="passwordValidation"></div>
            </div>
            <div class="mb-3">
                <label for="password-confirm" class="form-label">Confirmar contraseña</label>
                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required minlength="8">
                <div class="validation-message" id="passwordConfirmValidation"></div>
            </div>
            <button type="submit" class="btn btn-register w-100" id="submitBtn">Registrarse</button>
            <div class="d-flex justify-content-between mt-2">
                <a class="register-footer-link" href="{{ route('login') }}">¿Ya tienes cuenta? Inicia sesión</a>
            </div>
        </form>
    </div>
</div>
<footer>
    &copy; {{ date('Y') }} EvalTrack &mdash; Plataforma de Gestión de Talento Humano
</footer>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registerForm');
    const submitBtn = document.getElementById('submitBtn');
    
    // Validaciones en tiempo real
    const validations = {
        nombre: {
            pattern: /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/,
            message: 'El nombre solo puede contener letras y espacios.'
        },
        apellido: {
            pattern: /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/,
            message: 'El apellido solo puede contener letras y espacios.'
        },
        cedula: {
            pattern: /^17\d{8}$/,
            message: 'La cédula debe empezar con 17 y tener 10 dígitos.'
        },
        email: {
            pattern: /^[^@]+@evaltrack\.com$/,
            message: 'Solo se permiten correos con dominio @evaltrack.com.'
        },
        telefono: {
            pattern: /^09\d{8}$/,
            message: 'El teléfono debe empezar con 09 y tener 10 dígitos.'
        },
        cargo: {
            required: true,
            message: 'Debes seleccionar un cargo.'
        },
        password: {
            minLength: 8,
            message: 'La contraseña debe tener al menos 8 caracteres.'
        },
        'password-confirm': {
            match: 'password',
            message: 'Las contraseñas no coinciden.'
        }
    };

    // Función para validar fecha de nacimiento
    function validateFechaNacimiento(fecha) {
        const fechaNac = new Date(fecha);
        const hoy = new Date();
        const edadMin = new Date(hoy.getFullYear() - 19, hoy.getMonth(), hoy.getDate());
        const edadMax = new Date(hoy.getFullYear() - 50, hoy.getMonth(), hoy.getDate());
        
        if (fechaNac > edadMin) {
            return 'Debes tener al menos 19 años para registrarte.';
        }
        if (fechaNac < edadMax) {
            return 'No puedes tener más de 50 años para registrarte.';
        }
        return '';
    }

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

        if (fieldId === 'fecha_nacimiento') {
            const error = validateFechaNacimiento(value);
            if (error) {
                showValidation(fieldId, false, error);
                return false;
            } else {
                showValidation(fieldId, true, 'Fecha válida');
                return true;
            }
        }

        if (fieldId === 'password-confirm') {
            const password = document.getElementById('password').value;
            if (value !== password) {
                showValidation(fieldId, false, validation.message);
                return false;
            } else {
                showValidation(fieldId, true, 'Las contraseñas coinciden');
                return true;
            }
        }

        if (validation.required && !value) {
            showValidation(fieldId, false, 'Este campo es obligatorio');
            return false;
        }

        if (validation.pattern && !validation.pattern.test(value)) {
            showValidation(fieldId, false, validation.message);
            return false;
        }

        if (validation.minLength && value.length < validation.minLength) {
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

    // Validar fecha de nacimiento
    const fechaField = document.getElementById('fecha_nacimiento');
    if (fechaField) {
        fechaField.addEventListener('change', function() {
            validateField('fecha_nacimiento', this.value);
        });
    }

    // Validar confirmación de contraseña
    const passwordConfirmField = document.getElementById('password-confirm');
    if (passwordConfirmField) {
        passwordConfirmField.addEventListener('input', function() {
            validateField('password-confirm', this.value);
        });
    }

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
