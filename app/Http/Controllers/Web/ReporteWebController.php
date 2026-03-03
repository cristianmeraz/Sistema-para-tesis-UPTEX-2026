<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Usuario;
use App\Models\Area;
use App\Models\Prioridad;
use App\Models\Estado;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ReporteWebController extends Controller
{
    public function index(Request $request) {
        $ticketsQuery = Ticket::query();
        if ($request->ajax()) {
            $areas_data = (clone $ticketsQuery)->join('areas', 'tickets.area_id', '=', 'areas.id_area')->select('areas.nombre', DB::raw('count(*) as total'))->groupBy('areas.nombre')->get();
            return response()->json(['areas_labels' => $areas_data->pluck('nombre'), 'areas_data' => $areas_data->pluck('total')]);
        }
        // Caché de catálogos: 60 minutos
        $areas = Cache::remember('areas_catalogo', 60, function() { return Area::all(); });
        $estados = Cache::remember('estados_catalogo', 60, function() { return Estado::all(); });
        $prioridades = Cache::remember('prioridades_catalogo', 60, function() { return Prioridad::all(); });
        return view('reportes.index', compact('areas', 'estados', 'prioridades'));
    }

    public function panelAdmin() {
        // Validar que sea administrador
        if (session('usuario_rol') !== 'Administrador') {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para acceder a este módulo');
        }

        // Caché de estadísticas: 5 minutos
        $stats = Cache::remember('admin_dashboard_stats', 5, function() {
            $ticketsQuery = Ticket::query();
            return [
                'total_usuarios' => Usuario::where('activo', true)->count(),
                'tecnicos' => Usuario::whereHas('rol', function($q){ $q->where('nombre', 'Técnico'); })->count(),
                'usuarios_normales' => Usuario::whereHas('rol', function($q){ $q->where('nombre', 'Usuario Normal')->orWhere('nombre', 'Usuario'); })->count(),
                'total_tickets' => $ticketsQuery->count(),
                'tickets_cerrados' => (clone $ticketsQuery)->whereHas('estado', function($q){ $q->where('tipo', 'cerrado'); })->count(),
                'tickets_abiertos' => (clone $ticketsQuery)->whereHas('estado', function($q){ $q->where('tipo', 'abierto'); })->count(),
                'prioridad_baja'  => (clone $ticketsQuery)->whereHas('prioridad', function($q){ $q->where('nombre', 'Baja'); })->count(),
                'prioridad_media' => (clone $ticketsQuery)->whereHas('prioridad', function($q){ $q->where('nombre', 'Media'); })->count(),
                'prioridad_alta'  => (clone $ticketsQuery)->whereHas('prioridad', function($q){ $q->where('nombre', 'Alta'); })->count(),
                // Panel técnico integrado
                'tickets_pendientes' => (clone $ticketsQuery)->whereHas('estado', function($q){ $q->where('tipo', 'pendiente'); })->count(),
                'tickets_en_proceso' => (clone $ticketsQuery)->whereHas('estado', function($q){ $q->where('tipo', 'en_proceso'); })->count(),
                'tickets_sin_tecnico' => (clone $ticketsQuery)->whereNull('tecnico_asignado_id')->whereHas('estado', function($q){ $q->whereIn('tipo', ['abierto', 'pendiente']); })->count(),
                'resueltos_hoy' => (clone $ticketsQuery)->whereDate('fecha_cierre', now()->today())->count(),
                // Alta SIN técnico asignado
                'criticos_sin_asignar' => (clone $ticketsQuery)
                    ->whereHas('prioridad', function($q){ $q->where('nombre', 'Alta'); })
                    ->whereNull('tecnico_asignado_id')
                    ->whereHas('estado', function($q){ $q->whereIn('tipo', ['abierto', 'pendiente', 'en_proceso']); })
                    ->count(),
                // Alta SIN técnico Y creado hace más de 1 hora
                'criticos_en_1h' => (clone $ticketsQuery)
                    ->whereHas('prioridad', function($q){ $q->where('nombre', 'Alta'); })
                    ->whereNull('tecnico_asignado_id')
                    ->whereHas('estado', function($q){ $q->whereIn('tipo', ['abierto', 'pendiente', 'en_proceso']); })
                    ->where('fecha_creacion', '<=', now()->subHour())
                    ->count(),
            ];
        });

        // Tickets de Alta Prioridad sin técnico asignado — requieren asignación inmediata
        $critical_tickets = Ticket::with(['usuario', 'area', 'prioridad', 'estado', 'tecnicoAsignado'])
            ->whereHas('prioridad', function($q){ $q->where('nombre', 'Alta'); })
            ->whereNull('tecnico_asignado_id')
            ->whereHas('estado', function($q){ $q->whereIn('tipo', ['abierto', 'pendiente', 'en_proceso']); })
            ->orderBy('fecha_creacion', 'asc')
            ->limit(10)
            ->get()
            ->map(function($t) {
                return [
                    'id_ticket' => $t->id_ticket,
                    'titulo' => $t->titulo,
                    'usuario' => ['nombre_completo' => ($t->usuario->nombre ?? 'N/A') . ' ' . ($t->usuario->apellido ?? '')],
                    'area' => ['nombre' => $t->area->nombre ?? 'N/A'],
                    'prioridad' => ['nombre' => $t->prioridad->nombre ?? 'N/A'],
                    'estado' => ['nombre' => $t->estado->nombre ?? 'N/A', 'tipo' => $t->estado->tipo ?? 'abierto'],
                    'tecnico_asignado' => $t->tecnicoAsignado ? ['nombre_completo' => ($t->tecnicoAsignado->nombre ?? 'N/A') . ' ' . ($t->tecnicoAsignado->apellido ?? '')] : null,
                    'fecha_creacion' => $t->fecha_creacion,
                    'es_critico' => $t->fecha_creacion && $t->fecha_creacion->lte(now()->subHour()),
                ];
            });

        return view('admin.dashboard', compact('stats', 'critical_tickets'));
    }

    /**
     * AJAX: Refresh manual de estadísticas (limpia caché)
     */
    public function refreshStats() {
        if (session('usuario_rol') !== 'Administrador') {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        
        // Siempre consultar BD fresca (sin caché) para refresh
        Cache::forget('admin_dashboard_stats');
        
        $ticketsQuery = Ticket::query();
        $stats = [
            'total_usuarios' => Usuario::where('activo', true)->count(),
            'tecnicos' => Usuario::whereHas('rol', function($q){ $q->where('nombre', 'Técnico'); })->count(),
            'usuarios_normales' => Usuario::whereHas('rol', function($q){ $q->where('nombre', 'Usuario Normal')->orWhere('nombre', 'Usuario'); })->count(),
            'total_tickets' => $ticketsQuery->count(),
            'tickets_cerrados' => (clone $ticketsQuery)->whereHas('estado', function($q){ $q->where('tipo', 'cerrado'); })->count(),
            'tickets_abiertos' => (clone $ticketsQuery)->whereHas('estado', function($q){ $q->where('tipo', 'abierto'); })->count(),
            'prioridad_baja'  => (clone $ticketsQuery)->whereHas('prioridad', function($q){ $q->where('nombre', 'Baja'); })->count(),
            'prioridad_media' => (clone $ticketsQuery)->whereHas('prioridad', function($q){ $q->where('nombre', 'Media'); })->count(),
            'prioridad_alta'  => (clone $ticketsQuery)->whereHas('prioridad', function($q){ $q->where('nombre', 'Alta'); })->count(),
            'tickets_pendientes' => (clone $ticketsQuery)->whereHas('estado', function($q){ $q->where('tipo', 'pendiente'); })->count(),
            'tickets_en_proceso' => (clone $ticketsQuery)->whereHas('estado', function($q){ $q->where('tipo', 'en_proceso'); })->count(),
            'tickets_sin_tecnico' => (clone $ticketsQuery)->whereNull('tecnico_asignado_id')->whereHas('estado', function($q){ $q->whereIn('tipo', ['abierto', 'pendiente']); })->count(),
            'resueltos_hoy' => (clone $ticketsQuery)->whereDate('fecha_cierre', now()->today())->count(),
            'criticos_sin_asignar' => (clone $ticketsQuery)
                ->whereHas('prioridad', function($q){ $q->where('nombre', 'Alta'); })
                ->whereNull('tecnico_asignado_id')
                ->whereHas('estado', function($q){ $q->whereIn('tipo', ['abierto', 'pendiente', 'en_proceso']); })
                ->count(),
            'criticos_en_1h' => (clone $ticketsQuery)
                ->whereHas('prioridad', function($q){ $q->where('nombre', 'Alta'); })
                ->whereNull('tecnico_asignado_id')
                ->whereHas('estado', function($q){ $q->whereIn('tipo', ['abierto', 'pendiente', 'en_proceso']); })
                ->where('fecha_creacion', '<=', now()->subHour())
                ->count(),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'timestamp' => now()->format('H:i')
        ]);
    }

    public function exportar() { return response()->json(['ok']); }
    public function rendimiento() { return view('reportes.rendimiento'); }
    public function porFecha() { return view('reportes.por-fecha'); }
}