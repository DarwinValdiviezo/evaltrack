@extends('layouts.sbadmin')
@section('title', 'Asistencias')
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="font-size: 2rem;">Gestión de Asistencias</h2>
        <div class="text-muted" style="font-size: 1rem;">Administra y visualiza las asistencias registradas</div>
    </div>
    <a href="{{ route('asistencias.create') }}" class="btn btn-primary rounded-pill mt-3 mt-md-0">
        <i class="fas fa-calendar-plus"></i> Nueva Asistencia
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
        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Buscar por empleado, evento o estado...">
    </div>
    <div class="col-12 col-md-3">
        <input type="date" name="fecha" value="{{ request('fecha') }}" class="form-control" placeholder="Fecha">
    </div>
    <div class="col-12 col-md-2">
        <button class="btn btn-outline-primary w-100" type="submit">
            <i class="fas fa-search"></i> Buscar
        </button>
    </div>
    <div class="col-12 col-md-2">
        <a href="{{ route('asistencias.index') }}" class="btn btn-outline-secondary w-100">
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
                <th>Evento</th>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($asistencias as $asis)
                <tr>
                    <td>{{ $asis->id }}</td>
                    <td>{{ $asis->empleado->nombre }} {{ $asis->empleado->apellido }}</td>
                    <td>{{ $asis->evento ? $asis->evento->nombre : '-' }}</td>
                    <td>{{ $asis->fecha_asistencia }}</td>
                    <td>{{ $asis->hora_asistencia }}</td>
                    <td>
                        @if($asis->status == 'Confirmada')
                            <span class="badge badge-success">Confirmada</span>
                        @elseif($asis->status == 'Registrada')
                            <span class="badge badge-warning text-white">Registrada</span>
                        @else
                            <span class="badge badge-secondary">{{ $asis->status }}</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('asistencias.edit', $asis) }}" class="btn btn-sm btn-outline-warning rounded-pill" title="Editar asistencia">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button class="btn btn-sm btn-outline-danger rounded-pill" data-toggle="modal" data-target="#deleteModal{{ $asis->id }}" title="Eliminar asistencia">
                            <i class="fas fa-trash"></i>
                        </button>
                        <!-- Modal eliminar -->
                        <div class="modal fade" id="deleteModal{{ $asis->id }}" tabindex="-1" role="dialog">
                          <div class="modal-dialog" role="document">
                            <form action="{{ route('asistencias.destroy', $asis) }}" method="POST">
                              @csrf
                              @method('DELETE')
                              <div class="modal-content">
                                <div class="modal-header bg-danger text-white">
                                  <h5 class="modal-title">Eliminar Asistencia</h5>
                                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                  <div class="alert alert-danger">
                                    ¿Seguro que deseas eliminar la asistencia de <b>{{ $asis->empleado->nombre }} {{ $asis->empleado->apellido }}</b> para el evento <b>{{ $asis->evento ? $asis->evento->nombre : '-' }}</b>?<br>
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
    {{ $asistencias->appends(request()->query())->links() }}
</div>
@endsection
