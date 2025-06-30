@extends('layouts.sbadmin')
@section('title', 'Editar Usuario')
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h3 class="fw-bold mb-3">Asignar Rol a <span class="text-primary">{{ $user->username }}</span></h3>
                <form action="{{ route('users.update',$user) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label for="role_id" class="form-label">Rol</label>
                        <select name="role_id" id="role_id" class="form-control">
                            <option value="">Seleccionar rol</option>
                            @foreach($roles as $r)
                                <option value="{{ $r->id }}"
                                    {{ old('role_id', $user->hasRole($r->name) ? $r->id : '') == $r->id ? 'selected' : '' }}>
                                    {{ $r->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('role_id')<div class="text-danger">{{ $message }}</div>@enderror
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button class="btn btn-success rounded-pill px-4">Guardar</button>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary rounded-pill px-4">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection