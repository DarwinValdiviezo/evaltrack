@extends('layouts.sbadmin')

@section('title', 'Dashboard')
@section('content')
<div class="row">
@if(Auth::user()->hasRole('Administrador'))
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Usuarios</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ \App\Models\User::count() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
                <a href="{{ route('users.index') }}" class="btn btn-primary btn-block mt-3">Ver usuarios</a>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Roles</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ \App\Models\Role::count() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-shield fa-2x text-gray-300"></i>
                    </div>
                </div>
                <a href="{{ route('roles.index') }}" class="btn btn-success btn-block mt-3">Ver roles</a>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Empleados</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ \App\Models\Employee::count() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-id-badge fa-2x text-gray-300"></i>
                    </div>
                </div>
                <a href="{{ route('empleados.index') }}" class="btn btn-info btn-block mt-3">Ver empleados</a>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Eventos</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ \App\Models\Evento::count() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                    </div>
                </div>
                <a href="{{ route('eventos.index') }}" class="btn btn-warning btn-block mt-3 text-white">Ver eventos</a>
            </div>
        </div>
    </div>
@elseif(Auth::user()->hasRole('Gestor de Talento Humano'))
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Próximos Eventos</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ \App\Models\Evento::activos()->count() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                    </div>
                </div>
                <a href="{{ route('eventos.index') }}" class="btn btn-warning btn-block mt-3 text-white">Ver eventos</a>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Asistencias Pendientes</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ \App\Models\Asistencia::pendientes()->count() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-check fa-2x text-gray-300"></i>
                    </div>
                </div>
                <a href="{{ route('asistencias.index') }}" class="btn btn-primary btn-block mt-3">Ver asistencias</a>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Evaluaciones por Calificar</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ \App\Models\Evaluacion::pendientes()->count() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                    </div>
                </div>
                <a href="{{ route('evaluaciones.index') }}" class="btn btn-success btn-block mt-3">Ver evaluaciones</a>
            </div>
        </div>
    </div>
@elseif(Auth::user()->hasRole('Empleado'))
    @php
        $empleado = \App\Models\Employee::where('email', Auth::user()->email)->first();
        $asistencias = $empleado ? $empleado->asistencias() : collect();
        $evaluaciones = $empleado ? $empleado->evaluaciones() : collect();
    @endphp
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Asistencias Confirmadas</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $asistencias->where('asistio', 'Sí')->count() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                    </div>
                </div>
                <a href="{{ route('mis-asistencias') }}" class="btn btn-success btn-block mt-3">Ver asistencias</a>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Asistencias Pendientes</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $asistencias->where('asistio', 'Pendiente')->count() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
                <a href="{{ route('mis-asistencias') }}" class="btn btn-warning btn-block mt-3 text-white">Confirmar</a>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Evaluaciones Pendientes</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $evaluaciones->where('status', 'Pendiente')->count() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                    </div>
                </div>
                <a href="{{ route('mis-evaluaciones') }}" class="btn btn-primary btn-block mt-3">Responder</a>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Promedio Evaluaciones</div>
                        @php
                            $evaluacionesCalificadas = $evaluaciones->where('status', 'Calificada');
                            $promedio = $evaluacionesCalificadas->count() > 0 ? $evaluacionesCalificadas->avg('nota') : 0;
                        @endphp
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($promedio, 1) }}/100</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-star fa-2x text-gray-300"></i>
                    </div>
                </div>
                <a href="{{ route('mis-evaluaciones') }}" class="btn btn-info btn-block mt-3">Ver evaluaciones</a>
            </div>
        </div>
    </div>
    <div class="col-xl-6 col-md-12 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Próximos Eventos</h6>
            </div>
            <div class="card-body">
                @php
                    $proximosEventos = \App\Models\Evento::where('fecha_evento', '>=', now()->toDateString())
                        ->where('estado', '!=', 'Cancelado')
                        ->orderBy('fecha_evento')
                        ->limit(5)
                        ->get();
                @endphp
                @if($proximosEventos->count() > 0)
                    <ul class="list-group list-group-flush">
                        @foreach($proximosEventos as $evento)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $evento->nombre }}</strong><br>
                                    <small class="text-muted">{{ $evento->fecha_evento->format('d/m/Y') }} - {{ $evento->hora_inicio }}</small>
                                </div>
                                <span class="badge badge-primary">{{ $evento->tipo }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted">No hay eventos próximos programados.</p>
                @endif
            </div>
        </div>
    </div>
    <div class="col-xl-6 col-md-12 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Acciones Rápidas</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('asistencias.registrar') }}" class="btn btn-success mb-2">
                        <i class="fas fa-plus-circle"></i> Registrar Asistencia
                    </a>
                    <a href="{{ route('mis-asistencias') }}" class="btn btn-primary mb-2">
                        <i class="fas fa-calendar-check"></i> Ver Mis Asistencias
                    </a>
                    <a href="{{ route('mis-evaluaciones') }}" class="btn btn-warning mb-2 text-white">
                        <i class="fas fa-clipboard-list"></i> Ver Mis Evaluaciones
                    </a>
                    <a href="{{ route('empleados.mi-perfil') }}" class="btn btn-info mb-2">
                        <i class="fas fa-user"></i> Editar Mi Perfil
                    </a>
                </div>
            </div>
        </div>
    </div>
@endif
</div>
@endsection
