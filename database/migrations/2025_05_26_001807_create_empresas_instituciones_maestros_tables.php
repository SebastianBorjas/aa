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
        // Tabla de empresas
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user')
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->foreignId('id_plantel')
                  ->constrained('planteles')
                  ->onDelete('cascade');
            $table->string('name');
            $table->string('telefono');
            $table->timestamps();
        });

        // Tabla de instituciones
        Schema::create('instituciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user')
                  ->constrained('users')
                  ->onDelete('cascade')
                  ->nullable(false);
            $table->foreignId('id_plantel')
                  ->constrained('planteles')
                  ->onDelete('cascade');
            $table->string('name');
            $table->timestamps();
        });

        // Tabla de maestros
        Schema::create('maestros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user')
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->foreignId('id_institucion')
                  ->constrained('instituciones')
                  ->onDelete('cascade');
            $table->foreignId('id_plantel')
                  ->constrained('planteles')
                  ->onDelete('cascade');
            $table->string('name');
            $table->string('telefono');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop tables in reverse order to avoid foreign key constraint errors
        Schema::dropIfExists('maestros');
        Schema::dropIfExists('instituciones');
        Schema::dropIfExists('empresas');
    }
};