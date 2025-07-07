<?php
namespace App\Http\Controllers;

use App\Models\Evaluacion;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\EmpleadoHelper;

class EvaluacionController extends Controller
{
    public function index()
    {
        $userIdsEmpleado = EmpleadoHelper::getUserIdsConRolEmpleado();
        $evaluaciones = Evaluacion::whereHas('empleado', function($query) use ($userIdsEmpleado) {
            $query->whereIn('user_id', $userIdsEmpleado);
        })->with('empleado')
          ->orderBy('fecha_evaluacion','desc')
          ->paginate(10);

        return view('evaluaciones.index', compact('evaluaciones'));
    }

    public function create()
    {
        $userIdsEmpleado = EmpleadoHelper::getUserIdsConRolEmpleado();
        $empleados = Employee::whereIn('user_id', $userIdsEmpleado)->get();
        return view('evaluaciones.create', compact('empleados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'empleado_id'       => 'required|exists:employees,id',
            'nota'              => 'required|numeric|min:0|max:100',
            'descripcion'       => 'nullable|string',
            'fecha_evaluacion'  => 'required|date',
            'status'            => 'required|in:Pendiente,Calificada',
            'preguntas'         => 'nullable|array',
            'preguntas.*'       => 'string|max:500',
        ]);

        Evaluacion::create($request->all());

        return redirect()->route('evaluaciones.index')
                         ->with('success','Evaluación creada correctamente.');
    }

    public function edit(Evaluacion $evaluacion)
    {
        $empleados = Employee::all();
        return view('evaluaciones.edit', compact('evaluacion','empleados'));
    }

    public function update(Request $request, Evaluacion $evaluacion)
    {
        $request->validate([
            'empleado_id'       => 'required|exists:employees,id',
            'nota'              => 'required|numeric|min:0|max:100',
            'descripcion'       => 'nullable|string',
            'fecha_evaluacion'  => 'required|date',
            'status'            => 'required|in:Pendiente,Calificada',
            'preguntas'         => 'nullable|array',
            'preguntas.*'       => 'string|max:500',
        ]);

        $evaluacion->update($request->all());

        return redirect()->route('evaluaciones.index')
                         ->with('success','Evaluación actualizada correctamente.');
    }

    public function destroy(Evaluacion $evaluacion)
    {
        $evaluacion->delete();
        return redirect()->route('evaluaciones.index')
                         ->with('success','Evaluación eliminada correctamente.');
    }

    public function misEvaluaciones()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $empleado = Employee::where('email', Auth::user()->email)->first();
        
        if (!$empleado) {
            return redirect()->route('home')
                             ->with('error', 'No se encontró tu perfil de empleado. Contacta al administrador.');
        }
        
        $evaluaciones = $empleado->evaluaciones()
            ->whereHas('evento', function($q) {
                $q->where('estado', 'Activo');
            })
            ->whereHas('evento.asistencias', function($q) use ($empleado) {
                $q->where('empleado_id', $empleado->id)
                  ->where('status', 'Confirmada');
            })
            ->orderBy('fecha_evaluacion','desc')
            ->paginate(10);

        return view('evaluaciones.mis', compact('evaluaciones'));
    }

    public function responder(Evaluacion $evaluacion)
    {
        if (!in_array($evaluacion->status, ['Pendiente', 'Disponible'])) {
            return redirect()->route('mis-evaluaciones')
                             ->with('error', 'Esta evaluación ya no está disponible para responder.');
        }

        return view('evaluaciones.responder', compact('evaluacion'));
    }

    public function guardarRespuesta(Request $request, Evaluacion $evaluacion)
    {
        $request->validate([
            'respuestas' => 'required|array',
            'respuestas.*' => 'required|string|max:1000',
        ]);

        $evaluacion->update([
            'respuestas' => $request->respuestas,
            'status' => 'Completada'
        ]);

        return redirect()->route('mis-evaluaciones')
                         ->with('success', 'Evaluación respondida correctamente.');
    }

    public function calificar(Evaluacion $evaluacion)
    {
        if ($evaluacion->status !== 'Completada') {
            return redirect()->route('evaluaciones.index')
                             ->with('error', 'Esta evaluación no está lista para calificar.');
        }

        return view('evaluaciones.calificar', compact('evaluacion'));
    }

    public function guardarCalificacion(Request $request, Evaluacion $evaluacion)
    {
        $request->validate([
            'nota' => 'required|numeric|min:0|max:100',
            'comentario_calificacion' => 'nullable|string',
        ]);

        $evaluacion->update([
            'nota' => $request->nota,
            'descripcion' => $request->comentario_calificacion,
            'status' => 'Calificada',
            'evaluado_por' => auth()->id(),
        ]);

        return redirect()->route('evaluaciones.index')
                         ->with('success', 'Evaluación calificada correctamente.');
    }

    public function show(Evaluacion $evaluacion)
    {
        return view('evaluaciones.show', compact('evaluacion'));
    }
}
