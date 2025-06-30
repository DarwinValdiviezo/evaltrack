<?php

namespace Tests\Unit\Helpers;

use Tests\TestCase;
use App\Helpers\EmpleadoHelper;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmpleadoHelperTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear un usuario para asociar al empleado
        $this->user = User::create([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);
    }

    public function test_get_empleados_activos()
    {
        // Crear empleados activos e inactivos
        Employee::create([
            'user_id' => $this->user->id,
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'cedula' => '12345678',
            'email' => 'juan@example.com',
            'fecha_nacimiento' => '1990-01-01',
            'cargo' => 'Desarrollador',
            'estado' => 'activo',
        ]);

        Employee::create([
            'user_id' => null, // Sin usuario asociado
            'nombre' => 'María',
            'apellido' => 'García',
            'cedula' => '87654321',
            'email' => 'maria@example.com',
            'fecha_nacimiento' => '1995-01-01',
            'cargo' => 'Analista',
            'estado' => 'inactivo',
        ]);

        $activos = EmpleadoHelper::getEmpleadosActivos();
        
        $this->assertCount(1, $activos);
        $this->assertEquals('Juan', $activos->first()->nombre);
    }

    public function test_format_nombre_completo()
    {
        $nombre = 'Juan';
        $apellido = 'Pérez';
        
        $formateado = EmpleadoHelper::formatNombreCompleto($nombre, $apellido);
        
        $this->assertEquals('Juan Pérez', $formateado);
    }

    public function test_calcular_edad()
    {
        $fechaNacimiento = '1990-01-01';
        $edad = EmpleadoHelper::calcularEdad($fechaNacimiento);
        
        $this->assertIsInt($edad);
        $this->assertGreaterThan(0, $edad);
    }
} 