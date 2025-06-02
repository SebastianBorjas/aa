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
        // Modificar tabla alumnos
        Schema::table('alumnos', function (Blueprint $table) {
            $table->foreignId('id_plan')
                  ->nullable()
                  ->constrained('planes')
                  ->onDelete('set null')
                  ->after('id_institucion');
        });

        // Crear tabla entregas
        Schema::create('entregas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_subtema')
                  ->constrained('subtemas')
                  ->onDelete('cascade');
            $table->foreignId('id_alumno')
                  ->constrained('alumnos')
                  ->onDelete('cascade');
            $table->text('contenido');
            $table->json('rutas')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar tabla entregas
        Schema::dropIfExists('entregas');

        // Revertir cambios en tabla alumnos
        Schema::table('alumnos', function (Blueprint $table) {
            $table->dropForeign(['id_plan']);
            $table->dropColumn('id_plan');
        });
    }
};