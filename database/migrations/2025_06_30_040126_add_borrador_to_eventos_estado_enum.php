<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modificar el enum para incluir 'Borrador'
        DB::statement("ALTER TABLE eventos MODIFY COLUMN estado ENUM('Borrador', 'Programado', 'En Curso', 'Completado', 'Cancelado', 'Activo') DEFAULT 'Borrador'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir el enum a su estado original
        DB::statement("ALTER TABLE eventos MODIFY COLUMN estado ENUM('Programado', 'En Curso', 'Completado', 'Cancelado') DEFAULT 'Programado'");
    }
};
