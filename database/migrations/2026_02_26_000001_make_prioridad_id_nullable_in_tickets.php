<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            // Eliminar la FK anterior y redefinir como nullable
            $table->dropForeign(['prioridad_id']);
            $table->unsignedBigInteger('prioridad_id')->nullable()->change();
            $table->foreign('prioridad_id')
                  ->references('id_prioridad')
                  ->on('prioridades')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['prioridad_id']);
            $table->unsignedBigInteger('prioridad_id')->nullable(false)->change();
            $table->foreign('prioridad_id')
                  ->references('id_prioridad')
                  ->on('prioridades')
                  ->onDelete('restrict');
        });
    }
};
