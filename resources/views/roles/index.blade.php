@extends('layouts.sbadmin')
@section('title', 'Roles')
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="font-size: 2rem;">Gestión de Roles</h2>
        <div class="text-muted" style="font-size: 1rem;">Administra los roles del sistema</div>
    </div>
    <div>
        <a href="{{ route('roles.create') }}" class="btn btn-primary rounded-pill mt-3 mt-md-0">
            <i class="fas fa-plus"></i> Nuevo Rol
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="table-responsive">
    <table class="table table-hover align-middle shadow-sm rounded">
        <thead class="thead-light">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Guard</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($roles as $role)
                <tr>
                    <td>{{ $role->id }}</td>
                    <td><span class="badge badge-info">{{ $role->name }}</span></td>
                    <td>{{ $role->guard_name }}</td>
                    <td>
                        <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-sm btn-outline-warning rounded-pill">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <button class="btn btn-sm btn-outline-danger rounded-pill" data-toggle="modal" data-target="#deleteModal{{ $role->id }}">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                        <div class="modal fade" id="deleteModal{{ $role->id }}" tabindex="-1" role="dialog">
                          <div class="modal-dialog" role="document">
                            <form action="{{ route('roles.destroy', $role->id) }}" method="POST">
                              @csrf
                              @method('DELETE')
                              <div class="modal-content">
                                <div class="modal-header bg-danger text-white">
                                  <h5 class="modal-title">Eliminar Rol</h5>
                                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                  @php $predefinidos = ['Administrador', 'Gestor de Talento Humano', 'Empleado']; @endphp
                                  @if(in_array($role->name, $predefinidos))
                                    <div class="alert alert-warning">
                                      <b>Este rol es predefinido y no puede ser eliminado.</b>
                                    </div>
                                  @else
                                    <div class="alert alert-danger">
                                      <b>¡Advertencia crítica!</b> Esta acción eliminará el rol <b>{{ $role->name }}</b>.<br>
                                      Por seguridad, ingresa tu contraseña de administrador para continuar.
                                    </div>
                                    <input type="password" name="admin_password" class="form-control" placeholder="Contraseña de administrador" required>
                                  @endif
                                </div>
                                <div class="modal-footer">
                                  @if(!in_array($role->name, $predefinidos))
                                    <button type="submit" class="btn btn-danger">Eliminar</button>
                                  @endif
                                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                </div>
                              </div>
                            </form>
                          </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center text-muted">No hay roles registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection