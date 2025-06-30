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
} 