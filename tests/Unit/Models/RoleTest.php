<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Role;

class RoleTest extends TestCase
{
    public function test_role_model_exists()
    {
        $this->assertTrue(class_exists(Role::class));
    }

    public function test_role_model_has_table()
    {
        $role = new Role();
        $this->assertEquals('roles', $role->getTable());
    }
} 