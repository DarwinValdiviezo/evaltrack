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
        $hasColumn = DB::connection('mysql_business')->getSchemaBuilder()->hasColumn('evaluaciones', 'preguntas');
        if (!$hasColumn) {
            Schema::connection('mysql_business')->table('evaluaciones', function (Blueprint $table) {
                $table->json('preguntas')->after('status')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $hasColumn = DB::connection('mysql_business')->getSchemaBuilder()->hasColumn('evaluaciones', 'preguntas');
        if ($hasColumn) {
            Schema::connection('mysql_business')->table('evaluaciones', function (Blueprint $table) {
                $table->dropColumn('preguntas');
            });
        }
    }
};
