<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('pgsql')->table('users', function (Blueprint $table) {
            $table->string('nombre')->nullable()->after('email');
            $table->string('apellido')->nullable()->after('nombre');
            $table->string('cedula', 10)->unique()->nullable()->after('apellido');
            $table->string('telefono', 10)->unique()->nullable()->after('cedula');
            $table->date('fecha_nacimiento')->nullable()->after('telefono');
            $table->enum('cargo', ['Desarrollador', 'Marketing', 'Finanzas', 'Analista'])->nullable()->after('fecha_nacimiento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('pgsql')->table('users', function (Blueprint $table) {
            $table->dropColumn(['nombre', 'apellido', 'cedula', 'telefono', 'fecha_nacimiento', 'cargo']);
        });
    }
};
