<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_model_exists()
    {
        $this->assertTrue(class_exists(User::class));
    }

    public function test_user_model_has_fillable_fields()
    {
        $user = new User();
        $fillable = $user->getFillable();
        
        $this->assertContains('username', $fillable);
        $this->assertContains('email', $fillable);
        $this->assertContains('password', $fillable);
    }

    public function test_user_model_has_connection()
    {
        $user = new User();
        $this->assertEquals('pgsql', $user->getConnectionName());
    }

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

    public function test_user_can_have_roles()
    {
        $user = User::create([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // Esto ejecuta código del modelo User
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $user->roles);
    }
} 