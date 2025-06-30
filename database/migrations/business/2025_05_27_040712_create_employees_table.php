<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up() {
    Schema::connection('mysql_business')->create('employees', function(\Illuminate\Database\Schema\Blueprint $table){
        $table->id();
        $table->bigInteger('user_id')->unsigned();
        $table->string('nombre');
        $table->string('apellido');
        $table->string('cedula')->unique();
        $table->string('email')->unique();
        $table->string('telefono')->nullable();
        $table->date('fecha_nacimiento');
        $table->string('cargo');
        $table->timestamps();
    });
}
public function down() {
    Schema::connection('mysql_business')->dropIfExists('employees');
}

};
