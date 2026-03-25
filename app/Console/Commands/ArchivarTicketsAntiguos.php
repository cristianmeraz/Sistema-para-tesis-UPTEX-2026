<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ticket;
use App\Models\EncuestaSatisfaccion;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * ArchivarTicketsAntiguos
 *
 * Cron diario (02:00) — gestiona el ciclo de vida de tickets:
 *
 * PASO 1: Tickets en estado resuelto/cerrado/cancelado con fecha_cierre
 *         hace 4+ meses → se mueven a la papelera (archivado_at = ahora).
 *         Sus encuestas de satisfacción se archivan junto con ellos.
 *
 * PASO 2: Tickets que llevan 5+ días en papelera (archivado_at <= hace 5 días)
 *         → eliminación permanente (forceDelete) incluyendo comentarios y encuestas.
 *
 * Uso manual: php artisan tickets:archivar
 * Uso en prod: php artisan tickets:archivar --dry-run  (solo muestra qué haría)
 */
class ArchivarTicketsAntiguos extends Command
{
    protected $signature = 'tickets:archivar {--dry-run : Muestra qué se haría sin ejecutar cambios}';
    protected $description = 'Archiva tickets cerrados (+4 meses) y elimina permanentemente los que llevan +5 días en papelera';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $ahora  = Carbon::now();

        $this->info('=== Sistema de Papelera Automática - UPTEX ===');
        if ($dryRun) {
            $this->warn('[MODO SIMULACIÓN — no se realizarán cambios]');
        }

        // ─── PASO 1: Archivar tickets cerrados hace 4+ meses ──────────────────
        $this->info('');
        $this->info('PASO 1: Buscando tickets para archivar (fecha_cierre hace 4+ meses)...');

        $limite4Meses = $ahora->copy()->subMonths(4);

        $paraArchivar = Ticket::whereNull('archivado_at')
            ->whereNotNull('fecha_cierre')
            ->whereHas('estado', fn($q) => $q->whereIn('tipo', ['resuelto', 'cerrado', 'cancelado']))
            ->where('fecha_cierre', '<=', $limite4Meses)
            ->get();

        $this->line("  → Encontrados: {$paraArchivar->count()} ticket(s) para archivar.");

        if (!$dryRun && $paraArchivar->count() > 0) {
            foreach ($paraArchivar as $ticket) {
                $ticket->update(['archivado_at' => $ahora]);

                // Archivar encuestas relacionadas
                EncuestaSatisfaccion::where('ticket_id', $ticket->id_ticket)
                    ->whereNull('archivado_at')
                    ->update(['archivado_at' => $ahora]);

                $this->line("  ✓ Ticket #{$ticket->id_ticket} archivado: \"{$ticket->titulo}\"");
            }

            Log::info('Papelera automática [PASO 1]: ' . $paraArchivar->count() . ' tickets archivados.', [
                'ids' => $paraArchivar->pluck('id_ticket')->toArray(),
            ]);
        }

        // ─── PASO 2: Eliminar permanentemente los que llevan 5+ días en papelera ──
        $this->info('');
        $this->info('PASO 2: Buscando tickets para eliminación permanente (en papelera 5+ días)...');

        $limite5Dias = $ahora->copy()->subDays(5);

        // Usamos withoutGlobalScope para poder leer tickets archivados
        $paraEliminar = Ticket::withoutGlobalScope('no_archivado')
            ->whereNotNull('archivado_at')
            ->where('archivado_at', '<=', $limite5Dias)
            ->get();

        $this->line("  → Encontrados: {$paraEliminar->count()} ticket(s) para eliminación permanente.");

        if (!$dryRun && $paraEliminar->count() > 0) {
            foreach ($paraEliminar as $ticket) {
                // Eliminar comentarios (forceDelete si tienen soft deletes)
                $ticket->comentarios()->forceDelete();

                // Eliminar encuestas
                EncuestaSatisfaccion::where('ticket_id', $ticket->id_ticket)->forceDelete();

                $tituloGuardado = $ticket->titulo;
                $idGuardado     = $ticket->id_ticket;

                $ticket->forceDelete();

                $this->line("  ✗ Ticket #{$idGuardado} eliminado permanentemente: \"{$tituloGuardado}\"");
            }

            Log::info('Papelera automática [PASO 2]: ' . $paraEliminar->count() . ' tickets eliminados permanentemente.', [
                'ids' => $paraEliminar->pluck('id_ticket')->toArray(),
            ]);
        }

        // ─── Resumen ─────────────────────────────────────────────────────────
        $this->info('');
        $this->info('─────────────────────────────────────────');
        $this->info("Archivados hoy  : {$paraArchivar->count()}");
        $this->info("Eliminados hoy  : {$paraEliminar->count()}");
        $this->info("Límite archivado: tickets cerrados antes de " . $limite4Meses->format('d/m/Y'));
        $this->info("Límite papelera : archivados antes de " . $limite5Dias->format('d/m/Y'));
        $this->info('─────────────────────────────────────────');

        return Command::SUCCESS;
    }
}
