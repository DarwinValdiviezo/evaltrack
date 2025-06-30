<?php

namespace Tests\Unit\Middleware;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Http\Middleware\CheckRole;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ReflectionClass;
use ReflectionMethod;

class CheckRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_check_role_middleware_exists()
    {
        $this->assertTrue(class_exists(CheckRole::class));
    }

    public function test_check_role_middleware_has_handle_method()
    {
        $middleware = new CheckRole();
        $this->assertTrue(method_exists($middleware, 'handle'));
    }

    public function test_check_role_middleware_constructor()
    {
        $middleware = new CheckRole();
        $this->assertInstanceOf(CheckRole::class, $middleware);
    }

    public function test_check_role_middleware_handle_method_signature()
    {
        $reflection = new ReflectionClass(CheckRole::class);
        $method = $reflection->getMethod('handle');
        $parameters = $method->getParameters();
        
        $this->assertCount(3, $parameters);
        $this->assertEquals('request', $parameters[0]->getName());
        $this->assertEquals('next', $parameters[1]->getName());
        $this->assertEquals('roles', $parameters[2]->getName());
    }
} 