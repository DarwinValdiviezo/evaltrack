@extends('layouts.sbadmin')
@section('title', 'Calificar Evaluación')
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
            <div>
                <h2 class="fw-bold mb-1" style="font-size: 2rem;">Calificar Evaluación</h2>
                <div class="text-muted" style="font-size: 1rem;">Asigna calificación y comentarios a la evaluación completada</div>
            </div>
        </div>
        
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Información de la Evaluación</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <p><strong><i class="fas fa-user"></i> Empleado:</strong><br>{{ $evaluacion->empleado->nombre }} {{ $evaluacion->empleado->apellido }}</p>
                    </div>
                    <div class="col-md-4">
                        <p><strong><i class="fas fa-calendar"></i> Fecha:</strong><br>{{ $evaluacion->fecha_evaluacion }}</p>
                    </div>
                    <div class="col-md-4">
                        <p><strong><i class="fas fa-clipboard"></i> Título:</strong><br>{{ $evaluacion->titulo }}</p>
                    </div>
                </div>
                @if($evaluacion->descripcion)
                    <div class="mt-3">
                        <p><strong><i class="fas fa-align-left"></i> Descripción:</strong><br>{{ $evaluacion->descripcion }}</p>
                    </div>
                @endif
            </div>
        </div>

        @if($evaluacion->preguntas && count($evaluacion->preguntas) > 0)
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-question-circle"></i> Preguntas y Respuestas</h5>
                </div>
                <div class="card-body">
                    @foreach($evaluacion->preguntas as $i => $pregunta)
                        <div class="mb-4 p-3 border rounded" style="background: #f8f9fa;">
                            <h6 class="text-primary"><i class="fas fa-question"></i> Pregunta {{ $i + 1 }}: {{ $pregunta }}</h6>
                            <div class="alert alert-light mt-2">
                                <strong><i class="fas fa-comment"></i> Respuesta:</strong><br>
                                {{ $evaluacion->respuestas[$i] ?? 'Sin respuesta' }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-star"></i> Calificación</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('evaluaciones.guardar-calificacion', $evaluacion) }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="nota" class="form-label"><i class="fas fa-star"></i> Nota (0-10)</label>
                        <input type="number" name="nota" id="nota" 
                               class="form-control" min="0" max="10" step="0.1" required
                               value="{{ old('nota', $evaluacion->nota ?? '') }}"
                               placeholder="Ingresa la nota de 0 a 10">
                        @error('nota')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="comentario_calificacion" class="form-label"><i class="fas fa-comment"></i> Comentario de Calificación</label>
                        <textarea name="comentario_calificacion" id="comentario_calificacion" 
                                  class="form-control" rows="4"
                                  placeholder="Comentarios sobre la evaluación, fortalezas, áreas de mejora...">{{ old('comentario_calificacion', $evaluacion->descripcion ?? '') }}</textarea>
                        @error('comentario_calificacion')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-success rounded-pill px-4">
                            <i class="fas fa-save"></i> Guardar Calificación
                        </button>
                        <a href="{{ route('evaluaciones.index') }}" class="btn btn-secondary rounded-pill px-4">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 