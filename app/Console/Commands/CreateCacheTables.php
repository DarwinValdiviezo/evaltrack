<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateCacheTables extends Command
{
    protected $signature = 'create:cache-tables';
    protected $description = 'Create cache and sessions tables in PostgreSQL';

    public function handle()
    {
        $this->info('Creating cache and sessions tables in PostgreSQL...');

        try {
            // Crear tabla cache
            DB::connection('pgsql')->statement("
                CREATE TABLE IF NOT EXISTS cache (
                    key VARCHAR(255) PRIMARY KEY,
                    value TEXT NOT NULL,
                    expiration INTEGER NOT NULL
                )
            ");
            $this->info('✅ Cache table created successfully');

            // Crear tabla cache_locks
            DB::connection('pgsql')->statement("
                CREATE TABLE IF NOT EXISTS cache_locks (
                    key VARCHAR(255) PRIMARY KEY,
                    owner VARCHAR(255) NOT NULL,
                    expiration INTEGER NOT NULL
                )
            ");
            $this->info('✅ Cache locks table created successfully');

            // Crear tabla sessions
            DB::connection('pgsql')->statement("
                CREATE TABLE IF NOT EXISTS sessions (
                    id VARCHAR(255) PRIMARY KEY,
                    user_id BIGINT NULL,
                    ip_address VARCHAR(45) NULL,
                    user_agent TEXT NULL,
                    payload TEXT NOT NULL,
                    last_activity INTEGER NOT NULL
                )
            ");
            $this->info('✅ Sessions table created successfully');

            // Crear índices
            DB::connection('pgsql')->statement("
                CREATE INDEX IF NOT EXISTS cache_expiration_index ON cache (expiration)
            ");
            DB::connection('pgsql')->statement("
                CREATE INDEX IF NOT EXISTS sessions_user_id_index ON sessions (user_id)
            ");
            DB::connection('pgsql')->statement("
                CREATE INDEX IF NOT EXISTS sessions_last_activity_index ON sessions (last_activity)
            ");
            $this->info('✅ Indexes created successfully');

            $this->info('🎉 All cache and sessions tables created successfully!');

        } catch (\Exception $e) {
            $this->error('❌ Error creating tables: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
} 