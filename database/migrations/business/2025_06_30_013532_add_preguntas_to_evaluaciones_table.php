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
        Schema::connection('mysql_business')->table('evaluaciones', function (Blueprint $table) {
            $table->json('preguntas')->after('status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mysql_business')->table('evaluaciones', function (Blueprint $table) {
            $table->dropColumn('preguntas');
        });
    }
};
