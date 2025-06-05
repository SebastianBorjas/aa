<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alumnos', function (Blueprint $table) {
            $table->dropForeign(['id_maestro']);
            $table->foreignId('id_maestro')
                  ->nullable()
                  ->change();
            $table->foreign('id_maestro')
                  ->references('id')->on('maestros')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('alumnos', function (Blueprint $table) {
            $table->dropForeign(['id_maestro']);
            $table->foreignId('id_maestro')
                  ->nullable(false)
                  ->change();
            $table->foreign('id_maestro')
                  ->references('id')->on('maestros')
                  ->onDelete('cascade');
        });
    }
};