<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Obtener id de Alta y Crítica
        $alta    = DB::table('prioridades')->where('nombre', 'Alta')->first();
        $critica = DB::table('prioridades')->where('nombre', 'Crítica')->first();

        if ($critica) {
            // Reasignar tickets críticos a Alta (o null si no existe Alta)
            DB::table('tickets')
                ->where('prioridad_id', $critica->id_prioridad)
                ->update(['prioridad_id' => $alta?->id_prioridad ?? null]);

            // Eliminar Crítica permanentemente (fuerza si tiene soft delete)
            DB::table('prioridades')->where('id_prioridad', $critica->id_prioridad)->delete();
        }
    }

    public function down(): void
    {
        // Restaurar Crítica si se hace rollback
        DB::table('prioridades')->insert([
            'nombre'     => 'Crítica',
            'nivel'      => 4,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
};
