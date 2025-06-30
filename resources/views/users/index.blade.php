@extends('layouts.sbadmin')
@section('title', 'Usuarios')
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="font-size: 2rem;">Gestión de Usuarios</h2>
        <div class="text-muted" style="font-size: 1rem;">Administra los usuarios y sus roles</div>
    </div>
    <a href="{{ route('users.create') }}" class="btn btn-primary rounded-pill mt-3 mt-md-0">
        <i class="fas fa-user-plus"></i> Nuevo Usuario
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

{{-- Buscador y filtros --}}
<form method="GET" class="row g-2 mb-3">
    <div class="col-12 col-md-4">
        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Buscar por nombre o email...">
    </div>
    <div class="col-12 col-md-3">
        <select name="role" class="form-control">
            <option value="">Todos los roles</option>
            @foreach($roles as $role)
                <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12 col-md-2">
        <button class="btn btn-outline-primary w-100" type="submit">
            <i class="fas fa-search"></i> Buscar
        </button>
    </div>
    <div class="col-12 col-md-2">
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary w-100">
            Limpiar
        </a>
    </div>
</form>

<div class="table-responsive">
    <table class="table table-hover align-middle shadow-sm rounded">
        <thead class="thead-light">
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        @forelse($users as $u)
            <tr>
                <td>{{ $u->id }}</td>
                <td>
                    <div class="d-flex align-items-center">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($u->name) }}&background=2563eb&color=fff&size=32" class="rounded-circle mr-2" style="width:32px; height:32px;">
                        <span>{{ $u->username }}</span>
                    </div>
                </td>
                <td>{{ $u->email }}</td>
                <td>
                    <span class="badge badge-info">{{ $u->getRoleNames()->implode(', ') ?: 'Sin rol' }}</span>
                </td>
                <td>
                    <a href="{{ route('users.edit',$u) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                        <i class="fas fa-user-cog"></i> Asignar Rol
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center text-muted">No se encontraron usuarios.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-center">
    {{ $users->appends(request()->query())->links() }}
</div>
@endsection