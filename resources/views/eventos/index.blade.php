@extends('layouts.sbadmin')

@section('title', 'Eventos')
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="font-size: 2rem;">Gestión de Eventos</h2>
        <div class="text-muted" style="font-size: 1rem;">Administra y visualiza los eventos programados</div>
    </div>
    @if(auth()->user() && auth()->user()->hasRole('Administrador'))
        <a href="{{ route('eventos.create') }}" class="btn btn-primary rounded-pill mt-3 mt-md-0">
            <i class="fas fa-calendar-plus"></i> Nuevo Evento
        </a>
    @endif
</div>
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif
{{-- Filtros y buscador --}}
<form method="GET" class="row g-2 mb-3">
    <div class="col-12 col-md-3">
        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Buscar por nombre o lugar...">
    </div>
    <div class="col-12 col-md-2">
        <select name="tipo" class="form-control">
            <option value="">Todos los tipos</option>
            <option value="Capacitación" {{ request('tipo')=='Capacitación'?'selected':'' }}>Capacitación</option>
            <option value="Taller" {{ request('tipo')=='Taller'?'selected':'' }}>Taller</option>
            <option value="Conferencia" {{ request('tipo')=='Conferencia'?'selected':'' }}>Conferencia</option>
            <option value="Reunión" {{ request('tipo')=='Reunión'?'selected':'' }}>Reunión</option>
            <option value="Otro" {{ request('tipo')=='Otro'?'selected':'' }}>Otro</option>
        </select>
    </div>
    <div class="col-12 col-md-2">
        <select name="estado" class="form-control">
            <option value="">Todos los estados</option>
            <option value="Activo" {{ request('estado')=='Activo'?'selected':'' }}>Activo</option>
            <option value="Borrador" {{ request('estado')=='Borrador'?'selected':'' }}>Borrador</option>
            <option value="Cancelado" {{ request('estado')=='Cancelado'?'selected':'' }}>Cancelado</option>
        </select>
    </div>
    <div class="col-12 col-md-2">
        <input type="date" name="fecha" value="{{ request('fecha') }}" class="form-control" placeholder="Fecha">
    </div>
    <div class="col-12 col-md-1">
        <button class="btn btn-outline-primary w-100" type="submit">
            <i class="fas fa-search"></i> Buscar
        </button>
    </div>
    <div class="col-12 col-md-2">
        <a href="{{ route('eventos.index') }}" class="btn btn-outline-secondary w-100">
            Limpiar
        </a>
    </div>
</form>
<div class="table-responsive">
    <table class="table table-hover align-middle shadow-sm rounded" style="background: #fff; border-radius: 1rem;">
        <thead class="thead-light">
            <tr style="background: #f8fafc;">
                <th>#</th>
                <th>Nombre</th>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Lugar</th>
                <th>Tipo</th>
                <th>Estado</th>
                @if(auth()->user() && (auth()->user()->hasRole('Administrador') || auth()->user()->hasRole('Gestor de Talento Humano')))
                    <th>Acciones</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($eventos as $index => $evento)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><b>{{ $evento->nombre }}</b></td>
                    <td>{{ \Carbon\Carbon::parse($evento->fecha_evento)->format('d/m/Y') }}</td>
                    <td>{{ $evento->hora_inicio }} - {{ $evento->hora_fin }}</td>
                    <td>{{ $evento->lugar }}</td>
                    <td><span class="badge badge-info">{{ $evento->tipo }}</span></td>
                    <td>
                        @if($evento->estado == 'Activo')
                            <span class="badge badge-success">Activo</span>
                        @elseif($evento->estado == 'Borrador')
                            <span class="badge badge-warning text-white">Borrador</span>
                        @elseif($evento->estado == 'Cancelado')
                            <span class="badge badge-danger">Cancelado</span>
                        @else
                            <span class="badge badge-secondary">{{ $evento->estado }}</span>
                        @endif
                    </td>
                    @if(auth()->user() && (auth()->user()->hasRole('Administrador') || auth()->user()->hasRole('Gestor de Talento Humano')))
                        <td>
                            @if(auth()->user()->hasRole('Administrador'))
                                @if($evento->estado == 'Borrador')
                                    <form action="{{ route('eventos.activar', $evento) }}" method="POST" style="display:inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm rounded-pill" title="Activar evento">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('eventos.desactivar', $evento) }}" method="POST" style="display:inline">
                                        @csrf
                                        <button type="submit" class="btn btn-warning btn-sm rounded-pill text-white" title="Desactivar evento">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    </form>
                                @endif
                            @endif
                            @if(auth()->user()->hasRole('Administrador') || auth()->user()->hasRole('Gestor de Talento Humano'))
                                <a href="{{ route('eventos.edit', $evento) }}" class="btn btn-info btn-sm rounded-pill" title="Editar evento">
                                    <i class="fas fa-edit"></i>
                                </a>
                            @endif
                            @if(auth()->user()->hasRole('Administrador'))
                                <button class="btn btn-danger btn-sm rounded-pill" data-toggle="modal" data-target="#deleteModal{{ $evento->id }}" title="Eliminar evento">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <!-- Modal eliminar -->
                                <div class="modal fade" id="deleteModal{{ $evento->id }}" tabindex="-1" role="dialog">
                                  <div class="modal-dialog" role="document">
                                    <form action="{{ route('eventos.destroy', $evento) }}" method="POST">
                                      @csrf
                                      @method('DELETE')
                                      <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                          <h5 class="modal-title">Eliminar Evento</h5>
                                          <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                          <div class="alert alert-danger">
                                            ¿Seguro que deseas eliminar el evento <b>{{ $evento->nombre }}</b>?<br>
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
                            @endif
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="d-flex justify-content-center">
    {{ $eventos->appends(request()->query())->links() }}
</div>
@endsection 