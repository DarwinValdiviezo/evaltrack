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
        Schema::connection('mysql_business')->table('asistencias', function (Blueprint $table) {
            $table->unique(['evento_id', 'empleado_id'], 'unique_evento_empleado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mysql_business')->table('asistencias', function (Blueprint $table) {
            $table->dropUnique('unique_evento_empleado');
        });
    }
};
