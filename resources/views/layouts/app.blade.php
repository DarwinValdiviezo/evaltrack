<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <style>
        body { background: #f8fafc; }
        .sidebar { min-height: 100vh; background: #343a40; color: #fff; }
        .sidebar a { color: #fff; text-decoration: none; display: block; padding: 10px 20px; }
        .sidebar a.active, .sidebar a:hover { background: #495057; }
        .sidebar .sidebar-title { padding: 20px; font-weight: bold; font-size: 1.2rem; background: #23272b; }
        .sidebar .sidebar-footer { position: absolute; bottom: 0; width: 100%; padding: 10px 20px; background: #23272b; }
        .main-content { margin-left: 220px; }
        @media (max-width: 768px) { .sidebar { min-width: 100vw; position: static; } .main-content { margin-left: 0; } }
    </style>
</head>
<body>
    <div id="app">
        <div class="d-flex">
            <!-- Sidebar -->
            @auth
            <div class="sidebar d-flex flex-column position-fixed" style="width:220px;">
                <div class="sidebar-title">
                    {{ config('app.name', 'Laravel') }}
                </div>
                <a href="{{ url('/home') }}" class="{{ request()->is('home') ? 'active' : '' }}">
                    <i class="bi bi-house"></i> Inicio
                </a>
                @if(Auth::user()->hasRole('Administrador'))
                    <a href="{{ route('users.index') }}" class="{{ request()->is('users*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i> Usuarios
                    </a>
                    <a href="{{ route('roles.index') }}" class="{{ request()->is('roles*') ? 'active' : '' }}">
                        <i class="bi bi-shield-lock"></i> Roles
                    </a>
                    <a href="{{ route('empleados.index') }}" class="{{ request()->is('empleados*') ? 'active' : '' }}">
                        <i class="bi bi-person-badge"></i> Empleados
                    </a>
                    <a href="{{ route('eventos.index') }}" class="{{ request()->is('eventos*') ? 'active' : '' }}">
                        <i class="bi bi-calendar-event"></i> Eventos
                    </a>
                    <a href="{{ route('asistencias.index') }}" class="{{ request()->is('asistencias*') ? 'active' : '' }}">
                        <i class="bi bi-clipboard-check"></i> Asistencias
                    </a>
                    <a href="{{ route('evaluaciones.index') }}" class="{{ request()->is('evaluaciones*') ? 'active' : '' }}">
                        <i class="bi bi-clipboard-data"></i> Evaluaciones
                    </a>
                @endif
                @if(Auth::user()->hasRole('Gestor de Talento Humano'))
                    <a href="{{ route('empleados.index') }}" class="{{ request()->is('empleados*') ? 'active' : '' }}">
                        <i class="bi bi-person-badge"></i> Empleados
                    </a>
                    <a href="{{ route('eventos.index') }}" class="{{ request()->is('eventos*') ? 'active' : '' }}">
                        <i class="bi bi-calendar-event"></i> Eventos
                    </a>
                    <a href="{{ route('asistencias.index') }}" class="{{ request()->is('asistencias*') ? 'active' : '' }}">
                        <i class="bi bi-clipboard-check"></i> Asistencias
                    </a>
                    <a href="{{ route('evaluaciones.index') }}" class="{{ request()->is('evaluaciones*') ? 'active' : '' }}">
                        <i class="bi bi-clipboard-data"></i> Evaluaciones
                    </a>
                @endif
                @if(Auth::user()->hasRole('Empleado'))
                    <a href="{{ route('mis-asistencias') }}" class="{{ request()->is('mis-asistencias') ? 'active' : '' }}">
                        <i class="bi bi-calendar-check"></i> Mis Asistencias
                    </a>
                    <a href="{{ route('mis-evaluaciones') }}" class="{{ request()->is('mis-evaluaciones') ? 'active' : '' }}">
                        <i class="bi bi-clipboard-data"></i> Mis Evaluaciones
                    </a>
                    <a href="{{ route('empleados.mi-perfil') }}" class="{{ request()->is('mi-perfil') ? 'active' : '' }}">
                        <i class="bi bi-person"></i> Mi Perfil
                    </a>
                @endif
                <div class="sidebar-footer mt-auto">
                    <div class="small">{{ Auth::user()->email }}</div>
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="bi bi-box-arrow-right"></i> Cerrar sesión
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
            @endauth
            <!-- Main Content -->
            <div class="main-content flex-grow-1">
                <nav class="navbar navbar-light bg-white shadow-sm">
                    <div class="container-fluid">
                        <span class="navbar-brand mb-0 h1">@yield('title', 'Panel')</span>
                    </div>
                </nav>
                <div class="container-fluid mt-3">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</body>
</html>
