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
        Schema::connection('mysql')->table('asistencias', function (Blueprint $table) {
            // $table->date('fecha_asistencia')->after('empleado_id');
            // $table->time('hora_asistencia')->after('fecha_asistencia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mysql')->table('asistencias', function (Blueprint $table) {
            $table->dropColumn(['fecha_asistencia', 'hora_asistencia']);
        });
    }
};
