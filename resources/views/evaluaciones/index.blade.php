@extends('layouts.sbadmin')
@section('title', 'Evaluaciones')
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="font-size: 2rem;">Gestión de Evaluaciones</h2>
        <div class="text-muted" style="font-size: 1rem;">Administra y visualiza las evaluaciones del personal</div>
    </div>
    <a href="{{ route('evaluaciones.create') }}" class="btn btn-primary rounded-pill mt-3 mt-md-0">
        <i class="fas fa-clipboard-plus"></i> Nueva Evaluación
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
        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Buscar por empleado, estado o fecha...">
    </div>
    <div class="col-12 col-md-3">
        <select name="status" class="form-control">
            <option value="">Todos los estados</option>
            <option value="Pendiente" {{ request('status') == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
            <option value="Completada" {{ request('status') == 'Completada' ? 'selected' : '' }}>Completada</option>
            <option value="Calificada" {{ request('status') == 'Calificada' ? 'selected' : '' }}>Calificada</option>
            <option value="Disponible" {{ request('status') == 'Disponible' ? 'selected' : '' }}>Disponible</option>
        </select>
    </div>
    <div class="col-12 col-md-2">
        <button class="btn btn-outline-primary w-100" type="submit">
            <i class="fas fa-search"></i> Buscar
        </button>
    </div>
    <div class="col-12 col-md-2">
        <a href="{{ route('evaluaciones.index') }}" class="btn btn-outline-secondary w-100">
            Limpiar
        </a>
    </div>
</form>
<div class="table-responsive">
    <table class="table table-hover align-middle shadow-sm rounded" style="background: #fff; border-radius: 1rem;">
        <thead class="thead-light">
            <tr style="background: #f8fafc;">
                <th>ID</th>
                <th>Empleado</th>
                <th>Fecha</th>
                <th>Preguntas</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($evaluaciones as $eva)
                <tr>
                    <td>{{ $eva->id }}</td>
                    <td>{{ $eva->empleado->nombre }} {{ $eva->empleado->apellido }}</td>
                    <td>{{ $eva->fecha_evaluacion }}</td>
                    <td>
                        @if($eva->preguntas && count($eva->preguntas) > 0)
                            <span class="badge badge-info">{{ count($eva->preguntas) }} pregunta(s)</span>
                        @else
                            <span class="badge badge-secondary">Sin preguntas</span>
                        @endif
                    </td>
                    <td>
                        @if($eva->status == 'Pendiente')
                            <span class="badge badge-warning text-white">Pendiente</span>
                        @elseif($eva->status == 'Completada')
                            <span class="badge badge-primary">Completada</span>
                        @elseif($eva->status == 'Calificada')
                            <span class="badge badge-success">Calificada</span>
                        @elseif($eva->status == 'Disponible')
                            <span class="badge badge-info">Disponible</span>
                        @else
                            <span class="badge badge-secondary">{{ $eva->status }}</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('evaluaciones.edit', $eva) }}" class="btn btn-sm btn-outline-warning rounded-pill" title="Editar evaluación">
                            <i class="fas fa-edit"></i>
                        </a>
                        @if($eva->status === 'Completada')
                            <a href="{{ route('evaluaciones.calificar', $eva) }}" class="btn btn-sm btn-outline-success rounded-pill" title="Calificar evaluación">
                                <i class="fas fa-star"></i>
                            </a>
                        @endif
                        <button class="btn btn-sm btn-outline-danger rounded-pill" data-toggle="modal" data-target="#deleteModal{{ $eva->id }}" title="Eliminar evaluación">
                            <i class="fas fa-trash"></i>
                        </button>
                        <!-- Modal eliminar -->
                        <div class="modal fade" id="deleteModal{{ $eva->id }}" tabindex="-1" role="dialog">
                          <div class="modal-dialog" role="document">
                            <form action="{{ route('evaluaciones.destroy', $eva) }}" method="POST">
                              @csrf
                              @method('DELETE')
                              <div class="modal-content">
                                <div class="modal-header bg-danger text-white">
                                  <h5 class="modal-title">Eliminar Evaluación</h5>
                                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                  <div class="alert alert-danger">
                                    ¿Seguro que deseas eliminar la evaluación de <b>{{ $eva->empleado->nombre }} {{ $eva->empleado->apellido }}</b> del <b>{{ $eva->fecha_evaluacion }}</b>?<br>
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
    {{ $evaluaciones->appends(request()->query())->links() }}
</div>
@endsection
