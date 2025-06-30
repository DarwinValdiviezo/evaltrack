<?php

namespace Tests\Feature\Commands;

use Tests\TestCase;

class MigrateAllTest extends TestCase
{
    public function test_migrate_all_command_exists()
    {
        $this->artisan('migrate:all')
            ->expectsOutput('🚀 Starting migrations for both databases...')
            ->assertExitCode(0);
    }

    public function test_migrate_all_command_has_description()
    {
        $this->artisan('list')
            ->expectsOutput('migrate:all')
            ->assertExitCode(0);
    }

    public function test_migrate_all_command_class_exists()
    {
        $this->assertTrue(class_exists(\App\Console\Commands\MigrateAll::class));
    }

    public function test_migrate_all_with_fresh_option()
    {
        $this->artisan('migrate:all', ['--fresh' => true])
            ->expectsOutput('🚀 Starting migrations for both databases...')
            ->expectsOutput('📊 Migrating PostgreSQL database (users/roles)...')
            ->expectsOutput('💼 Migrating MySQL database (business)...')
            ->expectsOutput('✅ All migrations completed successfully!')
            ->assertExitCode(0);
    }

    public function test_migrate_all_with_seed_option()
    {
        $this->artisan('migrate:all', ['--seed' => true])
            ->expectsOutput('🚀 Starting migrations for both databases...')
            ->expectsOutput('📊 Migrating PostgreSQL database (users/roles)...')
            ->expectsOutput('💼 Migrating MySQL database (business)...')
            ->expectsOutput('🌱 Seeding databases...')
            ->expectsOutput('✅ All migrations completed successfully!')
            ->assertExitCode(0);
    }
} 