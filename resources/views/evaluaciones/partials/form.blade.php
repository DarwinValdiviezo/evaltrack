<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="empleado_id" class="form-label"><i class="fas fa-user"></i> Empleado</label>
            <select name="empleado_id" id="empleado_id" class="form-control @error('empleado_id') is-invalid @enderror" required>
                <option value="">Seleccionar empleado</option>
                @foreach($empleados as $emp)
                    <option value="{{ $emp->id }}"
                        {{ old('empleado_id', $evaluacion->empleado_id ?? '') == $emp->id ? 'selected' : '' }}>
                        {{ $emp->nombre }} {{ $emp->apellido }}
                    </option>
                @endforeach
            </select>
            @error('empleado_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label for="fecha_evaluacion" class="form-label"><i class="fas fa-calendar"></i> Fecha de Evaluación</label>
            <input type="date" name="fecha_evaluacion" id="fecha_evaluacion"
                   value="{{ old('fecha_evaluacion', $evaluacion->fecha_evaluacion ?? now()->toDateString()) }}"
                   class="form-control @error('fecha_evaluacion') is-invalid @enderror" required>
            @error('fecha_evaluacion')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

<div class="mb-3">
    <label for="titulo" class="form-label"><i class="fas fa-heading"></i> Título de la Evaluación</label>
    <input type="text" name="titulo" id="titulo" 
           value="{{ old('titulo', $evaluacion->titulo ?? '') }}"
           class="form-control @error('titulo') is-invalid @enderror" 
           placeholder="Ej: Evaluación de Desempeño Q1 2024" required>
    @error('titulo')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label for="descripcion" class="form-label"><i class="fas fa-align-left"></i> Descripción</label>
    <textarea name="descripcion" id="descripcion" rows="3"
              class="form-control @error('descripcion') is-invalid @enderror"
              placeholder="Describe el propósito y objetivos de esta evaluación...">{{ old('descripcion', $evaluacion->descripcion ?? '') }}</textarea>
    @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="status" class="form-label"><i class="fas fa-tasks"></i> Estado</label>
            <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                <option value="">Seleccionar estado</option>
                @foreach(['Pendiente', 'Disponible', 'Completada', 'Calificada'] as $estado)
                    <option value="{{ $estado }}"
                        {{ old('status', $evaluacion->status ?? '') === $estado ? 'selected' : '' }}>
                        {{ $estado }}
                    </option>
                @endforeach
            </select>
            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label for="nota" class="form-label"><i class="fas fa-star"></i> Nota (opcional)</label>
            <input type="number" name="nota" id="nota" step="0.1" min="0" max="10"
                   value="{{ old('nota', $evaluacion->nota ?? '') }}"
                   class="form-control @error('nota') is-invalid @enderror"
                   placeholder="0-10">
            @error('nota')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

<div class="mb-3">
    <label class="form-label"><i class="fas fa-question-circle"></i> Preguntas de la Evaluación</label>
    <div id="preguntas-list" class="border rounded p-3" style="background: #f8f9fa;">
        @php
            $preguntas = old('preguntas', isset($evaluacion) && $evaluacion->preguntas ? $evaluacion->preguntas : []);
        @endphp
        @if(count($preguntas) > 0)
            @foreach($preguntas as $i => $pregunta)
                <div class="input-group mb-2">
                    <span class="input-group-text bg-primary text-white">{{ $i+1 }}</span>
                    <input type="text" name="preguntas[]" class="form-control" value="{{ $pregunta }}" 
                           placeholder="Escribe la pregunta {{ $i+1 }} aquí..." required>
                    <button type="button" class="btn btn-outline-danger btn-remove-pregunta" title="Eliminar pregunta">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            @endforeach
        @else
            <div class="text-muted text-center py-3">
                <i class="fas fa-info-circle"></i> No hay preguntas agregadas. Haz clic en "Agregar pregunta" para comenzar.
            </div>
        @endif
    </div>
    <div class="d-flex justify-content-between align-items-center mt-2">
        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill" id="add-pregunta">
            <i class="fas fa-plus"></i> Agregar pregunta
        </button>
        <small class="text-muted">Mínimo 1 pregunta requerida</small>
    </div>
    @error('preguntas')<div class="text-danger mt-1">{{ $message }}</div>@enderror
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const preguntasList = document.getElementById('preguntas-list');
    const addPreguntaBtn = document.getElementById('add-pregunta');
    
    // Función para agregar nueva pregunta
    function addPregunta() {
        const existingPreguntas = preguntasList.querySelectorAll('.input-group');
        const idx = existingPreguntas.length + 1;
        
        const div = document.createElement('div');
        div.className = 'input-group mb-2';
        div.innerHTML = `
            <span class="input-group-text bg-primary text-white">${idx}</span>
            <input type="text" name="preguntas[]" class="form-control" 
                   placeholder="Escribe la pregunta ${idx} aquí..." required>
            <button type="button" class="btn btn-outline-danger btn-remove-pregunta" title="Eliminar pregunta">
                <i class="fas fa-trash"></i>
            </button>
        `;
        
        preguntasList.appendChild(div);
        
        // Remover mensaje de "no hay preguntas" si existe
        const noPreguntasMsg = preguntasList.querySelector('.text-muted.text-center');
        if (noPreguntasMsg) {
            noPreguntasMsg.remove();
        }
        
        // Agregar evento para eliminar
        div.querySelector('.btn-remove-pregunta').onclick = function() {
            div.remove();
            updatePreguntaNumbers();
        };
    }
    
    // Función para actualizar números de preguntas
    function updatePreguntaNumbers() {
        const preguntas = preguntasList.querySelectorAll('.input-group');
        preguntas.forEach((pregunta, index) => {
            const numeroSpan = pregunta.querySelector('.input-group-text');
            const input = pregunta.querySelector('input');
            numeroSpan.textContent = index + 1;
            input.placeholder = `Escribe la pregunta ${index + 1} aquí...`;
        });
        
        // Mostrar mensaje si no hay preguntas
        if (preguntas.length === 0) {
            preguntasList.innerHTML = `
                <div class="text-muted text-center py-3">
                    <i class="fas fa-info-circle"></i> No hay preguntas agregadas. Haz clic en "Agregar pregunta" para comenzar.
                </div>
            `;
        }
    }
    
    // Evento para agregar pregunta
    addPreguntaBtn.onclick = addPregunta;
    
    // Eventos para eliminar preguntas existentes
    document.querySelectorAll('.btn-remove-pregunta').forEach(btn => {
        btn.onclick = function() {
            btn.closest('.input-group').remove();
            updatePreguntaNumbers();
        };
    });
    
    // Agregar primera pregunta si no hay ninguna
    if (preguntasList.querySelectorAll('.input-group').length === 0) {
        addPregunta();
    }
});
</script>
