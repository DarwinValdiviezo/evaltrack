<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Solo agregar la columna si no existe
        if (!Schema::connection('pgsql')->hasColumn('roles', 'deleted_at')) {
            Schema::connection('pgsql')->table('roles', function ($table) {
                $table->timestamp('deleted_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::connection('pgsql')->hasColumn('roles', 'deleted_at')) {
            Schema::connection('pgsql')->table('roles', function ($table) {
                $table->dropColumn('deleted_at');
            });
        }
    }
}; 