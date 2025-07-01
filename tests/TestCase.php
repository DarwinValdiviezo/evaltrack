<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Forzar migración de la tabla users en la conexión pgsql antes de cada test
        \Artisan::call('migrate', [
            '--database' => 'pgsql',
            '--path' => 'database/migrations/users',
            '--force' => true,
        ]);
    }
}
