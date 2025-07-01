<?php

namespace Tests\Feature\Controllers;

use App\Models\Evento;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventoControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_evento_controller_exists()
    {
        $this->assertTrue(class_exists(\App\Http\Controllers\EventoController::class));
    }

    public function test_evento_controller_index_method()
    {
        $controller = new \App\Http\Controllers\EventoController();
        $this->assertTrue(method_exists($controller, 'index'));
    }

    public function test_evento_controller_create_method()
    {
        $controller = new \App\Http\Controllers\EventoController();
        $this->assertTrue(method_exists($controller, 'create'));
    }

    public function test_evento_controller_store_method()
    {
        $controller = new \App\Http\Controllers\EventoController();
        $this->assertTrue(method_exists($controller, 'store'));
    }

    public function test_evento_controller_show_method()
    {
        $controller = new \App\Http\Controllers\EventoController();
        $this->assertTrue(method_exists($controller, 'show'));
    }

    public function test_evento_controller_edit_method()
    {
        $controller = new \App\Http\Controllers\EventoController();
        $this->assertTrue(method_exists($controller, 'edit'));
    }

    public function test_evento_controller_update_method()
    {
        $controller = new \App\Http\Controllers\EventoController();
        $this->assertTrue(method_exists($controller, 'update'));
    }

    public function test_evento_controller_destroy_method()
    {
        $controller = new \App\Http\Controllers\EventoController();
        $this->assertTrue(method_exists($controller, 'destroy'));
    }

    public function test_evento_controller_index_returns_view()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/eventos');
        $response->assertStatus(200);
    }

    public function test_evento_controller_create_returns_view()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/eventos/create');
        $response->assertStatus(200);
    }

    public function test_evento_controller_store_creates_evento()
    {
        $user = User::factory()->create();
        $eventoData = [
            'nombre' => 'Test Evento',
            'descripcion' => 'Test Description',
            'fecha' => '2024-12-31',
            'hora' => '10:00',
            'lugar' => 'Test Location',
            'estado' => 'Activo'
        ];
        
        $response = $this->actingAs($user)->post('/eventos', $eventoData);
        $this->assertDatabaseHas('eventos', $eventoData);
    }

    public function test_evento_controller_show_displays_evento()
    {
        $user = User::factory()->create();
        $evento = Evento::factory()->create();
        
        $response = $this->actingAs($user)->get("/eventos/{$evento->id}");
        $response->assertStatus(200);
    }

    public function test_evento_controller_edit_returns_view()
    {
        $user = User::factory()->create();
        $evento = Evento::factory()->create();
        
        $response = $this->actingAs($user)->get("/eventos/{$evento->id}/edit");
        $response->assertStatus(200);
    }

    public function test_evento_controller_update_modifies_evento()
    {
        $user = User::factory()->create();
        $evento = Evento::factory()->create();
        $updateData = [
            'nombre' => 'Updated Evento',
            'descripcion' => 'Updated Description',
            'fecha' => '2024-12-31',
            'hora' => '11:00',
            'lugar' => 'Updated Location',
            'estado' => 'Activo'
        ];
        
        $response = $this->actingAs($user)->put("/eventos/{$evento->id}", $updateData);
        $this->assertDatabaseHas('eventos', $updateData);
    }

    public function test_evento_controller_destroy_deletes_evento()
    {
        $user = User::factory()->create();
        $evento = Evento::factory()->create();
        
        $response = $this->actingAs($user)->delete("/eventos/{$evento->id}");
        $this->assertDatabaseMissing('eventos', ['id' => $evento->id]);
    }
} 