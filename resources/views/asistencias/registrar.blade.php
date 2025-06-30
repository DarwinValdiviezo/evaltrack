@extends('layouts.sbadmin')
@section('title', 'Registrar Asistencia')
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Registrar Asistencia</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('asistencias.registrar') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="evento_id" class="form-label">Evento</label>
                        <select name="evento_id" id="evento_id" class="form-control @error('evento_id') is-invalid @enderror" required>
                            <option value="">Seleccionar evento</option>
                            @foreach($eventos as $evento)
                                <option value="{{ $evento->id }}" {{ old('evento_id') == $evento->id ? 'selected' : '' }}>
                                    {{ $evento->nombre }} - {{ $evento->fecha_evento->format('d/m/Y') }} {{ $evento->hora_inicio }}
                                </option>
                            @endforeach
                        </select>
                        @error('evento_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="fecha_asistencia" class="form-label">Fecha de Asistencia</label>
                        <input type="date" name="fecha_asistencia" id="fecha_asistencia" class="form-control @error('fecha_asistencia') is-invalid @enderror" value="{{ old('fecha_asistencia', now()->toDateString()) }}" required>
                        @error('fecha_asistencia')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="hora_asistencia" class="form-label">Hora de Asistencia</label>
                        <input type="time" name="hora_asistencia" id="hora_asistencia" class="form-control @error('hora_asistencia') is-invalid @enderror" value="{{ old('hora_asistencia', now()->format('H:i')) }}" required>
                        @error('hora_asistencia')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="comentario" class="form-label">Comentario (opcional)</label>
                        <textarea name="comentario" id="comentario" rows="3" class="form-control @error('comentario') is-invalid @enderror" placeholder="Agregar comentario sobre la asistencia...">{{ old('comentario') }}</textarea>
                        @error('comentario')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Nota:</strong> Al registrar tu asistencia, se marcará automáticamente como confirmada.
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('mis-asistencias') }}" class="btn btn-secondary rounded-pill px-4">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                        <button type="submit" class="btn btn-success rounded-pill px-4">
                            <i class="fas fa-check-circle"></i> Registrar Asistencia
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 