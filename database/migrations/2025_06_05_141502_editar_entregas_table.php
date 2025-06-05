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
        Schema::table('entregas', function (Blueprint $table) {
            // Enum para el estado
            $table->enum('estado', ['verificado', 'rechazado', 'pen_emp', 'pen_mae'])
                  ->nullable()
                  ->after('rutas');

            // Columnas de texto nulleables para comentarios
            $table->text('rce')->nullable()->after('estado');
            $table->text('rcm')->nullable()->after('rce');
            $table->text('vce')->nullable()->after('rcm');
            $table->text('vcm')->nullable()->after('vce');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entregas', function (Blueprint $table) {
            $table->dropColumn(['estado', 'rce', 'rcm', 'vce', 'vcm']);
        });
    }
};
