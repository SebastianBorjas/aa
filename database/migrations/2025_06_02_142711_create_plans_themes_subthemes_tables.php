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
        // Tabla de planes
        Schema::create('planes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_maestro')
                  ->constrained('maestros')
                  ->onDelete('cascade');
            $table->string('nombre');
            $table->timestamps();
        });

        // Tabla de temas
        Schema::create('temas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_plan')
                  ->constrained('planes')
                  ->onDelete('cascade');
            $table->string('nombre');
            $table->text('descripcion');
            $table->timestamps();
        });

        // Tabla de subtemas
        Schema::create('subtemas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_tema')
                  ->constrained('temas')
                  ->onDelete('cascade');
            $table->string('nombre');
            $table->text('descripcion');
            $table->json('rutas')->nullable(); // JSON para múltiples rutas de archivos
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop tables in reverse order to avoid foreign key constraint errors
        Schema::dropIfExists('subtemas');
        Schema::dropIfExists('temas');
        Schema::dropIfExists('planes');
    }
};