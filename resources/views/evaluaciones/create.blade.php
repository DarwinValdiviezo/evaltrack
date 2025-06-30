@extends('layouts.sbadmin')
@section('title', 'Nueva Evaluación')
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h3 class="fw-bold mb-3">Crear Nueva Evaluación</h3>
                <form action="{{ route('evaluaciones.store') }}" method="POST">
                    @csrf
                    @include('evaluaciones.partials.form')
                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-success rounded-pill px-4">Guardar</button>
                        <a href="{{ route('evaluaciones.index') }}" class="btn btn-secondary rounded-pill px-4">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
