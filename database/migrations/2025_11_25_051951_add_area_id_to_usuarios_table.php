<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->foreignId('area_id')
                  ->nullable()
                  ->after('id_rol')
                  ->constrained('areas', 'id_area')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropForeign(['area_id']);
            $table->dropColumn('area_id');
        });
    }
};
