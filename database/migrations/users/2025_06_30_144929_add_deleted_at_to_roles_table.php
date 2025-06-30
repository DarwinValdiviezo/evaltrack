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
        // Solo agregar la columna si no existe
        $hasColumn = DB::connection('pgsql')->getSchemaBuilder()->hasColumn('roles', 'deleted_at');
        if (!$hasColumn) {
            Schema::connection('pgsql')->table('roles', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $hasColumn = DB::connection('pgsql')->getSchemaBuilder()->hasColumn('roles', 'deleted_at');
        if ($hasColumn) {
            Schema::connection('pgsql')->table('roles', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
