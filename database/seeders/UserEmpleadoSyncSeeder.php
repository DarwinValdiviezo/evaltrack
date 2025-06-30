<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Support\Str;

class UserEmpleadoSyncSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();
        foreach ($users as $user) {
            $exists = Employee::where('email', $user->email)->first();
            if (!$exists) {
                Employee::create([
                    'user_id' => $user->id,
                    'nombre' => Str::before($user->email, '@'),
                    'apellido' => 'Empleado',
                    'cedula' => 'EMP' . str_pad($user->id, 6, '0', STR_PAD_LEFT),
                    'email' => $user->email,
                    'telefono' => null,
                    'fecha_nacimiento' => '1990-01-01',
                    'cargo' => 'Empleado',
                ]);
            }
        }
    }
} 