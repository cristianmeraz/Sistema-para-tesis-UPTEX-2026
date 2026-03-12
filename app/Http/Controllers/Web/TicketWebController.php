<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Estado;
use App\Models\Prioridad;
use App\Models\Area;
use App\Models\Usuario;
use App\Models\Comentario;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\TicketCreadoMail;
use App\Mail\TicketEstadoCambiadoMail;

class TicketWebController extends Controller
{
    /** 1. LISTADO: Solo Administrador y Técnico tienen acceso */
    public function index(Request $request)
    {
        try {
            $rol = session('usuario_rol');

            // Usuario Normal no puede acceder al listado general de tickets
            if ($rol !== 'Administrador' && !str_contains($rol, 'Técnico')) {
                return redirect()->route('tickets.mis-tickets')
                    ->with('error', 'No tienes permiso para ver esta sección.');
            }

            $query = Ticket::with(['usuario', 'area', 'prioridad', 'estado', 'tecnicoAsignado']);
            
            // Si el rol contiene la palabra "Técnico", solo ve sus tickets asignados
            if (str_contains($rol, 'Técnico')) {
                $tecnicoId = session('usuario_id');
                $query->where('tecnico_asignado_id', $tecnicoId);
            }

            // ── Filtros disponibles solo para Administrador ──────────────────
            if ($rol === 'Administrador') {
                // Filtro por tipo de estado (viene del dashboard: tipo=abierto|en_proceso|resuelto|pendiente|cerrado)
                if ($request->filled('tipo')) {
                    $query->whereHas('estado', fn($q) => $q->where('tipo', $request->tipo));
                }
                // Filtro por id_estado directo (selector del formulario)
                if ($request->filled('estado_id')) {
                    $query->where('estado_id', $request->estado_id);
                }
                // Filtro por prioridad
                if ($request->filled('prioridad_id')) {
                    $query->where('prioridad_id', $request->prioridad_id);
                }
                // Filtro por nombre de prioridad (viene de los cards del dashboard)
                if ($request->filled('prioridad_nombre')) {
                    $query->whereHas('prioridad', fn($q) => $q->where('nombre', $request->prioridad_nombre));
                }
                // Filtro por área/departamento
                if ($request->filled('area_id')) {
                    $query->where('area_id', $request->area_id);
                }
                // Filtro sin técnico asignado
                if ($request->filled('sin_tecnico') && $request->sin_tecnico === '1') {
                    $query->whereNull('tecnico_asignado_id');
                }
                // Búsqueda de texto
                if ($request->filled('search')) {
                    $search = '%' . $request->search . '%';
                    $query->where(function($q) use ($search) {
                        $q->where('titulo', 'like', $search)
                          ->orWhere('descripcion', 'like', $search);
                    });
                }
            }
            
            // ✅ FIX: Ordenar por prioridad (Alta→Media→Baja) y luego por fecha
            $tickets = $query
                ->orderByRaw('COALESCE((SELECT nivel FROM prioridades WHERE id_prioridad = tickets.prioridad_id), 0) DESC')
                ->orderBy('fecha_creacion', 'desc')
                ->paginate(50)->withQueryString();
            
            // Transformar tickets a formato array
            $tickets->getCollection()->transform(function($t) {
                return [
                    'id_ticket' => $t->id_ticket,
                    'titulo' => $t->titulo,
                    'descripcion' => $t->descripcion,
                    'fecha_creacion' => $t->fecha_creacion,
                    'usuario' => [ 'nombre_completo' => ($t->usuario->nombre ?? 'N/A') . ' ' . ($t->usuario->apellido ?? '') ],
                    'area' => [ 'nombre' => $t->area->nombre ?? 'N/A' ],
                    'prioridad' => [ 'nombre' => $t->prioridad->nombre ?? 'N/A' ],
                    'estado' => [ 'nombre' => $t->estado->nombre ?? 'N/A', 'tipo' => $t->estado->tipo ?? 'abierto' ],
                    'tecnico_asignado' => $t->tecnicoAsignado ? [ 'nombre_completo' => ($t->tecnicoAsignado->nombre ?? 'N/A') . ' ' . ($t->tecnicoAsignado->apellido ?? '') ] : null,
                ];
            });
            
            // Caché de catálogos: 60 minutos
            $estados = Cache::remember('estados_catalogo', 60, function() { return Estado::all(); });
            $prioridades = Cache::remember('prioridades_catalogo', 60, function() { return Prioridad::all(); });
            $areas = Cache::remember('areas_catalogo', 60, function() { return Area::all(); });

            // ── Estadísticas para KPI cards (cacheadas 5 min) ────────────────
            $cacheKey = 'ticket_stats_' . ($rol === 'Administrador' ? 'admin' : ('tec_' . session('usuario_id')));
            $ticketStats = Cache::remember($cacheKey, 5, function() use ($rol) {
                $base = Ticket::query();
                if (str_contains($rol, 'Técnico')) {
                    $base->where('tecnico_asignado_id', session('usuario_id'));
                }
                $abiertos   = (clone $base)->whereHas('estado', fn($q) => $q->where('tipo', 'abierto'))->count();
                $en_proceso = (clone $base)->whereHas('estado', fn($q) => $q->where('tipo', 'en_proceso'))->count();
                $criticos   = (clone $base)->whereHas('prioridad', fn($q) => $q->where('nombre', 'Alta'))->count();
                $total      = (clone $base)->count();
                return compact('abiertos', 'en_proceso', 'criticos', 'total');
            });
            
            return view('tickets.index', compact('tickets', 'estados', 'prioridades', 'areas', 'ticketStats'));
        } catch (\Exception $e) { return view('tickets.index', ['tickets' => null]); }
    }

    /** 2. DETALLE: Optimizado con eager loading correcto */
    public function show($id)
    {
        try {
            // ✅ Eager loading optimizado: INCLUIR rol EN Usuario
            $t = Ticket::with([
                'usuario', 
                'area', 
                'prioridad', 
                'estado', 
                'tecnicoAsignado',
                'comentarios' => function($q) {
                    // ✅ IMPORTANTE: Cargar usuario.rol para evitar N+1 queries
                    $q->with(['usuario' => function($q2) {
                        $q2->with('rol');
                    }])
                    ->orderBy('created_at', 'desc')
                    ->limit(10);
                }
            ])->findOrFail($id);
            
            // ── Control de acceso por rol ──
            $rol = session('usuario_rol');
            $uid = session('usuario_id');

            if ($rol === 'Usuario Normal') {
                // Un usuario normal solo puede ver SUS propios tickets
                if ($t->usuario_id != $uid) {
                    return redirect()->route('tickets.mis-tickets')
                        ->with('error', 'No tienes permiso para ver ese ticket.');
                }
            } elseif (str_contains($rol, 'Técnico')) {
                // Un técnico solo puede ver los tickets que tiene asignados
                if ($t->tecnico_asignado_id != $uid) {
                    return redirect()->route('tickets.asignados')
                        ->with('error', 'No tienes permiso para ver ese ticket.');
                }
            }
            // Administrador: acceso total — sin restricción
            $ticket = $t->toArray();
            $ticket['usuario']['nombre_completo'] = ($t->usuario->nombre ?? 'N/A') . ' ' . ($t->usuario->apellido ?? '');
            $ticket['area']['nombre'] = $t->area->nombre ?? 'N/A';
            $ticket['prioridad']['nombre'] = $t->prioridad->nombre ?? 'N/A';
            $ticket['estado']['nombre'] = $t->estado->nombre ?? 'N/A';
            $ticket['estado']['tipo'] = $t->estado->tipo ?? 'abierto';
            // Formatear updated_at con la zona horaria correcta
            $ticket['updated_at_formatted'] = $t->updated_at->setTimezone('America/Mexico_City')->format('d/m/Y H:i');
            // Nombre completo del técnico asignado
            $ticket['tecnico_asignado']['nombre_completo'] = ($t->tecnicoAsignado->nombre ?? 'N/A') . ' ' . ($t->tecnicoAsignado->apellido ?? '');
            if (str_contains(session('usuario_rol'), 'Técnico')) {
                $estados = Estado::whereIn('tipo', ['en_proceso', 'pendiente', 'resuelto'])->get();
            } else { $estados = Estado::all(); }
            
            // ✅ Obtener comentarios con rol del usuario (ya está en eager loading)
            $comentarios = $t->comentarios->map(function($c) {
                return [
                    'id_comentario'    => $c->id_comentario,
                    'usuario' => [
                        'nombre'   => $c->usuario->nombre ?? 'N/A',
                        'apellido' => $c->usuario->apellido ?? '',
                        'rol'      => $c->usuario->rol?->nombre ?? 'Usuario Normal'
                    ],
                    'contenido'        => $c->contenido,
                    'es_actualizacion' => (bool) $c->es_actualizacion,
                    'created_at'       => $c->created_at,
                ];
            })->toArray();
            
            // Para administrador: obtener lista de técnicos disponibles
            $tecnicos    = Usuario::whereHas('rol', function($q) { $q->where('nombre', 'Técnico'); })->where('activo', true)->get();
            $prioridades = Cache::remember('prioridades_catalogo', 60, fn() => Prioridad::orderBy('nivel')->get());

            // Variables de rol explícitas — el controlador determina el panel, NO session() en la vista
            $esAdmin   = ($rol === 'Administrador');
            $esTecnico = str_contains($rol, 'Técnico');

            return view('tickets.show', compact('ticket', 'estados', 'comentarios', 'tecnicos', 'prioridades', 'esAdmin', 'esTecnico'));
        } catch (\Exception $e) { return redirect()->route('dashboard')->with('error', 'Error al abrir el ticket'); }
    }

    /** 3. EDICIÓN Y ACTUALIZACIÓN: CON CACHÉ OPTIMIZADO */
    public function edit($id) {
        $ticket = Ticket::findOrFail($id);
        $rol = session('usuario_rol');
        $uid = session('usuario_id');

        // Usuario Normal solo puede editar sus propios tickets
        if ($rol === 'Usuario Normal') {
            if ($ticket->usuario_id != $uid) {
                return redirect()->route('tickets.mis-tickets')
                    ->with('error', 'No tienes permiso para editar ese ticket.');
            }
            // ✅ FIX U-3: Usuario Normal no puede editar tickets en estado cerrado/resuelto/cancelado
            $ticket->load('estado');
            if (in_array($ticket->estado?->tipo, ['resuelto', 'cerrado', 'cancelado'])) {
                return redirect()->route('tickets.show', $id)
                    ->with('error', 'No puedes editar un ticket que ya está resuelto, cerrado o cancelado.');
            }
        } elseif (str_contains($rol, 'Técnico')) {
            if ($ticket->tecnico_asignado_id != $uid) {
                return redirect()->route('tickets.asignados')
                    ->with('error', 'No tienes permiso para editar este ticket.');
            }
        }
        $estados = Cache::remember('estados_catalogo', 60, function() { return Estado::all(); });
        $areas = Cache::remember('areas_catalogo', 60, function() { return Area::all(); });
        $prioridades = Cache::remember('prioridades_catalogo', 60, function() { return Prioridad::all(); });
        return view('tickets.edit', compact('ticket', 'estados', 'areas', 'prioridades'));
    }

    public function update(Request $request, $id) {
        $ticket = Ticket::findOrFail($id);
        if (str_contains(session('usuario_rol'), 'Técnico')) {
            if ($ticket->tecnico_asignado_id != session('usuario_id')) {
                // ✅ FIX A-8: Técnicos no pueden acceder a tickets.index (ruta de Admin)
                return redirect()->route('tickets.asignados')->with('error', 'No tienes permiso para actualizar este ticket');
            }
        }

        // Validar solo los campos editables; nunca confiar en los del request
        $rules = [
            'titulo'       => 'sometimes|required|string|min:5|max:200',
            'descripcion'  => 'sometimes|required|string|min:10|max:5000',
            'area_id'      => 'sometimes|required|integer|exists:areas,id_area',
            'prioridad_id' => 'sometimes|required|integer|exists:prioridades,id_prioridad',
            'solucion'     => 'nullable|string|max:5000',
        ];
        // Solo administrador puede cambiar estado directamente desde edición
        if (session('usuario_rol') === 'Administrador') {
            $rules['estado_id'] = 'sometimes|required|integer|exists:estados,id_estado';
        }

        $validated = $request->validate($rules);
        $ticket->update($validated);

        // ✅ FIX A-8: Redirigir según rol (tickets.index es solo Admin)
        $rolActual = session('usuario_rol');
        if ($rolActual === 'Administrador') {
            return redirect()->route('tickets.index')->with('success', 'Ticket actualizado');
        } elseif (str_contains($rolActual, 'Técnico')) {
            return redirect()->route('tickets.asignados')->with('success', 'Ticket actualizado');
        } else {
            return redirect()->route('tickets.mis-tickets')->with('success', 'Ticket actualizado');
        }
    }

    /** 4. PANEL DE TRABAJO TÉCNICO: CON EAGER LOADING OPTIMIZADO */
    public function asignados() { 
        $tecnicoId = session('usuario_id'); 
        
        // Eager loading optimizado: obtener tickets con todas las relaciones
        $allTickets = Ticket::with(['usuario', 'area', 'prioridad', 'estado'])
            ->where('tecnico_asignado_id', $tecnicoId)
            ->get();
        
        // Calcular estadísticas desde los datos cargados (sin consultas adicionales)
        $stats = [
            // ✅ FIX T-7: "totales" solo cuenta tickets activos (no cerrados/resueltos/cancelados)
            'totales'   => $allTickets->filter(fn($t) => in_array($t->estado?->tipo, ['abierto', 'en_proceso', 'pendiente']))->count(),
            'en_proceso' => $allTickets->filter(fn($t) => $t->estado?->tipo === 'en_proceso')->count(),
            'pendientes' => $allTickets->filter(fn($t) => $t->estado?->tipo === 'pendiente')->count(),
            'resueltos'  => $allTickets->filter(fn($t) => $t->estado?->tipo === 'resuelto')->count(),
            'baja'    => $allTickets->filter(fn($t) => $t->prioridad?->nombre === 'Baja')->count(),
            'media'   => $allTickets->filter(fn($t) => $t->prioridad?->nombre === 'Media')->count(),
            'alta'    => $allTickets->filter(fn($t) => $t->prioridad?->nombre === 'Alta')->count(),
            'critico' => $allTickets->filter(fn($t) => $t->prioridad?->nombre === 'Alta' && in_array($t->estado?->tipo, ['abierto', 'en_proceso', 'pendiente']))->count(),
        ];
        
        // Filtrar solo los pendientes
        $tickets_pendientes = $allTickets->filter(fn($t) => in_array($t->estado?->tipo, ['abierto', 'en_proceso', 'pendiente'])); 
        foreach ($tickets_pendientes as $ticket) {
            if (!$ticket->usuario) { $ticket->setRelation('usuario', new \App\Models\Usuario(['nombre' => 'N/A', 'apellido' => ''])); }
        }
        return view('tickets.asignados', ['tickets' => $tickets_pendientes, 'stats' => $stats]); 
    }

    /** 5. USUARIO NORMAL Y TÉCNICO: Ver sus tickets */
    public function misTickets(Request $request) {
        try {
            $usuarioId = session('usuario_id');
            
            // Si es técnico, ve sus tickets asignados
            if (str_contains(session('usuario_rol'), 'Técnico')) {
                $query = Ticket::with(['usuario', 'area', 'prioridad', 'estado', 'tecnicoAsignado'])
                    ->where('tecnico_asignado_id', $usuarioId);
                
                // Aplicar filtros de estado
                if ($request->estado_id) {
                    $query->where('estado_id', $request->estado_id);
                }
                
                // Aplicar filtros de prioridad
                if ($request->prioridad_id) {
                    $query->where('prioridad_id', $request->prioridad_id);
                }
                
                // Aplicar búsqueda por texto
                if ($request->search) {
                    $search = '%' . $request->search . '%';
                    $query->where(function($q) use ($search) {
                        $q->where('titulo', 'like', $search)
                          ->orWhere('descripcion', 'like', $search);
                    });
                }
                
                $tickets = $query->orderBy('fecha_creacion', 'desc')->get();
            } else {
                // Si es usuario normal, ve sus propios tickets creados
                $query = Ticket::with(['estado', 'prioridad', 'area'])
                    ->where('usuario_id', $usuarioId);
                
                // Aplicar filtros de estado
                if ($request->estado_id) {
                    $query->where('estado_id', $request->estado_id);
                }
                
                // Aplicar filtros de prioridad
                if ($request->prioridad_id) {
                    $query->where('prioridad_id', $request->prioridad_id);
                }
                
                // Búsqueda de texto dentro de una subcondición para no romper el where de usuario_id
                if ($request->search) {
                    $search = '%' . $request->search . '%';
                    $query->where(function($q) use ($search) {
                        $q->where('titulo', 'like', $search)
                          ->orWhere('descripcion', 'like', $search);
                    });
                }
                
                $tickets = $query->orderBy('fecha_creacion', 'desc')->get();
            }
            
            // Pasar los catálogos y filtros actuales a la vista (con caché)
            $estados = Cache::remember('estados_catalogo', 60, function() { return Estado::all(); });
            $prioridades = Cache::remember('prioridades_catalogo', 60, function() { return Prioridad::all(); });
            
            return view('tickets.mis-tickets', compact('tickets', 'estados', 'prioridades'));
        } catch (\Exception $e) { return redirect()->route('dashboard'); }
    }

    /** 6. CREACIÓN: SECCIÓN BLOQUEADA PARA TÉCNICOS - CON CACHÉ */
    public function create() { 
        // BLOQUEO PARA EL ROL TÉCNICO
        if (str_contains(session('usuario_rol'), 'Técnico')) {
            return redirect()->route('tickets.index')->with('error', 'Los técnicos no pueden crear tickets.');
        }
        $areas = Cache::remember('areas_catalogo', 60, function() { return Area::all(); });

        // Solo el Admin elige prioridad al crear; el Usuario Normal no la selecciona
        if (session('usuario_rol') === 'Administrador') {
            $prioridades = Cache::remember('prioridades_catalogo', 60, fn() => Prioridad::orderBy('nivel')->get());
        } else {
            $prioridades = collect(); // Usuario Normal: vacío, no se muestra el select
        }

        $userAreaId = session('usuario_area_id');
        $esAdmin    = session('usuario_rol') === 'Administrador';
        return view('tickets.create', compact('areas', 'prioridades', 'userAreaId', 'esAdmin')); 
    }

    public function store(Request $request) {
        // BLOQUEO PARA EL ROL TÉCNICO
        if (str_contains(session('usuario_rol'), 'Técnico')) {
            return redirect()->route('tickets.index')->with('error', 'Operación no permitida.');
        }

        $esAdmin = session('usuario_rol') === 'Administrador';

        $validated = $request->validate([
            'titulo'       => 'required|string|min:5|max:200',
            'descripcion'  => 'required|string|min:10|max:5000',
            'area_id'      => 'required|integer|exists:areas,id_area',
            // Solo el Admin envía prioridad al crear el ticket
            'prioridad_id' => $esAdmin ? 'required|integer|exists:prioridades,id_prioridad' : 'nullable',
        ]);

        // usuario_id y estado_id siempre los asigna el servidor, nunca el cliente
        // Usuario Normal: prioridad_id queda null hasta que el Admin la asigne
        $ticket = Ticket::create([
            'titulo'         => $validated['titulo'],
            'descripcion'    => $validated['descripcion'],
            'area_id'        => $validated['area_id'],
            'prioridad_id'   => $esAdmin ? ($validated['prioridad_id'] ?? null) : null,
            'usuario_id'     => session('usuario_id'),
            'estado_id'      => Estado::where('tipo', 'abierto')->value('id_estado') ?? 1,
            'fecha_creacion' => now(),
        ]);

        // ── NOTIFICACIONES POR CORREO ──────────────────────────────────────
        try {
            $ticket->load(['usuario', 'area', 'prioridad', 'estado']);

            // 1. Notificar al usuario que creó el ticket
            if (!empty($ticket->usuario->correo)) {
                Mail::to($ticket->usuario->correo)
                    ->send(new TicketCreadoMail($ticket, 'usuario'));
            }

            // 2. Notificar a todos los Administradores
            $admins = Usuario::whereHas('rol', fn($q) => $q->where('nombre', 'Administrador'))
                ->where('activo', true)
                ->whereNotNull('correo')
                ->get();

            foreach ($admins as $admin) {
                // No duplicar si el admin fue quien creó el ticket
                if ($admin->id_usuario === session('usuario_id')) continue;
                Mail::to($admin->correo)
                    ->send(new TicketCreadoMail($ticket, 'admin'));
            }
        } catch (\Exception $mailEx) {
            // El fallo de correo NO debe impedir que el ticket se cree
            Log::error('TicketCreadoMail error ticket#' . $ticket->id_ticket . ': ' . $mailEx->getMessage());
        }
        // ──────────────────────────────────────────────────────────────────

        return redirect()->route('dashboard')->with('success', 'Ticket creado correctamente. Recibirás una confirmación por correo.');
    }

    /** 7. CAMBIO DE ESTADO Y COMENTARIOS: Técnico y Admin */
    public function cambiarEstado(Request $request, $id) {
        try {
            // Solo Admin y Técnico pueden cambiar estado
            $rol = session('usuario_rol');
            if (!str_contains($rol, 'Administrador') && !str_contains($rol, 'Técnico')) {
                return redirect()->route('tickets.show', $id)->with('error', 'No tienes permiso para modificar el estado del ticket');
            }

            $request->validate([
                'estado_id' => 'required|exists:estados,id_estado',
                'contenido' => 'required|string|min:5',
            ]);

            $ticket = Ticket::with(['usuario', 'area', 'prioridad', 'estado', 'tecnicoAsignado'])
                ->findOrFail($id);

            // El Técnico solo puede modificar tickets que tiene asignados
            if (str_contains($rol, 'Técnico')) {
                if ($ticket->tecnico_asignado_id != session('usuario_id')) {
                    return redirect()->route('tickets.show', $id)->with('error', 'No tienes permiso para modificar este ticket');
                }

                // ✅ FIX T-6: Máquina de estados — Técnicos solo pueden hacer transiciones válidas
                $estadoActualTipo = $ticket->estado?->tipo ?? 'abierto';
                $estadoNuevoObj  = Estado::find($request->estado_id);
                $estadoNuevoTipoReq = $estadoNuevoObj?->tipo ?? '';
                $transicionesPermitidas = [
                    'abierto'    => ['en_proceso', 'pendiente'],
                    'en_proceso' => ['pendiente', 'resuelto'],
                    'pendiente'  => ['en_proceso', 'resuelto'],
                ];
                $permitidos = $transicionesPermitidas[$estadoActualTipo] ?? [];
                if (!in_array($estadoNuevoTipoReq, $permitidos)) {
                    return redirect()->route('tickets.show', $id)
                        ->with('error', "Transición no permitida: no puedes pasar de '{$estadoActualTipo}' a '{$estadoNuevoTipoReq}'.");
                }
            }

            // ── BLOQUEAR CIERRE SI LA ENCUESTA NO FUE RESPONDIDA ─────────
            $estadoNuevoSolicitado = Estado::find($request->estado_id);
            if ($estadoNuevoSolicitado && strtolower($estadoNuevoSolicitado->tipo) === 'cerrado') {
                $encuestaPendiente = \App\Models\EncuestaSatisfaccion::where('ticket_id', $id)->first();
                if ($encuestaPendiente && !$encuestaPendiente->estaRespondida()) {
                    return redirect()->route('tickets.show', $id)
                        ->with('error', 'No puedes cerrar el ticket hasta que el usuario responda la encuesta de satisfacción.');
                }
            }
            // ─────────────────────────────────────────────────────────────

            // Capturar estado anterior ANTES de actualizarlo
            $estadoAnteriorNombre = $ticket->estado->nombre ?? 'N/A';
            $estadoAnteriorTipo   = $ticket->estado->tipo   ?? 'abierto';

            // Actualizar estado
            $ticket->estado_id = $request->estado_id;
            $ticket->save();

            // Si el estado es "Resuelto" o "Cerrado", registrar fecha de cierre
            $estadoNuevo = Estado::find($request->estado_id);
            if ($estadoNuevo && in_array(strtolower($estadoNuevo->tipo), ['resuelto', 'cerrado'])) {
                $ticket->fecha_cierre = now();
                $ticket->save();
            }

            // Recargar relación de estado para tener el nuevo
            $ticket->load('estado');

            // Crear comentario (marcado como actualización técnica/admin)
            Comentario::create([
                'ticket_id'        => $id,
                'usuario_id'       => session('usuario_id'),
                'contenido'        => $request->contenido,
                'es_actualizacion' => true,
            ]);

            // ── NOTIFICACIONES POR CORREO ────────────────────────────────
            try {
                $operadorNombre = session('usuario_nombre', 'Equipo de Soporte');
                $estadoNuevoNombre = $ticket->estado->nombre ?? 'N/A';
                $estadoNuevoTipo   = $ticket->estado->tipo   ?? 'abierto';
                $esTecnicoOp = str_contains($rol, 'Técnico');

                // 1. Notificar al usuario creador del ticket
                if (!empty($ticket->usuario->correo)) {
                    Mail::to($ticket->usuario->correo)
                        ->send(new TicketEstadoCambiadoMail(
                            $ticket, $estadoAnteriorNombre, $estadoAnteriorTipo,
                            $estadoNuevoNombre, $estadoNuevoTipo,
                            $request->contenido, $operadorNombre, 'usuario'
                        ));
                }

                // 2. Notificar al técnico asignado (si existe y no es quien opera)
                $tecnico = $ticket->tecnicoAsignado;
                if ($tecnico && !empty($tecnico->correo) && $tecnico->id_usuario !== session('usuario_id')) {
                    Mail::to($tecnico->correo)
                        ->send(new TicketEstadoCambiadoMail(
                            $ticket, $estadoAnteriorNombre, $estadoAnteriorTipo,
                            $estadoNuevoNombre, $estadoNuevoTipo,
                            $request->contenido, $operadorNombre, 'tecnico'
                        ));
                }

                // 3. Notificar a todos los Administradores
                $admins = Usuario::whereHas('rol', fn($q) => $q->where('nombre', 'Administrador'))
                    ->where('activo', true)
                    ->whereNotNull('correo')
                    ->get();

                foreach ($admins as $admin) {
                    // No duplicar si el admin es quien operó
                    if ($admin->id_usuario === session('usuario_id')) continue;
                    Mail::to($admin->correo)
                        ->send(new TicketEstadoCambiadoMail(
                            $ticket, $estadoAnteriorNombre, $estadoAnteriorTipo,
                            $estadoNuevoNombre, $estadoNuevoTipo,
                            $request->contenido, $operadorNombre, 'admin'
                        ));
                }
            } catch (\Exception $mailEx) {
                // El fallo de correo NO impide guardar el cambio
                Log::error('TicketEstadoCambiadoMail error ticket#' . $id . ': ' . $mailEx->getMessage());
            }
            // ─────────────────────────────────────────────────────────────

            // ── ENCUESTA DE SATISFACCIÓN (si nuevo estado = resuelto) ────
            if (strtolower($estadoNuevoTipo) === 'resuelto') {
                try {
                    $yaExiste = \App\Models\EncuestaSatisfaccion::where('ticket_id', $id)->exists();
                    if (!$yaExiste) {
                        $encuesta = \App\Models\EncuestaSatisfaccion::create([
                            'ticket_id'  => $id,
                            'usuario_id' => $ticket->usuario_id,
                            'token'      => bin2hex(random_bytes(32)),
                        ]);
                        $encuesta->load(['ticket', 'usuario']);
                        if (!empty($ticket->usuario->correo)) {
                            Mail::to($ticket->usuario->correo)
                                ->send(new \App\Mail\EncuestaSatisfaccionMail($encuesta));
                        }
                    }
                } catch (\Exception $encEx) {
                    Log::error('EncuestaSatisfaccionMail error ticket#' . $id . ': ' . $encEx->getMessage());
                }
            }
            // ─────────────────────────────────────────────────────────────

            $msgExito = $esTecnicoOp
                ? 'Estado actualizado, comentario guardado y notificaciones enviadas.'
                : 'Estado y comentario guardados. Notificaciones enviadas.';

            return redirect()->route('tickets.show', $id)->with('success', $msgExito);
        } catch (\Exception $e) {
            \Log::error('cambiarEstado error ticket#' . $id . ': ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al actualizar el estado. Inténtalo de nuevo.');
        }
    }

    /** 8. ASIGNAR TÉCNICO: Solo Administrador */
    public function asignarTecnico(Request $request, $id)
    {
        try {
            // Validar que solo administrador pueda hacer esto
            if (!str_contains(session('usuario_rol'), 'Administrador')) {
                return redirect()->back()->with('error', 'No tienes permiso para asignar técnicos');
            }

            $request->validate([
                'tecnico_id' => 'nullable|exists:usuarios,id_usuario',
            ]);

            $ticket = Ticket::findOrFail($id);
            $ticket->tecnico_asignado_id = $request->tecnico_id;
            $ticket->save();

            // ✅ Notificar al técnico que se le asignó la tarea
            if ($request->tecnico_id) {
                try {
                    $tecnico = Usuario::find($request->tecnico_id);
                    if ($tecnico && !empty($tecnico->correo)) {
                        $ticket->load(['usuario', 'area', 'prioridad', 'estado']);
                        Mail::to($tecnico->correo)
                            ->send(new TicketCreadoMail($ticket, 'tecnico'));
                    }
                } catch (\Exception $mailEx) {
                    Log::error('Notificación asignación técnico error ticket#' . $id . ': ' . $mailEx->getMessage());
                }
            }

            return redirect()->route('tickets.show', $id)->with('success', 'Técnico asignado correctamente');
        } catch (\Exception $e) {
            \Log::error('asignarTecnico error ticket#' . $id . ': ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al asignar el técnico. Inténtalo de nuevo.');
        }
    }

    /** CAMBIAR PRIORIDAD: Solo Administrador */
    public function cambiarPrioridad(Request $request, $id)
    {
        if (session('usuario_rol') !== 'Administrador') {
            return redirect()->route('tickets.show', $id)
                ->with('error', 'Solo el administrador puede asignar la prioridad.');
        }

        $request->validate([
            'prioridad_id' => 'required|integer|exists:prioridades,id_prioridad',
        ], [
            'prioridad_id.required' => 'Debes seleccionar una prioridad.',
            'prioridad_id.exists'   => 'La prioridad seleccionada no es válida.',
        ]);

        $ticket = Ticket::findOrFail($id);
        $ticket->prioridad_id = $request->prioridad_id;
        $ticket->save();

        Cache::forget('prioridades_catalogo');

        return redirect()->route('tickets.show', $id)
            ->with('success', 'Prioridad asignada correctamente.');
    }

    // ===== ENDPOINTS API PARA AUTO-REFRESH =====

    /** API: Obtener contadores de tickets */
    public function apiContadores()
    {
        try {
            $tecnicoId = session('usuario_id');
            $estado = Ticket::where('tecnico_asignado_id', $tecnicoId)->with('estado')->get();
            
            $contadores = [
                'total' => $estado->count(),
                'en_proceso' => $estado->filter(fn($t) => $t->estado->nombre === 'En Proceso')->count(),
                'pendiente' => $estado->filter(fn($t) => $t->estado->nombre === 'Pendiente')->count(),
                'resuelto' => $estado->filter(fn($t) => $t->estado->nombre === 'Resuelto')->count(),
            ];
            
            return response()->json($contadores);
        } catch (\Exception $e) {
            \Log::error('apiContadores error: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener contadores'], 500);
        }
    }

    /** API: Obtener lista de mis tickets en JSON */
    public function apiMisTickets(Request $request)
    {
        try {
            // ✅ FIX T-3: Solo Técnicos pueden usar este endpoint
            if (!str_contains(session('usuario_rol'), 'Técnico')) {
                return response()->json(['error' => 'No autorizado'], 403);
            }

            $tecnicoId = session('usuario_id');
            $query = Ticket::where('tecnico_asignado_id', $tecnicoId)->with(['usuario', 'area', 'prioridad', 'estado']);
            
            // Filtros
            if ($request->estado_id) {
                // ✅ FIX T-2: Columna correcta es estado_id, no id_estado
                $query->where('estado_id', $request->estado_id);
            }
            if ($request->prioridad_id) {
                // ✅ FIX T-2: Columna correcta es prioridad_id, no id_prioridad
                $query->where('prioridad_id', $request->prioridad_id);
            }
            if ($request->search) {
                $search = '%' . $request->search . '%';
                // Subquery para que el orWhere no escape el where de tecnico_asignado_id
                $query->where(function($q) use ($search) {
                    $q->where('titulo', 'like', $search)
                      ->orWhere('descripcion', 'like', $search);
                });
            }
            
            $tickets = $query->orderBy('fecha_creacion', 'desc')->get();
            
            $resultado = $tickets->map(function($t) {
                return [
                    'id_ticket' => $t->id_ticket,
                    'titulo' => $t->titulo,
                    'usuario_nombre' => ($t->usuario->nombre ?? 'N/A') . ' ' . ($t->usuario->apellido ?? ''),
                    'prioridad_nombre' => $t->prioridad->nombre ?? 'N/A',
                    'prioridad_nivel' => $t->prioridad->nivel ?? 'media',
                    'estado_nombre' => $t->estado->nombre ?? 'N/A',
                    'estado_tipo' => $t->estado->tipo ?? 'abierto',
                    'fecha_creacion' => \Carbon\Carbon::parse($t->fecha_creacion)->format('d/m/Y H:i'),
                    'fecha_cierre' => $t->fecha_cierre ? \Carbon\Carbon::parse($t->fecha_cierre)->format('d/m/Y H:i') : 'N/A',
                ];
            });
            
            return response()->json($resultado);
        } catch (\Exception $e) {
            \Log::error('apiMisTickets error: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener tickets'], 500);
        }
    }

    /** API: Obtener detalle del ticket en JSON */
    public function apiTicketDetalle($id)
    {
        try {
            $t = Ticket::with(['usuario', 'area', 'prioridad', 'estado', 'tecnicoAsignado'])->findOrFail($id);

            // Verificar permisos: usuario solo puede ver sus tickets; técnico solo los asignados
            $rol = session('usuario_rol');
            $uid = session('usuario_id');
            if ($rol === 'Usuario Normal') {
                if ($t->usuario_id != $uid) {
                    return response()->json(['error' => 'No autorizado'], 403);
                }
            } elseif (str_contains($rol, 'Técnico')) {
                if ($t->tecnico_asignado_id != $uid) {
                    return response()->json(['error' => 'No autorizado'], 403);
                }
            }
            
            return response()->json([
                'id_ticket' => $t->id_ticket,
                'titulo' => $t->titulo,
                'descripcion' => $t->descripcion,
                'usuario_nombre' => ($t->usuario->nombre ?? 'N/A') . ' ' . ($t->usuario->apellido ?? ''),
                'area_nombre' => $t->area->nombre ?? 'N/A',
                'prioridad_nombre' => $t->prioridad->nombre ?? 'N/A',
                'prioridad_nivel' => $t->prioridad->nivel ?? 'media',
                'estado_nombre' => $t->estado->nombre ?? 'N/A',
                'estado_id' => $t->id_estado,
                'fecha_creacion' => \Carbon\Carbon::parse($t->fecha_creacion)->format('d/m/Y H:i'),
                'fecha_cierre' => $t->fecha_cierre ? \Carbon\Carbon::parse($t->fecha_cierre)->format('d/m/Y H:i') : null,
                'updated_at' => \Carbon\Carbon::parse($t->updated_at)->format('d/m/Y H:i'),
            ]);
        } catch (\Exception $e) {
            \Log::error('apiTicketDetalle error: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener el ticket'], 500);
        }
    }

    /** API: Obtener comentarios actualizados de un ticket (optimizado) */
    public function apiComentariosTicket($id)
    {
        try {
            $ticket = Ticket::findOrFail($id);
            
            // Validar permisos
            if (str_contains(session('usuario_rol'), 'Técnico')) {
                if ($ticket->tecnico_asignado_id != session('usuario_id')) {
                    return response()->json(['error' => 'No autorizado'], 403);
                }
            } elseif (session('usuario_rol') !== 'Administrador') {
                // ✅ FIX A-4: Usuario Normal solo puede leer comentarios de sus propios tickets
                if ($ticket->usuario_id != session('usuario_id')) {
                    return response()->json(['error' => 'No autorizado'], 403);
                }
            }
            
            // Obtener últimos 10 comentarios con eager loading optimizado
            $comentarios = $ticket->comentarios()
                ->with(['usuario' => fn($q) => $q->with('rol')])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->reverse()
                ->values();  // IMPORTANTE: resetear keys para que json_encode genere array, no objeto
            
            return response()->json([
                'success' => true,
                'comentarios' => $comentarios->map(fn($c) => [
                    'id_comentario' => $c->id_comentario,
                    'usuario' => [
                        'id_usuario' => $c->usuario->id_usuario,
                        'nombre' => $c->usuario->nombre ?? 'N/A',
                        'apellido' => $c->usuario->apellido ?? '',
                        'rol' => ['nombre' => $c->usuario->rol?->nombre ?? 'Usuario Normal']
                    ],
                    'contenido'        => $c->contenido,
                    'es_actualizacion' => (bool) $c->es_actualizacion,
                    'created_at'       => $c->created_at->toIso8601String(),
                ])
            ]);  // IMPORTANTE: $comentarios ya es un array reseteado con ->values()
        } catch (\Exception $e) {
            \Log::error('apiComentariosTicket error ticket#' . $id . ': ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener comentarios'], 500);
        }
    }

    /** API: CREAR comentario (POST) - Con validaciones completas */
    public function apiCrearComentario(Request $request, $id)
    {
        try {
            // ✅ Validar input
            $validated = $request->validate([
                'contenido' => 'required|string|min:5|max:2000',
            ]);

            // ✅ Obtener ticket
            $ticket = Ticket::findOrFail($id);

            // ✅ VALIDACIÓN 1: ¿User puede comentar en este ticket?
            if (str_contains(session('usuario_rol'), 'Técnico')) {
                if ($ticket->tecnico_asignado_id != session('usuario_id')) {
                    return response()->json(['error' => 'No tienes permiso para comentar en este ticket'], 403);
                }
            } elseif (session('usuario_rol') !== 'Administrador') {
                // ✅ FIX A-5: Usuario Normal solo puede comentar en sus propios tickets
                if ($ticket->usuario_id != session('usuario_id')) {
                    return response()->json(['error' => 'No tienes permiso para comentar en este ticket'], 403);
                }
            }

            // ✅ VALIDACIÓN 2: ¿User está activo?
            $user = Usuario::findOrFail(session('usuario_id'));
            if (!$user->activo) {
                return response()->json(['error' => 'Tu cuenta está inactiva'], 403);
            }

            // ✅ Crear comentario
            $comentario = Comentario::create([
                'ticket_id' => $id,
                'usuario_id' => session('usuario_id'),
                'contenido' => $validated['contenido'],
            ]);

            // ✅ Cargar relaciones
            $comentario->load('usuario.rol');

            return response()->json([
                'success' => true,
                'message' => 'Comentario guardado exitosamente',
                'comentario' => [
                    'id_comentario' => $comentario->id_comentario,
                    'usuario' => [
                        'id_usuario' => $comentario->usuario->id_usuario,
                        'nombre' => $comentario->usuario->nombre,
                        'apellido' => $comentario->usuario->apellido,
                        'rol' => ['nombre' => $comentario->usuario->rol?->nombre ?? 'Usuario Normal']
                    ],
                    'contenido' => $comentario->contenido,
                    'created_at' => $comentario->created_at->toIso8601String(),
                ]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validación fallida',
                'messages' => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Ticket no encontrado'], 404);
        } catch (\Exception $e) {
            \Log::error('Error en apiCrearComentario: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Error al guardar el comentario'], 500);
        }
    }

    /** API: EDITAR comentario (PUT) - Solo autor o admin, dentro de 5 min */
    public function apiEditarComentario(Request $request, $ticketId, $comentarioId)
    {
        try {
            $request->validate([
                'contenido' => 'required|string|min:5|max:2000',
            ]);

            $comentario = Comentario::findOrFail($comentarioId);

            // ✅ FIX A-6: verificar que el comentario pertenece al ticket indicado
            if ((int) $comentario->ticket_id !== (int) $ticketId) {
                return response()->json(['error' => 'Comentario no pertenece a este ticket'], 404);
            }

            // ✅ VALIDACIÓN 1: ¿Es autor o admin?
            if ($comentario->usuario_id != session('usuario_id') && session('usuario_rol') !== 'Administrador') {
                return response()->json(['error' => 'Solo puedes editar tus comentarios'], 403);
            }

            // ✅ VALIDACIÓN 2: ¿Dentro de 5 minutos?
            $hace5min = now()->subMinutes(5);
            if ($comentario->created_at < $hace5min && session('usuario_rol') !== 'Administrador') {
                return response()->json(['error' => 'Solo puedes editar comentarios dentro de 5 minutos'], 403);
            }

            // Editar
            $comentario->update(['contenido' => $request->contenido]);
            $comentario->load('usuario.rol');

            return response()->json([
                'success' => true,
                'message' => 'Comentario actualizado',
                'comentario' => [
                    'id_comentario' => $comentario->id_comentario,
                    'contenido' => $comentario->contenido,
                    'created_at' => $comentario->created_at->toIso8601String(),
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('apiEditarComentario error: ' . $e->getMessage());
            return response()->json(['error' => 'Error al editar el comentario'], 500);
        }
    }

    /** API: ELIMINAR comentario (DELETE) - Solo autor o admin */
    public function apiEliminarComentario($ticketId, $comentarioId)
    {
        try {
            $comentario = Comentario::findOrFail($comentarioId);

            // ✅ FIX A-7: verificar que el comentario pertenece al ticket indicado
            if ((int) $comentario->ticket_id !== (int) $ticketId) {
                return response()->json(['error' => 'Comentario no pertenece a este ticket'], 404);
            }

            // ✅ VALIDACIÓN: ¿Es autor o admin?
            if ($comentario->usuario_id != session('usuario_id') && session('usuario_rol') !== 'Administrador') {
                return response()->json(['error' => 'No tienes permiso para eliminar este comentario'], 403);
            }

            $comentario->delete();

            return response()->json([
                'success' => true,
                'message' => 'Comentario eliminado'
            ]);

        } catch (\Exception $e) {
            \Log::error('apiEliminarComentario error: ' . $e->getMessage());
            return response()->json(['error' => 'Error al eliminar el comentario'], 500);
        }
    }

    /** TÉCNICO: Ver ficha técnica de un ticket asignado (impresión) */
    public function verFichaTecnica($id)
    {
        // ✅ FIX T-1: Verificar que el ticket esté asignado a este técnico
        $ticket = Ticket::with(['usuario', 'area', 'prioridad', 'estado', 'comentarios.usuario'])
            ->findOrFail($id);

        if ($ticket->tecnico_asignado_id != session('usuario_id') && session('usuario_rol') !== 'Administrador') {
            abort(403, 'No tienes permiso para ver esta ficha técnica.');
        }

        return view('tecnicos.ver-ticket', compact('ticket'));
    }

    /** TÉCNICO: Historial de todos los tickets atendidos */
    public function misTicketsHistorial()
    {
        // ✅ FIX T-9: Método que faltaba (ruta /historial-tickets daba 500)
        $tickets = Ticket::with(['usuario', 'area', 'prioridad', 'estado'])
            ->where('tecnico_asignado_id', session('usuario_id'))
            ->whereHas('estado', fn($q) => $q->whereIn('tipo', ['resuelto', 'cerrado', 'cancelado']))
            ->orderBy('updated_at', 'desc')
            ->paginate(20);

        return view('tecnicos.historial', compact('tickets'));
    }
}