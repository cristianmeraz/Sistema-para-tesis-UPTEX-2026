<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            // Contador de intentos fallidos consecutivos
            $table->unsignedTinyInteger('login_attempts')->default(0)->after('last_login');
            // Fecha en que se bloqueó la cuenta (null = no bloqueada)
            $table->timestamp('locked_at')->nullable()->after('login_attempts');
        });
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropColumn(['login_attempts', 'locked_at']);
        });
    }
};
