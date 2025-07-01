<?php
namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\Employee;
use App\Models\Evaluacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\EmpleadoHelper;

class AsistenciaController extends Controller
{
    public function index()
    {
        $userIdsEmpleado = \App\Helpers\EmpleadoHelper::getUserIdsConRolEmpleado();
        $asistencias = Asistencia::whereHas('empleado', function($query) use ($userIdsEmpleado) {
            $query->whereIn('user_id', $userIdsEmpleado);
        })
        ->whereHas('evento', function($query) {
            $query->where('estado', 'Activo');
        })
        ->with(['empleado', 'evento'])
        ->orderBy('fecha_asistencia','desc')
        ->paginate(10);

        return view('asistencias.index', compact('asistencias'));
    }

    public function create()
    {
        $userIdsEmpleado = EmpleadoHelper::getUserIdsConRolEmpleado();
        $empleados = Employee::whereIn('user_id', $userIdsEmpleado)->get();
        return view('asistencias.create', compact('empleados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'empleado_id'      => 'required|exists:employees,id',
            'fecha_asistencia' => 'required|date',
            'hora_asistencia'  => 'required',
            'comentario'       => 'nullable|string',
            'status'           => 'required|in:Registrada,Confirmada',
        ]);

        Asistencia::create($request->all());

        return redirect()->route('asistencias.index')
                         ->with('success','Asistencia registrada correctamente.');
    }

    public function show(Asistencia $asistencia)
    {
        return view('asistencias.show', compact('asistencia'));
    }

    public function edit(Asistencia $asistencia)
    {
        $empleados = Employee::all();
        return view('asistencias.edit', compact('asistencia','empleados'));
    }

    public function update(Request $request, Asistencia $asistencia)
    {
        $request->validate([
            'empleado_id'      => 'required|exists:employees,id',
            'fecha_asistencia' => 'required|date',
            'hora_asistencia'  => 'required',
            'comentario'       => 'nullable|string',
            'status'           => 'required|in:Registrada,Confirmada',
        ]);

        $asistencia->update($request->all());

        return redirect()->route('asistencias.index')
                         ->with('success','Asistencia actualizada correctamente.');
    }

    public function destroy(Asistencia $asistencia)
    {
        $asistencia->delete();
        return redirect()->route('asistencias.index')
                         ->with('success','Asistencia eliminada correctamente.');
    }

    public function confirmar(Asistencia $asistencia)
    {
        // Verificar que la asistencia pertenece al empleado actual
        $empleado = Employee::where('email', Auth::user()->email)->firstOrFail();
        
        if ($asistencia->empleado_id !== $empleado->id) {
            return back()->with('error', 'No tienes permisos para confirmar esta asistencia.');
        }

        $asistencia->update([
            'asistio' => 'Sí',
            'status' => 'Confirmada',
            'fecha_asistencia' => now()->toDateString(),
            'hora_asistencia' => now()->format('H:i'),
        ]);

        // Crear evaluación para este empleado si no existe
        if ($asistencia->evento) {
            $evaluacion_existente = Evaluacion::where('evento_id', $asistencia->evento->id)
                                            ->where('empleado_id', $empleado->id)
                                            ->first();
            
            if (!$evaluacion_existente) {
                $preguntas_genericas = [
                    '¿Qué aprendiste en el evento?',
                    '¿Cómo aplicarías lo aprendido en tu trabajo?',
                    '¿Recomendarías este evento a tus compañeros?',
                    '¿Qué mejorarías del evento?'
                ];
                
                Evaluacion::create([
                    'evento_id' => $asistencia->evento->id,
                    'empleado_id' => $empleado->id,
                    'titulo' => 'Evaluación de ' . $asistencia->evento->nombre,
                    'descripcion' => 'Evalúa tu experiencia en el evento.',
                    'fecha_evaluacion' => $asistencia->evento->fecha_evento,
                    'status' => 'Disponible', // Directamente disponible para responder
                    'preguntas' => $preguntas_genericas,
                ]);
            } else {
                // Si ya existe, cambiar a disponible
                $evaluacion_existente->update(['status' => 'Disponible']);
            }
        }

        return back()->with('success', 'Asistencia confirmada correctamente. La evaluación está ahora disponible.');
    }

    public function misAsistencias()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $empleado = Employee::where('email', Auth::user()->email)->first();
        
        if (!$empleado) {
            return redirect()->route('home')
                             ->with('error', 'No se encontró tu perfil de empleado. Contacta al administrador.');
        }
        
        // Solo mostrar asistencias de eventos activos y con estado 'Registrada' (pendientes de confirmar)
        $asistencias = $empleado->asistencias()
                               ->whereHas('evento', function($query) {
                                   $query->where('estado', 'Activo');
                               })
                               ->where('status', 'Registrada')
                               ->with('evento')
                               ->orderBy('fecha_asistencia', 'desc')
                               ->paginate(10);

        return view('asistencias.mis', compact('asistencias', 'empleado'));
    }

    public function registrarAsistencia(Request $request)
    {
        $request->validate([
            'evento_id' => 'required|exists:eventos,id',
            'fecha_asistencia' => 'required|date',
            'hora_asistencia' => 'required',
            'comentario' => 'nullable|string',
        ]);

        $empleado = Employee::where('email', Auth::user()->email)->firstOrFail();
        
        // Verificar que no exista ya una asistencia para este evento
        $asistenciaExistente = Asistencia::where('evento_id', $request->evento_id)
                                        ->where('empleado_id', $empleado->id)
                                        ->first();
        
        if ($asistenciaExistente) {
            return back()->with('error', 'Ya tienes una asistencia registrada para este evento.');
        }

        Asistencia::create([
            'evento_id' => $request->evento_id,
            'empleado_id' => $empleado->id,
            'fecha_asistencia' => $request->fecha_asistencia,
            'hora_asistencia' => $request->hora_asistencia,
            'asistio' => 'Sí',
            'status' => 'Confirmada',
            'comentario' => $request->comentario,
        ]);

        return redirect()->route('mis-asistencias')
                         ->with('success', 'Asistencia registrada correctamente.');
    }

    public function mostrarRegistro()
    {
        $eventos = \App\Models\Evento::where('estado', '!=', 'Cancelado')
                                    ->where('fecha_evento', '>=', now()->subDays(7)->toDateString())
                                    ->orderBy('fecha_evento')
                                    ->get();
        
        return view('asistencias.registrar', compact('eventos'));
    }
}
