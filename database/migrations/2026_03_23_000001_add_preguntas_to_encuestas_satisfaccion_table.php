<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Agrega las 5 columnas de preguntas de satisfacción a la tabla encuestas_satisfaccion.
 * Escala: 1=Nada Satisfecho, 2=Poco Satisfecho, 3=Satisfecho, 4=Muy Satisfecho
 *
 * Preguntas:
 * 1. ¿Está satisfecho con el trabajo realizado por el servicio de IT?
 * 2. ¿El personal de IT atiende sus solicitudes técnicas?
 * 3. ¿El servicio de IT soluciona su problema en un tiempo adecuado?
 * 4. ¿El personal de IT demuestra conocimientos suficientes para sus solicitudes?
 * 5. ¿Se encuentra satisfecho con la atención recibida por el personal de IT?
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('encuestas_satisfaccion', function (Blueprint $table) {
            $table->tinyInteger('pregunta_1')->nullable()->comment('Satisfaccion general con el trabajo del servicio IT');
            $table->tinyInteger('pregunta_2')->nullable()->comment('Atencion de solicitudes tecnicas');
            $table->tinyInteger('pregunta_3')->nullable()->comment('Solucion en tiempo adecuado');
            $table->tinyInteger('pregunta_4')->nullable()->comment('Conocimientos tecnicos del personal');
            $table->tinyInteger('pregunta_5')->nullable()->comment('Satisfaccion final con la atencion recibida');
        });
    }

    public function down(): void
    {
        Schema::table('encuestas_satisfaccion', function (Blueprint $table) {
            $table->dropColumn(['pregunta_1', 'pregunta_2', 'pregunta_3', 'pregunta_4', 'pregunta_5']);
        });
    }
};
