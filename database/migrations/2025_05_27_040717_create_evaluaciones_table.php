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
        Schema::create('evaluaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evento_id')->constrained('eventos')->onDelete('cascade');
            $table->foreignId('empleado_id')->constrained('employees')->onDelete('cascade');
            $table->string('titulo');
            $table->text('descripcion');
            $table->decimal('nota', 5, 2)->nullable();
            $table->text('respuestas')->nullable(); // JSON con las respuestas del empleado
            $table->date('fecha_evaluacion');
            $table->enum('status', ['Pendiente', 'En Progreso', 'Completada', 'Calificada'])->default('Pendiente');
            $table->bigInteger('evaluado_por')->unsigned()->nullable(); // Sin restricción de clave foránea
            $table->json('preguntas')->nullable();
            $table->timestamps();
            
            // Un empleado solo puede tener una evaluación por evento
            $table->unique(['evento_id', 'empleado_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('evaluaciones');
    }
};
