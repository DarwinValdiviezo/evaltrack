@extends('layouts.sbadmin')
@section('title', 'Empleados')
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="font-size: 2rem;">Gestión de Empleados</h2>
        <div class="text-muted" style="font-size: 1rem;">Administra los empleados registrados</div>
    </div>
    <a href="{{ route('empleados.create') }}" class="btn btn-primary rounded-pill mt-3 mt-md-0">
        <i class="fas fa-user-plus"></i> Nuevo Empleado
    </a>
</div>
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif
{{-- Filtros y buscador --}}
<form method="GET" class="row g-2 mb-3">
    <div class="col-12 col-md-4">
        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Buscar por nombre, cédula o email...">
    </div>
    <div class="col-12 col-md-3">
        <input type="text" name="cargo" value="{{ request('cargo') }}" class="form-control" placeholder="Filtrar por cargo">
    </div>
    <div class="col-12 col-md-2">
        <button class="btn btn-outline-primary w-100" type="submit">
            <i class="fas fa-search"></i> Buscar
        </button>
    </div>
    <div class="col-12 col-md-2">
        <a href="{{ route('empleados.index') }}" class="btn btn-outline-secondary w-100">
            Limpiar
        </a>
    </div>
</form>
<div class="table-responsive">
    <table class="table table-hover align-middle shadow-sm rounded" style="background: #fff; border-radius: 1rem;">
        <thead class="thead-light">
            <tr style="background: #f8fafc;">
                <th>ID</th>
                <th>Nombre</th>
                <th>Cédula</th>
                <th>Email</th>
                <th>Cargo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($empleados as $emp)
                <tr>
                    <td>{{ $emp->id }}</td>
                    <td>{{ $emp->nombre }} {{ $emp->apellido }}</td>
                    <td>{{ $emp->cedula }}</td>
                    <td>{{ $emp->email }}</td>
                    <td>{{ $emp->cargo }}</td>
                    <td>
                        <a href="{{ route('empleados.edit', $emp) }}" class="btn btn-sm btn-outline-warning rounded-pill">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <button class="btn btn-sm btn-outline-danger rounded-pill" data-toggle="modal" data-target="#deleteModal{{ $emp->id }}">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                        <!-- Modal eliminar -->
                        <div class="modal fade" id="deleteModal{{ $emp->id }}" tabindex="-1" role="dialog">
                          <div class="modal-dialog" role="document">
                            <form action="{{ route('empleados.destroy', $emp) }}" method="POST">
                              @csrf
                              @method('DELETE')
                              <div class="modal-content">
                                <div class="modal-header bg-danger text-white">
                                  <h5 class="modal-title">Eliminar Empleado</h5>
                                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                  <div class="alert alert-danger">
                                    ¿Seguro que deseas eliminar al empleado <b>{{ $emp->nombre }} {{ $emp->apellido }}</b>?<br>
                                    Por seguridad, ingresa tu contraseña de administrador para continuar.
                                  </div>
                                  <input type="password" name="admin_password" class="form-control" placeholder="Contraseña de administrador" required>
                                </div>
                                <div class="modal-footer">
                                  <button type="submit" class="btn btn-danger">Eliminar</button>
                                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                </div>
                              </div>
                            </form>
                          </div>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="d-flex justify-content-center">
    {{ $empleados->appends(request()->query())->links() }}
</div>
@endsection
