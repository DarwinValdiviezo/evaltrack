<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserTest extends TestCase
{
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
        $this->assertContains('nombre', $fillable);
        $this->assertContains('apellido', $fillable);
    }

    public function test_user_model_has_connection()
    {
        $user = new User();
        $this->assertTrue(method_exists($user, 'getConnectionName'));
    }

    public function test_user_has_required_fields()
    {
        $user = new User();
        $user->username = 'testuser';
        $user->email = 'test@example.com';
        $user->password = 'password';
        $this->assertEquals('testuser', $user->username);
        $this->assertEquals('test@example.com', $user->email);
        $this->assertNotNull($user->password);
        $this->assertIsString($user->password);
        $this->assertTrue(Hash::check('password', $user->password));
    }
} 