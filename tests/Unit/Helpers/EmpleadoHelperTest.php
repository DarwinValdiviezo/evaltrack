<?php

namespace Tests\Unit\Helpers;

use Tests\TestCase;
use App\Helpers\EmpleadoHelper;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmpleadoHelperTest extends TestCase
{
    use RefreshDatabase;

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

    public function test_get_user_ids_con_rol_empleado_returns_array()
    {
        // Crear un rol de empleado
        $role = Role::create([
            'name' => 'Empleado',
            'guard_name' => 'web',
        ]);

        // Crear un usuario
        $user = User::create([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // Asignar rol al usuario
        $user->assignRole($role);

        // Ejecutar el método real
        $ids = EmpleadoHelper::getUserIdsConRolEmpleado();
        
        $this->assertIsArray($ids);
    }

    public function test_get_user_ids_con_rol_empleado_with_no_employees()
    {
        // Ejecutar el método sin empleados
        $ids = EmpleadoHelper::getUserIdsConRolEmpleado();
        
        $this->assertIsArray($ids);
        $this->assertEmpty($ids);
    }
} 