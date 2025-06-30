<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;

class HomeControllerTest extends TestCase
{
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
} 