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
        Schema::create('personas', function (Blueprint $table) {
            $table->id();
            $table->string('primer_nombre');
            $table->string('segundo_nombre')->nullable();
            $table->string('primer_apellido');
            $table->string('segundo_apellido')->nullable();
            $table->string('apellido_casada')->nullable();
            $table->string('tipo_documento'); // Ej: CUI o Pasaporte
            $table->string('pasaporte')->nullable();
            $table->string('cui')->unique();
            $table->date('fecha_nacimiento');
            $table->string('telefono');
            $table->string('email')->unique();
            $table->enum('sexo', ['M', 'F', 'Otro']);
            $table->string('estado_civil');
            $table->text('direccion');
            $table->string('ruta_archivo')->nullable(); // para el PDF
            $table->timestamps();
        });
    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personas');
    }
};
