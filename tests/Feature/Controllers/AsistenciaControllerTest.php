<?php

namespace Tests\Feature\Controllers;

use App\Models\Asistencia;
use App\Models\Evento;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AsistenciaControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_asistencia_controller_exists()
    {
        $this->assertTrue(class_exists(\App\Http\Controllers\AsistenciaController::class));
    }

    public function test_asistencia_controller_index_method()
    {
        $controller = new \App\Http\Controllers\AsistenciaController();
        $this->assertTrue(method_exists($controller, 'index'));
    }

    public function test_asistencia_controller_create_method()
    {
        $controller = new \App\Http\Controllers\AsistenciaController();
        $this->assertTrue(method_exists($controller, 'create'));
    }

    public function test_asistencia_controller_store_method()
    {
        $controller = new \App\Http\Controllers\AsistenciaController();
        $this->assertTrue(method_exists($controller, 'store'));
    }

    public function test_asistencia_controller_show_method()
    {
        $controller = new \App\Http\Controllers\AsistenciaController();
        $this->assertTrue(method_exists($controller, 'show'));
    }

    public function test_asistencia_controller_edit_method()
    {
        $controller = new \App\Http\Controllers\AsistenciaController();
        $this->assertTrue(method_exists($controller, 'edit'));
    }

    public function test_asistencia_controller_update_method()
    {
        $controller = new \App\Http\Controllers\AsistenciaController();
        $this->assertTrue(method_exists($controller, 'update'));
    }

    public function test_asistencia_controller_destroy_method()
    {
        $controller = new \App\Http\Controllers\AsistenciaController();
        $this->assertTrue(method_exists($controller, 'destroy'));
    }

    public function test_asistencia_controller_index_returns_view()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/asistencias');
        $response->assertStatus(200);
    }

    public function test_asistencia_controller_create_returns_view()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/asistencias/create');
        $response->assertStatus(200);
    }

    public function test_asistencia_controller_store_creates_asistencia()
    {
        $user = User::factory()->create();
        $evento = Evento::factory()->create();
        
        $asistenciaData = [
            'evento_id' => $evento->id,
            'empleado_id' => 1,
            'asistio' => 'Sí',
            'fecha_asistencia' => '2024-12-31',
            'hora_asistencia' => '10:00',
            'comentario' => 'Test comment'
        ];
        
        $response = $this->actingAs($user)->post('/asistencias', $asistenciaData);
        $this->assertDatabaseHas('asistencias', $asistenciaData);
    }

    public function test_asistencia_controller_show_displays_asistencia()
    {
        $user = User::factory()->create();
        $asistencia = Asistencia::factory()->create();
        
        $response = $this->actingAs($user)->get("/asistencias/{$asistencia->id}");
        $response->assertStatus(200);
    }

    public function test_asistencia_controller_edit_returns_view()
    {
        $user = User::factory()->create();
        $asistencia = Asistencia::factory()->create();
        
        $response = $this->actingAs($user)->get("/asistencias/{$asistencia->id}/edit");
        $response->assertStatus(200);
    }

    public function test_asistencia_controller_update_modifies_asistencia()
    {
        $user = User::factory()->create();
        $asistencia = Asistencia::factory()->create();
        $updateData = [
            'asistio' => 'No',
            'comentario' => 'Updated comment'
        ];
        
        $response = $this->actingAs($user)->put("/asistencias/{$asistencia->id}", $updateData);
        $this->assertDatabaseHas('asistencias', $updateData);
    }

    public function test_asistencia_controller_destroy_deletes_asistencia()
    {
        $user = User::factory()->create();
        $asistencia = Asistencia::factory()->create();
        
        $response = $this->actingAs($user)->delete("/asistencias/{$asistencia->id}");
        $this->assertDatabaseMissing('asistencias', ['id' => $asistencia->id]);
    }
} 