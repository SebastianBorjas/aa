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
        Schema::create('alumnos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user')
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->foreignId('id_plantel')
                  ->constrained('planteles')
                  ->onDelete('cascade');
            $table->foreignId('id_especialidad')
                  ->constrained('especialidades')
                  ->onDelete('cascade');
            $table->string('name');
            $table->string('telefono');
            $table->string('telefono_emergencia');
            $table->boolean('lunes')->default(false);
            $table->boolean('martes')->default(false);
            $table->boolean('miercoles')->default(false);
            $table->boolean('jueves')->default(false);
            $table->boolean('viernes')->default(false);
            $table->boolean('sabado')->default(false);
            $table->boolean('domingo')->default(false);
            $table->date('fecha_inicio');
            $table->date('fecha_termino')->nullable();
            $table->foreignId('id_empresa')
                  ->constrained('empresas')
                  ->onDelete('cascade');
            $table->foreignId('id_maestro')
                  ->constrained('maestros')
                  ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alumnos');
    }
};