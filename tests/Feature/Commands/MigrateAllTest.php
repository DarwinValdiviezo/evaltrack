<?php

namespace Tests\Feature\Commands;

use Tests\TestCase;

class MigrateAllTest extends TestCase
{
    public function test_migrate_all_command_class_exists()
    {
        $this->assertTrue(class_exists(\App\Console\Commands\MigrateAll::class));
    }

    public function test_migrate_all_command_has_signature()
    {
        $reflection = new \ReflectionClass(\App\Console\Commands\MigrateAll::class);
        $property = $reflection->getProperty('signature');
        $property->setAccessible(true);
        $command = new \App\Console\Commands\MigrateAll();
        $signature = $property->getValue($command);
        $this->assertStringContainsString('migrate:all', $signature);
    }

    public function test_migrate_all_command_has_description()
    {
        $command = new \App\Console\Commands\MigrateAll();
        $this->assertNotEmpty($command->getDescription());
    }
} 