<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MigrateAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:all {--fresh : Drop all tables and re-run all migrations} {--seed : Seed the database with records}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run migrations for both PostgreSQL (users) and MySQL (business) databases';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting migrations for both databases...');

        // Migrate PostgreSQL (users/roles)
        $this->info('📊 Migrating PostgreSQL database (users/roles)...');
        $this->call('migrate', [
            '--database' => 'pgsql',
            '--path' => 'database/migrations/users',
            '--force' => true,
        ]);

        // Migrate MySQL (business)
        $this->info('💼 Migrating MySQL database (business)...');
        $this->call('migrate', [
            '--database' => 'mysql_business',
            '--path' => 'database/migrations/business',
            '--force' => true,
        ]);

        // Seed if requested
        if ($this->option('seed')) {
            $this->info('🌱 Seeding databases...');
            $this->call('db:seed', ['--force' => true]);
        }

        $this->info('✅ All migrations completed successfully!');
    }
} 