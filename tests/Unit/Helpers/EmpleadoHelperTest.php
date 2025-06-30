<?php

namespace Tests\Unit\Helpers;

use Tests\TestCase;
use App\Helpers\EmpleadoHelper;

class EmpleadoHelperTest extends TestCase
{
    public function test_empleado_helper_class_exists()
    {
        $this->assertTrue(class_exists(EmpleadoHelper::class));
    }

    public function test_get_user_ids_con_rol_empleado_method_exists()
    {
        $this->assertTrue(method_exists(EmpleadoHelper::class, 'getUserIdsConRolEmpleado'));
    }

    public function test_empleado_helper_is_static_class()
    {
        $reflection = new \ReflectionClass(EmpleadoHelper::class);
        $this->assertTrue($reflection->isInstantiable() === false || $reflection->getConstructor() === null);
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

    public function test_calcular_edad_with_different_date()
    {
        $fechaNacimiento = '2000-06-15';
        $edad = EmpleadoHelper::calcularEdad($fechaNacimiento);
        
        $this->assertIsInt($edad);
        $this->assertGreaterThan(0, $edad);
    }
} 