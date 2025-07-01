<?php

namespace Tests\Feature\Controllers;

use App\Models\Evaluacion;
use App\Models\Evento;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EvaluacionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_evaluacion_controller_exists()
    {
        $this->assertTrue(class_exists(\App\Http\Controllers\EvaluacionController::class));
    }

    public function test_evaluacion_controller_index_method()
    {
        $controller = new \App\Http\Controllers\EvaluacionController();
        $this->assertTrue(method_exists($controller, 'index'));
    }

    public function test_evaluacion_controller_create_method()
    {
        $controller = new \App\Http\Controllers\EvaluacionController();
        $this->assertTrue(method_exists($controller, 'create'));
    }

    public function test_evaluacion_controller_store_method()
    {
        $controller = new \App\Http\Controllers\EvaluacionController();
        $this->assertTrue(method_exists($controller, 'store'));
    }

    public function test_evaluacion_controller_show_method()
    {
        $controller = new \App\Http\Controllers\EvaluacionController();
        $this->assertTrue(method_exists($controller, 'show'));
    }

    public function test_evaluacion_controller_edit_method()
    {
        $controller = new \App\Http\Controllers\EvaluacionController();
        $this->assertTrue(method_exists($controller, 'edit'));
    }

    public function test_evaluacion_controller_update_method()
    {
        $controller = new \App\Http\Controllers\EvaluacionController();
        $this->assertTrue(method_exists($controller, 'update'));
    }

    public function test_evaluacion_controller_destroy_method()
    {
        $controller = new \App\Http\Controllers\EvaluacionController();
        $this->assertTrue(method_exists($controller, 'destroy'));
    }

    public function test_evaluacion_controller_index_returns_view()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/evaluaciones');
        $response->assertStatus(200);
    }

    public function test_evaluacion_controller_create_returns_view()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/evaluaciones/create');
        $response->assertStatus(200);
    }

    public function test_evaluacion_controller_store_creates_evaluacion()
    {
        $user = User::factory()->create();
        $evento = Evento::factory()->create();
        
        $evaluacionData = [
            'evento_id' => $evento->id,
            'empleado_id' => 1,
            'preguntas' => json_encode(['pregunta1', 'pregunta2']),
            'respuestas' => json_encode(['respuesta1', 'respuesta2']),
            'nota' => 8.5,
            'status' => 'Pendiente'
        ];
        
        $response = $this->actingAs($user)->post('/evaluaciones', $evaluacionData);
        $this->assertDatabaseHas('evaluaciones', $evaluacionData);
    }

    public function test_evaluacion_controller_show_displays_evaluacion()
    {
        $user = User::factory()->create();
        $evaluacion = Evaluacion::factory()->create();
        
        $response = $this->actingAs($user)->get("/evaluaciones/{$evaluacion->id}");
        $response->assertStatus(200);
    }

    public function test_evaluacion_controller_edit_returns_view()
    {
        $user = User::factory()->create();
        $evaluacion = Evaluacion::factory()->create();
        
        $response = $this->actingAs($user)->get("/evaluaciones/{$evaluacion->id}/edit");
        $response->assertStatus(200);
    }

    public function test_evaluacion_controller_update_modifies_evaluacion()
    {
        $user = User::factory()->create();
        $evaluacion = Evaluacion::factory()->create();
        $updateData = [
            'nota' => 9.0,
            'status' => 'Calificada'
        ];
        
        $response = $this->actingAs($user)->put("/evaluaciones/{$evaluacion->id}", $updateData);
        $this->assertDatabaseHas('evaluaciones', $updateData);
    }

    public function test_evaluacion_controller_destroy_deletes_evaluacion()
    {
        $user = User::factory()->create();
        $evaluacion = Evaluacion::factory()->create();
        
        $response = $this->actingAs($user)->delete("/evaluaciones/{$evaluacion->id}");
        $this->assertDatabaseMissing('evaluaciones', ['id' => $evaluacion->id]);
    }
} 