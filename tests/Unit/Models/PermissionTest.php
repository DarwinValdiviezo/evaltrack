<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Permission;

class PermissionTest extends TestCase
{
    public function test_permission_model_exists()
    {
        $this->assertTrue(class_exists(Permission::class));
    }

    public function test_permission_model_has_connection()
    {
        $permission = new Permission();
        $this->assertEquals('pgsql', $permission->getConnectionName());
    }

    public function test_permission_model_has_table()
    {
        $permission = new Permission();
        $this->assertEquals('permissions', $permission->getTable());
    }
} 