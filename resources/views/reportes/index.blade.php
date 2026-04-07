@extends('layouts.app')

@section('title', 'Estadísticas')


@section('content')
<style>
/* ═══════════════════════════════════════════════════════════════
   BANNER
═══════════════════════════════════════════════════════════════ */
.rep-banner {
    background: linear-gradient(135deg, #1e3a5f 0%, #1d4ed8 100%);
    border-radius: 18px; padding: 1.5rem 2rem; margin-bottom: 1.5rem;
    display: flex; align-items: center; flex-wrap: wrap; gap: 1rem;
    position: relative; overflow: hidden;
    box-shadow: 0 8px 30px rgba(29,78,216,.25);
}
.rep-banner::before {
    content:''; position:absolute; top:-40px; right:-40px;
    width:180px; height:180px; border-radius:50%;
    background:rgba(255,255,255,.06);
}
.rep-banner::after {
    content:''; position:absolute; bottom:-50px; right:120px;
    width:130px; height:130px; border-radius:50%;
    background:rgba(255,255,255,.04);
}
.rep-banner-logo {
    width:50px; height:50px; background:rgba(255,255,255,.92);
    border-radius:12px; display:flex; align-items:center; justify-content:center;
    flex-shrink:0; padding:6px; position:relative; z-index:1;
}
.rep-banner-logo img { width:100%; height:100%; object-fit:contain; }
.rep-banner-info { position:relative; z-index:1; }
.rep-banner-title { color:#fff; font-size:1.4rem; font-weight:700; line-height:1.2; margin:0; }
.rep-banner-sub   { color:rgba(255,255,255,.7); font-size:.82rem; margin:.1rem 0 0; }
.rep-banner-actions { margin-left:auto; display:flex; gap:.55rem; flex-wrap:wrap; position:relative; z-index:1; }
.rep-banner-btn {
    display:inline-flex; align-items:center; gap:.4rem;
    background:rgba(255,255,255,.13); border:1.5px solid rgba(255,255,255,.35);
    color:#fff; border-radius:10px; font-size:.78rem; font-weight:600;
    padding:.42rem .9rem; cursor:pointer; text-decoration:none;
    transition: background .15s, border-color .15s; white-space:nowrap;
}
.rep-banner-btn:hover { background:rgba(255,255,255,.22); border-color:rgba(255,255,255,.6); color:#fff; }
.rep-banner-btn.pdf-btn { background:rgba(255,96,89,.35); border-color:rgba(255,150,150,.55); }
.rep-banner-btn.pdf-btn:hover { background:rgba(255,96,89,.5); }

/* ═══════════════════════════════════════════════════════════════
   PANEL DE CONTROL (FILTROS)
═══════════════════════════════════════════════════════════════ */
.pbi-panel {
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    border: 1.5px solid #e2e8f0; border-radius: 14px;
    padding: .85rem 1.3rem; margin-bottom: 1.4rem;
    display: flex; align-items: center; flex-wrap: wrap; gap: .75rem;
}
.pbi-panel-label {
    font-size:.72rem; font-weight:700; text-transform:uppercase;
    letter-spacing:.06em; color:#475569; white-space:nowrap;
}
.pbi-select {
    border:1.5px solid #cbd5e1; border-radius:9px;
    padding:.42rem .8rem; font-size:.82rem; color:#334155;
    background:#fff; min-width:175px; outline:none; cursor:pointer;
    transition:border-color .15s;
}
.pbi-select:focus { border-color:#1d4ed8; box-shadow:0 0 0 3px rgba(29,78,216,.08); }
.pbi-badge-filter {
    font-size:.71rem; font-weight:600; padding:.2rem .7rem; border-radius:20px;
    background:#dbeafe; color:#1d4ed8; animation:pbi-pulse .4s ease;
}
@keyframes pbi-pulse { from{opacity:.4;transform:scale(.95)} to{opacity:1;transform:scale(1)} }
.pbi-spinner {
    display:none; width:15px; height:15px; border:2px solid #cbd5e1;
    border-top-color:#1d4ed8; border-radius:50%; animation:spin .7s linear infinite;
}
@keyframes spin { to{transform:rotate(360deg)} }

/* ═══════════════════════════════════════════════════════════════
   TABS
═══════════════════════════════════════════════════════════════ */
.rep-tabs-wrap {
    border-bottom:2px solid #e2e8f0; margin-bottom:1.5rem;
    display:flex; gap:0;
}
.rep-tab-btn {
    display:inline-flex; align-items:center; gap:.45rem;
    padding:.7rem 1.4rem; font-size:.84rem; font-weight:600; color:#64748b;
    background:none; border:none; border-bottom:3px solid transparent;
    margin-bottom:-2px; cursor:pointer; transition:color .15s, border-color .15s;
    white-space:nowrap;
}
.rep-tab-btn:hover { color:#1d4ed8; }
.rep-tab-btn.active { color:#1d4ed8; border-bottom-color:#1d4ed8; }
.rep-tab-btn .tab-badge {
    font-size:.65rem; font-weight:700; padding:.12rem .45rem;
    border-radius:20px; background:#e2e8f0; color:#64748b;
    transition:background .15s, color .15s;
}
.rep-tab-btn.active .tab-badge { background:#dbeafe; color:#1d4ed8; }
.rep-tab-btn.active-enc { color:#15803d; border-bottom-color:#16a34a; }
.rep-tab-btn.active-enc .tab-badge { background:#dcfce7; color:#15803d; }
.rep-tab-pane { display:none; }
.rep-tab-pane.active { display:block; }

/* SECTION HEADER */
.sec-header {
    display:flex; align-items:center; gap:.65rem; flex-wrap:wrap;
    padding:.5rem 0 1rem;
}
.sec-header-bar { width:4px; height:1.3rem; border-radius:2px; flex-shrink:0; }
.sec-header-title {
    font-size:.78rem; font-weight:700; text-transform:uppercase;
    letter-spacing:.07em; color:#475569;
}

/* KPI DINÁMICOS */
.dkpi-card {
    background:#fff; border-radius:14px; border:1px solid #e8edf5;
    box-shadow:0 2px 10px rgba(0,0,0,.04); padding:1rem 1.2rem;
    display:flex; align-items:center; gap:.8rem; height:100%;
}
.dkpi-icon { width:46px; height:46px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.25rem; flex-shrink:0; }
.dkpi-val { font-size:1.85rem; font-weight:800; line-height:1; color:#1e293b; transition:color .3s; }
.dkpi-lbl { font-size:.71rem; color:#64748b; margin-top:.1rem; }

/* GRÁFICAS */
.dyn-chart-card {
    background:#fff; border-radius:16px; border:1px solid #e8edf5;
    box-shadow:0 2px 10px rgba(0,0,0,.04); padding:1.2rem 1.4rem; height:100%;
}
.dyn-chart-title {
    font-size:.73rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em;
    color:#64748b; margin-bottom:.8rem; display:flex; align-items:center; gap:.4rem;
}

/* ENCUESTAS — HERO */
.enc-hero {
    background:linear-gradient(135deg,#f0fdf4,#dcfce7);
    border:1.5px solid #bbf7d0; border-radius:18px;
    padding:1.6rem 2rem; display:flex; align-items:center; gap:1.4rem;
    box-shadow:0 4px 18px rgba(22,163,74,.10); position:relative; overflow:hidden; height:100%;
}
.enc-hero.amarillo { background:linear-gradient(135deg,#fffbeb,#fef3c7); border-color:#fde68a; box-shadow:0 4px 18px rgba(245,158,11,.12); }
.enc-hero.rojo     { background:linear-gradient(135deg,#fff1f2,#fee2e2); border-color:#fecaca; box-shadow:0 4px 18px rgba(220,38,38,.10); }
.enc-hero::after   { content:''; position:absolute; right:-20px; top:-20px; width:110px; height:110px; border-radius:50%; background:rgba(255,255,255,.35); }
.enc-hero-pct  { font-size:3.6rem; font-weight:900; line-height:1; color:#15803d; }
.enc-hero.amarillo .enc-hero-pct { color:#b45309; }
.enc-hero.rojo     .enc-hero-pct { color:#b91c1c; }
.enc-hero-label { font-size:.88rem; font-weight:600; color:#166534; margin-bottom:.2rem; }
.enc-hero.amarillo .enc-hero-label { color:#92400e; }
.enc-hero.rojo     .enc-hero-label { color:#991b1b; }
.enc-hero-sub { font-size:.76rem; color:#4ade80; }
.enc-hero.amarillo .enc-hero-sub { color:#f59e0b; }
.enc-hero.rojo     .enc-hero-sub  { color:#f87171; }
.enc-hero-icon {
    width:60px; height:60px; border-radius:16px; background:rgba(255,255,255,.6);
    display:flex; align-items:center; justify-content:center;
    font-size:1.9rem; flex-shrink:0; color:#16a34a; box-shadow:0 2px 8px rgba(0,0,0,.06);
    position:relative; z-index:1;
}
.enc-hero.amarillo .enc-hero-icon { color:#d97706; }
.enc-hero.rojo     .enc-hero-icon { color:#dc2626; }

/* KPI pequeñas */
.enc-kpi {
    background:#fff; border-radius:14px; border:1px solid #e8edf5;
    box-shadow:0 2px 8px rgba(0,0,0,.04); padding:.85rem 1.05rem;
    display:flex; align-items:center; gap:.7rem; height:100%;
}
.enc-kpi-icon { width:40px; height:40px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1.1rem; flex-shrink:0; }
.enc-kpi-val  { font-size:1.45rem; font-weight:700; line-height:1; color:#1e293b; }
.enc-kpi-lbl  { font-size:.7rem; color:#64748b; margin-top:.1rem; }

/* PROMEDIOS P1-P5 */
.q-avg-card {
    background:#fff; border-radius:14px; border:1px solid #e8edf5;
    box-shadow:0 2px 8px rgba(0,0,0,.04); padding:.85rem 1.05rem; height:100%;
}
.q-avg-bar      { width:100%; height:6px; background:#e2e8f0; border-radius:3px; overflow:hidden; margin:.35rem 0 .2rem; }
.q-avg-bar-fill { height:100%; border-radius:3px; transition:width .6s ease; }

/* TABLA */
.enc-table-wrap { background:#fff; border-radius:16px; border:1px solid #e8edf5; box-shadow:0 2px 12px rgba(0,0,0,.04); overflow:hidden; }
.enc-table-header { background:linear-gradient(135deg,#1e3a5f,#1d4ed8); padding:.85rem 1.2rem; display:flex; align-items:center; gap:.5rem; }
.enc-table-header span { color:#fff; font-size:.8rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; }
.enc-table-wrap table { margin:0; font-size:.84rem; }
.enc-table-wrap thead th { background:#f8fafc; color:#475569; font-size:.73rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em; padding:.62rem 1rem; border-bottom:2px solid #e2e8f0; white-space:nowrap; }
.enc-table-wrap tbody td { padding:.68rem 1rem; color:#334155; border-bottom:1px solid #f1f5f9; vertical-align:middle; }
.enc-table-wrap tbody tr:last-child td { border-bottom:none; }
.enc-table-wrap tbody tr:hover td { background:#f8fafc; }
.enc-pct-bar { height:6px; border-radius:4px; background:#e2e8f0; overflow:hidden; min-width:55px; }
.enc-pct-chip { font-size:.7rem; font-weight:700; padding:.18rem .5rem; border-radius:20px; display:inline-block; }

/* GRÁFICA CARD enc */
.enc-chart-card { background:#fff; border-radius:16px; border:1px solid #e8edf5; box-shadow:0 2px 12px rgba(0,0,0,.04); padding:1.2rem 1.3rem; height:100%; }
.enc-chart-title { font-size:.73rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:#64748b; margin-bottom:.85rem; display:flex; align-items:center; gap:.4rem; }

/* MODAL PDF */
.pdf-check-row {
    display:flex; align-items:flex-start; gap:.75rem;
    padding:.75rem 1rem; border-radius:10px; border:1.5px solid #e2e8f0;
    cursor:pointer; transition:border-color .15s, background .15s;
}
.pdf-check-row:hover   { border-color:#1d4ed8; background:#f8faff; }
.pdf-check-row.checked { border-color:#1d4ed8; background:#eff6ff; }
.pdf-check-row input   { margin-top:.18rem; accent-color:#1d4ed8; width:16px; height:16px; flex-shrink:0; cursor:pointer; }
.pdf-check-title  { font-size:.85rem; font-weight:600; color:#1e293b; }
.pdf-check-desc   { font-size:.75rem; color:#64748b; margin-top:.1rem; line-height:1.4; }

/* RESPONSIVE */
@media (max-width: 768px) {
    .rep-banner        { padding:1.2rem; flex-wrap:wrap; }
    .rep-banner-title  { font-size:1.2rem; }
    .rep-banner-actions{ margin-left:0; width:100%; }
    .enc-hero-pct      { font-size:2.8rem; }
    .pbi-panel         { flex-direction:column; align-items:flex-start; }
    .pbi-select        { min-width:100%; }
    .rep-tab-btn       { padding:.6rem .9rem; font-size:.78rem; }
}
</style>

<div class="container-fluid">

{{-- ══════ BANNER ══════ --}}
<div class="rep-banner">
    <div class="rep-banner-logo">
        <img src="{{ asset('images/logo-uptex.png') }}" alt="UPTEX">
    </div>
    <div class="rep-banner-info">
        <h1 class="rep-banner-title">Estadísticas del Sistema</h1>
        <p class="rep-banner-sub">Panel de análisis y reportes &mdash; Universidad Politécnica de Texcoco &middot; {{ date('Y') }}</p>
    </div>
    <div class="rep-banner-actions">
        <a href="{{ route('reportes.por-fecha') }}" class="rep-banner-btn">
            <i class="bi bi-calendar-range"></i>Tickets por Fecha
        </a>
        <a href="{{ route('reportes.rendimiento') }}" class="rep-banner-btn">
            <i class="bi bi-people-fill"></i>Rendimiento Técnicos
        </a>
        <button class="rep-banner-btn pdf-btn" onclick="abrirModalPDF()">
            <i class="bi bi-file-earmark-pdf-fill"></i>Exportar PDF
        </button>
    </div>
</div>

{{-- ══════ PANEL DE CONTROL (FILTROS) ══════ --}}
<div class="pbi-panel">
    <div>
        <i class="bi bi-sliders2 me-1" style="color:#1d4ed8;"></i>
        <span class="pbi-panel-label">Panel de Control</span>
    </div>
    <select id="filtroArea" class="pbi-select">
        <option value="">Todas las áreas</option>
        @foreach($areas as $area)
        <option value="{{ $area->id_area }}">{{ $area->nombre }}</option>
        @endforeach
    </select>
    <select id="filtroTecnico" class="pbi-select">
        <option value="">Todos los técnicos</option>
        @foreach($tecnicos as $tec)
        <option value="{{ $tec->id_usuario }}">{{ $tec->nombre }} {{ $tec->apellido }}</option>
        @endforeach
    </select>
    <div class="pbi-spinner" id="pbiSpinner"></div>
    <span class="pbi-badge-filter d-none" id="pbiBadge">Filtro activo</span>
    <button class="btn btn-sm btn-outline-secondary ms-auto" id="pbiReset"
        style="border-radius:9px;font-size:.78rem;">
        <i class="bi bi-x-circle me-1"></i>Limpiar filtros
    </button>
</div>

{{-- ══════ TABS ══════ --}}
<div class="rep-tabs-wrap">
    <button class="rep-tab-btn active" id="tabBtnTickets" onclick="switchTab('tickets')">
        <i class="bi bi-ticket-detailed-fill"></i>
        Gestión de Tickets
        <span class="tab-badge" id="tabBadgeTickets">{{ $ticketStats['total'] }}</span>
    </button>
    <button class="rep-tab-btn" id="tabBtnEncuestas" onclick="switchTab('encuestas')">
        <i class="bi bi-emoji-smile-fill"></i>
        Encuestas de Satisfacción
        @if(isset($satisfaccionStats))
        <span class="tab-badge" id="tabBadgeEncuestas">{{ $satisfaccionStats['respondidas'] }}/{{ $satisfaccionStats['total'] }}</span>
        @endif
    </button>
</div>

{{-- ══════════════════════════════════════════════════════════════ --}}
{{--                    TAB 1 — TICKETS                           --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div class="rep-tab-pane active" id="paneTickets">

    <div class="sec-header">
        <span class="sec-header-bar" style="background:linear-gradient(180deg,#1d4ed8,#3b82f6);"></span>
        <span class="sec-header-title">Indicadores de Tickets</span>
        <span class="text-muted ms-1" style="font-size:.75rem;">
            <i class="bi bi-arrow-repeat me-1"></i>Se actualiza automáticamente al aplicar filtros
        </span>
    </div>

    {{-- KPI CARDS --}}
    <div class="row g-3 mb-4" id="print-kpis-tickets">
        <div class="col-6 col-xl-3">
            <div class="dkpi-card">
                <div class="dkpi-icon" style="background:#dbeafe;color:#1d4ed8;">
                    <i class="bi bi-ticket-detailed-fill"></i>
                </div>
                <div>
                    <div class="dkpi-val" id="dkpiTotal">{{ $ticketStats['total'] }}</div>
                    <div class="dkpi-lbl">Total Tickets</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="dkpi-card">
                <div class="dkpi-icon" style="background:#fef3c7;color:#d97706;">
                    <i class="bi bi-clock-fill"></i>
                </div>
                <div>
                    <div class="dkpi-val" id="dkpiAbiertos" style="color:#d97706;">{{ $ticketStats['abiertos'] }}</div>
                    <div class="dkpi-lbl">Abiertos / Pendientes</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="dkpi-card">
                <div class="dkpi-icon" style="background:#ede9fe;color:#7c3aed;">
                    <i class="bi bi-gear-fill"></i>
                </div>
                <div>
                    <div class="dkpi-val" id="dkpiEnProceso" style="color:#7c3aed;">{{ $ticketStats['en_proceso'] }}</div>
                    <div class="dkpi-lbl">En Proceso</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="dkpi-card">
                <div class="dkpi-icon" style="background:#dcfce7;color:#16a34a;">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div>
                    <div class="dkpi-val" id="dkpiResueltos" style="color:#16a34a;">{{ $ticketStats['resueltos'] }}</div>
                    <div class="dkpi-lbl">Resueltos / Cerrados</div>
                </div>
            </div>
        </div>
    </div>

    {{-- GRÁFICAS --}}
    <div class="row g-3 mb-4" id="print-charts-tickets">
        <div class="col-12 col-lg-5">
            <div class="dyn-chart-card">
                <div class="dyn-chart-title">
                    <i class="bi bi-bar-chart-fill text-primary"></i>
                    Tickets por Área
                    <span style="margin-left:auto;font-size:.67rem;color:#94a3b8;font-weight:400;">Eje Y = Nº de tickets</span>
                </div>
                <div style="height:230px;position:relative;">
                    <canvas id="chartPorArea"></canvas>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
            <div class="dyn-chart-card">
                <div class="dyn-chart-title">
                    <i class="bi bi-pie-chart-fill text-warning"></i>
                    Por Prioridad
                    <span style="margin-left:auto;font-size:.67rem;color:#94a3b8;font-weight:400;">Distribución</span>
                </div>
                <div style="height:230px;position:relative;">
                    <canvas id="chartPorPrioridad"></canvas>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-4">
            <div class="dyn-chart-card">
                <div class="dyn-chart-title">
                    <i class="bi bi-graph-up-arrow text-info"></i>
                    Resolución — Últimos 14 días
                    <span style="margin-left:auto;font-size:.67rem;color:#94a3b8;font-weight:400;">Eje Y = Resueltos/día</span>
                </div>
                <div style="height:230px;position:relative;">
                    <canvas id="chartLinea"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>{{-- /paneTickets --}}

{{-- ══════════════════════════════════════════════════════════════ --}}
{{--                TAB 2 — ENCUESTAS                             --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
@if(isset($satisfaccionStats))
@php
    $satisfechos      = $satisfaccionStats['satisfechos'];
    $respondidas      = $satisfaccionStats['respondidas'];
    $total            = $satisfaccionStats['total'];
    $noSatisfechos    = $satisfaccionStats['no_satisfechos'];
    $sinResponder     = $satisfaccionStats['sin_responder'];
    $pctSatisfaccion  = $respondidas > 0 ? round($satisfechos / $respondidas * 100, 1) : 0;
    $tasaPct          = $total > 0 ? round($respondidas / $total * 100, 1) : 0;
    $heroClass        = $pctSatisfaccion >= 80 ? '' : ($pctSatisfaccion >= 50 ? 'amarillo' : 'rojo');
    $heroIcon         = $pctSatisfaccion >= 80 ? 'bi-emoji-laughing-fill' : ($pctSatisfaccion >= 50 ? 'bi-emoji-neutral-fill' : 'bi-emoji-frown-fill');
    $heroMsg          = $pctSatisfaccion >= 80 ? 'Nivel óptimo >= 80%' : ($pctSatisfaccion >= 50 ? 'Por debajo del objetivo (80%)' : 'Nivel crítico - requiere atención');
    $qLabels          = [
        1 => 'Calidad del servicio',
        2 => 'Atención de solicitudes',
        3 => 'Tiempo de resolución',
        4 => 'Conocimientos técnicos',
        5 => 'Satisfacción general',
    ];
@endphp

<div class="rep-tab-pane" id="paneEncuestas">

    <div class="sec-header">
        <span class="sec-header-bar" style="background:linear-gradient(180deg,#16a34a,#0d9488);"></span>
        <span class="sec-header-title">Encuestas de Satisfacción</span>
        <span class="badge bg-success ms-1" id="encHeaderBadge" style="font-size:.68rem;font-weight:600;">
            {{ $respondidas }}/{{ $total }} respondidas
        </span>
        <span class="text-muted d-none d-md-inline" style="font-size:.73rem;">
            <i class="bi bi-clock-history me-1"></i>Mantenimiento TI &mdash; Periodo {{ date('Y') }}
        </span>
        <a href="{{ route('reportes.encuestas') }}" class="ms-auto btn btn-sm btn-outline-success"
           style="border-radius:9px;font-size:.76rem;white-space:nowrap;">
            <i class="bi bi-table me-1"></i>Ver respuestas detalladas
        </a>
    </div>

    {{-- Bloque 1: Hero + 4 KPIs --}}
    <div class="row g-3 mb-4" id="print-enc-hero">
        <div class="col-12 col-md-4">
            <div class="enc-hero {{ $heroClass }}" id="encHeroCard">
                <div class="enc-hero-icon" id="encHeroIconWrap">
                    <i class="bi {{ $heroIcon }}" id="encHeroIconI"></i>
                </div>
                <div style="position:relative;z-index:1;">
                    <div class="enc-hero-pct" id="encHeroPct">{{ $pctSatisfaccion }}%</div>
                    <div class="enc-hero-label">de satisfacción global</div>
                    <div class="enc-hero-sub" id="encHeroSub">{{ $heroMsg }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-8">
            <div class="row g-3 h-100">
                <div class="col-6 col-xl-3">
                    <div class="enc-kpi">
                        <div class="enc-kpi-icon" style="background:#dbeafe;color:#1d4ed8;"><i class="bi bi-clipboard-data-fill"></i></div>
                        <div>
                            <div class="enc-kpi-val" id="encKpiTotal">{{ $total }}</div>
                            <div class="enc-kpi-lbl">Total Enviadas</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-xl-3">
                    <div class="enc-kpi">
                        <div class="enc-kpi-icon" style="background:#ede9fe;color:#7c3aed;"><i class="bi bi-check-circle-fill"></i></div>
                        <div>
                            <div class="enc-kpi-val" id="encKpiRespondidas">{{ $respondidas }}</div>
                            <div class="enc-kpi-lbl">Respondidas</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-xl-3">
                    <div class="enc-kpi">
                        <div class="enc-kpi-icon" style="background:#dcfce7;color:#16a34a;"><i class="bi bi-emoji-smile-fill"></i></div>
                        <div>
                            <div class="enc-kpi-val" id="encKpiSatisfechos" style="color:#16a34a;">{{ $satisfechos }}</div>
                            <div class="enc-kpi-lbl">Satisfechos</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-xl-3">
                    <div class="enc-kpi">
                        <div class="enc-kpi-icon" style="background:#fee2e2;color:#dc2626;"><i class="bi bi-emoji-frown-fill"></i></div>
                        <div>
                            <div class="enc-kpi-val" id="encKpiNoSatisfechos" style="color:#dc2626;">{{ $noSatisfechos }}</div>
                            <div class="enc-kpi-lbl">No Satisfechos</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bloque 2: Promedios P1-P5 --}}
    <div class="row g-3 mb-4" id="print-enc-promedios">
        @foreach(range(1,5) as $qi)
        @php
            $qv    = $preguntaPromedios[$qi] ?? 0;
            $qPct  = $qv > 0 ? round($qv / 4 * 100) : 0;
            $qC    = $qv >= 3 ? '#16a34a' : ($qv >= 2.5 ? '#f59e0b' : '#dc2626');
            $qBg   = $qv >= 3 ? '#dcfce7' : ($qv >= 2.5 ? '#fef3c7' : '#fee2e2');
        @endphp
        <div class="col-6 col-md-4 col-xl">
            <div class="q-avg-card">
                <span style="display:inline-block;padding:.22rem .6rem;border-radius:8px;font-size:.69rem;font-weight:800;background:{{ $qBg }};color:{{ $qC }};margin-bottom:.4rem;">P{{ $qi }}</span>
                <div style="font-size:.7rem;color:#64748b;line-height:1.3;margin-bottom:.4rem;">{{ $qLabels[$qi] }}</div>
                <div class="q-avg-bar">
                    <div class="q-avg-bar-fill" style="width:{{ $qPct }}%;background:{{ $qC }};"></div>
                </div>
                <div style="font-size:1.05rem;font-weight:800;color:{{ $qC }};margin-top:.3rem;">
                    {{ $qv > 0 ? number_format($qv, 1) : '—' }}<span style="font-size:.68rem;color:#94a3b8;font-weight:400;"> /4.0</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Bloque 3: Tabla + gráfica por área --}}
    <div class="row g-3 mb-4" id="print-enc-tabla">
        <div class="col-12 col-lg-6">
            <div class="enc-table-wrap h-100">
                <div class="enc-table-header">
                    <i class="bi bi-table text-white" style="font-size:.88rem;"></i>
                    <span>Indicadores por Área</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Área</th>
                                <th class="text-center">Satisfechos</th>
                                <th class="text-center">No Satisf.</th>
                                <th class="text-center">Total</th>
                                <th>% Satisf.</th>
                            </tr>
                        </thead>
                        <tbody id="encTableBody">
                            @forelse($satisfaccionStats['por_area'] as $fila)
                            @php
                                $tot      = ($fila->satisfechos + $fila->no_satisfechos);
                                $pct      = $tot > 0 ? round($fila->satisfechos / $tot * 100) : 0;
                                $barColor = $pct >= 80 ? '#16a34a' : ($pct >= 50 ? '#f59e0b' : '#dc2626');
                                $chipBg   = $pct >= 80 ? '#dcfce7' : ($pct >= 50 ? '#fef3c7' : '#fee2e2');
                                $chipTxt  = $pct >= 80 ? '#15803d' : ($pct >= 50 ? '#b45309' : '#b91c1c');
                            @endphp
                            <tr>
                                <td style="max-width:130px;white-space:normal;font-size:.79rem;">{{ $fila->area }}</td>
                                <td class="text-center fw-semibold" style="color:#16a34a;">{{ $fila->satisfechos }}</td>
                                <td class="text-center fw-semibold" style="color:#dc2626;">{{ $fila->no_satisfechos }}</td>
                                <td class="text-center fw-semibold">{{ $tot }}</td>
                                <td style="min-width:88px;">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="enc-pct-bar flex-grow-1">
                                            <div style="height:100%;border-radius:4px;width:{{ $pct }}%;background:{{ $barColor }};"></div>
                                        </div>
                                        <span class="enc-pct-chip" style="background:{{ $chipBg }};color:{{ $chipTxt }};">{{ $pct }}%</span>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4" style="font-size:.84rem;">
                                    <i class="bi bi-inbox d-block mb-1" style="font-size:1.5rem;"></i>
                                    Sin encuestas respondidas aún
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="enc-chart-card h-100">
                <div class="enc-chart-title">
                    <i class="bi bi-bar-chart-fill text-primary"></i>
                    Satisfechos vs No Satisfechos por Área
                </div>
                <div style="height:250px;position:relative;">
                    <canvas id="encChartPorArea"></canvas>
                </div>
                @if(empty($satisfaccionStats['por_area']))
                    <p class="text-center text-muted mt-2 mb-0" style="font-size:.79rem;">Sin datos aún</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Bloque 4: Donut Global + Tasa + Q-Avg --}}
    <div class="row g-3 mb-4" id="print-enc-charts">
        <div class="col-12 col-sm-4">
            <div class="enc-chart-card h-100">
                <div class="enc-chart-title">
                    <i class="bi bi-pie-chart-fill text-success"></i> Distribución Global
                </div>
                <div style="height:160px;position:relative;">
                    <canvas id="encChartGlobal"></canvas>
                </div>
                <div class="mt-3 d-flex flex-column gap-1" style="font-size:.75rem;color:#64748b;">
                    <span><span style="display:inline-block;width:9px;height:9px;border-radius:50%;background:#16a34a;margin-right:5px;"></span>{{ $satisfechos }} satisfechos</span>
                    <span><span style="display:inline-block;width:9px;height:9px;border-radius:50%;background:#dc2626;margin-right:5px;"></span>{{ $noSatisfechos }} no satisfechos</span>
                    <span><span style="display:inline-block;width:9px;height:9px;border-radius:50%;background:#94a3b8;margin-right:5px;"></span>{{ $sinResponder }} sin responder</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-4">
            <div class="enc-chart-card h-100 text-center">
                <div class="enc-chart-title justify-content-center">
                    <i class="bi bi-reply-fill text-warning"></i> Tasa de Respuesta
                </div>
                <div style="height:160px;position:relative;display:flex;align-items:center;justify-content:center;">
                    <canvas id="encChartTasa"></canvas>
                    <div style="position:absolute;text-align:center;pointer-events:none;">
                        <div class="fw-bold" id="encTasaText" style="font-size:1.8rem;color:#1e3a5f;line-height:1;">{{ $tasaPct }}%</div>
                        <div style="font-size:.68rem;color:#64748b;">respondidas</div>
                    </div>
                </div>
                <div class="text-muted mt-2" style="font-size:.76rem;">
                    <strong style="color:#1e293b;">{{ $respondidas }}</strong> de <strong style="color:#1e293b;">{{ $total }}</strong> encuestas contestadas
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-4">
            <div class="enc-chart-card h-100">
                <div class="enc-chart-title">
                    <i class="bi bi-bar-chart-steps text-success"></i> Promedio por Pregunta
                    <span style="margin-left:auto;font-size:.65rem;color:#94a3b8;font-weight:400;">escala 1–4</span>
                </div>
                <div style="height:190px;position:relative;">
                    <canvas id="encChartQAvg"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>{{-- /paneEncuestas --}}
@endif

</div>{{-- /container-fluid --}}

{{-- ══════════════════════════════════════════════════════════════
     MODAL PDF
══════════════════════════════════════════════════════════════ --}}
<div id="pdf-overlay" onclick="cerrarModalPDF()"
    style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1040;backdrop-filter:blur(2px);"></div>

<div id="pdf-modal"
    style="display:none;position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);
           z-index:1050;background:#fff;border-radius:18px;padding:1.8rem 2rem;
           width:min(480px,92vw);box-shadow:0 20px 60px rgba(0,0,0,.2);max-height:90vh;overflow-y:auto;">

    <div class="d-flex align-items-center mb-3" style="gap:.75rem;">
        <div style="width:38px;height:38px;border-radius:10px;background:#fee2e2;color:#dc2626;
                    display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0;">
            <i class="bi bi-file-earmark-pdf-fill"></i>
        </div>
        <div>
            <div style="font-size:1rem;font-weight:700;color:#1e293b;">Exportar como PDF</div>
            <div style="font-size:.78rem;color:#64748b;">Selecciona qué secciones incluir</div>
        </div>
        <button onclick="cerrarModalPDF()" class="btn-close ms-auto"></button>
    </div>

    <div class="d-flex flex-column gap-2 mb-4">
        <label class="pdf-check-row checked" onclick="togglePdfRow(this,'pdfBanner')">
            <input type="checkbox" id="pdfBanner" checked onclick="event.stopPropagation()">
            <div>
                <div class="pdf-check-title"><i class="bi bi-image me-1 text-primary"></i>Encabezado / Banner</div>
                <div class="pdf-check-desc">Logo UPTEX, título y fecha del reporte.</div>
            </div>
        </label>
        <label class="pdf-check-row checked" onclick="togglePdfRow(this,'pdfTickets')">
            <input type="checkbox" id="pdfTickets" checked onclick="event.stopPropagation()">
            <div>
                <div class="pdf-check-title"><i class="bi bi-ticket-detailed-fill me-1 text-primary"></i>Gestión de Tickets</div>
                <div class="pdf-check-desc">KPIs actuales: Total, Abiertos, En Proceso y Resueltos.</div>
            </div>
        </label>
        <label class="pdf-check-row checked" onclick="togglePdfRow(this,'pdfEncKPIs')">
            <input type="checkbox" id="pdfEncKPIs" checked onclick="event.stopPropagation()">
            <div>
                <div class="pdf-check-title"><i class="bi bi-emoji-smile-fill me-1 text-success"></i>Encuestas — KPIs y Promedios P1-P5</div>
                <div class="pdf-check-desc">% satisfacción, KPIs numéricos y mini-barras P1–P5.</div>
            </div>
        </label>
        <label class="pdf-check-row checked" onclick="togglePdfRow(this,'pdfEncTabla')">
            <input type="checkbox" id="pdfEncTabla" checked onclick="event.stopPropagation()">
            <div>
                <div class="pdf-check-title"><i class="bi bi-table me-1 text-success"></i>Encuestas — Tabla por Área</div>
                <div class="pdf-check-desc">Indicadores de satisfacción desglosados por área.</div>
            </div>
        </label>
    </div>

    <div class="d-flex gap-2 justify-content-end">
        <button onclick="cerrarModalPDF()" class="btn btn-outline-secondary" style="border-radius:10px;font-size:.83rem;">
            Cancelar
        </button>
        <button onclick="ejecutarPDF()" class="btn btn-danger" style="border-radius:10px;font-size:.83rem;">
            <i class="bi bi-file-earmark-pdf-fill me-1"></i>Generar PDF
        </button>
    </div>
</div>

@if(isset($satisfaccionStats))
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var stats             = @json($satisfaccionStats);
    var porAreaInit       = @json($porAreaChart);
    var porPrioInit       = @json($porPrioridadChart);
    var preguntaPromedios = @json($preguntaPromedios);
    var filterUrl         = '{{ route("reportes.filter-data") }}';

    /* ── SWITCH TABS ── */
    window.switchTab = function(tab) {
        var pT = document.getElementById('paneTickets');
        var pE = document.getElementById('paneEncuestas');
        var bT = document.getElementById('tabBtnTickets');
        var bE = document.getElementById('tabBtnEncuestas');
        if(pT) pT.classList.toggle('active', tab === 'tickets');
        if(pE) pE.classList.toggle('active', tab === 'encuestas');
        if(bT) { bT.classList.toggle('active', tab === 'tickets'); }
        if(bE) {
            bE.classList.toggle('active', tab === 'encuestas');
            bE.classList.toggle('active-enc', tab === 'encuestas');
        }
    };

    /* ── MODAL PDF ── */
    window.abrirModalPDF = function() {
        document.getElementById('pdf-modal').style.display = 'block';
        document.getElementById('pdf-overlay').style.display = 'block';
    };
    window.cerrarModalPDF = function() {
        document.getElementById('pdf-modal').style.display = 'none';
        document.getElementById('pdf-overlay').style.display = 'none';
    };
    window.togglePdfRow = function(labelEl, checkId) {
        var chk = document.getElementById(checkId);
        chk.checked = !chk.checked;
        labelEl.classList.toggle('checked', chk.checked);
    };
    window.ejecutarPDF = function() {
        var inclBanner   = document.getElementById('pdfBanner').checked;
        var inclTickets  = document.getElementById('pdfTickets').checked;
        var inclEncKPIs  = document.getElementById('pdfEncKPIs').checked;
        var inclEncTabla = document.getElementById('pdfEncTabla').checked;

        var pLabels = @json($qLabels);
        var pProms  = @json($preguntaPromedios);

        var printWin = window.open('', '_blank', 'width=1000,height=750');
        var html = '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8">' +
            '<title>Estadísticas UPTEX</title>' +
            '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">' +
            '<style>' +
            'body{font-family:system-ui,sans-serif;padding:24px;color:#1e293b;background:#fff;}' +
            '.pdf-header{background:linear-gradient(135deg,#1e3a5f,#1d4ed8);padding:1.2rem 1.8rem;border-radius:14px;margin-bottom:1.5rem;-webkit-print-color-adjust:exact;print-color-adjust:exact;}' +
            '.pdf-header h1{color:#fff;font-size:1.3rem;font-weight:700;margin:0;}' +
            '.pdf-header p{color:rgba(255,255,255,.75);font-size:.8rem;margin:.15rem 0 0;}' +
            '.sec-title{font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#475569;padding:.5rem 0 .8rem;border-bottom:2px solid #e2e8f0;margin-bottom:1rem;}' +
            '.kpi-row{display:flex;gap:1rem;flex-wrap:wrap;margin-bottom:1.2rem;}' +
            '.kpi-box{flex:1;min-width:115px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:.75rem 1rem;}' +
            '.kpi-val{font-size:1.75rem;font-weight:800;line-height:1;}' +
            '.kpi-lbl{font-size:.7rem;color:#64748b;margin-top:.15rem;}' +
            'table{width:100%;border-collapse:collapse;font-size:.82rem;}' +
            'th{background:#f1f5f9;padding:.5rem .75rem;text-align:left;font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;color:#475569;border-bottom:2px solid #e2e8f0;}' +
            'td{padding:.5rem .75rem;border-bottom:1px solid #f1f5f9;color:#334155;}' +
            '.enc-hero-box{background:#f0fdf4;border:1.5px solid #bbf7d0;border-radius:14px;padding:1rem 1.5rem;display:inline-flex;align-items:center;gap:1rem;margin-bottom:1rem;-webkit-print-color-adjust:exact;print-color-adjust:exact;}' +
            '.enc-hero-pct{font-size:2.8rem;font-weight:900;color:#15803d;line-height:1;}' +
            '.pbar{height:5px;background:#e2e8f0;border-radius:3px;overflow:hidden;margin:.3rem 0;}' +
            '.pbar-fill{height:100%;border-radius:3px;-webkit-print-color-adjust:exact;print-color-adjust:exact;}' +
            '.prom-grid{display:flex;flex-wrap:wrap;gap:.75rem;margin-top:.75rem;}' +
            '.prom-box{flex:1;min-width:130px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:9px;padding:.65rem .9rem;}' +
            '@media print{body{padding:0;}@page{margin:1.2cm;}}' +
            '</style></head><body>';

        if (inclBanner) {
            html += '<div class="pdf-header">' +
                '<h1>Estadísticas del Sistema &mdash; UPTEX</h1>' +
                '<p>Universidad Politécnica de Texcoco &middot; Generado: ' + new Date().toLocaleString('es-MX') + '</p>' +
                '</div>';
        }

        if (inclTickets) {
            var t  = document.getElementById('dkpiTotal').textContent;
            var a  = document.getElementById('dkpiAbiertos').textContent;
            var ep = document.getElementById('dkpiEnProceso').textContent;
            var r  = document.getElementById('dkpiResueltos').textContent;
            html += '<div style="margin-bottom:1.5rem;">' +
                '<div class="sec-title"><span style="color:#1d4ed8;">|</span> Gestión de Tickets</div>' +
                '<div class="kpi-row">' +
                '<div class="kpi-box"><div class="kpi-val" style="color:#1d4ed8;">'  + t  + '</div><div class="kpi-lbl">Total Tickets</div></div>' +
                '<div class="kpi-box"><div class="kpi-val" style="color:#d97706;">'  + a  + '</div><div class="kpi-lbl">Abiertos/Pendientes</div></div>' +
                '<div class="kpi-box"><div class="kpi-val" style="color:#7c3aed;">'  + ep + '</div><div class="kpi-lbl">En Proceso</div></div>' +
                '<div class="kpi-box"><div class="kpi-val" style="color:#16a34a;">'  + r  + '</div><div class="kpi-lbl">Resueltos/Cerrados</div></div>' +
                '</div></div>';
        }

        if (inclEncKPIs) {
            var eT   = document.getElementById('encKpiTotal').textContent;
            var eR   = document.getElementById('encKpiRespondidas').textContent;
            var eS   = document.getElementById('encKpiSatisfechos').textContent;
            var eNS  = document.getElementById('encKpiNoSatisfechos').textContent;
            var ePct = document.getElementById('encHeroPct').textContent;
            html += '<div style="margin-bottom:1.5rem;">' +
                '<div class="sec-title"><span style="color:#16a34a;">|</span> Encuestas — Indicadores Generales</div>' +
                '<div class="enc-hero-box">' +
                '<div class="enc-hero-pct">' + ePct + '</div>' +
                '<div><div style="font-size:.88rem;font-weight:600;color:#166534;">Satisfacción Global</div><div style="font-size:.75rem;color:#4ade80;">' + (parseFloat(ePct) >= 80 ? 'Nivel óptimo' : parseFloat(ePct) >= 50 ? 'Por debajo del 80%' : 'Nivel crítico') + '</div></div>' +
                '</div>' +
                '<div class="kpi-row">' +
                '<div class="kpi-box"><div class="kpi-val">' + eT + '</div><div class="kpi-lbl">Total Enviadas</div></div>' +
                '<div class="kpi-box"><div class="kpi-val">' + eR + '</div><div class="kpi-lbl">Respondidas</div></div>' +
                '<div class="kpi-box"><div class="kpi-val" style="color:#16a34a;">' + eS + '</div><div class="kpi-lbl">Satisfechos</div></div>' +
                '<div class="kpi-box"><div class="kpi-val" style="color:#dc2626;">' + eNS + '</div><div class="kpi-lbl">No Satisfechos</div></div>' +
                '</div>';
            // Promedios P1-P5
            html += '<div style="margin-top:.5rem;"><div style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#475569;margin-bottom:.6rem;">Promedios por Pregunta</div>' +
                '<div class="prom-grid">';
            [1,2,3,4,5].forEach(function(i) {
                var v   = pProms[i] || 0;
                var pct = v > 0 ? Math.round(v/4*100) : 0;
                var c   = v >= 3 ? '#16a34a' : (v >= 2.5 ? '#f59e0b' : '#dc2626');
                var bg  = v >= 3 ? '#dcfce7' : (v >= 2.5 ? '#fef3c7' : '#fee2e2');
                html += '<div class="prom-box">' +
                    '<span style="display:inline-block;padding:.15rem .5rem;border-radius:6px;font-size:.68rem;font-weight:800;background:'+bg+';color:'+c+';">P'+i+'</span>' +
                    '<div style="font-size:.65rem;color:#64748b;margin:.25rem 0;">' + (pLabels[i] || '') + '</div>' +
                    '<div class="pbar"><div class="pbar-fill" style="width:'+pct+'%;background:'+c+';"></div></div>' +
                    '<div style="font-size:.95rem;font-weight:800;color:'+c+';">' + (v > 0 ? v.toFixed(1) : '—') + ' <span style="font-size:.65rem;color:#94a3b8;">/4.0</span></div>' +
                    '</div>';
            });
            html += '</div></div></div>';
        }

        if (inclEncTabla) {
            var tbody = document.getElementById('encTableBody');
            html += '<div style="margin-bottom:1.5rem;">' +
                '<div class="sec-title"><span style="color:#16a34a;">|</span> Indicadores por Área</div>' +
                '<table><thead><tr>' +
                '<th>Área</th><th>Satisfechos</th><th>No Satisf.</th><th>Total</th><th>% Satisf.</th>' +
                '</tr></thead><tbody>' + (tbody ? tbody.innerHTML : '') + '</tbody></table></div>';
        }

        html += '<script>window.onload=function(){window.print();window.close();};<\/script>';
        html += '</body></html>';

        printWin.document.write(html);
        printWin.document.close();
        cerrarModalPDF();
    };

    /* ── GRÁFICAS ENCUESTAS ── */
    var instEncGlobal = null, instEncPorArea = null, instEncTasa = null, instEncQAvg = null;

    var c1 = document.getElementById('encChartGlobal');
    if (c1) {
        instEncGlobal = new Chart(c1, {
            type: 'doughnut',
            data: {
                labels: ['Satisfechos','No satisfechos','Sin responder'],
                datasets: [{ data: [stats.satisfechos, stats.no_satisfechos, stats.sin_responder],
                    backgroundColor: ['#16a34a','#dc2626','#94a3b8'], borderWidth: 2, borderColor: '#fff' }]
            },
            options: { responsive:true, maintainAspectRatio:false, cutout:'65%',
                plugins: { legend:{display:false} } }
        });
    }

    var c2 = document.getElementById('encChartPorArea');
    if (c2) {
        instEncPorArea = new Chart(c2, {
            type: 'bar',
            data: {
                labels: stats.por_area ? stats.por_area.map(function(r){return r.area;}) : [],
                datasets: [
                    { label:'Satisfechos',    data: stats.por_area ? stats.por_area.map(function(r){return r.satisfechos;})    : [], backgroundColor:'#16a34a', borderRadius:5 },
                    { label:'No satisfechos', data: stats.por_area ? stats.por_area.map(function(r){return r.no_satisfechos;}) : [], backgroundColor:'#dc2626', borderRadius:5 }
                ]
            },
            options: { responsive:true, maintainAspectRatio:false,
                plugins:{legend:{position:'bottom',labels:{font:{size:11},boxWidth:12,padding:12}}},
                scales:{
                    y:{title:{display:true,text:'Nº de Respuestas',font:{size:11,weight:'bold'},color:'#64748b'},beginAtZero:true,ticks:{stepSize:1,font:{size:10}},grid:{color:'#f1f5f9'}},
                    x:{title:{display:true,text:'Área',font:{size:11,weight:'bold'},color:'#64748b'},ticks:{font:{size:10},maxRotation:40},grid:{display:false}}
                }
            }
        });
    }

    var c3 = document.getElementById('encChartTasa');
    if (c3) {
        var encTasaInit = stats.total > 0 ? Math.round(stats.respondidas/stats.total*100) : 0;
        instEncTasa = new Chart(c3, {
            type: 'doughnut',
            data: { datasets:[{data:[encTasaInit,100-encTasaInit],backgroundColor:['#1d4ed8','#e2e8f0'],borderWidth:0}] },
            options: { responsive:true, maintainAspectRatio:false, cutout:'75%',
                plugins:{legend:{display:false},tooltip:{enabled:false}} }
        });
    }

    var ctxQAvg = document.getElementById('encChartQAvg');
    if (ctxQAvg) {
        var qVals   = [preguntaPromedios[1]||0, preguntaPromedios[2]||0, preguntaPromedios[3]||0, preguntaPromedios[4]||0, preguntaPromedios[5]||0];
        var qColors = qVals.map(function(v){ return v>=3?'rgba(22,163,74,.85)':(v>=2.5?'rgba(245,158,11,.85)':'rgba(220,38,38,.85)'); });
        instEncQAvg = new Chart(ctxQAvg, {
            type: 'bar',
            data: {
                labels: ['P1','P2','P3','P4','P5'],
                datasets:[{label:'Promedio',data:qVals,backgroundColor:qColors,borderRadius:5,borderWidth:0}]
            },
            options: { indexAxis:'y', responsive:true, maintainAspectRatio:false,
                plugins:{legend:{display:false},tooltip:{callbacks:{label:function(c){return ' '+(c.raw||0).toFixed(1)+' / 4.0';}}}},
                scales:{
                    x:{title:{display:true,text:'Promedio (escala 1–4)',font:{size:11,weight:'bold'},color:'#64748b'},min:0,max:4,ticks:{stepSize:1,font:{size:10}},grid:{color:'#f1f5f9'}},
                    y:{ticks:{font:{size:11,weight:'600'},color:'#334155'},grid:{display:false}}
                }
            }
        });
    }

    /* ── GRÁFICAS TICKETS ── */
    var ctxArea = document.getElementById('chartPorArea');
    var instArea = null;
    if (ctxArea) {
        instArea = new Chart(ctxArea, {
            type: 'bar',
            data: {
                labels: porAreaInit.map(function(r){return r.nombre;}),
                datasets:[{label:'Nº de Tickets',data:porAreaInit.map(function(r){return r.total;}),backgroundColor:'#3b82f6',borderRadius:6,borderWidth:0}]
            },
            options: { responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}},
                scales:{
                    x:{title:{display:true,text:'Área',font:{size:11,weight:'bold'},color:'#64748b'},ticks:{font:{size:10},maxRotation:35},grid:{display:false}},
                    y:{title:{display:true,text:'Nº de Tickets',font:{size:11,weight:'bold'},color:'#64748b'},beginAtZero:true,ticks:{stepSize:1,font:{size:10}},grid:{color:'#f1f5f9'}}
                }
            }
        });
    }

    var ctxPrio = document.getElementById('chartPorPrioridad');
    var instPrio = null;
    var prioColors = {'Alta':'#ef4444','Media':'#f59e0b','Baja':'#22c55e'};
    if (ctxPrio) {
        instPrio = new Chart(ctxPrio, {
            type: 'doughnut',
            data: {
                labels: porPrioInit.map(function(r){return r.nombre;}),
                datasets:[{data:porPrioInit.map(function(r){return r.total;}),backgroundColor:porPrioInit.map(function(r){return prioColors[r.nombre]||'#94a3b8';}),borderWidth:2,borderColor:'#fff'}]
            },
            options: { responsive:true, maintainAspectRatio:false, cutout:'58%',
                plugins:{legend:{position:'bottom',labels:{font:{size:11},boxWidth:12,padding:10}},
                    tooltip:{callbacks:{label:function(c){return ' '+c.label+': '+c.raw+' tickets';}}}}
            }
        });
    }

    var ctxLinea = document.getElementById('chartLinea');
    var instLinea = null;
    if (ctxLinea) {
        instLinea = new Chart(ctxLinea, {
            type: 'line',
            data: {
                labels: stats.dias_labels || [],
                datasets:[{label:'Resueltos',data:stats.dias_data || [],borderColor:'#8b5cf6',backgroundColor:'rgba(139,92,246,0.08)',borderWidth:2.5,fill:true,tension:0.4,pointRadius:3,pointBackgroundColor:'#8b5cf6'}]
            },
            options: { responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}},
                scales:{
                    x:{title:{display:true,text:'Fecha (día/mes)',font:{size:11,weight:'bold'},color:'#64748b'},ticks:{font:{size:10}},grid:{display:false}},
                    y:{title:{display:true,text:'Tickets Resueltos/día',font:{size:11,weight:'bold'},color:'#64748b'},beginAtZero:true,ticks:{stepSize:1,font:{size:10}},grid:{color:'#f1f5f9'}}
                }
            }
        });
    }

    /* ── UPDATE ENCUESTAS ── */
    function updateEncSection(d) {
        var encSat   = d.enc_satisfechos   || 0;
        var encNoSat = d.enc_no_satisfechos || 0;
        var encSin   = d.enc_sin_responder  || 0;
        var encTotal = encSat + encNoSat + encSin;
        var encResp  = encSat + encNoSat;
        var pctEnc   = d.enc_satisfaccion_pct || 0;
        var tasaPct  = encTotal > 0 ? Math.round(encResp/encTotal*100) : 0;

        var heroEl = document.getElementById('encHeroCard');
        if(heroEl) heroEl.className = 'enc-hero' + (pctEnc>=80 ? '' : pctEnc>=50 ? ' amarillo' : ' rojo');
        var pctEl  = document.getElementById('encHeroPct');      if(pctEl)  pctEl.textContent  = pctEnc + '%';
        var subEl  = document.getElementById('encHeroSub');      if(subEl)  subEl.textContent  = pctEnc>=80 ? 'Nivel óptimo >= 80%' : (pctEnc>=50 ? 'Por debajo del objetivo (80%)' : 'Nivel crítico - requiere atención');
        var iconEl = document.getElementById('encHeroIconI');     if(iconEl) iconEl.className   = 'bi ' + (pctEnc>=80 ? 'bi-emoji-laughing-fill' : (pctEnc>=50 ? 'bi-emoji-neutral-fill' : 'bi-emoji-frown-fill'));

        var e1=document.getElementById('encKpiTotal');       if(e1) e1.textContent=encTotal;
        var e2=document.getElementById('encKpiRespondidas'); if(e2) e2.textContent=encResp;
        var e3=document.getElementById('encKpiSatisfechos'); if(e3) e3.textContent=encSat;
        var e4=document.getElementById('encKpiNoSatisfechos'); if(e4) e4.textContent=encNoSat;

        var hBadge=document.getElementById('encHeaderBadge'); if(hBadge) hBadge.textContent=encResp+'/'+encTotal+' respondidas';
        var tb2=document.getElementById('tabBadgeEncuestas'); if(tb2) tb2.textContent=encResp+'/'+encTotal;

        if(instEncGlobal){ instEncGlobal.data.datasets[0].data=[encSat,encNoSat,encSin]; instEncGlobal.update(); }
        if(instEncPorArea && d.enc_por_area){
            instEncPorArea.data.labels=d.enc_por_area.map(function(r){return r.area;});
            instEncPorArea.data.datasets[0].data=d.enc_por_area.map(function(r){return r.satisfechos;});
            instEncPorArea.data.datasets[1].data=d.enc_por_area.map(function(r){return r.no_satisfechos;});
            instEncPorArea.update();
        }
        if(instEncTasa){ instEncTasa.data.datasets[0].data=[tasaPct,100-tasaPct]; instEncTasa.update(); }
        var tasaTxtEl=document.getElementById('encTasaText'); if(tasaTxtEl) tasaTxtEl.textContent=tasaPct+'%';

        var tbody=document.getElementById('encTableBody');
        if(tbody){
            if(!d.enc_por_area || d.enc_por_area.length===0){
                tbody.innerHTML='<tr><td colspan="5" class="text-center text-muted py-4" style="font-size:.84rem;"><i class="bi bi-inbox d-block mb-1" style="font-size:1.5rem;"></i>Sin encuestas respondidas aún</td></tr>';
            } else {
                tbody.innerHTML=d.enc_por_area.map(function(fila){
                    var tot=(fila.satisfechos||0)+(fila.no_satisfechos||0);
                    var p=tot>0?Math.round(fila.satisfechos/tot*100):0;
                    var bc=p>=80?'#16a34a':(p>=50?'#f59e0b':'#dc2626');
                    var bg=p>=80?'#dcfce7':(p>=50?'#fef3c7':'#fee2e2');
                    var tc=p>=80?'#15803d':(p>=50?'#b45309':'#b91c1c');
                    return '<tr>'+
                        '<td style="max-width:130px;white-space:normal;font-size:.79rem;">'+fila.area+'</td>'+
                        '<td class="text-center fw-semibold" style="color:#16a34a;">'+fila.satisfechos+'</td>'+
                        '<td class="text-center fw-semibold" style="color:#dc2626;">'+fila.no_satisfechos+'</td>'+
                        '<td class="text-center fw-semibold">'+tot+'</td>'+
                        '<td style="min-width:88px;"><div class="d-flex align-items-center gap-2">'+
                        '<div class="enc-pct-bar flex-grow-1"><div style="height:100%;border-radius:4px;width:'+p+'%;background:'+bc+';"></div></div>'+
                        '<span class="enc-pct-chip" style="background:'+bg+';color:'+tc+';">'+p+'%</span>'+
                        '</div></td></tr>';
                }).join('');
            }
        }
    }

    /* ── FILTROS POWER BI ── */
    var spinner = document.getElementById('pbiSpinner');
    var badge   = document.getElementById('pbiBadge');
    var filterTimeout = null;

    function updateCharts() {
        var areaId    = document.getElementById('filtroArea').value;
        var tecnicoId = document.getElementById('filtroTecnico').value;

        if(spinner) spinner.style.display='inline-block';
        if(badge)   badge.classList.add('d-none');

        clearTimeout(filterTimeout);
        filterTimeout = setTimeout(function() {
            var url = filterUrl+'?area_id='+encodeURIComponent(areaId)+'&tecnico_id='+encodeURIComponent(tecnicoId);
            fetch(url, { headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'} })
            .then(function(res){ if(!res.ok) throw new Error('HTTP '+res.status); return res.json(); })
            .then(function(d){
                document.getElementById('dkpiTotal').textContent     = d.total;
                document.getElementById('dkpiAbiertos').textContent  = d.abiertos;
                document.getElementById('dkpiEnProceso').textContent = d.en_proceso;
                document.getElementById('dkpiResueltos').textContent = d.resueltos;

                var tb = document.getElementById('tabBadgeTickets');
                if(tb) tb.textContent = d.total;

                if(instArea){ instArea.data.labels=d.por_area.map(function(r){return r.nombre;}); instArea.data.datasets[0].data=d.por_area.map(function(r){return r.total;}); instArea.update(); }
                if(instPrio){ instPrio.data.labels=d.por_prioridad.map(function(r){return r.nombre;}); instPrio.data.datasets[0].data=d.por_prioridad.map(function(r){return r.total;}); instPrio.data.datasets[0].backgroundColor=d.por_prioridad.map(function(r){return prioColors[r.nombre]||'#94a3b8';}); instPrio.update(); }
                if(instLinea){ instLinea.data.labels=d.dias_labels; instLinea.data.datasets[0].data=d.dias_data; instLinea.update(); }

                updateEncSection(d);

                if(badge && (areaId||tecnicoId)){ badge.classList.remove('d-none'); badge.textContent='Filtro activo'; }
            })
            .catch(function(e){ console.warn('Error al filtrar:', e); })
            .finally(function(){ if(spinner) spinner.style.display='none'; });
        }, 350);
    }

    var selArea    = document.getElementById('filtroArea');
    var selTecnico = document.getElementById('filtroTecnico');
    var btnReset   = document.getElementById('pbiReset');
    if(selArea)    selArea.addEventListener('change', updateCharts);
    if(selTecnico) selTecnico.addEventListener('change', updateCharts);
    if(btnReset) {
        btnReset.addEventListener('click', function(){
            if(selArea)    selArea.value = '';
            if(selTecnico) selTecnico.value = '';
            if(badge)      badge.classList.add('d-none');
            updateCharts();
        });
    }
});
</script>
@endpush
@endif

@endsection
