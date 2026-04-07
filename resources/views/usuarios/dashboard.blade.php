@extends('layouts.app')

@section('title', 'Mi Dashboard')

@section('content')
@php
    $stepFlow = [
        ['tipo' => 'abierto',      'label' => 'Abierto'],
        ['tipo' => 'en_atencion',  'label' => 'En Atención'],
        ['tipo' => 'resuelto',     'label' => 'Resuelto'],
        ['tipo' => 'cerrado',      'label' => 'Cerrado'],
    ];
@endphp
<style>
    /* ══════ VARIABLES TEAL ══════ */
    :root {
        --tl1: #0f766e; --tl2: #0891b2;
        --tl-grad: linear-gradient(135deg, #0f766e 0%, #0891b2 100%);
        --tl-light: #f0fdfa;
        --tl-glow: rgba(8,145,178,.22);
    }

    /* ══════ BANNER ══════ */
    .dash-banner {
        background: var(--tl-grad);
        border-radius: 20px; padding: 2rem 2.2rem; margin-bottom: 1.8rem;
        position: relative; overflow: hidden;
        box-shadow: 0 8px 32px var(--tl-glow);
    }
    .dash-banner::before { content:''; position:absolute; top:-60px; right:-60px; width:220px; height:220px; border-radius:50%; background:rgba(255,255,255,.07); }
    .dash-banner::after  { content:''; position:absolute; bottom:-60px; right:80px;  width:160px; height:160px; border-radius:50%; background:rgba(255,255,255,.04); }
    .dash-greeting { color:rgba(255,255,255,.8); font-size:.88rem; font-weight:500; margin-bottom:.15rem; position:relative; z-index:1; }
    .dash-name     { color:#fff; font-size:1.9rem; font-weight:800; margin:0 0 .2rem; position:relative; z-index:1; line-height:1.2; }
    .dash-sub      { color:rgba(255,255,255,.65); font-size:.85rem; margin:0; position:relative; z-index:1; }
    .dash-cta {
        background: rgba(255,255,255,.18); border: 1.5px solid rgba(255,255,255,.4);
        color: #fff; border-radius: 12px; padding: .75rem 1.6rem; font-weight: 700;
        font-size: .95rem; text-decoration: none; display: inline-flex; align-items: center; gap: .5rem;
        transition: background .18s, transform .18s; position: relative; z-index: 1; white-space: nowrap;
    }
    .dash-cta:hover { background: rgba(255,255,255,.28); color: #fff; transform: translateY(-2px); }

    /* ══════ SECCIÓN TÍTULO ══════ */
    .sec-title { font-size:.74rem; font-weight:800; color:#64748b; text-transform:uppercase; letter-spacing:.08em; margin: 0 0 1rem; display:flex; align-items:center; gap:.5rem; }
    .sec-title::after { content:''; flex:1; height:1px; background:#e2e8f0; }

    /* ══════ KPI CARDS ══════ */
    .kpi-card {
        background: #fff; border-radius: 16px; border: 1px solid #e8edf5;
        box-shadow: 0 2px 10px rgba(0,0,0,.05); padding: 1.4rem 1.5rem;
        display: flex; align-items: center; gap: 1.1rem;
        transition: transform .2s, box-shadow .2s;
    }
    .kpi-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,.09); }
    .kpi-icon { width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.45rem; flex-shrink: 0; }
    .kpi-val  { font-size: 2rem; font-weight: 800; line-height: 1; }
    .kpi-lbl  { font-size: .8rem; color: #64748b; font-weight: 600; margin-top: .2rem; }

    /* ══════ TICKET CARDS MINI ══════ */
    .tk-mini {
        background: #fff; border-radius: 14px; border: 1px solid #e8edf5;
        box-shadow: 0 2px 8px rgba(0,0,0,.04); padding: 1.1rem 1.3rem; margin-bottom: .8rem;
        transition: transform .18s, box-shadow .18s;
    }
    .tk-mini:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,.08); }
    .tk-folio { background: #ccfbf1; color: var(--tl1); font-size: .72rem; font-weight: 700; padding: .16rem .55rem; border-radius: 6px; white-space: nowrap; }
    .tk-title { font-weight: 700; color: #1e293b; font-size: .93rem; text-decoration: none; }
    .tk-title:hover { color: var(--tl1); }

    /* ══════ MINI STEPPER ══════ */
    .mini-flow { display: flex; align-items: flex-start; gap: 0; }
    .mini-step { position: relative; flex: 1; display: flex; flex-direction: column; align-items: center; }
    .mini-step:not(:last-child)::after { content:''; position:absolute; top:4px; left:55%; width:90%; height:2px; background:#dde1e7; z-index:0; }
    .mini-step.ms-done:not(:last-child)::after { background: var(--tl1); }
    .mini-dot { width:10px; height:10px; border-radius:50%; border:2px solid #dde1e7; background:#fff; margin:0 auto; position:relative; z-index:1; transition:all .2s; }
    .mini-step.ms-done .mini-dot   { background:var(--tl1); border-color:var(--tl1); }
    .mini-step.ms-active .mini-dot { background:var(--tl1); border-color:var(--tl1); box-shadow:0 0 0 3px rgba(15,118,110,.2); }
    .mini-step:not(:last-child).ms-active::after { background: linear-gradient(90deg, var(--tl1), #dde1e7); }
    .mini-lbl { font-size:.52rem; font-weight:700; text-align:center; color:#94a3b8; margin-top:3px; text-transform:uppercase; letter-spacing:.02em; line-height:1.1; }
    .mini-step.ms-done .mini-lbl, .mini-step.ms-active .mini-lbl { color:var(--tl1); }

    /* ══════ CHIPS ══════ */
    .chip { display:inline-block; padding:.2rem .6rem; border-radius:14px; font-size:.73rem; font-weight:700; }
    .chip-abierto    { background:#dbeafe; color:#1d4ed8; }
    .chip-en_proceso { background:#e0f2fe; color:#0369a1; }
    .chip-pendiente  { background:#fef9c3; color:#854d0e; }
    .chip-resuelto   { background:#dcfce7; color:#15803d; }
    .chip-cerrado    { background:#f1f5f9; color:#475569; }
    .chip-baja       { background:#ccfbf1; color:#0f766e; }
    .chip-media      { background:#fef9c3; color:#854d0e; }
    .chip-alta       { background:#fee2e2; color:#dc2626; }

    /* ══════ BTN VER ══════ */
    .btn-tl { background:var(--tl-grad); color:#fff; border:none; border-radius:9px; padding:.42rem .95rem; font-size:.82rem; font-weight:700; text-decoration:none; display:inline-flex; align-items:center; gap:.35rem; transition:filter .18s; white-space:nowrap; box-shadow:0 2px 8px var(--tl-glow); }
    .btn-tl:hover { filter:brightness(1.08); color:#fff; }

    /* ══════ EMPTY STATE ══════ */
    .empty-dash { text-align:center; padding:2.5rem 1rem; }
    .empty-dash-icon { width:64px; height:64px; border-radius:50%; background:var(--tl-light); display:flex; align-items:center; justify-content:center; margin:0 auto 1rem; font-size:1.8rem; color:var(--tl1); }

    @media(max-width:768px) {
        .dash-banner { padding:1.4rem 1.3rem; }
        .dash-name { font-size:1.5rem; }
        .kpi-card { padding:1.1rem; }
    }
</style>

{{-- ══════ BANNER ══════ --}}
<div class="dash-banner">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <p class="dash-greeting"><i class="bi bi-hand-wave me-1"></i>Bienvenido de nuevo,</p>
            <h1 class="dash-name">{{ session('usuario_nombre') }}</h1>
            <p class="dash-sub">Panel de soporte técnico · UPTEX</p>
        </div>
        <a href="{{ route('tickets.create') }}" class="dash-cta">
            <i class="bi bi-plus-circle-fill"></i> Crear Nuevo Ticket
        </a>
    </div>
</div>

{{-- ══════ KPIs ══════ --}}
<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <div class="kpi-card">
            <div class="kpi-icon" style="background:var(--tl-light); color:var(--tl1);">
                <i class="bi bi-ticket-perforated-fill"></i>
            </div>
            <div>
                <div class="kpi-val" style="color:var(--tl1);">{{ $stats['total'] ?? 0 }}</div>
                <div class="kpi-lbl">Total de Tickets</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="kpi-card">
            <div class="kpi-icon" style="background:#e0f2fe; color:#0369a1;">
                <i class="bi bi-clock-history"></i>
            </div>
            <div>
                <div class="kpi-val" style="color:#0369a1;">{{ $stats['en_proceso'] ?? 0 }}</div>
                <div class="kpi-lbl">En Proceso</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="kpi-card">
            <div class="kpi-icon" style="background:#dcfce7; color:#15803d;">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div>
                <div class="kpi-val" style="color:#15803d;">{{ $stats['resueltos'] ?? 0 }}</div>
                <div class="kpi-lbl">Resueltos</div>
            </div>
        </div>
    </div>
</div>

{{-- ══════ TICKETS RECIENTES ══════ --}}
<div class="sec-title"><i class="bi bi-clock-history"></i> Tickets Recientes</div>

@if(!empty($tickets) && count($tickets) > 0)

@foreach($tickets as $ticket)
@php
    $estadoTipo = $ticket->estado->tipo ?? 'abierto';
    $estadoMapped = match($estadoTipo) { 'en_proceso', 'pendiente' => 'en_atencion', default => $estadoTipo };
    $prioKey    = strtolower(str_replace(['á','é','í','ó','ú','ü','ñ',' '],['a','e','i','o','u','u','n','_'], $ticket->prioridad->nombre ?? 'media'));
    $idxActual  = collect($stepFlow)->search(fn($s) => $s['tipo'] === $estadoMapped);
    if ($idxActual === false) $idxActual = 0;
@endphp
<div class="tk-mini">
    <div class="d-flex align-items-start justify-content-between gap-2 flex-wrap">
        <div style="flex:1; min-width:0;">
            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                <span class="tk-folio">#{{ $ticket->id_ticket }}</span>
                <a href="{{ route('tickets.show', $ticket->id_ticket) }}" class="tk-title text-truncate" style="max-width:380px;">
                    {{ $ticket->titulo }}
                </a>
            </div>
            <div class="d-flex gap-2 flex-wrap mb-2">
                <span class="chip chip-{{ $estadoTipo }}">{{ $ticket->estado->nombre ?? 'N/A' }}</span>
                <span class="chip chip-{{ $prioKey }}">{{ $ticket->prioridad->nombre ?? 'N/A' }}</span>
                <span class="chip" style="background:#f1f5f9;color:#475569;"><i class="bi bi-building me-1"></i>{{ $ticket->area->nombre ?? 'N/A' }}</span>
            </div>
            {{-- MINI STEPPER --}}
            <div class="mini-flow">
                @foreach($stepFlow as $i => $step)
                    @php $cls = $i < $idxActual ? 'ms-done' : ($i === $idxActual ? 'ms-active' : ''); @endphp
                    <div class="mini-step {{ $cls }}">
                        <div class="mini-dot"></div>
                        <div class="mini-lbl">{{ $step['label'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="d-flex flex-column align-items-end gap-2 flex-shrink-0">
            <small class="text-muted" style="font-size:.75rem; white-space:nowrap;">
                <i class="bi bi-clock me-1"></i>{{ $ticket->fecha_creacion ? $ticket->fecha_creacion->format('d/m/Y') : 'N/A' }}
            </small>
            <a href="{{ route('tickets.show', $ticket->id_ticket) }}" class="btn-tl">
                <i class="bi bi-eye"></i> Ver
            </a>
        </div>
    </div>
</div>
@endforeach

<div class="text-end mt-1 mb-3">
    <a href="{{ route('tickets.mis-tickets') }}" style="color:var(--tl1); font-size:.88rem; font-weight:700; text-decoration:none;">
        Ver todos mis tickets <i class="bi bi-arrow-right-circle"></i>
    </a>
</div>

@else
<div class="tk-mini empty-dash">
    <div class="empty-dash-icon"><i class="bi bi-inbox"></i></div>
    <h6 style="color:#1e293b; font-weight:700;">No tienes tickets aún</h6>
    <p class="text-muted small mb-3">Crea tu primer ticket para recibir soporte técnico.</p>
    <a href="{{ route('tickets.create') }}" class="btn-tl">
        <i class="bi bi-plus-circle"></i> Crear Ticket
    </a>
</div>
@endif
@endsection