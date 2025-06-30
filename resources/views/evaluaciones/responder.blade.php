@extends('layouts.sbadmin')
@section('title', 'Responder Evaluación')
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
            <div>
                <h2 class="fw-bold mb-1" style="font-size: 2rem;">Responder Evaluación</h2>
                <div class="text-muted" style="font-size: 1rem;">Completa la evaluación asignada con tus respuestas</div>
            </div>
        </div>
        
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-clipboard"></i> {{ $evaluacion->titulo }}</h5>
            </div>
            <div class="card-body">
                <p class="mb-0"><i class="fas fa-align-left"></i> <strong>Descripción:</strong><br>{{ $evaluacion->descripcion }}</p>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-question-circle"></i> Preguntas de la Evaluación</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('evaluaciones.guardar-respuesta', $evaluacion) }}" method="POST">
                    @csrf
                    
                    @if($evaluacion->preguntas && count($evaluacion->preguntas) > 0)
                        @foreach($evaluacion->preguntas as $i => $pregunta)
                            <div class="mb-4 p-3 border rounded" style="background: #f8f9fa;">
                                <label for="respuesta_{{ $i }}" class="form-label">
                                    <strong class="text-primary"><i class="fas fa-question"></i> Pregunta {{ $i + 1 }}:</strong><br>
                                    <span class="text-dark">{{ $pregunta }}</span>
                                </label>
                                <textarea name="respuestas[]" id="respuesta_{{ $i }}" 
                                          class="form-control mt-2" rows="4" required
                                          placeholder="Escribe tu respuesta detallada aquí...">{{ old('respuestas.' . $i) }}</textarea>
                                @error('respuestas.' . $i)
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        @endforeach
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Nota:</strong> Esta evaluación no tiene preguntas específicas definidas.
                        </div>
                    @endif

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Importante:</strong> Una vez que envíes tus respuestas, no podrás modificarlas. Asegúrate de revisar bien antes de enviar.
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('mis-evaluaciones') }}" class="btn btn-secondary rounded-pill px-4">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                        <button type="submit" class="btn btn-success rounded-pill px-4">
                            <i class="fas fa-paper-plane"></i> Enviar Respuestas
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 