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
        // Leer usuarios desde PostgreSQL
        $users = (new User())->setConnection('pgsql')->get();
        foreach ($users as $user) {
            // Crear empleados en MySQL
            $exists = (new Employee())->setConnection('mysql')->where('email', $user->email)->first();
            if (!$exists) {
                (new Employee())->setConnection('mysql')->create([
                    'user_id' => $user->id,
                    'nombre' => $user->nombre ?? Str::before($user->email, '@'),
                    'apellido' => $user->apellido ?? 'Empleado',
                    'cedula' => $user->cedula ?? ('EMP' . str_pad($user->id, 6, '0', STR_PAD_LEFT)),
                    'email' => $user->email,
                    'telefono' => $user->telefono ?? null,
                    'fecha_nacimiento' => $user->fecha_nacimiento ?? '1990-01-01',
                    'cargo' => $user->cargo ?? 'Empleado',
                    'estado' => 'activo',
                ]);
            }
        }
    }
} 