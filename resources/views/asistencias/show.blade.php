@extends('layouts.sbadmin')
@section('title', 'Detalle de Asistencia')
@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detalle de Asistencia</h1>
        <a href="{{ route('asistencias.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Información de la Asistencia</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>ID:</strong> {{ $asistencia->id }}</p>
                    <p><strong>Empleado:</strong> {{ $asistencia->empleado->nombre ?? 'N/A' }} {{ $asistencia->empleado->apellido ?? '' }}</p>
                    <p><strong>Evento:</strong> {{ $asistencia->evento->nombre ?? 'N/A' }}</p>
                    <p><strong>Fecha:</strong> {{ $asistencia->fecha_asistencia }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Hora:</strong> {{ $asistencia->hora_asistencia }}</p>
                    <p><strong>Estado:</strong> {{ $asistencia->status }}</p>
                    <p><strong>Asistió:</strong> {{ $asistencia->asistio }}</p>
                    <p><strong>Comentario:</strong> {{ $asistencia->comentario ?? 'Sin comentarios' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 