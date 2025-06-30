<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido | EvalTrack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(120deg, #1e293b 0%, #2563eb 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .welcome-section {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }
        .welcome-card {
            background: #fff;
            border-radius: 2rem;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.18);
            padding: 3.5rem 3rem 2.5rem 3rem;
            max-width: 520px;
            width: 100%;
            text-align: center;
            position: relative;
            animation: fadeInUp 1.1s cubic-bezier(.39,.575,.565,1.000);
        }
        @keyframes fadeInUp {
            0% { opacity: 0; transform: translateY(40px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .welcome-title {
            font-size: 2.5rem;
            font-weight: bold;
            color: #1e293b;
            margin-bottom: 0.7rem;
            letter-spacing: 0.5px;
        }
        .welcome-desc {
            color: #475569;
            margin-bottom: 2.5rem;
            font-size: 1.15rem;
            line-height: 1.6;
        }
        .btn-main, .btn-outline {
            transition: all 0.2s;
            font-size: 1.15rem;
            border-radius: 2rem;
            padding: 0.85rem 2.5rem;
            font-weight: 600;
            margin-bottom: 0.7rem;
            box-shadow: 0 2px 8px 0 rgba(37,99,235,0.08);
        }
        .btn-main {
            background: #2563eb;
            color: #fff;
            border: none;
        }
        .btn-main:hover {
            background: #1d4ed8;
            color: #fff;
            transform: translateY(-2px) scale(1.03);
            box-shadow: 0 4px 16px 0 rgba(37,99,235,0.13);
        }
        .btn-outline {
            background: #fff;
            color: #2563eb;
            border: 2px solid #2563eb;
        }
        .btn-outline:hover {
            background: #2563eb;
            color: #fff;
            transform: translateY(-2px) scale(1.03);
            box-shadow: 0 4px 16px 0 rgba(37,99,235,0.13);
        }
        .logo {
            width: 90px;
            margin-bottom: 1.5rem;
            filter: drop-shadow(0 2px 8px #2563eb22);
            animation: fadeIn 1.2s;
        }
        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }
        .divider {
            border-top: 1px solid #e5e7eb;
            margin: 2.2rem 0 1.5rem 0;
        }
        footer {
            background: #1e293b;
            color: #cbd5e1;
            text-align: center;
            padding: 1.2rem 0 0.7rem 0;
            font-size: 1rem;
            letter-spacing: 0.5px;
        }
        .footer-link {
            color: #60a5fa;
            text-decoration: underline;
        }
        .footer-link:hover {
            color: #fff;
        }
        @media (max-width: 600px) {
            .welcome-card {
                padding: 2.2rem 0.7rem 1.5rem 0.7rem;
                max-width: 98vw;
            }
            .welcome-title {
                font-size: 1.5rem;
            }
            .welcome-desc {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="welcome-section">
        <div class="welcome-card">
            <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Logo" class="logo">
            <div class="welcome-title">EvalTrack</div>
            <div class="welcome-desc">
                Plataforma empresarial para la administración eficiente de <b>eventos, asistencias y evaluaciones</b>.<br>
                Optimiza el desarrollo profesional y la gestión de tu equipo.<br>
            </div>
            <a href="{{ route('login') }}" class="btn btn-main w-100 mb-2">Iniciar sesión</a>
            <a href="{{ route('register') }}" class="btn btn-outline w-100">Registrarse</a>
            <div class="divider"></div>
            <div style="font-size: 0.98rem; color: #64748b;">
                ¿Necesitas ayuda? <a href="mailto:soporte@empresa.com" class="footer-link">Contacta soporte</a>
            </div>
        </div>
    </div>
    <footer>
        &copy; {{ date('Y') }} EvalTrack &mdash; Plataforma de Gestión de Talento Humano
    </footer>
</body>
</html>
