<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'pgsql') {
            // PostgreSQL: Agregar valor al tipo ENUM existente
            DB::statement("DO $$ BEGIN IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'eventos_estado_enum') THEN CREATE TYPE eventos_estado_enum AS ENUM ('Programado', 'En Curso', 'Completado', 'Cancelado', 'Activo'); END IF; END $$;");
            DB::statement("ALTER TYPE eventos_estado_enum ADD VALUE IF NOT EXISTS 'Borrador';");
        } else {
            // MySQL: Modificar ENUM
            DB::statement("ALTER TABLE eventos MODIFY COLUMN estado ENUM('Borrador', 'Programado', 'En Curso', 'Completado', 'Cancelado', 'Activo') DEFAULT 'Borrador'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'pgsql') {
            // PostgreSQL: No se puede eliminar un valor de ENUM, solo se puede recrear el tipo
            // (opcional: dejarlo vacío o documentar)
        } else {
            // MySQL: Revertir el enum a su estado original
            DB::statement("ALTER TABLE eventos MODIFY COLUMN estado ENUM('Programado', 'En Curso', 'Completado', 'Cancelado') DEFAULT 'Programado'");
        }
    }
};
