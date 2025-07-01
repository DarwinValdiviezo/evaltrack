@extends('layouts.sbadmin')

@section('title', 'Inicio')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Bienvenido</h4>
                </div>
            <div class="card-body">
                    <p class="lead mb-4">
                        ¡Bienvenido al sistema de gestión de talento humano!
                    </p>
                    @if(auth()->check() && auth()->user()->hasRole('Administrador'))
                        <div class="alert alert-info">
                            Eres administrador.
                    </div>
                    @endif
                    @if(auth()->check() && auth()->user()->hasRole('Gestor de Talento Humano'))
                        <div class="alert alert-success">
                            Eres gestor de talento humano.
                    </div>
                    @endif
                    @if(auth()->check() && auth()->user()->hasRole('Empleado'))
                        <div class="alert alert-warning">
                            Eres empleado.
                                </div>
                @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
