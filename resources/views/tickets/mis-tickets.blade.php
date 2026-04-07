@extends('layouts.app')

@section('title', 'Mis Tickets')
@section('no_header_title', true)

@section('content')
@php
    $esTecnico = str_contains(session('usuario_rol'), 'Técnico');
@endphp
<style>
    /* ══════ BANNER TÉCNICO ══════ */
    .tec-banner {
        background: linear-gradient(135deg, #15803d 0%, #16a34a 100%);
        border-radius: 18px;
        padding: 1.8rem 2.2rem;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        gap: 1.2rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 8px 30px rgba(22,163,74,.28);
    }
    .tec-banner::before { content:''; position:absolute; top:-50px; right:-50px; width:190px; height:190px; border-radius:50%; background:rgba(255,255,255,.06); }
    .tec-banner::after  { content:''; position:absolute; bottom:-55px; right:130px; width:140px; height:140px; border-radius:50%; background:rgba(255,255,255,.04); }
    .tec-banner-icon  { width:54px; height:54px; border-radius:14px; background:rgba(255,255,255,.18); display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:1.5rem; color:#fff; position:relative; z-index:1; }
    .tec-banner-title { color:#fff; font-size:1.5rem; font-weight:700; margin:0; position:relative; z-index:1; }
    .tec-banner-sub   { color:rgba(255,255,255,.75); font-size:.88rem; margin:.1rem 0 0; position:relative; z-index:1; }

    /* ══════ BANNER USUARIO ══════ */
    .usr-banner {
        background: linear-gradient(135deg, #0f766e 0%, #0891b2 100%);
        border-radius: 18px;
        padding: 1.8rem 2.2rem;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 8px 30px rgba(8,145,178,.25);
        flex-wrap: wrap;
    }
    .usr-banner::before { content:''; position:absolute; top:-50px; right:-50px; width:190px; height:190px; border-radius:50%; background:rgba(255,255,255,.06); }
    .usr-banner::after  { content:''; position:absolute; bottom:-55px; right:130px; width:130px; height:130px; border-radius:50%; background:rgba(255,255,255,.04); }
    .usr-banner-left  { display:flex; align-items:center; gap:1.1rem; position:relative; z-index:1; }
    .usr-banner-icon  { width:52px; height:52px; border-radius:14px; background:rgba(255,255,255,.18); display:flex; align-items:center; justify-content:center; font-size:1.45rem; color:#fff; flex-shrink:0; }
    .usr-banner-title { color:#fff; font-size:1.45rem; font-weight:700; margin:0; }
    .usr-banner-sub   { color:rgba(255,255,255,.75); font-size:.88rem; margin:.1rem 0 0; }
    .usr-banner-right { position:relative; z-index:1; }

    .btn-nuevo {
        background:rgba(255,255,255,.18);
        border:1.5px solid rgba(255,255,255,.45);
        color:#fff;
        border-radius:10px;
        padding:.55rem 1.1rem;
        font-weight:600;
        font-size:.9rem;
        text-decoration:none;
        display:inline-flex;
        align-items:center;
        gap:.45rem;
        transition:background .18s;
    }
    .btn-nuevo:hover { background:rgba(255,255,255,.28); color:#fff; }

    /* ══════ SECCIÓN TÍTULO (igual que asignados) ══════ */
    .tec-section-title { font-size:.78rem; font-weight:800; color:#64748b; text-transform:uppercase; letter-spacing:.08em; margin-bottom:.9rem; display:flex; align-items:center; gap:.5rem; }
    .tec-section-title::after { content:''; flex:1; height:1px; background:#e2e8f0; }

    /* ══════ FILTROS ══════ */
    .filtros-card {
        background: #fff;
        border-radius: 14px;
        border: 1px solid #e8edf5;
        box-shadow: 0 2px 10px rgba(0,0,0,.05);
        padding: 1.1rem 1.3rem;
        margin-bottom: 1.5rem;
    }
    .filtros-card .form-select,
    .filtros-card .form-control {
        border: 1.5px solid #e2e8f0;
        border-radius: 9px;
        font-size: .88rem;
        padding: .6rem .85rem;
        color: #334155;
        transition: border-color .18s, box-shadow .18s;
        background-color: #f8fafc;
    }
    .filtros-tec .form-select:focus,
    .filtros-tec .form-control:focus {
        border-color: #16a34a;
        box-shadow: 0 0 0 3px rgba(22,163,74,.12);
        background-color: #fff;
        outline: none;
    }
    .filtros-usr .form-select:focus,
    .filtros-usr .form-control:focus {
        border-color: #0891b2;
        box-shadow: 0 0 0 3px rgba(8,145,178,.12);
        background-color: #fff;
        outline: none;
    }
    .btn-buscar-tec {
        border: none; border-radius: 9px; padding: .62rem 1.2rem; font-weight: 700; font-size: .88rem; color: #fff;
        display: inline-flex; align-items: center; gap: .4rem; width: 100%; justify-content: center;
        background: linear-gradient(135deg,#15803d,#16a34a);
        box-shadow: 0 3px 12px rgba(22,163,74,.28);
        transition: filter .18s, transform .18s;
    }
    .btn-buscar-tec:hover { filter:brightness(1.07); transform:translateY(-1px); color:#fff; }
    .btn-buscar-usr {
        border: none; border-radius: 9px; padding: .62rem 1.2rem; font-weight: 700; font-size: .88rem; color: #fff;
        display: inline-flex; align-items: center; gap: .4rem; width: 100%; justify-content: center;
        background: linear-gradient(135deg,#0f766e,#0891b2);
        box-shadow: 0 3px 12px rgba(8,145,178,.28);
        transition: filter .18s, transform .18s;
    }
    .btn-buscar-usr:hover { filter:brightness(1.07); transform:translateY(-1px); color:#fff; }

    /* ══════ CARDS HISTORIAL TÉCNICO ══════ */
    .tec-card-hist { background:#fff; border-radius:14px; border:1px solid #e8edf5; border-left:3px solid #16a34a; box-shadow:0 2px 8px rgba(0,0,0,.04); padding:1.2rem 1.4rem; margin-bottom:.75rem; transition:transform .18s, box-shadow .18s; }
    .tec-card-hist:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(0,0,0,.08); }
    .tec-folio-h { background:#f0fdf4; color:#15803d; font-size:.77rem; font-weight:700; padding:.2rem .6rem; border-radius:6px; white-space:nowrap; flex-shrink:0; }
    .tec-title-h { font-weight:700; color:#1e293b; font-size:.97rem; text-decoration:none; }
    .tec-title-h:hover { color:#15803d; }
    /* mini-stepper verde técnico */
    .mini-flow-t { display:flex; align-items:flex-start; gap:0; margin-top:.55rem; }
    .mini-step-t { position:relative; flex:1; display:flex; flex-direction:column; align-items:center; }
    .mini-step-t:not(:last-child)::after { content:''; position:absolute; top:4px; left:55%; width:90%; height:2px; background:#dde1e7; z-index:0; }
    .mini-step-t.ms-done:not(:last-child)::after   { background:#15803d; }
    .mini-step-t.ms-active:not(:last-child)::after { background:linear-gradient(90deg,#15803d,#dde1e7); }
    .mini-dot-t { width:10px; height:10px; border-radius:50%; border:2px solid #dde1e7; background:#fff; margin:0 auto; position:relative; z-index:1; }
    .mini-step-t.ms-done   .mini-dot-t { background:#15803d; border-color:#15803d; }
    .mini-step-t.ms-active .mini-dot-t { background:#15803d; border-color:#15803d; box-shadow:0 0 0 3px rgba(21,128,61,.2); }
    .mini-lbl-t { font-size:.52rem; font-weight:700; text-align:center; color:#94a3b8; margin-top:3px; text-transform:uppercase; letter-spacing:.02em; line-height:1.1; }
    .mini-step-t.ms-done   .mini-lbl-t,
    .mini-step-t.ms-active .mini-lbl-t { color:#15803d; }
    .btn-ver-th { background:linear-gradient(135deg,#15803d,#16a34a); color:#fff; border:none; border-radius:8px; padding:.38rem .8rem; font-size:.8rem; font-weight:600; text-decoration:none; display:inline-flex; align-items:center; gap:.3rem; transition:filter .18s; white-space:nowrap; }
    .btn-ver-th:hover { filter:brightness(1.08); color:#fff; }
    .btn-estado-tec { background:#f0fdf4; color:#15803d; border:1.5px solid #bbf7d0; border-radius:8px; padding:.38rem .8rem; font-size:.8rem; font-weight:600; display:inline-flex; align-items:center; gap:.3rem; cursor:pointer; transition:background .18s,border-color .18s; white-space:nowrap; }
    .btn-estado-tec:hover { background:#dcfce7; border-color:#86efac; }
    /* modal rápido */
    .qs-header { background:linear-gradient(135deg,#14532d,#16a34a); color:#fff; border-radius:12px 12px 0 0; padding:1.1rem 1.4rem; }
    .qs-select { width:100%; padding:.65rem .9rem; font-size:.9rem; font-weight:600; border-radius:10px; border:2px solid #e2e8f0; background:#fff; outline:none; }
    .qs-select:focus { border-color:#16a34a; box-shadow:0 0 0 3px rgba(22,163,74,.12); }
    .qs-textarea { width:100%; padding:.7rem .9rem; font-size:.88rem; border-radius:10px; border:2px solid #e2e8f0; resize:vertical; min-height:100px; outline:none; font-family:inherit; }
    .qs-textarea:focus { border-color:#16a34a; box-shadow:0 0 0 3px rgba(22,163,74,.12); }
    .btn-qs-submit { background:linear-gradient(135deg,#15803d,#16a34a); color:#fff; border:none; border-radius:10px; padding:.65rem 1.4rem; font-weight:700; font-size:.92rem; display:inline-flex; align-items:center; gap:.4rem; cursor:pointer; transition:filter .18s; }
    .btn-qs-submit:hover { filter:brightness(1.07); }

    /* ══════ CHIPS ══════ */
    .chip { display:inline-block; padding:.22rem .65rem; border-radius:20px; font-size:.76rem; font-weight:700; }
    .chip-abierto    { background:#dbeafe; color:#1d4ed8; }
    .chip-pendiente  { background:#fef9c3; color:#854d0e; }
    .chip-en_proceso { background:#e0f2fe; color:#0369a1; }
    .chip-resuelto   { background:#dcfce7; color:#15803d; }
    .chip-cerrado    { background:#f1f5f9; color:#475569; }
    .chip-baja       { background:#dcfce7; color:#15803d; }
    .chip-media      { background:#fef9c3; color:#854d0e; }
    .chip-alta       { background:#fee2e2; color:#dc2626; }

    /* ══════ BOTONES ACCIÓN ══════ */
    .btn-gestionar { background:linear-gradient(135deg,#15803d,#16a34a); color:#fff; border:none; border-radius:8px; padding:.38rem .85rem; font-size:.82rem; font-weight:600; text-decoration:none; display:inline-flex; align-items:center; gap:.35rem; transition:filter .18s, transform .18s; box-shadow:0 3px 10px rgba(22,163,74,.28); }
    .btn-gestionar:hover { filter:brightness(1.08); transform:translateY(-1px); color:#fff; }

    /* ══════ CARDS USUARIO ══════ */
    .ticket-card-u { background:#fff; border-radius:14px; border:1px solid #e8edf5; box-shadow:0 2px 10px rgba(0,0,0,.05); padding:1.2rem 1.4rem; margin-bottom:1rem; transition:transform .2s, box-shadow .2s; }
    .ticket-card-u:hover { transform:translateY(-3px); box-shadow:0 8px 22px rgba(0,0,0,.09); }
    .tc-folio { background:#ccfbf1; color:#0f766e; font-size:.77rem; font-weight:700; padding:.2rem .6rem; border-radius:6px; }
    .tc-title { font-weight:700; color:#1e293b; font-size:.97rem; text-decoration:none; }
    .tc-title:hover { color:#0f766e; }
    .tc-desc { font-size:.85rem; color:#64748b; line-height:1.5; margin:.35rem 0 .6rem; }
    .btn-ver-u { background:linear-gradient(135deg,#0f766e,#0891b2); color:#fff; border:none; border-radius:8px; padding:.4rem .9rem; font-size:.82rem; font-weight:600; text-decoration:none; display:inline-flex; align-items:center; gap:.35rem; transition:filter .18s; box-shadow:0 2px 8px rgba(8,145,178,.25); white-space:nowrap; }
    .btn-ver-u:hover { filter:brightness(1.08); color:#fff; }

    /* ══════ EMPTY STATE ══════ */
    .empty-state { text-align:center; padding:3.5rem 1rem; }
    .empty-state-icon { width:72px; height:72px; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 1rem; font-size:2rem; }

    /* ══════ REFRESH WIDGET ══════ */
    .refresh-badge { background:rgba(255,255,255,.12); font-size:.82rem; border-radius:20px; display:flex; align-items:center; gap:.5rem; padding:.4rem .85rem; color:#fff; white-space:nowrap; }
    .live-dot { width:8px; height:8px; border-radius:50%; background:#10B981; display:inline-block; animation:livePulse 2s ease-in-out infinite; }
    .btn-refresh-tec { background:rgba(255,255,255,.92); border:none; border-radius:8px; padding:.42rem .9rem; font-size:.82rem; font-weight:700; color:#15803d; display:inline-flex; align-items:center; gap:.35rem; cursor:pointer; transition:background .18s; white-space:nowrap; }
    .btn-refresh-tec:hover { background:#fff; }
    /* ══════ MINI STEPPER USUARIO ══════ */
    .mini-flow-u { display:flex; align-items:flex-start; gap:0; margin-top:.55rem; }
    .mini-step-u { position:relative; flex:1; display:flex; flex-direction:column; align-items:center; }
    .mini-step-u:not(:last-child)::after { content:''; position:absolute; top:4px; left:55%; width:90%; height:2px; background:#dde1e7; z-index:0; }
    .mini-step-u.ms-done:not(:last-child)::after { background:#0f766e; }
    .mini-step-u.ms-active:not(:last-child)::after { background:linear-gradient(90deg,#0f766e,#dde1e7); }
    .mini-dot-u { width:10px; height:10px; border-radius:50%; border:2px solid #dde1e7; background:#fff; margin:0 auto; position:relative; z-index:1; transition:all .2s; }
    .mini-step-u.ms-done .mini-dot-u   { background:#0f766e; border-color:#0f766e; }
    .mini-step-u.ms-active .mini-dot-u { background:#0f766e; border-color:#0f766e; box-shadow:0 0 0 3px rgba(15,118,110,.2); }
    .mini-lbl-u { font-size:.52rem; font-weight:700; text-align:center; color:#94a3b8; margin-top:3px; text-transform:uppercase; letter-spacing:.02em; line-height:1.1; }
    .mini-step-u.ms-done .mini-lbl-u, .mini-step-u.ms-active .mini-lbl-u { color:#0f766e; }

    .btn-refresh-usr { background:rgba(255,255,255,.92); border:none; border-radius:8px; padding:.42rem .9rem; font-size:.82rem; font-weight:700; color:#0f766e; display:inline-flex; align-items:center; gap:.35rem; cursor:pointer; transition:background .18s; white-space:nowrap; }
    .btn-refresh-usr:hover { background:#fff; }
    @keyframes livePulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.4;transform:scale(.85)} }

    @media(max-width:768px) {
        .tec-banner, .usr-banner { padding:1.3rem 1.2rem; }
        .tec-banner-title, .usr-banner-title { font-size:1.2rem; }
    }
</style>

<div class="container-fluid">

    @if($esTecnico)
    {{-- ══════ BANNER TÉCNICO ══════ --}}
    <div class="tec-banner" style="justify-content:space-between;">
        <div style="display:flex;align-items:center;gap:1.2rem;position:relative;z-index:1;">
            <div class="tec-banner-icon"><i class="bi bi-journal-check"></i></div>
            <div>
                <h1 class="tec-banner-title">Historial de Tickets</h1>
                <p class="tec-banner-sub">Consulta todos los tickets que tienes o tuviste asignados</p>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:.6rem;position:relative;z-index:1;flex-wrap:wrap;">
            <div class="refresh-badge" id="badgeUpdate">
                <span class="live-dot"></span>
                Actualizado: <strong id="lastUpdate">Ahora</strong>
            </div>
            <button class="btn-refresh-tec" id="btnRefresh">
                <i class="bi bi-arrow-clockwise"></i> Actualizar
            </button>
        </div>
    </div>

    {{-- ══════ FILTROS TÉCNICO ══════ --}}
    <div class="tec-section-title"><i class="bi bi-funnel"></i> Filtrar historial</div>
    <div class="filtros-card filtros-tec">
        <form action="{{ route('tickets.mis-tickets') }}" method="GET">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-3">
                    <select name="estado_id" class="form-select">
                        <option value="">Todos los estados</option>
                        @foreach($estados ?? [] as $estado)
                        <option value="{{ $estado->id_estado }}" {{ request('estado_id') == $estado->id_estado ? 'selected' : '' }}>
                            {{ $estado->nombre }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <select name="prioridad_id" class="form-select">
                        <option value="">Todas las prioridades</option>
                        @foreach($prioridades ?? [] as $prioridad)
                        <option value="{{ $prioridad->id_prioridad }}" {{ request('prioridad_id') == $prioridad->id_prioridad ? 'selected' : '' }}>
                            {{ $prioridad->nombre }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Buscar en el historial..." value="{{ request('search') }}">
                </div>
                <div class="col-12 col-md-2">
                    <button type="submit" class="btn-buscar-tec">
                        <i class="bi bi-search"></i> Buscar
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- ══════ CARDS HISTORIAL TÉCNICO ══════ --}}
    @php
    $stepFlowT = [
        ['tipo'=>'abierto',    'label'=>'Abierto'],
        ['tipo'=>'en_proceso', 'label'=>'En Proceso'],
        ['tipo'=>'pendiente',  'label'=>'Pendiente'],
        ['tipo'=>'resuelto',   'label'=>'Resuelto'],
        ['tipo'=>'cerrado',    'label'=>'Cerrado'],
    ];
    @endphp
    <div id="tec-cards-container">
    @if(count($tickets) > 0)
    @foreach($tickets as $ticket)
    @php
        $prioNivel       = strtolower(str_replace(['á','é','í','ó','ú','ü','ñ',' '],['a','e','i','o','u','u','n','_'], $ticket->prioridad->nombre ?? 'media'));
        $estadoTipo      = str_replace(' ','_', strtolower($ticket->estado->tipo ?? 'abierto'));
        $idxActualT      = collect($stepFlowT)->search(fn($s) => $s['tipo'] === $estadoTipo);
        if ($idxActualT === false) $idxActualT = 0;
        $estadoTerminalT = in_array($estadoTipo, ['cerrado', 'cancelado']);
        $siguientesT = match($estadoTipo) {
            'abierto'    => ['En Proceso', 'Pendiente'],
            'en_proceso' => ['Pendiente', 'Resuelto'],
            'pendiente'  => ['En Proceso', 'Resuelto'],
            default      => [],
        };
    @endphp
    <div class="tec-card-hist">
        {{-- Fila 1: folio + título + botones --}}
        <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
            <div class="d-flex align-items-center gap-2 min-w-0 flex-grow-1">
                <span class="tec-folio-h">#{{ $ticket->id_ticket }}</span>
                <a href="{{ route('tickets.show', $ticket->id_ticket) }}" class="tec-title-h text-truncate">{{ $ticket->titulo }}</a>
            </div>
            <div class="d-flex gap-1 flex-shrink-0">
                <a href="{{ route('tickets.show', $ticket->id_ticket) }}" class="btn-ver-th">
                    <i class="bi bi-eye"></i> Ver
                </a>
                @if(!$estadoTerminalT && count($siguientesT) > 0)
                <button type="button" class="btn-estado-tec"
                        data-ticket-id="{{ $ticket->id_ticket }}"
                        data-ticket-titulo="{{ Str::limit($ticket->titulo, 50) }}"
                        data-estado-actual="{{ $ticket->estado->nombre ?? '' }}"
                        data-siguientes='@json($siguientesT)'
                        onclick="abrirQS(this)">
                    <i class="bi bi-arrow-repeat"></i> Estado
                </button>
                @endif
            </div>
        </div>
        {{-- Fila 2: chips + solicitante + fecha --}}
        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
            <span class="chip chip-{{ $estadoTipo }}">{{ $ticket->estado->nombre ?? 'N/A' }}</span>
            <span class="chip chip-{{ $prioNivel }}">{{ $ticket->prioridad->nombre ?? 'Sin prioridad' }}</span>
            <span class="chip" style="background:#f1f5f9;color:#475569;"><i class="bi bi-building me-1"></i>{{ $ticket->area?->nombre ?? 'N/A' }}</span>
            <span class="ms-auto text-muted" style="font-size:.78rem;"><i class="bi bi-clock me-1"></i>{{ $ticket->fecha_creacion?->diffForHumans() }}</span>
            <span class="text-muted" style="font-size:.78rem;"><i class="bi bi-person me-1"></i>{{ $ticket->usuario->nombre ?? 'N/A' }} {{ $ticket->usuario->apellido ?? '' }}</span>
        </div>
        {{-- Fila 3: stepper --}}
        <div class="mini-flow-t">
            @foreach($stepFlowT as $si => $step)
                @php $cls = $si < $idxActualT ? 'ms-done' : ($si === $idxActualT ? 'ms-active' : ''); @endphp
                <div class="mini-step-t {{ $cls }}"><div class="mini-dot-t"></div><div class="mini-lbl-t">{{ $step['label'] }}</div></div>
            @endforeach
        </div>
    </div>
    @endforeach
    @else
    <div class="empty-state">
        <div class="empty-state-icon" style="background:#f0fdf4;"><i class="bi bi-inbox" style="color:#16a34a;"></i></div>
        <h6 style="color:#1e293b; font-weight:700;">Sin tickets en el historial</h6>
        <p class="text-muted small mb-0">Aún no tienes tickets asignados registrados.</p>
    </div>
    @endif
    </div>

    {{-- MODAL RÁPIDO: CAMBIAR ESTADO --}}
    <div class="modal fade" id="quickStateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:440px;">
            <div class="modal-content border-0 shadow-lg" style="border-radius:12px;overflow:hidden;">
                <div class="qs-header d-flex align-items-center justify-content-between">
                    <div>
                        <div style="font-size:.66rem;font-weight:700;opacity:.78;letter-spacing:.06em;text-transform:uppercase;">Actualizar Estado</div>
                        <div id="qsTitulo" style="font-size:.95rem;font-weight:700;"></div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="qsForm" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <div style="font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#64748b;margin-bottom:5px;">Estado actual</div>
                            <div id="qsEstadoActual" style="font-size:.9rem;font-weight:600;color:#374151;"></div>
                        </div>
                        <div class="mb-3">
                            <label style="font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#64748b;margin-bottom:5px;display:block;">Cambiar a</label>
                            <select name="estado_id" id="qsSelect" class="qs-select" required></select>
                        </div>
                        <div>
                            <label style="font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#64748b;margin-bottom:5px;display:block;">Comentario <span style="color:#ef4444;">*</span></label>
                            <textarea name="contenido" id="qsContenido" class="qs-textarea" placeholder="Describe el avance o motivo del cambio..." required minlength="5"></textarea>
                            <div style="font-size:.71rem;color:#94a3b8;margin-top:3px;"><i class="bi bi-info-circle me-1"></i>Mínimo 5 caracteres. Queda en el historial del ticket.</div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-light px-4 py-3 gap-2">
                        <button type="button" class="btn btn-light fw-bold px-4" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn-qs-submit"><i class="bi bi-check-circle me-1"></i>Guardar cambio</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @else
    {{-- ══════ BANNER USUARIO ══════ --}}
    <div class="usr-banner">
        <div class="usr-banner-left">
            <div class="usr-banner-icon"><i class="bi bi-ticket-perforated"></i></div>
            <div>
                <h1 class="usr-banner-title">Mis Tickets</h1>
                <p class="usr-banner-sub">Todos tus tickets de soporte</p>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:.6rem;position:relative;z-index:1;flex-wrap:wrap;">
            <div class="refresh-badge" id="badgeUpdate">
                <span class="live-dot"></span>
                Actualizado: <strong id="lastUpdate">Ahora</strong>
            </div>
            <button class="btn-refresh-usr" id="btnRefresh">
                <i class="bi bi-arrow-clockwise"></i> Actualizar
            </button>
            <a href="{{ route('tickets.create') }}" class="btn-nuevo">
                <i class="bi bi-plus-circle"></i> Nuevo Ticket
            </a>
        </div>
    </div>

    {{-- ══════ FILTROS USUARIO ══════ --}}
    <div class="filtros-card filtros-usr">
        <form action="{{ route('tickets.mis-tickets') }}" method="GET">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-3">
                    <select name="estado_id" class="form-select">
                        <option value="">Todos los estados</option>
                        @foreach($estados ?? [] as $estado)
                        <option value="{{ $estado->id_estado }}" {{ request('estado_id') == $estado->id_estado ? 'selected' : '' }}>
                            {{ $estado->nombre }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <select name="prioridad_id" class="form-select">
                        <option value="">Todas las prioridades</option>
                        @foreach($prioridades ?? [] as $prioridad)
                        <option value="{{ $prioridad->id_prioridad }}" {{ request('prioridad_id') == $prioridad->id_prioridad ? 'selected' : '' }}>
                            {{ $prioridad->nombre }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Buscar por título o descripción del ticket..." value="{{ request('search') }}">
                </div>
                <div class="col-12 col-md-2">
                    <button type="submit" class="btn-buscar-usr">
                        <i class="bi bi-search"></i> Buscar
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- ══════ CARDS USUARIO ══════ --}}
    @php
    $stepFlowU = [
        ['tipo'=>'abierto',    'label'=>'Abierto'],
        ['tipo'=>'en_proceso', 'label'=>'En Proceso'],
        ['tipo'=>'pendiente',  'label'=>'Pendiente'],
        ['tipo'=>'resuelto',   'label'=>'Resuelto'],
        ['tipo'=>'cerrado',    'label'=>'Cerrado'],
    ];
    @endphp
    @forelse($tickets as $ticket)
    @php
        $prioNivel  = strtolower(str_replace(['á','é','í','ó','ú','ü','ñ',' '],['a','e','i','o','u','u','n','_'], $ticket->prioridad->nombre ?? 'media'));
        $estadoTipo = str_replace(' ','_', strtolower($ticket->estado->tipo ?? 'abierto'));
        $idxActualU = collect($stepFlowU)->search(fn($s) => $s['tipo'] === $estadoTipo);
        if ($idxActualU === false) $idxActualU = 0;
    @endphp
    <div class="ticket-card-u">
        {{-- Fila 1: Folio + Título + Botón --}}
        <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
            <div class="d-flex align-items-center gap-2 min-w-0 flex-grow-1">
                <span class="tc-folio flex-shrink-0">#{{ $ticket->id_ticket }}</span>
                <a href="{{ route('tickets.show', $ticket->id_ticket) }}" class="tc-title text-truncate">{{ $ticket->titulo }}</a>
            </div>
            <a href="{{ route('tickets.show', $ticket->id_ticket) }}" class="btn-ver-u flex-shrink-0">
                <i class="bi bi-eye"></i> Ver
            </a>
        </div>
        {{-- Fila 2: Descripción --}}
        @if($ticket->descripcion)
        <p class="tc-desc">{{ Str::limit($ticket->descripcion, 100) }}</p>
        @endif
        {{-- Fila 3: Chips + Fecha + Técnico --}}
        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
            <span class="chip chip-{{ $estadoTipo }}">{{ $ticket->estado?->nombre ?? 'N/A' }}</span>
            <span class="chip chip-{{ $prioNivel }}">{{ $ticket->prioridad?->nombre ?? 'Sin prioridad' }}</span>
            <span class="chip" style="background:#f1f5f9; color:#475569;"><i class="bi bi-building me-1"></i>{{ $ticket->area?->nombre ?? 'N/A' }}</span>
            <span class="ms-auto text-muted" style="font-size:.78rem;"><i class="bi bi-clock me-1"></i>{{ $ticket->fecha_creacion->diffForHumans() }}</span>
            @if($ticket->tecnicoAsignado)
            <span class="text-muted" style="font-size:.78rem;"><i class="bi bi-person-badge me-1"></i>{{ $ticket->tecnicoAsignado->nombre_completo }}</span>
            @endif
        </div>
        {{-- Fila 4: Stepper --}}
        <div class="mini-flow-u">
            @foreach($stepFlowU as $si => $step)
                @php $cls = $si < $idxActualU ? 'ms-done' : ($si === $idxActualU ? 'ms-active' : ''); @endphp
                <div class="mini-step-u {{ $cls }}"><div class="mini-dot-u"></div><div class="mini-lbl-u">{{ $step['label'] }}</div></div>
            @endforeach
        </div>
    </div>
    @empty
    <div class="empty-state">
        <div class="empty-state-icon" style="background:#ccfbf1;"><i class="bi bi-inbox" style="color:#0f766e;"></i></div>
        <h6 style="color:#1e293b; font-weight:700;">No tienes tickets aún</h6>
        <p class="text-muted small mb-3">Crea tu primer ticket para recibir soporte técnico.</p>
        <a href="{{ route('tickets.create') }}" class="btn-ver-u">
            <i class="bi bi-plus-circle"></i> Crear Ticket
        </a>
    </div>
    @endforelse
    @endif

</div>

@push('scripts')
<script>
// Abrir modal rápido de cambio de estado
function abrirQS(btn) {
    const ticketId   = btn.dataset.ticketId;
    const titulo     = btn.dataset.ticketTitulo;
    const estadoAct  = btn.dataset.estadoActual;
    const siguientes = JSON.parse(btn.dataset.siguientes);
    const estados    = @json($estados ?? []);

    document.getElementById('qsTitulo').textContent      = '#' + ticketId + ' — ' + titulo;
    document.getElementById('qsEstadoActual').textContent = estadoAct;
    document.getElementById('qsForm').action = '/tickets/' + ticketId + '/cambiar-estado';

    const sel = document.getElementById('qsSelect');
    sel.innerHTML = '';
    estados.forEach(e => {
        if (siguientes.includes(e.nombre)) {
            const opt = document.createElement('option');
            opt.value = e.id_estado;
            opt.textContent = e.nombre;
            sel.appendChild(opt);
        }
    });
    document.getElementById('qsContenido').value = '';
    new bootstrap.Modal(document.getElementById('quickStateModal')).show();
}

document.addEventListener('DOMContentLoaded', function() {
    @if(str_contains(session('usuario_rol'), 'Técnico'))

    let currentFilters = {
        estado_id:    new URLSearchParams(window.location.search).get('estado_id')    || '',
        prioridad_id: new URLSearchParams(window.location.search).get('prioridad_id') || '',
        search:       new URLSearchParams(window.location.search).get('search')       || ''
    };

    const selectEstado    = document.querySelector('select[name="estado_id"]');
    const selectPrioridad = document.querySelector('select[name="prioridad_id"]');
    const inputSearch     = document.querySelector('input[name="search"]');
    const formFiltros     = document.querySelector('form');

    function getSiguientesT(estadoTipo) {
        const map = {
            'abierto':    ['En Proceso', 'Pendiente'],
            'en_proceso': ['Pendiente', 'Resuelto'],
            'pendiente':  ['En Proceso', 'Resuelto'],
        };
        return map[estadoTipo] || [];
    }

    function actualizarTickets() {
        const params = new URLSearchParams(currentFilters);
        fetch(`{{ route('api.mis-tickets') }}?${params}`)
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('tec-cards-container');
                if (!container) return;
                if (data.length === 0) {
                    container.innerHTML = `
                        <div class="empty-state">
                            <div class="empty-state-icon" style="background:#f0fdf4;"><i class="bi bi-inbox" style="color:#16a34a;"></i></div>
                            <h6 style="color:#1e293b; font-weight:700;">No hay tickets que coincidan</h6>
                            <p class="text-muted small mb-0">Prueba con otros filtros.</p>
                        </div>`;
                    return;
                }
                container.innerHTML = '';
                data.forEach(ticket => {
                    const ticketUrl  = `/tickets/${ticket.id_ticket}`;
                    const siguientes = getSiguientesT(ticket.estado_tipo);
                    const esTerminal = ['cerrado','cancelado'].includes(ticket.estado_tipo);
                    const btnEstado  = (!esTerminal && siguientes.length > 0)
                        ? `<button type="button" class="btn-estado-tec"
                               data-ticket-id="${ticket.id_ticket}"
                               data-ticket-titulo="${ticket.titulo.substring(0,50)}"
                               data-estado-actual="${ticket.estado_nombre}"
                               data-siguientes='${JSON.stringify(siguientes)}'
                               onclick="abrirQS(this)">
                               <i class="bi bi-arrow-repeat"></i> Estado
                           </button>`
                        : '';
                    const card = document.createElement('div');
                    card.className = 'tec-card-hist';
                    card.innerHTML = `
                        <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                            <div class="d-flex align-items-center gap-2 min-w-0 flex-grow-1">
                                <span class="tec-folio-h">#${ticket.id_ticket}</span>
                                <a href="${ticketUrl}" class="tec-title-h text-truncate">${ticket.titulo}</a>
                            </div>
                            <div class="d-flex gap-1 flex-shrink-0">
                                <a href="${ticketUrl}" class="btn-ver-th"><i class="bi bi-eye"></i> Ver</a>
                                ${btnEstado}
                            </div>
                        </div>
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <span class="chip chip-${ticket.estado_tipo}">${ticket.estado_nombre}</span>
                            <span class="chip chip-${ticket.prioridad_nivel}">${ticket.prioridad_nombre}</span>
                            <span class="ms-auto text-muted" style="font-size:.78rem;"><i class="bi bi-clock me-1"></i>${ticket.fecha_creacion}</span>
                            <span class="text-muted" style="font-size:.78rem;"><i class="bi bi-person me-1"></i>${ticket.usuario_nombre}</span>
                        </div>`;
                    container.appendChild(card);
                });
            })
            .catch(error => console.error('Error al actualizar tickets:', error));
    }

    if (selectEstado)    { selectEstado.addEventListener('change',    function() { currentFilters.estado_id    = this.value; actualizarTickets(); }); }
    if (selectPrioridad) { selectPrioridad.addEventListener('change', function() { currentFilters.prioridad_id = this.value; actualizarTickets(); }); }
    if (inputSearch) {
        let searchTimeout;
        inputSearch.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => { currentFilters.search = this.value; actualizarTickets(); }, 500);
        });
    }
    if (formFiltros) {
        formFiltros.addEventListener('submit', function(e) {
            e.preventDefault();
            currentFilters = {
                estado_id:    selectEstado?.value    || '',
                prioridad_id: selectPrioridad?.value || '',
                search:       inputSearch?.value     || ''
            };
            actualizarTickets();
        });
    }
    setInterval(actualizarTickets, 60000);

    // Refresh widget
    function horaActual(){
        const n=new Date();
        return n.getHours().toString().padStart(2,'0')+':'+n.getMinutes().toString().padStart(2,'0');
    }
    const ts  = document.getElementById('lastUpdate');
    const btn = document.getElementById('btnRefresh');
    if(ts) ts.textContent = horaActual();
    setInterval(()=>{ if(ts) ts.textContent = horaActual(); }, 60000);
    if(btn){
        btn.addEventListener('click', function(){
            const orig = this.innerHTML;
            this.disabled = true;
            this.innerHTML = '<i class="bi bi-hourglass-split"></i> Actualizando...';
            @if(str_contains(session('usuario_rol'), 'Técnico'))
            actualizarTickets();
            setTimeout(()=>{ if(ts) ts.textContent = horaActual(); this.innerHTML = orig; this.disabled = false; }, 800);
            @else
            window.location.reload();
            @endif
        });
    }
    @endif

    // Refresh widget usuario normal (fuera del bloque técnico)
    @if(!str_contains(session('usuario_rol'), 'Técnico'))
    (function(){
        function horaActual(){
            const n=new Date();
            return n.getHours().toString().padStart(2,'0')+':'+n.getMinutes().toString().padStart(2,'0');
        }
        const ts  = document.getElementById('lastUpdate');
        const btn = document.getElementById('btnRefresh');
        if(ts) ts.textContent = horaActual();
        setInterval(()=>{ if(ts) ts.textContent = horaActual(); }, 60000);
        if(btn){
            btn.addEventListener('click', function(){
                this.disabled = true;
                this.innerHTML = '<i class="bi bi-hourglass-split"></i> Actualizando...';
                window.location.reload();
            });
        }
    })();
    @endif
});
</script>
@endpush
@endsection