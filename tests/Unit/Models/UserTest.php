<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_be_created()
    {
        $user = User::create([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->assertDatabaseHas('users', [
            'username' => 'testuser',
            'email' => 'test@example.com',
        ]);
    }

    public function test_user_has_required_fields()
    {
        $user = User::create([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->assertNotNull($user->username);
        $this->assertNotNull($user->email);
        $this->assertNotNull($user->password);
    }
} 