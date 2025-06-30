@extends('layouts.sbadmin')
@section('title', 'Mis Asistencias')
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="font-size: 2rem;">Mis Asistencias</h2>
        <div class="text-muted" style="font-size: 1rem;">Gestiona y visualiza tu historial de asistencias</div>
    </div>
    <a href="{{ route('asistencias.registrar') }}" class="btn btn-primary rounded-pill mt-3 mt-md-0">
        <i class="fas fa-calendar-plus"></i> Registrar Asistencia
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

{{-- Tarjetas de Resumen --}}
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Confirmadas</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $asistencias->where('asistio', 'Sí')->count() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pendientes</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $asistencias->where('asistio', 'Pendiente')->count() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Ausencias</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $asistencias->where('asistio', 'No')->count() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-times-circle fa-2x text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $asistencias->count() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-check fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filtros --}}
<form method="GET" class="row g-2 mb-3">
    <div class="col-12 col-md-4">
        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Buscar por evento...">
    </div>
    <div class="col-12 col-md-3">
        <select name="estado" class="form-control">
            <option value="">Todos los estados</option>
            <option value="Sí" {{ request('estado') == 'Sí' ? 'selected' : '' }}>Confirmadas</option>
            <option value="No" {{ request('estado') == 'No' ? 'selected' : '' }}>Ausencias</option>
            <option value="Pendiente" {{ request('estado') == 'Pendiente' ? 'selected' : '' }}>Pendientes</option>
        </select>
    </div>
    <div class="col-12 col-md-2">
        <button class="btn btn-outline-primary w-100" type="submit">
            <i class="fas fa-search"></i> Buscar
        </button>
    </div>
    <div class="col-12 col-md-2">
        <a href="{{ route('mis-asistencias') }}" class="btn btn-outline-secondary w-100">
            Limpiar
        </a>
    </div>
</form>

{{-- Tabla de Asistencias --}}
<div class="card shadow-sm border-0">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-history"></i> Historial de Asistencias</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="thead-light">
                    <tr style="background: #f8fafc;">
                        <th>#</th>
                        <th>Evento</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Estado</th>
                        <th>Evaluación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($asistencias as $index => $asis)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $asis->evento->nombre ?? 'Evento no disponible' }}</strong><br>
                                <small class="text-muted">{{ $asis->evento->descripcion ?? '' }}</small>
                            </td>
                            <td>{{ $asis->fecha_asistencia }}</td>
                            <td>{{ $asis->hora_asistencia }}</td>
                            <td>
                                @if($asis->asistio === 'Sí')
                                    <span class="badge badge-success">Confirmada</span>
                                @elseif($asis->asistio === 'No')
                                    <span class="badge badge-danger">Ausente</span>
                                @else
                                    <span class="badge badge-warning text-white">Pendiente</span>
                                @endif
                            </td>
                            <td>
                                @if($asis->evento)
                                    @php
                                        $evaluacion = $empleado->evaluaciones()
                                                              ->where('evento_id', $asis->evento->id)
                                                              ->first();
                                    @endphp
                                    @if($evaluacion)
                                        @if($evaluacion->status === 'Pendiente')
                                            <a href="{{ route('evaluaciones.responder', $evaluacion) }}" 
                                               class="btn btn-sm btn-outline-primary rounded-pill">
                                                <i class="fas fa-edit"></i> Responder
                                            </a>
                                        @elseif($evaluacion->status === 'Calificada')
                                            <span class="badge badge-success">{{ $evaluacion->nota }}/10</span>
                                        @else
                                            <span class="badge badge-info">{{ $evaluacion->status }}</span>
                                        @endif
                                    @else
                                        <span class="text-muted"><i class="fas fa-minus"></i> Sin evaluación</span>
                                    @endif
                                @endif
                            </td>
                            <td>
                                @if($asis->status === 'Registrada' && $asis->asistio === 'Pendiente')
                                    <form action="{{ route('asistencias.confirmar', $asis) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-success rounded-pill">
                                            <i class="fas fa-check"></i> Confirmar
                                        </button>
                                    </form>
                                @endif
                                @if($asis->comentario)
                                    <button class="btn btn-sm btn-outline-info rounded-pill" data-toggle="tooltip" 
                                            title="{{ $asis->comentario }}">
                                        <i class="fas fa-comment"></i> Ver comentario
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-calendar-times fa-3x mb-3"></i>
                                    <p>No tienes asistencias registradas.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="d-flex justify-content-center mt-3">
    {{ $asistencias->appends(request()->query())->links() }}
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips de Bootstrap
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
@endsection
