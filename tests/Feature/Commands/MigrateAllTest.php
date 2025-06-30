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
        $command = new \App\Console\Commands\MigrateAll();
        $this->assertStringContainsString('migrate:all', $command->getSignature());
    }

    public function test_migrate_all_command_has_description()
    {
        $command = new \App\Console\Commands\MigrateAll();
        $this->assertNotEmpty($command->getDescription());
    }
} 