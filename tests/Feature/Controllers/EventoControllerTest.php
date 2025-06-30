<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Evento;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EventoControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear un usuario gestor
        $this->user = User::create([
            'username' => 'gestor',
            'email' => 'gestor@example.com',
            'password' => bcrypt('password'),
        ]);
    }

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
} 