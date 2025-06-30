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
            DB::statement("DO $$ BEGIN IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'evaluaciones_status_enum') THEN CREATE TYPE evaluaciones_status_enum AS ENUM ('Pendiente', 'En Progreso', 'Completada', 'Calificada'); END IF; END $$;");
            DB::statement("ALTER TYPE evaluaciones_status_enum ADD VALUE IF NOT EXISTS 'Disponible';");
        } else {
            // MySQL: Modificar ENUM
            DB::statement("ALTER TABLE evaluaciones MODIFY COLUMN status ENUM('Pendiente', 'Disponible', 'En Progreso', 'Completada', 'Calificada') DEFAULT 'Pendiente'");
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
            DB::statement("ALTER TABLE evaluaciones MODIFY COLUMN status ENUM('Pendiente', 'En Progreso', 'Completada', 'Calificada') DEFAULT 'Pendiente'");
        }
    }
};
