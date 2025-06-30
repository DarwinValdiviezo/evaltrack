<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    // Usar campo email en lugar de username
    public function username()
    {
        return 'email';
    }

    // Validación personalizada para el login
    protected function validateLogin(Request $request)
    {
        $request->validate([
            'email' => [
                'required',
                'string',
                'email',
                'regex:/^[^@]+@evaltrack\.com$/'
            ],
            'password' => 'required|string',
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El formato del correo electrónico no es válido.',
            'email.regex' => 'Solo se permiten correos con dominio @evaltrack.com.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);
    }
}
