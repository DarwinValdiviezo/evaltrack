<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\Employee;
use App\Models\Asistencia;
use App\Models\Evaluacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\EmpleadoHelper;

class EventoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Evento::query();
        // Filtros
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nombre', 'like', '%'.$request->search.'%')
                  ->orWhere('lugar', 'like', '%'.$request->search.'%');
            });
        }
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('fecha')) {
            $query->whereDate('fecha_evento', $request->fecha);
        }
        // Si es admin ve todos, si no solo eventos activos
        if (Auth::user()->hasRole('Administrador')) {
            $eventos = $query->orderBy('fecha_evento', 'desc')->paginate(10);
        } else {
            $eventos = $query->where('estado', 'Activo')->orderBy('fecha_evento', 'desc')->paginate(10);
        }
        return view('eventos.index', compact('eventos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('eventos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'fecha_evento' => 'required|date|after:today',
            'hora_inicio' => 'required',
            'hora_fin' => 'required|after:hora_inicio',
            'lugar' => 'required|string|max:255',
            'tipo' => 'required|in:Capacitación,Reunión,Taller,Conferencia,Otro',
        ]);

        $evento = Evento::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'fecha_evento' => $request->fecha_evento,
            'hora_inicio' => $request->hora_inicio,
            'hora_fin' => $request->hora_fin,
            'lugar' => $request->lugar,
            'tipo' => $request->tipo,
            'estado' => 'Borrador', // Por defecto en borrador
            'creado_por' => Auth::id(),
        ]);

        return redirect()->route('eventos.index')
                         ->with('success', 'Evento creado correctamente. Debes activarlo para que sea visible.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Evento $evento)
    {
        return view('eventos.show', compact('evento'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Evento $evento)
    {
        return view('eventos.edit', compact('evento'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Evento $evento)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'fecha_evento' => 'required|date',
            'hora_inicio' => 'required',
            'hora_fin' => 'required|after:hora_inicio',
            'lugar' => 'required|string|max:255',
            'tipo' => 'required|in:Capacitación,Reunión,Taller,Conferencia,Otro',
        ]);

        $evento->update($request->all());

        return redirect()->route('eventos.index')
                         ->with('success', 'Evento actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Evento $evento)
    {
        // Eliminar evaluaciones y asistencias asociadas
        Evaluacion::where('evento_id', $evento->id)->delete();
        Asistencia::where('evento_id', $evento->id)->delete();
        
        $evento->delete();

        return redirect()->route('eventos.index')
                         ->with('success', 'Evento eliminado correctamente.');
    }

    public function activar(Evento $evento)
    {
        $evento->update(['estado' => 'Activo']);

        // Obtener los IDs de usuarios con rol Empleado desde PostgreSQL
        $userIdsEmpleado = EmpleadoHelper::getUserIdsConRolEmpleado();
        // Solo empleados reales
        $empleados = Employee::whereIn('user_id', $userIdsEmpleado)->get();

        foreach ($empleados as $empleado) {
            // Verificar si ya existe asistencia
            $asistencia = Asistencia::where('evento_id', $evento->id)
                                  ->where('empleado_id', $empleado->id)
                                  ->first();
            
            if (!$asistencia) {
                Asistencia::create([
                    'evento_id' => $evento->id,
                    'empleado_id' => $empleado->id,
                    'fecha_asistencia' => $evento->fecha_evento,
                    'hora_asistencia' => $evento->hora_inicio,
                    'asistio' => 'Pendiente',
                    'status' => 'Registrada',
                ]);
            }
        }

        return redirect()->route('eventos.index')
                         ->with('success', 'Evento activado correctamente. Ahora es visible para todos los usuarios.');
    }

    public function desactivar(Evento $evento)
    {
        $evento->update(['estado' => 'Borrador']);

        return redirect()->route('eventos.index')
                         ->with('success', 'Evento desactivado correctamente.');
    }
}
