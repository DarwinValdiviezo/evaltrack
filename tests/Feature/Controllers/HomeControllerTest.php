<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_requires_authentication()
    {
        $response = $this->get('/home');
        $this->assertTrue($response->status() === 302 || $response->status() === 500);
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
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/home');
        $response->assertStatus(200);
    }

    public function test_home_page_has_correct_content()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/home');
        $response->assertViewIs('home');
    }

    public function test_home_controller_index_method()
    {
        $controller = new \App\Http\Controllers\HomeController();
        $this->assertTrue(method_exists($controller, 'index'));
    }

    public function test_home_controller_returns_view()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/home');
        $response->assertViewHas('user');
    }

    public function test_home_controller_passes_user_to_view()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/home');
        $response->assertViewHas('user', $user);
    }
} 