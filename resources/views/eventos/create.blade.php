@extends('layouts.sbadmin')

@section('title', 'Crear Evento')
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Crear Nuevo Evento</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('eventos.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre del Evento</label>
                        <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" value="{{ old('nombre') }}" required>
                        @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control @error('descripcion') is-invalid @enderror" id="descripcion" name="descripcion" rows="3" required>{{ old('descripcion') }}</textarea>
                        @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha_evento" class="form-label">Fecha del Evento</label>
                                <input type="date" class="form-control @error('fecha_evento') is-invalid @enderror" id="fecha_evento" name="fecha_evento" value="{{ old('fecha_evento') }}" required>
                                @error('fecha_evento')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tipo" class="form-label">Tipo de Evento</label>
                                <select class="form-control @error('tipo') is-invalid @enderror" id="tipo" name="tipo" required>
                                    <option value="">Selecciona un tipo</option>
                                    <option value="Capacitación" {{ old('tipo') == 'Capacitación' ? 'selected' : '' }}>Capacitación</option>
                                    <option value="Taller" {{ old('tipo') == 'Taller' ? 'selected' : '' }}>Taller</option>
                                    <option value="Conferencia" {{ old('tipo') == 'Conferencia' ? 'selected' : '' }}>Conferencia</option>
                                    <option value="Reunión" {{ old('tipo') == 'Reunión' ? 'selected' : '' }}>Reunión</option>
                                    <option value="Otro" {{ old('tipo') == 'Otro' ? 'selected' : '' }}>Otro</option>
                                </select>
                                @error('tipo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="hora_inicio" class="form-label">Hora de Inicio</label>
                                <input type="time" class="form-control @error('hora_inicio') is-invalid @enderror" id="hora_inicio" name="hora_inicio" value="{{ old('hora_inicio') }}" required>
                                @error('hora_inicio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="hora_fin" class="form-label">Hora de Fin</label>
                                <input type="time" class="form-control @error('hora_fin') is-invalid @enderror" id="hora_fin" name="hora_fin" value="{{ old('hora_fin') }}" required>
                                @error('hora_fin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="lugar" class="form-label">Lugar</label>
                        <input type="text" class="form-control @error('lugar') is-invalid @enderror" id="lugar" name="lugar" value="{{ old('lugar') }}" required>
                        @error('lugar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('eventos.index') }}" class="btn btn-secondary rounded-pill px-4">Cancelar</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Crear Evento</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 