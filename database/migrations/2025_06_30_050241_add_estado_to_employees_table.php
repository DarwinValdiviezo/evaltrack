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
        Schema::connection('mysql')->table('employees', function (Blueprint $table) {
            $table->enum('estado', ['activo', 'inactivo'])->default('activo')->after('cargo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mysql')->table('employees', function (Blueprint $table) {
            $table->dropColumn('estado');
        });
    }
};
