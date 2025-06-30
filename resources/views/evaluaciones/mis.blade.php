@extends('layouts.sbadmin')
@section('title', 'Mis Evaluaciones')
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="font-size: 2rem;">Mis Evaluaciones</h2>
        <div class="text-muted" style="font-size: 1rem;">Gestiona y visualiza tus evaluaciones asignadas</div>
    </div>
</div>
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif
<div class="table-responsive">
    <table class="table table-hover align-middle shadow-sm rounded" style="background: #fff; border-radius: 1rem;">
        <thead class="thead-light">
            <tr style="background: #f8fafc;">
                <th>ID</th>
                <th>Título</th>
                <th>Fecha</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($evaluaciones as $eva)
                <tr>
                    <td>{{ $eva->id }}</td>
                    <td>{{ $eva->titulo }}</td>
                    <td>{{ $eva->fecha_evaluacion }}</td>
                    <td>
                        @if($eva->status === 'Pendiente')
                            <span class="badge badge-warning text-white">Pendiente de Asistencia</span>
                        @elseif($eva->status === 'Disponible')
                            <span class="badge badge-info">Disponible</span>
                        @elseif($eva->status === 'Completada')
                            <span class="badge badge-primary">Completada</span>
                        @elseif($eva->status === 'Calificada')
                            <span class="badge badge-success">{{ $eva->nota }}/10</span>
                        @else
                            <span class="badge badge-secondary">{{ $eva->status }}</span>
                        @endif
                    </td>
                    <td>
                        @if($eva->status === 'Pendiente')
                            <a href="{{ route('evaluaciones.responder', $eva) }}" class="btn btn-sm btn-outline-primary rounded-pill" title="Responder evaluación">
                                <i class="fas fa-edit"></i> Responder
                            </a>
                        @elseif($eva->status === 'Disponible')
                            <a href="{{ route('evaluaciones.responder', $eva) }}" class="btn btn-sm btn-outline-primary rounded-pill" title="Responder evaluación">
                                <i class="fas fa-edit"></i> Responder
                            </a>
                        @elseif($eva->status === 'Completada')
                            <span class="text-success"><i class="fas fa-check-circle"></i> Completada</span>
                        @elseif($eva->status === 'Calificada')
                            <span class="text-success"><i class="fas fa-star"></i> {{ $eva->nota }}/10</span>
                        @else
                            <span class="text-secondary"><i class="fas fa-info-circle"></i> {{ $eva->status }}</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="d-flex justify-content-center">
    {{ $evaluaciones->links() }}
</div>
@endsection
