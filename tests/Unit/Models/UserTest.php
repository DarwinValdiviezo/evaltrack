<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
    }

    public function test_user_model_has_connection()
    {
        $user = new User();
        $this->assertEquals('pgsql', $user->getConnectionName());
    }
} 