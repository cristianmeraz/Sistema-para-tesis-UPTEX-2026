<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            // Contador de cambios de contraseña en el mes actual (Técnico y Usuario Normal: máx 3/mes)
            $table->unsignedTinyInteger('password_changes_this_month')->default(0)->after('locked_at');
            // Fecha de inicio del mes para saber cuándo resetear el contador
            $table->date('password_month_reset_at')->nullable()->after('password_changes_this_month');
            // Contador de resets vía correo (forgot-password): máx 3, al 4to se desactiva la cuenta
            $table->unsignedTinyInteger('password_reset_count')->default(0)->after('password_month_reset_at');
        });
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropColumn([
                'password_changes_this_month',
                'password_month_reset_at',
                'password_reset_count',
            ]);
        });
    }
};
