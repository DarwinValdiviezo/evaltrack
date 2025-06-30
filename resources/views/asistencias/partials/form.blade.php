<div class="mb-3">
  <label for="empleado_id" class="form-label">Empleado</label>
  <select name="empleado_id" id="empleado_id" class="form-control">
    @foreach($empleados as $emp)
      <option value="{{ $emp->id }}"
        {{ old('empleado_id', $asistencia->empleado_id ?? '') == $emp->id ? 'selected' : '' }}>
        {{ $emp->nombre }} {{ $emp->apellido }}
      </option>
    @endforeach
  </select>
  @error('empleado_id')<div class="text-danger">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
  <label for="fecha_asistencia" class="form-label">Fecha</label>
  <input type="date" name="fecha_asistencia" id="fecha_asistencia"
         value="{{ old('fecha_asistencia', $asistencia->fecha_asistencia ?? '') }}"
         class="form-control">
  @error('fecha_asistencia')<div class="text-danger">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
  <label for="hora_asistencia" class="form-label">Hora</label>
  <input type="time" name="hora_asistencia" id="hora_asistencia"
         value="{{ old('hora_asistencia', $asistencia->hora_asistencia ?? '') }}"
         class="form-control">
  @error('hora_asistencia')<div class="text-danger">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
  <label for="comentario" class="form-label">Comentario</label>
  <textarea name="comentario" id="comentario" rows="3"
            class="form-control">{{ old('comentario', $asistencia->comentario ?? '') }}</textarea>
  @error('comentario')<div class="text-danger">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
  <label for="status" class="form-label">Estado</label>
  <select name="status" id="status" class="form-control">
    @foreach(['Registrada','Confirmada'] as $estado)
      <option value="{{ $estado }}"
        {{ old('status', $asistencia->status ?? '') == $estado ? 'selected' : '' }}>
        {{ $estado }}
      </option>
    @endforeach
  </select>
  @error('status')<div class="text-danger">{{ $message }}</div>@enderror
</div>
