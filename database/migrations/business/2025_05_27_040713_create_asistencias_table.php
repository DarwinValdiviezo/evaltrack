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
        Schema::connection('mysql_business')->create('asistencias', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('evento_id')->unsigned();
            $table->bigInteger('empleado_id')->unsigned();
            $table->enum('asistio', ['Sí', 'No', 'Pendiente'])->default('Pendiente');
            $table->enum('status', ['Registrada', 'Confirmada', 'Cancelada'])->default('Registrada');
            $table->text('comentario')->nullable();
            $table->timestamps();
            
            $table->foreign('evento_id')->references('id')->on('eventos')->onDelete('cascade');
            $table->foreign('empleado_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mysql_business')->dropIfExists('asistencias');
    }
};
