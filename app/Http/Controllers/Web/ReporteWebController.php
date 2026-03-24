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

        // Técnicos activos para el filtro Power BI
        $tecnicos = Usuario::whereHas('rol', fn($q) => $q->where('nombre', 'Técnico'))
            ->where('activo', true)
            ->get(['id_usuario', 'nombre', 'apellido']);

        // Estadísticas dinámicas de tickets (para sección Power BI)
        $ticketStats = Cache::remember('ticket_stats_index', 5, function() {
            $q = Ticket::query();
            return [
                'total'      => $q->count(),
                'abiertos'   => (clone $q)->whereHas('estado', fn($q) => $q->whereIn('tipo', ['abierto','pendiente']))->count(),
                'en_proceso' => (clone $q)->whereHas('estado', fn($q) => $q->where('tipo', 'en_proceso'))->count(),
                'resueltos'  => (clone $q)->whereHas('estado', fn($q) => $q->whereIn('tipo', ['resuelto','cerrado']))->count(),
            ];
        });

        // Distribución por área y prioridad para gráficas iniciales
        $porAreaChart = DB::table('tickets as t')
            ->join('areas as a', 'a.id_area', '=', 't.area_id')
            ->select('a.nombre', DB::raw('count(*) as total'))
            ->groupBy('a.nombre')->get();

        $porPrioridadChart = DB::table('tickets as t')
            ->join('prioridades as p', 'p.id_prioridad', '=', 't.prioridad_id')
            ->select('p.nombre', DB::raw('count(*) as total'))
            ->groupBy('p.nombre')->get();

        // Promedios por pregunta de encuesta (para preview)
        $preguntaPromedios = Cache::remember('pregunta_promedios', 10, function() {
            $result = [];
            for ($i = 1; $i <= 5; $i++) {
                $col = "pregunta_$i";
                $avg = DB::table('encuestas_satisfaccion')
                    ->whereNotNull('respondida_at')->whereNotNull($col)->avg($col);
                $result[$i] = $avg ? round($avg, 1) : 0;
            }
            return $result;
        });

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

        return view('reportes.index', compact(
            'areas', 'estados', 'prioridades', 'satisfaccionStats',
            'tecnicos', 'ticketStats', 'porAreaChart', 'porPrioridadChart', 'preguntaPromedios'
        ));
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
        $fechaInicio = $request->input('fecha_inicio', now()->subDays(6)->format('Y-m-d'));
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

    /**
     * GET /reportes/encuestas
     * Página de detalle de encuestas estilo Google Forms:
     * distribución de las 5 preguntas + tabla de respondidas + tabla de pendientes.
     */
    public function encuestasDetalle(Request $request)
    {
        $preguntasTexto = [
            1 => '¿Está satisfecho con el trabajo realizado por el servicio de IT?',
            2 => '¿El personal de IT atiende adecuadamente sus solicitudes técnicas?',
            3 => '¿El servicio de IT soluciona su problema en un tiempo adecuado?',
            4 => '¿El personal de IT demuestra los conocimientos suficientes?',
            5 => '¿Se encuentra satisfecho con la atención recibida por el personal de IT?',
        ];

        $preguntasStats = [];
        for ($i = 1; $i <= 5; $i++) {
            $col  = "pregunta_$i";
            $dist = DB::table('encuestas_satisfaccion')
                ->whereNotNull('respondida_at')
                ->whereNotNull($col)
                ->selectRaw("$col as valor, count(*) as total")
                ->groupBy($col)
                ->pluck('total', 'valor')
                ->toArray();

            $avg = DB::table('encuestas_satisfaccion')
                ->whereNotNull('respondida_at')->whereNotNull($col)->avg($col);

            $preguntasStats[$i] = [
                'texto'    => $preguntasTexto[$i],
                'dist'     => [1 => $dist[1] ?? 0, 2 => $dist[2] ?? 0, 3 => $dist[3] ?? 0, 4 => $dist[4] ?? 0],
                'promedio' => $avg ? round($avg, 1) : 0,
                'total'    => ($dist[1] ?? 0) + ($dist[2] ?? 0) + ($dist[3] ?? 0) + ($dist[4] ?? 0),
            ];
        }

        $respondidas = EncuestaSatisfaccion::with(['ticket.area', 'usuario'])
            ->whereNotNull('respondida_at')
            ->orderBy('respondida_at', 'desc')
            ->get()
            ->map(function ($e) {
                $vals = array_filter([$e->pregunta_1, $e->pregunta_2, $e->pregunta_3, $e->pregunta_4, $e->pregunta_5]);
                $prom = count($vals) > 0 ? round(array_sum($vals) / count($vals), 1) : 0;
                return [
                    'id'            => $e->id_encuesta,
                    'ticket_id'     => $e->ticket_id,
                    'titulo'        => $e->ticket->titulo ?? 'N/A',
                    'area'          => $e->ticket->area->nombre ?? 'N/A',
                    'usuario'       => trim(($e->usuario->nombre ?? '') . ' ' . ($e->usuario->apellido ?? '')),
                    'pregunta_1'    => $e->pregunta_1,
                    'pregunta_2'    => $e->pregunta_2,
                    'pregunta_3'    => $e->pregunta_3,
                    'pregunta_4'    => $e->pregunta_4,
                    'pregunta_5'    => $e->pregunta_5,
                    'promedio'      => $prom,
                    'satisfecho'    => $e->satisfecho,
                    'respondida_at' => $e->respondida_at,
                    'comentario'    => $e->comentario,
                ];
            });

        $pendientes = EncuestaSatisfaccion::with(['ticket.area', 'usuario'])
            ->whereNull('respondida_at')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($e) {
                return [
                    'id'          => $e->id_encuesta,
                    'ticket_id'   => $e->ticket_id,
                    'titulo'      => $e->ticket->titulo ?? 'N/A',
                    'area'        => $e->ticket->area->nombre ?? 'N/A',
                    'usuario'     => trim(($e->usuario->nombre ?? '') . ' ' . ($e->usuario->apellido ?? '')),
                    'creado_at'   => $e->created_at,
                    'dias_espera' => $e->created_at ? (int) now()->diffInDays($e->created_at) : 0,
                ];
            });

        $totales = [
            'total'       => EncuestaSatisfaccion::count(),
            'respondidas' => EncuestaSatisfaccion::whereNotNull('respondida_at')->count(),
            'pendientes'  => EncuestaSatisfaccion::whereNull('respondida_at')->count(),
            'satisfechos' => EncuestaSatisfaccion::where('satisfecho', true)->count(),
        ];

        return view('reportes.encuestas', compact('preguntasStats', 'respondidas', 'pendientes', 'totales'));
    }

    /**
     * GET /reportes/filter-data  (AJAX – Power BI style)
     * Devuelve estadísticas de tickets y encuestas filtradas por área y/o técnico.
     */
    public function filterData(Request $request)
    {
        if (session('usuario_rol') !== 'Administrador') {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $areaId    = $request->input('area_id')    ? (int) $request->input('area_id')    : null;
        $tecnicoId = $request->input('tecnico_id') ? (int) $request->input('tecnico_id') : null;

        // ── KPIs de tickets ─────────────────────────────────────────────────
        $q = Ticket::query();
        if ($areaId)    $q->where('area_id', $areaId);
        if ($tecnicoId) $q->where('tecnico_asignado_id', $tecnicoId);

        $total      = (clone $q)->count();
        $abiertos   = (clone $q)->whereHas('estado', fn($q) => $q->whereIn('tipo', ['abierto','pendiente']))->count();
        $enProceso  = (clone $q)->whereHas('estado', fn($q) => $q->where('tipo', 'en_proceso'))->count();
        $resueltos  = (clone $q)->whereHas('estado', fn($q) => $q->whereIn('tipo', ['resuelto','cerrado']))->count();

        // ── Por área (siempre todas, para comparar) ──────────────────────────
        $porArea = DB::table('tickets as t')
            ->join('areas as a', 'a.id_area', '=', 't.area_id')
            ->when($tecnicoId, fn($q) => $q->where('t.tecnico_asignado_id', $tecnicoId))
            ->select('a.nombre', DB::raw('count(*) as total'))
            ->groupBy('a.nombre')->get();

        // ── Por prioridad ────────────────────────────────────────────────────
        $porPrioridad = DB::table('tickets as t')
            ->join('prioridades as p', 'p.id_prioridad', '=', 't.prioridad_id')
            ->when($areaId,    fn($q) => $q->where('t.area_id', $areaId))
            ->when($tecnicoId, fn($q) => $q->where('t.tecnico_asignado_id', $tecnicoId))
            ->select('p.nombre', DB::raw('count(*) as total'))
            ->groupBy('p.nombre')->get();

        // ── Resolución últimos 14 días ───────────────────────────────────────
        $rpd = DB::table('tickets as t')
            ->join('estados as e', 'e.id_estado', '=', 't.estado_id')
            ->where('e.tipo', 'resuelto')
            ->whereNotNull('t.fecha_cierre')
            ->where('t.fecha_cierre', '>=', now()->subDays(14))
            ->when($areaId,    fn($q) => $q->where('t.area_id', $areaId))
            ->when($tecnicoId, fn($q) => $q->where('t.tecnico_asignado_id', $tecnicoId))
            ->select(DB::raw('DATE(t.fecha_cierre) as dia'), DB::raw('COUNT(*) as total'))
            ->groupBy('dia')->orderBy('dia')->get()->keyBy('dia');

        $diasLabels = []; $diasData = [];
        for ($i = 13; $i >= 0; $i--) {
            $fecha = now()->subDays($i)->format('Y-m-d');
            $diasLabels[] = now()->subDays($i)->format('d/m');
            $diasData[]   = $rpd->get($fecha)->total ?? 0;
        }

        // ── Encuesta stats (filtrado por área) ───────────────────────────────
        $encQ = DB::table('encuestas_satisfaccion as e')
            ->join('tickets as t', 't.id_ticket', '=', 'e.ticket_id');
        if ($areaId) $encQ->where('t.area_id', $areaId);

        $encTotal       = (clone $encQ)->count();
        $encRespondidas = (clone $encQ)->whereNotNull('e.respondida_at')->count();
        $encSatisfechos    = (clone $encQ)->whereNotNull('e.respondida_at')->where('e.satisfecho', 1)->count();
        $encNoSatisfechos  = (clone $encQ)->whereNotNull('e.respondida_at')->where('e.satisfecho', 0)->count();
        $encSinResponder   = $encTotal - $encRespondidas;
        $encPctSatisfaccion = $encRespondidas > 0 ? round($encSatisfechos / $encRespondidas * 100, 1) : 0;
        $encTasaPct         = $encTotal > 0 ? round($encRespondidas / $encTotal * 100, 1) : 0;

        $encPorArea = DB::table('encuestas_satisfaccion as e')
            ->join('tickets as t', 't.id_ticket', '=', 'e.ticket_id')
            ->join('areas as a',   'a.id_area',   '=', 't.area_id')
            ->whereNotNull('e.respondida_at')
            ->when($areaId, fn($q) => $q->where('t.area_id', $areaId))
            ->select('a.nombre as area',
                DB::raw('SUM(CASE WHEN e.satisfecho = 1 THEN 1 ELSE 0 END) as satisfechos'),
                DB::raw('SUM(CASE WHEN e.satisfecho = 0 THEN 1 ELSE 0 END) as no_satisfechos'))
            ->groupBy('a.nombre')->get();

        return response()->json([
            'total'                => $total,
            'abiertos'             => $abiertos,
            'en_proceso'           => $enProceso,
            'resueltos'            => $resueltos,
            'por_area'             => $porArea,
            'por_prioridad'        => $porPrioridad,
            'dias_labels'          => $diasLabels,
            'dias_data'            => $diasData,
            'enc_satisfechos'      => $encSatisfechos,
            'enc_no_satisfechos'   => $encNoSatisfechos,
            'enc_sin_responder'    => $encSinResponder,
            'enc_satisfaccion_pct' => $encPctSatisfaccion,
            'enc_tasa_pct'         => $encTasaPct,
            'enc_por_area'         => $encPorArea,
        ]);
    }
}