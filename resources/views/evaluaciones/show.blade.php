@extends('layouts.sbadmin')
@section('title', 'Detalle de Evaluación')
@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detalle de Evaluación</h1>
        <a href="{{ route('evaluaciones.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Información de la Evaluación</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>ID:</strong> {{ $evaluacion->id }}</p>
                    <p><strong>Empleado:</strong> {{ $evaluacion->empleado->nombre ?? 'N/A' }} {{ $evaluacion->empleado->apellido ?? '' }}</p>
                    <p><strong>Evento:</strong> {{ $evaluacion->evento->nombre ?? 'N/A' }}</p>
                    <p><strong>Título:</strong> {{ $evaluacion->titulo }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Fecha:</strong> {{ $evaluacion->fecha_evaluacion }}</p>
                    <p><strong>Estado:</strong> {{ $evaluacion->status }}</p>
                    <p><strong>Nota:</strong> {{ $evaluacion->nota ?? 'Sin calificar' }}</p>
                    <p><strong>Descripción:</strong> {{ $evaluacion->descripcion ?? 'Sin descripción' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 