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
        // Modificar el enum para incluir 'Disponible'
        DB::statement("ALTER TABLE evaluaciones MODIFY COLUMN status ENUM('Pendiente', 'Disponible', 'En Progreso', 'Completada', 'Calificada') DEFAULT 'Pendiente'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir el enum a su estado original
        DB::statement("ALTER TABLE evaluaciones MODIFY COLUMN status ENUM('Pendiente', 'En Progreso', 'Completada', 'Calificada') DEFAULT 'Pendiente'");
    }
};
