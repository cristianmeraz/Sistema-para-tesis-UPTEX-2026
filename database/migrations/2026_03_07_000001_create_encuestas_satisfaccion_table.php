<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('encuestas_satisfaccion', function (Blueprint $table) {
            $table->id('id_encuesta');
            $table->unsignedBigInteger('ticket_id');
            $table->unsignedBigInteger('usuario_id');
            $table->string('token', 64)->unique();
            $table->boolean('satisfecho')->nullable()->default(null);
            $table->text('comentario')->nullable();
            $table->timestamp('respondida_at')->nullable();
            $table->timestamps();

            $table->foreign('ticket_id')->references('id_ticket')->on('tickets')->onDelete('cascade');
            $table->foreign('usuario_id')->references('id_usuario')->on('usuarios')->onDelete('cascade');

            $table->index('ticket_id');
            $table->index('token');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('encuestas_satisfaccion');
    }
};
