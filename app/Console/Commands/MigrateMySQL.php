<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MigrateMySQL extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:mysql {--fresh : Drop all tables and re-run all migrations} {--seed : Seed the database with records}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run all migrations for MySQL database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting migrations for MySQL database...');

        // Migrate users tables
        $this->info('📊 Migrating users tables...');
        $this->call('migrate', [
            '--path' => 'database/migrations/users',
            '--force' => true,
        ]);

        // Migrate business tables
        $this->info('💼 Migrating business tables...');
        $this->call('migrate', [
            '--path' => 'database/migrations/business',
            '--force' => true,
        ]);

        // Seed if requested
        if ($this->option('seed')) {
            $this->info('🌱 Seeding database...');
            $this->call('db:seed', ['--force' => true]);
        }

        $this->info('✅ All migrations completed successfully!');
    }
} 