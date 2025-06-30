<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Employee;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuario Administrador
        $admin = User::create([
            'username' => 'admin',
            'email' => 'admin@empresa.com',
            'password' => 'password',
        ]);
        $admin->assignRole('Administrador');

        // Crear usuario Gestor de Talento Humano
        $gestor = User::create([
            'username' => 'gestor',
            'email' => 'gestor@empresa.com',
            'password' => 'password',
        ]);
        $gestor->assignRole('Gestor de Talento Humano');

        // Crear usuario Empleado
        $empleado = User::create([
            'username' => 'empleado',
            'email' => 'empleado@empresa.com',
            'password' => 'password',
        ]);
        $empleado->assignRole('Empleado');

        // Crear empleado asociado al usuario empleado
        Employee::create([
            'user_id' => $empleado->id,
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'cedula' => '12345678',
            'cargo' => 'Desarrollador',
            'email' => 'juan.perez@empresa.com',
            'telefono' => '123456789',
            'fecha_nacimiento' => '1990-01-15',
        ]);

        // Crear más empleados de ejemplo
        $empleado2 = User::create([
            'username' => 'maria',
            'email' => 'maria@empresa.com',
            'password' => 'password',
        ]);
        $empleado2->assignRole('Empleado');

        Employee::create([
            'user_id' => $empleado2->id,
            'nombre' => 'María',
            'apellido' => 'García',
            'cedula' => '87654321',
            'cargo' => 'Analista',
            'email' => 'maria.garcia@empresa.com',
            'telefono' => '987654321',
            'fecha_nacimiento' => '1988-05-20',
        ]);

        $empleado3 = User::create([
            'username' => 'carlos',
            'email' => 'carlos@empresa.com',
            'password' => 'password',
        ]);
        $empleado3->assignRole('Empleado');

        Employee::create([
            'user_id' => $empleado3->id,
            'nombre' => 'Carlos',
            'apellido' => 'López',
            'cedula' => '55566677',
            'cargo' => 'Diseñador',
            'email' => 'carlos.lopez@empresa.com',
            'telefono' => '555666777',
            'fecha_nacimiento' => '1992-12-10',
        ]);
    }
}
