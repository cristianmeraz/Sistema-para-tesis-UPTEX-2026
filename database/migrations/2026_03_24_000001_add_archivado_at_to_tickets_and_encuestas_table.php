<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración: Sistema de Papelera Automática
 *
 * Agrega columna `archivado_at` a tickets y encuestas_satisfaccion.
 * Flujo: ticket cerrado → 4 meses → archivado (papelera) → 5 días → eliminación permanente.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->timestamp('archivado_at')->nullable()->after('deleted_at')
                  ->comment('Fecha en que el ticket fue enviado a la papelera (auto o manual). NULL = activo.');
        });

        Schema::table('encuestas_satisfaccion', function (Blueprint $table) {
            $table->timestamp('archivado_at')->nullable()->after('updated_at')
                  ->comment('Fecha en que la encuesta fue archivada junto con su ticket.');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('archivado_at');
        });

        Schema::table('encuestas_satisfaccion', function (Blueprint $table) {
            $table->dropColumn('archivado_at');
        });
    }
};
