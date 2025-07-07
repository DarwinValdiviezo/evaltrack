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
        // Actualizar todos los eventos a 'Borrador' para evitar error de enum
        DB::connection('mysql_business')->table('eventos')->update(['estado' => 'Borrador']);
        Schema::connection('mysql_business')->table('eventos', function (Blueprint $table) {
            $table->enum('estado', ['Activo', 'Borrador'])->default('Borrador')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mysql_business')->table('eventos', function (Blueprint $table) {
            $table->enum('estado', ['Programado', 'En Curso', 'Completado', 'Cancelado'])->default('Programado')->change();
        });
    }
};
