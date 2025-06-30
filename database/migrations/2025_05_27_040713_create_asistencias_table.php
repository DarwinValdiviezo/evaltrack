<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('asistencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evento_id')->constrained('eventos')->onDelete('cascade');
            $table->foreignId('empleado_id')->constrained('employees')->onDelete('cascade');
            $table->date('fecha_asistencia');
            $table->time('hora_asistencia');
            $table->enum('asistio', ['Sí', 'No', 'Pendiente'])->default('Pendiente');
            $table->text('comentario')->nullable();
            $table->enum('status', ['Registrada', 'Confirmada'])->default('Registrada');
            $table->timestamps();
            
            // Un empleado solo puede tener una asistencia por evento
            $table->unique(['evento_id', 'empleado_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('asistencias');
    }
};
