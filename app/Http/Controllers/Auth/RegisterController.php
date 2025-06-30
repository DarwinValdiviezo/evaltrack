<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Employee;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'nombre' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/'
            ],
            'apellido' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/'
            ],
            'cedula' => [
                'required',
                'string',
                'size:10',
                'regex:/^17\d{8}$/',
                function ($attribute, $value, $fail) {
                    $exists = DB::connection('pgsql')
                        ->table('users')
                        ->where('cedula', $value)
                        ->exists();
                    
                    if ($exists) {
                        $fail('Esta cédula ya está registrada en el sistema.');
                    }
                }
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'regex:/^[^@]+@evaltrack\.com$/',
                function ($attribute, $value, $fail) {
                    $exists = DB::connection('pgsql')
                        ->table('users')
                        ->where('email', $value)
                        ->exists();
                    
                    if ($exists) {
                        $fail('Este correo electrónico ya está registrado.');
                    }
                }
            ],
            'telefono' => [
                'required',
                'string',
                'size:10',
                'regex:/^09\d{8}$/',
                function ($attribute, $value, $fail) {
                    $exists = DB::connection('pgsql')
                        ->table('users')
                        ->where('telefono', $value)
                        ->exists();
                    
                    if ($exists) {
                        $fail('Este teléfono ya está registrado en el sistema.');
                    }
                }
            ],
            'fecha_nacimiento' => [
                'required',
                'date',
                'before_or_equal:' . Carbon::now()->subYears(19)->format('Y-m-d'),
                'after_or_equal:' . Carbon::now()->subYears(50)->format('Y-m-d')
            ],
            'cargo' => [
                'required',
                'string',
                'in:Desarrollador,Marketing,Finanzas,Analista'
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed'
            ],
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.regex' => 'El nombre solo puede contener letras y espacios.',
            'apellido.required' => 'El apellido es obligatorio.',
            'apellido.regex' => 'El apellido solo puede contener letras y espacios.',
            'cedula.required' => 'La cédula es obligatoria.',
            'cedula.size' => 'La cédula debe tener exactamente 10 dígitos.',
            'cedula.regex' => 'La cédula debe empezar con 17 y contener solo números.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El formato del correo electrónico no es válido.',
            'email.regex' => 'Solo se permiten correos con dominio @evaltrack.com.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'telefono.size' => 'El teléfono debe tener exactamente 10 dígitos.',
            'telefono.regex' => 'El teléfono debe empezar con 09 y contener solo números.',
            'fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria.',
            'fecha_nacimiento.date' => 'La fecha de nacimiento no es válida.',
            'fecha_nacimiento.before_or_equal' => 'Debes tener al menos 19 años para registrarte.',
            'fecha_nacimiento.after_or_equal' => 'No puedes tener más de 50 años para registrarte.',
            'cargo.required' => 'El cargo es obligatorio.',
            'cargo.in' => 'El cargo seleccionado no es válido.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
        ]);
    }

    protected function create(array $data)
    {
        // Crear usuario en PostgreSQL
        $user = User::create([
            'username' => $data['email'],
            'email' => $data['email'],
            'password' => $data['password'],
            'nombre' => $data['nombre'],
            'apellido' => $data['apellido'],
            'cedula' => $data['cedula'],
            'telefono' => $data['telefono'],
            'fecha_nacimiento' => $data['fecha_nacimiento'],
            'cargo' => $data['cargo'],
        ]);

        // Asignar rol de Empleado por defecto
        $user->assignRole('Empleado');

        // Crear empleado en MySQL
        Employee::create([
            'user_id' => $user->id,
            'nombre' => $data['nombre'],
            'apellido' => $data['apellido'],
            'cedula' => $data['cedula'],
            'email' => $data['email'],
            'telefono' => $data['telefono'],
            'fecha_nacimiento' => $data['fecha_nacimiento'],
            'cargo' => $data['cargo'],
            'estado' => 'activo',
        ]);

        return $user;
    }
}
