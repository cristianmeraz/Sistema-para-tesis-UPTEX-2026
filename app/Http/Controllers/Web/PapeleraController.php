<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Ticket;
use App\Models\EncuestaSatisfaccion;

/**
 * PapeleraController
 *
 * Gestiona la papelera de tickets archivados automáticamente.
 * - Admin: ve TODOS los tickets archivados.
 * - Técnico: ve solo sus tickets archivados.
 * Ambos pueden restaurar. Solo Admin puede vaciar manualmente.
 */
class PapeleraController extends Controller
{
    public function index(Request $request)
    {
        $rol       = session('usuario_rol');
        $usuarioId = session('usuario_id');

        // Consulta sin el scope de archivados para ver la papelera
        $query = Ticket::withoutGlobalScope('no_archivado')
            ->whereNotNull('archivado_at')
            ->with(['usuario', 'area', 'estado', 'tecnicoAsignado', 'prioridad']);

        if ($rol === 'Técnico') {
            $query->where('tecnico_asignado_id', $usuarioId);
        }

        $tickets = $query->orderBy('archivado_at', 'desc')->get();

        // Calcular días restantes antes de eliminación definitiva (5 días desde archivado_at)
        $tickets = $tickets->map(function ($t) {
            $diasRestantes = now()->diffInDays($t->archivado_at->addDays(5), false);
            $t->dias_restantes = max(0, (int) $diasRestantes);
            $t->fecha_eliminacion = $t->archivado_at->copy()->addDays(5);
            return $t;
        });

        return view('admin.papelera.index', compact('tickets', 'rol'));
    }

    public function restaurar($id)
    {
        $rol       = session('usuario_rol');
        $usuarioId = session('usuario_id');

        $query = Ticket::withoutGlobalScope('no_archivado')
            ->whereNotNull('archivado_at')
            ->where('id_ticket', $id);

        // Técnico solo puede restaurar sus propios tickets
        if ($rol === 'Técnico') {
            $query->where('tecnico_asignado_id', $usuarioId);
        }

        $ticket = $query->firstOrFail();
        $ticket->update(['archivado_at' => null]);

        // Restaurar también las encuestas asociadas
        EncuestaSatisfaccion::where('ticket_id', $ticket->id_ticket)
            ->whereNotNull('archivado_at')
            ->update(['archivado_at' => null]);

        return back()->with('success', "Ticket #{$ticket->id_ticket} restaurado correctamente.");
    }
}
