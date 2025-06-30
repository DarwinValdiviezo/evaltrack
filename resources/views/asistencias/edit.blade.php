@extends('layouts.sbadmin')
@section('title', 'Editar Asistencia')
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h3 class="fw-bold mb-3">Editar Asistencia</h3>
                <form action="{{ route('asistencias.update', $asistencia) }}" method="POST">
                    @csrf
                    @method('PUT')
                    @include('asistencias.partials.form')
                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-success rounded-pill px-4">Actualizar</button>
                        <a href="{{ route('asistencias.index') }}" class="btn btn-secondary rounded-pill px-4">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
