<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comentarios', function (Blueprint $table) {
            $table->boolean('es_actualizacion')->default(false)->after('contenido')
                  ->comment('true = creado desde modal Gestión Técnica (cambiarEstado)');
        });
    }

    public function down(): void
    {
        Schema::table('comentarios', function (Blueprint $table) {
            $table->dropColumn('es_actualizacion');
        });
    }
};
