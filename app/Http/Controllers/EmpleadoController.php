<?php
namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmpleadoController extends Controller
{
    public function index()
    {
        // Obtener IDs de usuarios con rol Administrador
        $adminUserIds = \App\Models\User::role('Administrador')->pluck('id');
        // Excluir empleados cuyo user_id sea de un admin
        $empleados = Employee::whereNotIn('user_id', $adminUserIds)->paginate(10);
        return view('empleados.index', compact('empleados'));
    }

    public function create()
    {
        return view('empleados.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'           => 'required|string|max:255',
            'apellido'         => 'required|string|max:255',
            'cedula'           => 'required|string|unique:employees,cedula',
            'email'            => 'required|email|unique:employees,email',
            'telefono'         => 'nullable|string|max:20',
            'fecha_nacimiento' => 'required|date',
            'cargo'            => 'required|string|max:255',
        ]);

        Employee::create($data);

        return redirect()->route('empleados.index')
                         ->with('success','Empleado creado correctamente.');
    }

    public function edit(Employee $empleado)
    {
        return view('empleados.edit', compact('empleado'));
    }

    public function update(Request $request, Employee $empleado)
    {
        $data = $request->validate([
            'nombre'           => 'required|string|max:255',
            'apellido'         => 'required|string|max:255',
            "cedula"           => "required|string|unique:employees,cedula,{$empleado->id}",
            "email"            => "required|email|unique:employees,email,{$empleado->id}",
            'telefono'         => 'nullable|string|max:20',
            'fecha_nacimiento' => 'required|date',
            'cargo'            => 'required|string|max:255',
        ]);

        $empleado->update($data);

        return redirect()->route('empleados.index')
                         ->with('success','Empleado actualizado correctamente.');
    }

    public function destroy(Employee $empleado)
    {
        $empleado->delete();
        return redirect()->route('empleados.index')
                         ->with('success','Empleado eliminado correctamente.');
    }

    public function miPerfil()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $empleado = Employee::where('email', Auth::user()->email)->first();
        
        if (!$empleado) {
            return redirect()->route('home')
                             ->with('error', 'No se encontró tu perfil de empleado. Contacta al administrador.');
        }
        
        return view('empleados.mi-perfil', compact('empleado'));
    }

    public function actualizarMiPerfil(Request $request)
    {
        $empleado = Employee::where('email', Auth::user()->email)->firstOrFail();
        
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'cargo' => 'required|string|max:255',
        ]);

        $empleado->update($request->only(['nombre', 'apellido', 'telefono', 'cargo']));

        return redirect()->route('empleados.mi-perfil')
                         ->with('success', 'Perfil actualizado correctamente.');
    }
}
