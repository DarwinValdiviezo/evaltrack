@extends('layouts.sbadmin')
@section('title', 'Editar Evaluación')
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h3 class="fw-bold mb-3">Editar Evaluación</h3>
                <form action="{{ route('evaluaciones.update', $evaluacion) }}" method="POST">
                    @csrf
                    @method('PUT')
                    @include('evaluaciones.partials.form')
                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-success rounded-pill px-4">Actualizar</button>
                        <a href="{{ route('evaluaciones.index') }}" class="btn btn-secondary rounded-pill px-4">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
