<?php

namespace Tests\Feature\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmpleadoControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_empleado_controller_exists()
    {
        $this->assertTrue(class_exists(\App\Http\Controllers\EmpleadoController::class));
    }

    public function test_empleado_controller_index_method()
    {
        $controller = new \App\Http\Controllers\EmpleadoController();
        $this->assertTrue(method_exists($controller, 'index'));
    }

    public function test_empleado_controller_create_method()
    {
        $controller = new \App\Http\Controllers\EmpleadoController();
        $this->assertTrue(method_exists($controller, 'create'));
    }

    public function test_empleado_controller_store_method()
    {
        $controller = new \App\Http\Controllers\EmpleadoController();
        $this->assertTrue(method_exists($controller, 'store'));
    }

    public function test_empleado_controller_show_method()
    {
        $controller = new \App\Http\Controllers\EmpleadoController();
        $this->assertTrue(method_exists($controller, 'show'));
    }

    public function test_empleado_controller_edit_method()
    {
        $controller = new \App\Http\Controllers\EmpleadoController();
        $this->assertTrue(method_exists($controller, 'edit'));
    }

    public function test_empleado_controller_update_method()
    {
        $controller = new \App\Http\Controllers\EmpleadoController();
        $this->assertTrue(method_exists($controller, 'update'));
    }

    public function test_empleado_controller_destroy_method()
    {
        $controller = new \App\Http\Controllers\EmpleadoController();
        $this->assertTrue(method_exists($controller, 'destroy'));
    }

    public function test_empleado_controller_index_returns_view()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/empleados');
        $response->assertStatus(200);
    }

    public function test_empleado_controller_create_returns_view()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/empleados/create');
        $response->assertStatus(200);
    }

    public function test_empleado_controller_store_creates_empleado()
    {
        $user = User::factory()->create();
        $empleadoData = [
            'nombre' => 'John Doe',
            'apellido' => 'Smith',
            'email' => 'john@example.com',
            'telefono' => '123456789',
            'cargo' => 'Desarrollador',
            'departamento' => 'IT',
            'fecha_contratacion' => '2024-01-01',
            'estado' => 'Activo'
        ];
        
        $response = $this->actingAs($user)->post('/empleados', $empleadoData);
        $this->assertDatabaseHas('employees', $empleadoData);
    }

    public function test_empleado_controller_show_displays_empleado()
    {
        $user = User::factory()->create();
        $empleado = Employee::factory()->create();
        
        $response = $this->actingAs($user)->get("/empleados/{$empleado->id}");
        $response->assertStatus(200);
    }

    public function test_empleado_controller_edit_returns_view()
    {
        $user = User::factory()->create();
        $empleado = Employee::factory()->create();
        
        $response = $this->actingAs($user)->get("/empleados/{$empleado->id}/edit");
        $response->assertStatus(200);
    }

    public function test_empleado_controller_update_modifies_empleado()
    {
        $user = User::factory()->create();
        $empleado = Employee::factory()->create();
        $updateData = [
            'nombre' => 'Jane Doe',
            'cargo' => 'Senior Developer'
        ];
        
        $response = $this->actingAs($user)->put("/empleados/{$empleado->id}", $updateData);
        $this->assertDatabaseHas('employees', $updateData);
    }

    public function test_empleado_controller_destroy_deletes_empleado()
    {
        $user = User::factory()->create();
        $empleado = Employee::factory()->create();
        
        $response = $this->actingAs($user)->delete("/empleados/{$empleado->id}");
        $this->assertDatabaseMissing('employees', ['id' => $empleado->id]);
    }
} 