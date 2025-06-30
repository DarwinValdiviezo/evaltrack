<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title', 'EvalTrack')</title>
    <!-- SB Admin 2 CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2@4.1.4/css/sb-admin-2.min.css" rel="stylesheet">
    <style>
        .sidebar-brand-text {
            font-weight: 700;
            font-size: 1.3rem;
            letter-spacing: 1px;
        }
        .bg-gradient-primary {
            background: linear-gradient(180deg, #1e293b 10%, #2563eb 100%) !important;
        }
        .sidebar .nav-item .nav-link {
            font-size: 1.05rem;
        }
        .sidebar .nav-item.active .nav-link, .sidebar .nav-item .nav-link.active {
            color: #2563eb !important;
            font-weight: 700;
        }
        .sidebar .nav-item .nav-link i {
            font-size: 1.1rem;
        }
        .topbar .nav-link {
            color: #1e293b !important;
        }
        .topbar .dropdown-menu {
            min-width: 10rem;
        }
    </style>
    @yield('css')
</head>
<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="/">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="sidebar-brand-text mx-3">EvalTrack</div>
            </a>
            <hr class="sidebar-divider my-0">
            <li class="nav-item @if(request()->is('home')) active @endif">
                <a class="nav-link" href="{{ route('home') }}">
                    <i class="fas fa-fw fa-home"></i>
                    <span>Inicio</span></a>
            </li>
            @if(Auth::user()->hasRole('Administrador'))
                <li class="nav-item @if(request()->is('users*')) active @endif">
                    <a class="nav-link" href="{{ route('users.index') }}">
                        <i class="fas fa-fw fa-users"></i>
                        <span>Usuarios</span></a>
                </li>
                <li class="nav-item @if(request()->is('roles*')) active @endif">
                    <a class="nav-link" href="{{ route('roles.index') }}">
                        <i class="fas fa-fw fa-user-shield"></i>
                        <span>Roles</span></a>
                </li>
                <li class="nav-item @if(request()->is('empleados*')) active @endif">
                    <a class="nav-link" href="{{ route('empleados.index') }}">
                        <i class="fas fa-fw fa-id-badge"></i>
                        <span>Empleados</span></a>
                </li>
                <li class="nav-item @if(request()->is('eventos*')) active @endif">
                    <a class="nav-link" href="{{ route('eventos.index') }}">
                        <i class="fas fa-fw fa-calendar-alt"></i>
                        <span>Eventos</span></a>
                </li>
                <li class="nav-item @if(request()->is('asistencias*')) active @endif">
                    <a class="nav-link" href="{{ route('asistencias.index') }}">
                        <i class="fas fa-fw fa-clipboard-check"></i>
                        <span>Asistencias</span></a>
                </li>
                <li class="nav-item @if(request()->is('evaluaciones*')) active @endif">
                    <a class="nav-link" href="{{ route('evaluaciones.index') }}">
                        <i class="fas fa-fw fa-clipboard-list"></i>
                        <span>Evaluaciones</span></a>
                </li>
            @elseif(Auth::user()->hasRole('Gestor de Talento Humano'))
                <li class="nav-item @if(request()->is('empleados*')) active @endif">
                    <a class="nav-link" href="{{ route('empleados.index') }}">
                        <i class="fas fa-fw fa-id-badge"></i>
                        <span>Empleados</span></a>
                </li>
                <li class="nav-item @if(request()->is('eventos*')) active @endif">
                    <a class="nav-link" href="{{ route('eventos.index') }}">
                        <i class="fas fa-fw fa-calendar-alt"></i>
                        <span>Eventos</span></a>
                </li>
                <li class="nav-item @if(request()->is('asistencias*')) active @endif">
                    <a class="nav-link" href="{{ route('asistencias.index') }}">
                        <i class="fas fa-fw fa-clipboard-check"></i>
                        <span>Asistencias</span></a>
                </li>
                <li class="nav-item @if(request()->is('evaluaciones*')) active @endif">
                    <a class="nav-link" href="{{ route('evaluaciones.index') }}">
                        <i class="fas fa-fw fa-clipboard-list"></i>
                        <span>Evaluaciones</span></a>
                </li>
            @elseif(Auth::user()->hasRole('Empleado'))
                <li class="nav-item @if(request()->is('empleados/mi-perfil')) active @endif">
                    <a class="nav-link" href="{{ route('empleados.mi-perfil') }}">
                        <i class="fas fa-fw fa-user"></i>
                        <span>Mi Perfil</span></a>
                </li>
                <li class="nav-item @if(request()->is('mis-asistencias')) active @endif">
                    <a class="nav-link" href="{{ route('mis-asistencias') }}">
                        <i class="fas fa-fw fa-calendar-check"></i>
                        <span>Mis Asistencias</span></a>
                </li>
                <li class="nav-item @if(request()->is('mis-evaluaciones')) active @endif">
                    <a class="nav-link" href="{{ route('mis-evaluaciones') }}">
                        <i class="fas fa-fw fa-clipboard-list"></i>
                        <span>Mis Evaluaciones</span></a>
                </li>
                <li class="nav-item @if(request()->is('eventos*')) active @endif">
                    <a class="nav-link" href="{{ route('eventos.index') }}">
                        <i class="fas fa-fw fa-calendar-alt"></i>
                        <span>Eventos</span></a>
                </li>
            @endif
            <hr class="sidebar-divider d-none d-md-block">
            <div class="sidebar-footer text-center mb-2">
                <small>{{ Auth::user()->email }}</small>
            </div>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Cerrar sesión</span></a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </li>
        </ul>
        <!-- End of Sidebar -->
        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>
                    <span class="h5 mb-0 text-gray-800">@yield('title', 'Dashboard')</span>
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{ Auth::user()->username }}</span>
                                <img class="img-profile rounded-circle" src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->username) }}&background=2563eb&color=fff">
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="{{ route('empleados.mi-perfil') }}">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Mi Perfil
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Cerrar sesión
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>
                <!-- End of Topbar -->
                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>
            <!-- End of Main Content -->
            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>&copy; {{ date('Y') }} EvalTrack &mdash; Plataforma de Gestión de Talento Humano</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->
        </div>
        <!-- End of Content Wrapper -->
    </div>
    <!-- End of Page Wrapper -->
    <!-- SB Admin 2 JS (CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2@4.1.4/js/sb-admin-2.min.js"></script>
    @yield('js')
</body>
</html> 