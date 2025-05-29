<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alumnos', function (Blueprint $table) {
            $table->foreignId('id_institucion')
                  ->constrained('instituciones')
                  ->onDelete('cascade')
                  ->after('id_maestro');
        });
    }

    public function down(): void
    {
        Schema::table('alumnos', function (Blueprint $table) {
            $table->dropForeign(['id_institucion']);
            $table->dropColumn('id_institucion');
        });
    }
};