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
        Schema::connection('mysql_business')->create('eventos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion');
            $table->date('fecha_evento');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->string('lugar')->nullable();
            $table->enum('tipo', ['Capacitación', 'Reunión', 'Taller', 'Conferencia', 'Otro']);
            $table->enum('estado', ['Programado', 'En Curso', 'Completado', 'Cancelado'])->default('Programado');
            $table->bigInteger('creado_por')->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mysql_business')->dropIfExists('eventos');
    }
};
