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
        // Tabla de planteles
        Schema::create('planteles', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique(); // ← Ahora es único
        });

        // Tabla de moderadores
        Schema::create('moderadores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user')
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->foreignId('id_plantel')
                  ->constrained('planteles')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Primero borramos moderadores para evitar errores de FK
        Schema::dropIfExists('moderadores');
        Schema::dropIfExists('planteles');
    }
};
