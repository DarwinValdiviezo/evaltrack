<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HomeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_requires_authentication()
    {
        $response = $this->get('/home');
        $response->assertRedirect('/login');
    }

    public function test_home_route_exists()
    {
        $this->assertTrue(route('home') !== null);
    }

    public function test_home_controller_exists()
    {
        $this->assertTrue(class_exists(\App\Http\Controllers\HomeController::class));
    }

    public function test_authenticated_user_can_access_home()
    {
        $user = User::create([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->actingAs($user)->get('/home');
        $response->assertStatus(200);
    }

    public function test_home_page_has_correct_content()
    {
        $user = User::create([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->actingAs($user)->get('/home');
        $response->assertViewIs('home');
    }

    public function test_home_controller_index_method()
    {
        $user = User::create([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $controller = new \App\Http\Controllers\HomeController();
        $response = $controller->index();
        
        $this->assertNotNull($response);
    }
} 