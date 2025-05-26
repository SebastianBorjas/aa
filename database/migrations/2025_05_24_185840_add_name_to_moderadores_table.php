<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNameToModeradoresTable extends Migration
{
    public function up()
    {
        Schema::table('moderadores', function (Blueprint $table) {
            $table->string('name')->after('id'); // Adiciona a coluna 'name' após 'id'
        });
    }

    public function down()
    {
        Schema::table('moderadores', function (Blueprint $table) {
            $table->dropColumn('name'); // Remove a coluna 'name' se a migração for revertida
        });
    }
}