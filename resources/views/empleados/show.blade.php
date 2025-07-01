@extends('layouts.sbadmin')
@section('title', 'Detalle de Empleado')
@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detalle de Empleado</h1>
        <a href="{{ route('empleados.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Información del Empleado</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>ID:</strong> {{ $empleado->id }}</p>
                    <p><strong>Nombre:</strong> {{ $empleado->nombre }}</p>
                    <p><strong>Apellido:</strong> {{ $empleado->apellido }}</p>
                    <p><strong>Cédula:</strong> {{ $empleado->cedula }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Email:</strong> {{ $empleado->email }}</p>
                    <p><strong>Teléfono:</strong> {{ $empleado->telefono ?? 'No especificado' }}</p>
                    <p><strong>Cargo:</strong> {{ $empleado->cargo }}</p>
                    <p><strong>Fecha de Nacimiento:</strong> {{ $empleado->fecha_nacimiento }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 