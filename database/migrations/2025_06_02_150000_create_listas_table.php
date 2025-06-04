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
        Schema::create('listas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_alumno')
                  ->constrained('alumnos')
                  ->onDelete('cascade');
            $table->foreignId('id_empresa')
                  ->constrained('empresas')
                  ->onDelete('cascade');
            $table->date('fecha');
            $table->enum('estado', ['falta', 'asistencia', 'justificado']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listas');
    }
};
