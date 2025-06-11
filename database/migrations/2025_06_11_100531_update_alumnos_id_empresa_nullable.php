<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alumnos', function (Blueprint $table) {
            $table->dropForeign(['id_empresa']);
            $table->foreignId('id_empresa')
                  ->nullable()
                  ->change();
            $table->foreign('id_empresa')
                  ->references('id')->on('empresas')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('alumnos', function (Blueprint $table) {
            $table->dropForeign(['id_empresa']);
            $table->foreignId('id_empresa')
                  ->nullable(false)
                  ->change();
            $table->foreign('id_empresa')
                  ->references('id')->on('empresas')
                  ->onDelete('cascade');
        });
    }
};