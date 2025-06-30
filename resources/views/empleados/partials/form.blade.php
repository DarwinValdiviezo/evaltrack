<div class="mb-3">
  <label for="nombre" class="form-label">Nombre</label>
  <input type="text" name="nombre" id="nombre"
         value="{{ old('nombre', $empleado->nombre ?? '') }}"
         class="form-control">
  @error('nombre')<div class="text-danger">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
  <label for="apellido" class="form-label">Apellido</label>
  <input type="text" name="apellido" id="apellido"
         value="{{ old('apellido', $empleado->apellido ?? '') }}"
         class="form-control">
  @error('apellido')<div class="text-danger">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
  <label for="cedula" class="form-label">Cédula</label>
  <input type="text" name="cedula" id="cedula"
         value="{{ old('cedula', $empleado->cedula ?? '') }}"
         class="form-control">
  @error('cedula')<div class="text-danger">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
  <label for="email" class="form-label">Email</label>
  <input type="email" name="email" id="email"
         value="{{ old('email', $empleado->email ?? '') }}"
         class="form-control">
  @error('email')<div class="text-danger">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
  <label for="telefono" class="form-label">Teléfono</label>
  <input type="text" name="telefono" id="telefono"
         value="{{ old('telefono', $empleado->telefono ?? '') }}"
         class="form-control">
  @error('telefono')<div class="text-danger">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
  <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
  <input type="date" name="fecha_nacimiento" id="fecha_nacimiento"
         value="{{ old('fecha_nacimiento', $empleado->fecha_nacimiento ?? '') }}"
         class="form-control">
  @error('fecha_nacimiento')<div class="text-danger">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
  <label for="cargo" class="form-label">Cargo</label>
  <input type="text" name="cargo" id="cargo"
         value="{{ old('cargo', $empleado->cargo ?? '') }}"
         class="form-control">
  @error('cargo')<div class="text-danger">{{ $message }}</div>@enderror
</div>
