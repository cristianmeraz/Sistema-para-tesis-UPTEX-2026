<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Usuario;
use App\Models\Area;
use App\Models\Prioridad;
use App\Models\Estado;
use App\Models\EncuestaSatisfaccion;
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

        // Estadísticas de encuestas (misma caché que el dashboard)
        $satisfaccionStats = Cache::remember('satisfaccion_dashboard_stats', 5, function () {
            $total         = EncuestaSatisfaccion::count();
            $respondidas   = EncuestaSatisfaccion::whereNotNull('respondida_at')->count();
            $satisfechos   = EncuestaSatisfaccion::where('satisfecho', true)->count();
            $noSatisfechos = EncuestaSatisfaccion::where('satisfecho', false)->count();
            $sinResponder  = $total - $respondidas;

            $porArea = DB::table('encuestas_satisfaccion as e')
                ->join('tickets as t', 't.id_ticket', '=', 'e.ticket_id')
                ->join('areas as a', 'a.id_area', '=', 't.area_id')
                ->whereNotNull('e.respondida_at')
                ->select(
                    'a.nombre as area',
                    DB::raw('SUM(CASE WHEN e.satisfecho = 1 THEN 1 ELSE 0 END) as satisfechos'),
                    DB::raw('SUM(CASE WHEN e.satisfecho = 0 THEN 1 ELSE 0 END) as no_satisfechos')
                )
                ->groupBy('a.nombre')
                ->get();

            $resueltoPorDia = DB::table('tickets as t')
                ->join('estados as e', 'e.id_estado', '=', 't.estado_id')
                ->where('e.tipo', 'resuelto')
                ->whereNotNull('t.fecha_cierre')
                ->where('t.fecha_cierre', '>=', now()->subDays(14))
                ->select(DB::raw('DATE(t.fecha_cierre) as dia'), DB::raw('COUNT(*) as total'))
                ->groupBy('dia')->orderBy('dia')->get()->keyBy('dia');

            $diasLabels = []; $diasData = [];
            for ($i = 13; $i >= 0; $i--) {
                $fecha = now()->subDays($i)->format('Y-m-d');
                $diasLabels[] = now()->subDays($i)->format('d/m');
                $diasData[]   = $resueltoPorDia->get($fecha)->total ?? 0;
            }

            return [
                'total'          => $total,
                'respondidas'    => $respondidas,
                'satisfechos'    => $satisfechos,
                'no_satisfechos' => $noSatisfechos,
                'sin_responder'  => $sinResponder,
                'por_area'       => $porArea,
                'dias_labels'    => $diasLabels,
                'dias_data'      => $diasData,
            ];
        });

        return view('reportes.index', compact('areas', 'estados', 'prioridades', 'satisfaccionStats'));
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

        // ── Datos de Satisfacción para gráficas ─────────────────────────
        $satisfaccionStats = Cache::remember('satisfaccion_dashboard_stats', 5, function () {
            $total        = EncuestaSatisfaccion::count();
            $respondidas  = EncuestaSatisfaccion::whereNotNull('respondida_at')->count();
            $satisfechos  = EncuestaSatisfaccion::where('satisfecho', true)->count();
            $noSatisfechos= EncuestaSatisfaccion::where('satisfecho', false)->count();
            $sinResponder = $total - $respondidas;

            // Por área
            $porArea = DB::table('encuestas_satisfaccion as e')
                ->join('tickets as t', 't.id_ticket', '=', 'e.ticket_id')
                ->join('areas as a', 'a.id_area', '=', 't.area_id')
                ->whereNotNull('e.respondida_at')
                ->select(
                    'a.nombre as area',
                    DB::raw('SUM(CASE WHEN e.satisfecho = 1 THEN 1 ELSE 0 END) as satisfechos'),
                    DB::raw('SUM(CASE WHEN e.satisfecho = 0 THEN 1 ELSE 0 END) as no_satisfechos')
                )
                ->groupBy('a.nombre')
                ->get();

            // Tickets resueltos por día (últimos 14 días)
            $resueltoPorDia = DB::table('tickets as t')
                ->join('estados as e', 'e.id_estado', '=', 't.estado_id')
                ->where('e.tipo', 'resuelto')
                ->whereNotNull('t.fecha_cierre')
                ->where('t.fecha_cierre', '>=', now()->subDays(14))
                ->select(DB::raw('DATE(t.fecha_cierre) as dia'), DB::raw('COUNT(*) as total'))
                ->groupBy('dia')
                ->orderBy('dia')
                ->get()
                ->keyBy('dia');

            // Llenar días faltantes con 0
            $diasLabels = [];
            $diasData   = [];
            for ($i = 13; $i >= 0; $i--) {
                $fecha = now()->subDays($i)->format('Y-m-d');
                $diasLabels[] = now()->subDays($i)->format('d/m');
                $diasData[]   = $resueltoPorDia->get($fecha)->total ?? 0;
            }

            return [
                'total'           => $total,
                'respondidas'     => $respondidas,
                'satisfechos'     => $satisfechos,
                'no_satisfechos'  => $noSatisfechos,
                'sin_responder'   => $sinResponder,
                'por_area'        => $porArea,
                'dias_labels'     => $diasLabels,
                'dias_data'       => $diasData,
            ];
        });

        return view('admin.dashboard', compact('stats', 'critical_tickets', 'satisfaccionStats'));
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

    public function rendimiento()
    {
        $tecnicos = Usuario::with(['rol'])
            ->whereHas('rol', function ($q) { $q->where('nombre', 'Técnico'); })
            ->where('activo', true)
            ->get()
            ->map(function ($u) {
                $base = Ticket::where('tecnico_asignado_id', $u->id_usuario);

                $cerrados   = (clone $base)->whereHas('estado', fn($q) => $q->whereIn('tipo', ['resuelto', 'cerrado']))->count();
                $en_proceso = (clone $base)->whereHas('estado', fn($q) => $q->where('tipo', 'en_proceso'))->count();
                $abiertos   = (clone $base)->whereHas('estado', fn($q) => $q->whereIn('tipo', ['abierto', 'pendiente']))->count();
                $total      = (clone $base)->count();
                $efectividad = $total > 0 ? round($cerrados / $total * 100) : 0;

                // Tiempo promedio de cierre (en horas)
                $tiempoPromedio = (clone $base)
                    ->whereNotNull('fecha_cierre')
                    ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, fecha_creacion, fecha_cierre)) as promedio')
                    ->value('promedio');

                return [
                    'tecnico'         => $u->nombre . ' ' . $u->apellido,
                    'correo'          => $u->correo,
                    'total_asignados' => $total,
                    'cerrados'        => $cerrados,
                    'en_proceso'      => $en_proceso,
                    'abiertos'        => $abiertos,
                    'efectividad'     => $efectividad,
                    'tiempo_promedio' => $tiempoPromedio ? round($tiempoPromedio, 1) : null,
                ];
            })->sortByDesc('total_asignados')->values()->toArray();

        return view('reportes.rendimiento', compact('tecnicos'));
    }

    public function porFecha(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin    = $request->input('fecha_fin',    now()->format('Y-m-d'));

        $query = Ticket::with(['usuario', 'area', 'prioridad', 'estado', 'tecnicoAsignado'])
            ->whereBetween('fecha_creacion', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
            ->orderBy('fecha_creacion', 'desc');

        $ticketsRaw = $query->get();

        $tickets = $ticketsRaw->map(function ($t) {
            return [
                'id_ticket'        => $t->id_ticket,
                'titulo'           => $t->titulo,
                'descripcion'      => $t->descripcion,
                'usuario'          => ['nombre_completo' => ($t->usuario->nombre ?? 'N/A') . ' ' . ($t->usuario->apellido ?? '')],
                'area'             => ['nombre' => $t->area->nombre ?? 'N/A'],
                'prioridad'        => ['nombre' => $t->prioridad->nombre ?? 'N/A', 'nivel' => $t->prioridad->id_prioridad ?? 1],
                'estado'           => ['nombre' => $t->estado->nombre ?? 'N/A', 'tipo' => $t->estado->tipo ?? 'abierto'],
                'tecnico_asignado' => $t->tecnicoAsignado ? ($t->tecnicoAsignado->nombre . ' ' . $t->tecnicoAsignado->apellido) : 'Sin asignar',
                'fecha_creacion'   => $t->fecha_creacion,
                'fecha_cierre'     => $t->fecha_cierre,
            ];
        })->toArray();

        $resumen = [
            'total'      => count($tickets),
            'abiertos'   => collect($tickets)->filter(fn($t) => in_array($t['estado']['tipo'], ['abierto', 'pendiente']))->count(),
            'en_proceso' => collect($tickets)->filter(fn($t) => $t['estado']['tipo'] === 'en_proceso')->count(),
            'cerrados'   => collect($tickets)->filter(fn($t) => in_array($t['estado']['tipo'], ['resuelto', 'cerrado']))->count(),
        ];

        return view('reportes.por-fecha', compact('tickets', 'resumen', 'fechaInicio', 'fechaFin'));
    }
}