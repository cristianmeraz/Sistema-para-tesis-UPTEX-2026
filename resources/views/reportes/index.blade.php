@extends('layouts.app')

@section('title', 'Estadísticas')
@section('no_header_title', true)

@section('content')
<style>
    /* ══════ BANNER ══════ */
    .rep-banner {
        background: linear-gradient(135deg, #1e3a5f 0%, #1d4ed8 100%);
        border-radius: 18px;
        padding: 1.6rem 2rem;
        margin-bottom: 1.8rem;
        display: flex;
        align-items: center;
        gap: 1.2rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 8px 30px rgba(29,78,216,.25);
    }
    .rep-banner::before {
        content: '';
        position: absolute;
        top: -40px; right: -40px;
        width: 180px; height: 180px;
        border-radius: 50%;
        background: rgba(255,255,255,.06);
    }
    .rep-banner::after {
        content: '';
        position: absolute;
        bottom: -50px; right: 120px;
        width: 130px; height: 130px;
        border-radius: 50%;
        background: rgba(255,255,255,.04);
    }
    .rep-banner-logo {
        width: 52px; height: 52px;
        background: rgba(255,255,255,.92);
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        padding: 6px;
    }
    .rep-banner-logo img { width: 100%; height: 100%; object-fit: contain; }
    .rep-banner-title {
        color: #fff;
        font-size: 1.45rem;
        font-weight: 700;
        line-height: 1.2;
        margin: 0;
    }
    .rep-banner-sub {
        color: rgba(255,255,255,.72);
        font-size: .85rem;
        margin: .1rem 0 0;
    }

    /* ══════ REPORT CARDS — HORIZONTAL ══════ */
    .rep-hcard {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #e8edf5;
        box-shadow: 0 2px 12px rgba(0,0,0,.05);
        padding: 1.2rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 1.1rem;
        height: 100%;
        transition: transform .2s, box-shadow .2s;
        text-decoration: none;
    }
    .rep-hcard:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 28px rgba(0,0,0,.10);
    }
    .rep-hcard-icon {
        width: 54px; height: 54px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }
    .rep-hcard-badge {
        display: inline-block;
        font-size: .68rem;
        font-weight: 600;
        padding: .14rem .55rem;
        border-radius: 20px;
        margin-bottom: .3rem;
        letter-spacing: .03em;
    }
    .rep-hcard-title {
        font-size: 1rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: .15rem;
        line-height: 1.3;
    }
    .rep-hcard-desc {
        font-size: .8rem;
        color: #64748b;
        line-height: 1.45;
        margin: 0;
    }
    .rep-hcard-arrow {
        margin-left: auto;
        flex-shrink: 0;
        width: 34px; height: 34px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem;
    }
    .rep-icon-blue   { background: #dbeafe; color: #1d4ed8; }
    .rep-badge-blue  { background: #dbeafe; color: #1d4ed8; }
    .rep-arrow-blue  { background: #dbeafe; color: #1d4ed8; }
    .rep-icon-amber  { background: #fef9c3; color: #b45309; }
    .rep-badge-amber { background: #fef3c7; color: #b45309; }
    .rep-arrow-amber { background: #fef3c7; color: #b45309; }

    /* ══════ SECCIÓN ENCUESTAS ══════ */
    .enc-section-header {
        display: flex; align-items: center; flex-wrap: wrap; gap: .7rem;
        margin: 2rem 0 1.4rem;
        padding-bottom: .8rem;
        border-bottom: 2px solid #e8edf5;
    }
    .enc-section-bar {
        width: 4px; height: 1.4rem; border-radius: 2px;
        background: linear-gradient(180deg, #16a34a, #0d9488);
        flex-shrink: 0;
    }
    .enc-section-title {
        font-size: .82rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: .06em;
        color: #475569;
    }

    /* KPI HERO — porcentaje grande */
    .enc-hero {
        background: linear-gradient(135deg, #f0fdf4, #dcfce7);
        border: 1.5px solid #bbf7d0;
        border-radius: 18px;
        padding: 1.8rem 2rem;
        display: flex; align-items: center; gap: 1.5rem;
        box-shadow: 0 4px 18px rgba(22,163,74,.10);
        position: relative;
        overflow: hidden;
    }
    .enc-hero.amarillo {
        background: linear-gradient(135deg, #fffbeb, #fef3c7);
        border-color: #fde68a;
        box-shadow: 0 4px 18px rgba(245,158,11,.12);
    }
    .enc-hero.rojo {
        background: linear-gradient(135deg, #fff1f2, #fee2e2);
        border-color: #fecaca;
        box-shadow: 0 4px 18px rgba(220,38,38,.10);
    }
    .enc-hero::after {
        content: '';
        position: absolute; right: -20px; top: -20px;
        width: 110px; height: 110px; border-radius: 50%;
        background: rgba(255,255,255,.35);
    }
    .enc-hero-pct {
        font-size: 4rem; font-weight: 900; line-height: 1;
        color: #15803d;
    }
    .enc-hero.amarillo .enc-hero-pct { color: #b45309; }
    .enc-hero.rojo .enc-hero-pct { color: #b91c1c; }
    .enc-hero-label {
        font-size: .9rem; font-weight: 600; color: #166534; margin-bottom: .2rem;
    }
    .enc-hero.amarillo .enc-hero-label { color: #92400e; }
    .enc-hero.rojo .enc-hero-label { color: #991b1b; }
    .enc-hero-sub { font-size: .78rem; color: #4ade80; }
    .enc-hero.amarillo .enc-hero-sub { color: #f59e0b; }
    .enc-hero.rojo .enc-hero-sub { color: #f87171; }
    .enc-hero-icon {
        width: 64px; height: 64px; border-radius: 18px;
        background: rgba(255,255,255,.6);
        display: flex; align-items: center; justify-content: center;
        font-size: 2rem; flex-shrink: 0;
        color: #16a34a;
        box-shadow: 0 2px 8px rgba(0,0,0,.06);
    }
    .enc-hero.amarillo .enc-hero-icon { color: #d97706; }
    .enc-hero.rojo .enc-hero-icon { color: #dc2626; }

    /* KPI CARDS pequeñas */
    .enc-kpi {
        background: #fff; border-radius: 14px;
        border: 1px solid #e8edf5;
        box-shadow: 0 2px 8px rgba(0,0,0,.04);
        padding: .9rem 1.1rem;
        display: flex; align-items: center; gap: .75rem;
    }
    .enc-kpi-icon {
        width: 40px; height: 40px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.15rem; flex-shrink: 0;
    }
    .enc-kpi-val { font-size: 1.5rem; font-weight: 700; line-height: 1; color: #1e293b; }
    .enc-kpi-lbl { font-size: .72rem; color: #64748b; margin-top: .1rem; }

    /* TABLA DE INDICADORES */
    .enc-table-wrap {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #e8edf5;
        box-shadow: 0 2px 12px rgba(0,0,0,.04);
        overflow: hidden;
    }
    .enc-table-header {
        background: linear-gradient(135deg, #1e3a5f, #1d4ed8);
        padding: .9rem 1.3rem;
        display: flex; align-items: center; gap: .5rem;
    }
    .enc-table-header span {
        color: #fff; font-size: .82rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: .05em;
    }
    .enc-table-wrap table { margin: 0; font-size: .85rem; }
    .enc-table-wrap thead th {
        background: #f8fafc;
        color: #475569;
        font-size: .75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .04em;
        padding: .65rem 1rem;
        border-bottom: 2px solid #e2e8f0;
        white-space: nowrap;
    }
    .enc-table-wrap tbody td {
        padding: .7rem 1rem;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    .enc-table-wrap tbody tr:last-child td { border-bottom: none; }
    .enc-table-wrap tbody tr:hover td { background: #f8fafc; }
    .enc-pct-bar {
        height: 6px; border-radius: 4px;
        background: #e2e8f0; overflow: hidden; min-width: 60px;
    }
    .enc-pct-bar-fill {
        height: 100%; border-radius: 4px;
        transition: width .6s ease;
    }
    .enc-pct-chip {
        font-size: .72rem; font-weight: 700;
        padding: .2rem .55rem; border-radius: 20px;
        display: inline-block;
    }

    /* GRÁFICA CARD */
    .enc-chart-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #e8edf5;
        box-shadow: 0 2px 12px rgba(0,0,0,.04);
        padding: 1.3rem 1.4rem;
        height: 100%;
    }
    .enc-chart-title {
        font-size: .75rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: .05em;
        color: #64748b; margin-bottom: .9rem;
        display: flex; align-items: center; gap: .4rem;
    }

    /* ══════ PANEL POWER BI ══════ */
    .pbi-panel {
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        border: 1.5px solid #e2e8f0;
        border-radius: 16px;
        padding: 1rem 1.4rem;
        margin-bottom: 1.4rem;
        display: flex; align-items: center; flex-wrap: wrap; gap: .8rem;
    }
    .pbi-panel-label {
        font-size: .73rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: .06em;
        color: #475569; white-space: nowrap;
    }
    .pbi-select {
        border: 1.5px solid #cbd5e1; border-radius: 9px;
        padding: .45rem .85rem; font-size: .83rem; color: #334155;
        background: #fff; min-width: 180px; outline: none;
        cursor: pointer; transition: border-color .15s;
    }
    .pbi-select:focus { border-color: #1d4ed8; box-shadow: 0 0 0 3px rgba(29,78,216,.1); }
    .pbi-badge-filter {
        font-size: .72rem; font-weight: 600;
        padding: .22rem .75rem; border-radius: 20px;
        background: #dbeafe; color: #1d4ed8;
        animation: pbi-pulse .4s ease;
    }
    @keyframes pbi-pulse { from { opacity:.4; transform:scale(.95); } to { opacity:1; transform:scale(1); } }
    .pbi-spinner { display: none; width: 16px; height: 16px; border: 2px solid #cbd5e1;
        border-top-color: #1d4ed8; border-radius: 50%; animation: spin .7s linear infinite; }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* ══════ KPI DINÁMICOS ══════ */
    .dkpi-card {
        background: #fff; border-radius: 14px;
        border: 1px solid #e8edf5;
        box-shadow: 0 2px 10px rgba(0,0,0,.04);
        padding: 1rem 1.2rem;
        display: flex; align-items: center; gap: .8rem;
        height: 100%;
    }
    .dkpi-icon {
        width: 46px; height: 46px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.25rem; flex-shrink: 0;
    }
    .dkpi-val { font-size: 1.9rem; font-weight: 800; line-height: 1; color: #1e293b;
        transition: color .3s; }
    .dkpi-lbl { font-size: .72rem; color: #64748b; margin-top: .1rem; }

    /* ══════ CHART DINÁMICO ══════ */
    .dyn-chart-card {
        background: #fff; border-radius: 16px;
        border: 1px solid #e8edf5;
        box-shadow: 0 2px 10px rgba(0,0,0,.04);
        padding: 1.2rem 1.4rem;
        height: 100%;
    }
    .dyn-chart-title {
        font-size: .75rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: .05em;
        color: #64748b; margin-bottom: .8rem;
        display: flex; align-items: center; gap: .4rem;
    }

    /* ══════ PROMEDIOS PREGUNTAS ══════ */
    .q-avg-card {
        background: #fff; border-radius: 14px;
        border: 1px solid #e8edf5;
        box-shadow: 0 2px 8px rgba(0,0,0,.04);
        padding: .85rem 1.1rem;
        text-align: center; height: 100%;
    }
    .q-avg-num { font-size: .7rem; font-weight: 800; color: #1e293b;
        background: #dcfce7; color: #15803d;
        width: 24px; height: 24px; border-radius: 7px;
        display: inline-flex; align-items: center; justify-content: center;
        margin-bottom: .4rem; }
    .q-avg-bar { width: 100%; height: 6px; background: #e2e8f0;
        border-radius: 3px; overflow: hidden; margin: .4rem 0 .2rem; }
    .q-avg-bar-fill { height: 100%; border-radius: 3px; transition: width .6s ease; }
    .q-avg-val { font-size: 1.1rem; font-weight: 800; }

    @media (max-width: 768px) {
        .rep-banner { padding: 1.2rem; }
        .rep-banner-title { font-size: 1.2rem; }
        .enc-hero-pct { font-size: 3rem; }
        .pbi-panel { flex-direction: column; align-items: flex-start; }
        .pbi-select { min-width: 100%; }
    }
    @media print {
        .pbi-panel, nav, .navbar, aside, header, .btn-outline-secondary { display: none !important; }
        .rep-banner { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .dkpi-card, .dyn-chart-card, .enc-hero, .enc-kpi, .enc-chart-card,
        .enc-table-wrap, .q-avg-card, .rep-hcard { break-inside: avoid; page-break-inside: avoid; }
        body { font-size: 12px; }
        .enc-hero { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    }
</style>

<div class="container-fluid">

    {{-- ══════ BANNER con logo UPTEX ══════ --}}
    <div class="rep-banner">
        <div class="rep-banner-logo">
            <img src="{{ asset('images/logo-uptex.png') }}" alt="UPTEX">
        </div>
        <div>
            <h1 class="rep-banner-title">Estadísticas</h1>
            <p class="rep-banner-sub">Genera, analiza y exporta datos del sistema de soporte — UPTEX</p>
        </div>
        <div class="ms-auto" style="position:relative;z-index:1;">
            <button onclick="window.print()" class="btn btn-sm"
                style="background:rgba(255,255,255,.15);border:1.5px solid rgba(255,255,255,.4);color:#fff;border-radius:10px;font-size:.8rem;padding:.45rem .9rem;">
                <i class="bi bi-printer-fill me-1"></i>Imprimir / PDF
            </button>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- PANEL DE CONTROL — Power BI Style (filtros dinámicos)    --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
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
        <button class="btn btn-sm btn-outline-secondary" id="pbiReset"
            style="border-radius:9px; font-size:.8rem; margin-left:auto;">
            <i class="bi bi-x-circle me-1"></i>Limpiar filtros
        </button>
    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- KPI DINÁMICOS — actualizan con el filtro                  --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div class="row g-3 mb-3">
        <div class="col-6 col-xl-3">
            <div class="dkpi-card">
                <div class="dkpi-icon" style="background:#dbeafe;color:#1d4ed8;">
                    <i class="bi bi-ticket-detailed-fill"></i>
                </div>
                <div>
                    <div class="dkpi-val" id="dkpiTotal">{{ $ticketStats['total'] }}</div>
                    <div class="dkpi-lbl">Total de Tickets</div>
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

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- GRÁFICAS DINÁMICAS — Tickets por Área y por Prioridad     --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div class="row g-3 mb-3">
        <div class="col-12 col-lg-5">
            <div class="dyn-chart-card">
                <div class="dyn-chart-title">
                    <i class="bi bi-bar-chart-fill text-primary"></i>
                    Tickets por Área
                    <span style="margin-left:auto;font-size:.68rem;color:#94a3b8;font-weight:400">Eje Y = Nº de tickets</span>
                </div>
                <div style="height:220px;position:relative;">
                    <canvas id="chartPorArea"></canvas>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
            <div class="dyn-chart-card">
                <div class="dyn-chart-title">
                    <i class="bi bi-pie-chart-fill text-warning"></i>
                    Por Prioridad
                    <span style="margin-left:auto;font-size:.68rem;color:#94a3b8;font-weight:400">Distribución</span>
                </div>
                <div style="height:220px;position:relative;">
                    <canvas id="chartPorPrioridad"></canvas>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-4">
            <div class="dyn-chart-card">
                <div class="dyn-chart-title">
                    <i class="bi bi-graph-up-arrow text-info"></i>
                    Resolución — Últimos 14 días
                    <span style="margin-left:auto;font-size:.68rem;color:#94a3b8;font-weight:400">Eje Y = Resueltos/día</span>
                </div>
                <div style="height:220px;position:relative;">
                    <canvas id="chartLinea"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════ ACCESOS A REPORTES — horizontal compacto ══════ --}}
    <div class="row g-3 mb-2">
        <div class="col-12 col-md-6">
            <a href="{{ route('reportes.por-fecha') }}" class="rep-hcard">
                <div class="rep-hcard-icon rep-icon-blue">
                    <i class="bi bi-calendar-range"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="rep-hcard-badge rep-badge-blue">Filtro temporal</div>
                    <div class="rep-hcard-title">Tickets por Fecha</div>
                    <p class="rep-hcard-desc">Reporte detallado dentro de un rango personalizado.</p>
                </div>
                <div class="rep-hcard-arrow rep-arrow-blue">
                    <i class="bi bi-arrow-right"></i>
                </div>
            </a>
        </div>
        <div class="col-12 col-md-6">
            <a href="{{ route('reportes.rendimiento') }}" class="rep-hcard">
                <div class="rep-hcard-icon rep-icon-amber">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="rep-hcard-badge rep-badge-amber">Desempeño</div>
                    <div class="rep-hcard-title">Rendimiento de Técnicos</div>
                    <p class="rep-hcard-desc">Estadísticas de resolución individual de cada técnico.</p>
                </div>
                <div class="rep-hcard-arrow rep-arrow-amber">
                    <i class="bi bi-arrow-right"></i>
                </div>
            </a>
        </div>
    </div>

    {{-- ══════ ENCUESTAS DE SATISFACCIÓN ══════ --}}
    @if(isset($satisfaccionStats))
    @php
        $satisfechos   = $satisfaccionStats['satisfechos'];
        $respondidas   = $satisfaccionStats['respondidas'];
        $total         = $satisfaccionStats['total'];
        $noSatisfechos = $satisfaccionStats['no_satisfechos'];
        $sinResponder  = $satisfaccionStats['sin_responder'];

        // % de satisfacción sobre las respondidas
        $pctSatisfaccion = $respondidas > 0 ? round($satisfechos / $respondidas * 100, 1) : 0;
        // Tasa de respuesta
        $tasaPct = $total > 0 ? round($respondidas / $total * 100, 1) : 0;

        // Semáforo
        $heroClass = $pctSatisfaccion >= 80 ? '' : ($pctSatisfaccion >= 50 ? 'amarillo' : 'rojo');
        $heroIcon  = $pctSatisfaccion >= 80 ? 'bi-emoji-laughing-fill' : ($pctSatisfaccion >= 50 ? 'bi-emoji-neutral-fill' : 'bi-emoji-frown-fill');
        $heroMsg   = $pctSatisfaccion >= 80 ? 'Nivel óptimo ≥ 80%' : ($pctSatisfaccion >= 50 ? 'Por debajo del objetivo (80%)' : 'Nivel crítico — requiere atención');
    @endphp

    {{-- Encabezado sección --}}
    <div class="enc-section-header">
        <span class="enc-section-bar"></span>
        <i class="bi bi-emoji-smile-fill text-success" style="font-size:1.1rem;"></i>
        <span class="enc-section-title">Encuestas de Satisfacción</span>
        <span class="badge bg-success" id="encHeaderBadge" style="font-size:.7rem; font-weight:600;">
            {{ $respondidas }}/{{ $total }} respondidas
        </span>
        <span class="text-muted d-none d-md-inline" style="font-size:.75rem;">
            <i class="bi bi-clock-history me-1"></i>Indicadores Mantenimiento TI — Periodo {{ date('Y') }}
        </span>
        <a href="{{ route('reportes.encuestas') }}" class="ms-auto btn btn-sm btn-outline-success"
           style="border-radius:9px; font-size:.78rem; white-space:nowrap;">
            <i class="bi bi-table me-1"></i> Ver respuestas detalladas
        </a>
    </div>

    {{-- Fila 1: Hero KPI + KPIs secundarios --}}
    <div class="row g-3 mb-3">

        {{-- HERO: % de satisfacción --}}
        <div class="col-12 col-md-4">
            <div class="enc-hero {{ $heroClass }} h-100" id="encHeroCard">
                <div class="enc-hero-icon" id="encHeroIconWrap">
                    <i class="bi {{ $heroIcon }}" id="encHeroIconI"></i>
                </div>
                <div>
                    <div class="enc-hero-pct" id="encHeroPct">{{ $pctSatisfaccion }}%</div>
                    <div class="enc-hero-label">de satisfacción global</div>
                    <div class="enc-hero-sub" id="encHeroSub">{{ $heroMsg }}</div>
                </div>
            </div>
        </div>

        {{-- KPIs secundarios --}}
        <div class="col-12 col-md-8">
            <div class="row g-3 h-100">
                <div class="col-6 col-xl-3">
                    <div class="enc-kpi h-100">
                        <div class="enc-kpi-icon" style="background:#dbeafe; color:#1d4ed8;">
                            <i class="bi bi-clipboard-data-fill"></i>
                        </div>
                        <div>
                            <div class="enc-kpi-val" id="encKpiTotal">{{ $total }}</div>
                            <div class="enc-kpi-lbl">Total Enviadas</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-xl-3">
                    <div class="enc-kpi h-100">
                        <div class="enc-kpi-icon" style="background:#ede9fe; color:#7c3aed;">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                        <div>
                            <div class="enc-kpi-val" id="encKpiRespondidas">{{ $respondidas }}</div>
                            <div class="enc-kpi-lbl">Respondidas</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-xl-3">
                    <div class="enc-kpi h-100">
                        <div class="enc-kpi-icon" style="background:#dcfce7; color:#16a34a;">
                            <i class="bi bi-emoji-smile-fill"></i>
                        </div>
                        <div>
                            <div class="enc-kpi-val" id="encKpiSatisfechos" style="color:#16a34a;">{{ $satisfechos }}</div>
                            <div class="enc-kpi-lbl">Satisfechos</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-xl-3">
                    <div class="enc-kpi h-100">
                        <div class="enc-kpi-icon" style="background:#fee2e2; color:#dc2626;">
                            <i class="bi bi-emoji-frown-fill"></i>
                        </div>
                        <div>
                            <div class="enc-kpi-val" id="encKpiNoSatisfechos" style="color:#dc2626;">{{ $noSatisfechos }}</div>
                            <div class="enc-kpi-lbl">No Satisfechos</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ── Promedios por Pregunta (mini barras) ── --}}
    @php
        $qLabels = [
            1 => 'Calidad del servicio',
            2 => 'Atención de solicitudes',
            3 => 'Tiempo de resolución',
            4 => 'Conocimientos técnicos',
            5 => 'Satisfacción general',
        ];
    @endphp
    <div class="row g-3 mb-3">
        @foreach(range(1,5) as $qi)
        @php
            $qv     = $preguntaPromedios[$qi] ?? 0;
            $qPct   = $qv > 0 ? round($qv / 4 * 100) : 0;
            $qColor = $qv >= 3 ? '#16a34a' : ($qv >= 2.5 ? '#f59e0b' : '#dc2626');
            $qBg    = $qv >= 3 ? '#dcfce7' : ($qv >= 2.5 ? '#fef3c7' : '#fee2e2');
        @endphp
        <div class="col-6 col-md-4 col-xl">
            <div class="q-avg-card">
                <span style="display:inline-block;padding:.22rem .6rem;border-radius:8px;font-size:.7rem;font-weight:800;background:{{ $qBg }};color:{{ $qColor }};margin-bottom:.45rem;">P{{ $qi }}</span>
                <div style="font-size:.72rem;color:#64748b;line-height:1.3;margin-bottom:.45rem;">{{ $qLabels[$qi] }}</div>
                <div class="q-avg-bar">
                    <div class="q-avg-bar-fill" style="width:{{ $qPct }}%;background:{{ $qColor }};"></div>
                </div>
                <div style="font-size:1.1rem;font-weight:800;color:{{ $qColor }};margin-top:.3rem;">
                    {{ $qv > 0 ? number_format($qv, 1) : '—' }}<span style="font-size:.7rem;color:#94a3b8;font-weight:400;"> /4.0</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Fila 2: Tabla de indicadores + Gráfica de barras --}}
    <div class="row g-3 mb-3">

        {{-- Tabla de indicadores por área --}}
        <div class="col-12 col-lg-6">
            <div class="enc-table-wrap h-100">
                <div class="enc-table-header">
                    <i class="bi bi-table text-white" style="font-size:.9rem;"></i>
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
                                $tot = ($fila->satisfechos + $fila->no_satisfechos);
                                $pct = $tot > 0 ? round($fila->satisfechos / $tot * 100) : 0;
                                $barColor = $pct >= 80 ? '#16a34a' : ($pct >= 50 ? '#f59e0b' : '#dc2626');
                                $chipBg   = $pct >= 80 ? '#dcfce7' : ($pct >= 50 ? '#fef3c7' : '#fee2e2');
                                $chipTxt  = $pct >= 80 ? '#15803d' : ($pct >= 50 ? '#b45309' : '#b91c1c');
                            @endphp
                            <tr>
                                <td style="max-width:140px; white-space:normal; font-size:.8rem;">{{ $fila->area }}</td>
                                <td class="text-center fw-semibold" style="color:#16a34a;">{{ $fila->satisfechos }}</td>
                                <td class="text-center fw-semibold" style="color:#dc2626;">{{ $fila->no_satisfechos }}</td>
                                <td class="text-center fw-semibold">{{ $tot }}</td>
                                <td style="min-width:90px;">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="enc-pct-bar flex-grow-1">
                                            <div class="enc-pct-bar-fill" style="width:{{ $pct }}%; background:{{ $barColor }};"></div>
                                        </div>
                                        <span class="enc-pct-chip" style="background:{{ $chipBg }}; color:{{ $chipTxt }};">{{ $pct }}%</span>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4" style="font-size:.85rem;">
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

        {{-- Gráfica barras por área --}}
        <div class="col-12 col-lg-6">
            <div class="enc-chart-card h-100">
                <div class="enc-chart-title">
                    <i class="bi bi-bar-chart-fill text-primary"></i> Satisfechos vs No Satisfechos por Área
                </div>
                <div style="height:240px; position:relative;">
                    <canvas id="encChartPorArea"></canvas>
                </div>
                @if(empty($satisfaccionStats['por_area']) || count($satisfaccionStats['por_area']) === 0)
                    <p class="text-center text-muted mt-2 mb-0" style="font-size:.8rem;">Sin datos aún</p>
                @endif
            </div>
        </div>

    </div>

    {{-- Fila 3: Donut global + Tasa respuesta + Línea resueltos --}}
    <div class="row g-3">

        {{-- Donut Global --}}
        <div class="col-12 col-sm-4">
            <div class="enc-chart-card h-100">
                <div class="enc-chart-title">
                    <i class="bi bi-pie-chart-fill text-success"></i> Distribución Global
                </div>
                <div style="height:160px; position:relative;">
                    <canvas id="encChartGlobal"></canvas>
                </div>
                <div class="mt-3 d-flex flex-column gap-1" style="font-size:.78rem; color:#64748b;">
                    <span><span style="display:inline-block;width:9px;height:9px;border-radius:50%;background:#16a34a;margin-right:5px;"></span>{{ $satisfechos }} satisfechos</span>
                    <span><span style="display:inline-block;width:9px;height:9px;border-radius:50%;background:#dc2626;margin-right:5px;"></span>{{ $noSatisfechos }} no satisfechos</span>
                    <span><span style="display:inline-block;width:9px;height:9px;border-radius:50%;background:#94a3b8;margin-right:5px;"></span>{{ $sinResponder }} sin responder</span>
                </div>
            </div>
        </div>

        {{-- Tasa de respuesta --}}
        <div class="col-12 col-sm-4">
            <div class="enc-chart-card h-100 text-center">
                <div class="enc-chart-title justify-content-center">
                    <i class="bi bi-reply-fill text-warning"></i> Tasa de Respuesta
                </div>
                <div style="height:160px; position:relative; display:flex; align-items:center; justify-content:center;">
                    <canvas id="encChartTasa"></canvas>
                    <div style="position:absolute; text-align:center; pointer-events:none;">
                        <div class="fw-bold" id="encTasaText" style="font-size:1.9rem; color:#1e3a5f; line-height:1;">{{ $tasaPct }}%</div>
                        <div style="font-size:.7rem; color:#64748b;">respondidas</div>
                    </div>
                </div>
                <div class="text-muted mt-2" style="font-size:.78rem;">
                    <strong style="color:#1e293b;">{{ $respondidas }}</strong> de <strong style="color:#1e293b;">{{ $total }}</strong> encuestas contestadas
                </div>
            </div>
        </div>

        {{-- Promedios por Pregunta (gráfica horizontal) --}}
        <div class="col-12 col-sm-4">
            <div class="enc-chart-card h-100">
                <div class="enc-chart-title">
                    <i class="bi bi-bar-chart-steps text-success"></i> Promedio por Pregunta
                    <span style="margin-left:auto;font-size:.68rem;color:#94a3b8;font-weight:400;">escala 1–4</span>
                </div>
                <div style="height:190px; position:relative;">
                    <canvas id="encChartQAvg"></canvas>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var stats             = @json($satisfaccionStats);
        var porAreaInit       = @json($porAreaChart);
        var preguntaPromedios = @json($preguntaPromedios);
        var porPrioInit  = @json($porPrioridadChart);
        var diasInit     = { labels: @json($satisfaccionStats['dias_labels']), data: @json($satisfaccionStats['dias_data']) };
        var filterUrl    = '{{ route("reportes.filter-data") }}';

        // ─────────────────────────────────────────────────────────────────
        // GRÁFICAS DE ENCUESTA — instancias guardadas para actualizaciones
        // ─────────────────────────────────────────────────────────────────
        var instEncGlobal = null, instEncPorArea = null, instEncTasa = null, instEncQAvg = null;

        // Donut global distribución satisfacción
        var c1 = document.getElementById('encChartGlobal');
        if (c1) {
            instEncGlobal = new Chart(c1, {
                type: 'doughnut',
                data: {
                    labels: ['Satisfechos','No satisfechos','Sin responder'],
                    datasets: [{ data: [stats.satisfechos, stats.no_satisfechos, stats.sin_responder],
                        backgroundColor: ['#16a34a','#dc2626','#94a3b8'], borderWidth: 2, borderColor: '#fff' }]
                },
                options: { responsive: true, maintainAspectRatio: false, cutout: '65%',
                    plugins: { legend: { display: false },
                        tooltip: { callbacks: { label: function(c){ return ' ' + c.label + ': ' + c.raw; } } } } }
            });
        }

        // Barras satisfechos vs no-satisfechos por área
        var c2 = document.getElementById('encChartPorArea');
        if (c2) {
            instEncPorArea = new Chart(c2, {
                type: 'bar',
                data: {
                    labels: stats.por_area && stats.por_area.length ? stats.por_area.map(function(r){ return r.area; }) : [],
                    datasets: [
                        { label: 'Satisfechos',    data: stats.por_area && stats.por_area.length ? stats.por_area.map(function(r){ return r.satisfechos; }) : [],    backgroundColor: '#16a34a', borderRadius: 5 },
                        { label: 'No satisfechos', data: stats.por_area && stats.por_area.length ? stats.por_area.map(function(r){ return r.no_satisfechos; }) : [], backgroundColor: '#dc2626', borderRadius: 5 }
                    ]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom', labels: { font: { size: 11 }, boxWidth: 12, padding: 12 } } },
                    scales: {
                        y: { title: { display: true, text: 'Nº de Respuestas', font: { size: 11, weight: 'bold' }, color: '#64748b' },
                            beginAtZero: true, ticks: { stepSize: 1, font: { size: 10 } }, grid: { color: '#f1f5f9' } },
                        x: { title: { display: true, text: 'Área', font: { size: 11, weight: 'bold' }, color: '#64748b' },
                            ticks: { font: { size: 10 }, maxRotation: 40 }, grid: { display: false } }
                    }
                }
            });
        }

        // Donut tasa de respuesta
        var c3 = document.getElementById('encChartTasa');
        if (c3) {
            var encTasaInit = stats.total > 0 ? Math.round(stats.respondidas / stats.total * 100) : 0;
            instEncTasa = new Chart(c3, {
                type: 'doughnut',
                data: { datasets: [{ data: [encTasaInit, 100 - encTasaInit], backgroundColor: ['#1d4ed8','#e2e8f0'], borderWidth: 0 }] },
                options: { responsive: true, maintainAspectRatio: false, cutout: '75%',
                    plugins: { legend: { display: false }, tooltip: { enabled: false } } }
            });
        }

        // Gráfica horizontal — Promedios por Pregunta (P1-P5)
        var ctxQAvg = document.getElementById('encChartQAvg');
        if (ctxQAvg) {
            var qVals   = Object.values(preguntaPromedios);
            var qColors = qVals.map(function(v){ return v >= 3 ? 'rgba(22,163,74,.85)' : (v >= 2.5 ? 'rgba(245,158,11,.85)' : 'rgba(220,38,38,.85)'); });
            instEncQAvg = new Chart(ctxQAvg, {
                type: 'bar',
                data: {
                    labels: ['P1','P2','P3','P4','P5'],
                    datasets: [{ label: 'Promedio', data: qVals, backgroundColor: qColors, borderRadius: 5, borderWidth: 0 }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true, maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { callbacks: { label: function(c){ return ' ' + (c.raw || 0).toFixed(1) + ' / 4.0'; } } }
                    },
                    scales: {
                        x: { title: { display: true, text: 'Promedio (escala 1–4)', font: { size: 11, weight: 'bold' }, color: '#64748b' },
                            min: 0, max: 4, ticks: { stepSize: 1, font: { size: 10 } }, grid: { color: '#f1f5f9' } },
                        y: { ticks: { font: { size: 11, weight: '600' }, color: '#334155' }, grid: { display: false } }
                    }
                }
            });
        }

        // ─────────────────────────────────────────────────────────────────
        // GRÁFICAS DINÁMICAS (sección Power BI — filtros por área/técnico)
        // ─────────────────────────────────────────────────────────────────

        // Barras – Tickets por Área
        var ctxArea = document.getElementById('chartPorArea');
        var instArea = null;
        if (ctxArea) {
            instArea = new Chart(ctxArea, {
                type: 'bar',
                data: {
                    labels: porAreaInit.map(function(r){ return r.nombre; }),
                    datasets: [{
                        label: 'Nº de Tickets',
                        data: porAreaInit.map(function(r){ return r.total; }),
                        backgroundColor: '#3b82f6',
                        borderRadius: 6, borderWidth: 0
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: {
                            title: { display: true, text: 'Área', font: { size: 11, weight: 'bold' }, color: '#64748b' },
                            ticks: { font: { size: 10 }, maxRotation: 35 }, grid: { display: false }
                        },
                        y: {
                            title: { display: true, text: 'Nº de Tickets', font: { size: 11, weight: 'bold' }, color: '#64748b' },
                            beginAtZero: true, ticks: { stepSize: 1, font: { size: 10 } }, grid: { color: '#f1f5f9' }
                        }
                    }
                }
            });
        }

        // Donut – Tickets por Prioridad
        var ctxPrio = document.getElementById('chartPorPrioridad');
        var instPrio = null;
        var prioColors = { 'Alta': '#ef4444', 'Media': '#f59e0b', 'Baja': '#22c55e' };
        if (ctxPrio) {
            instPrio = new Chart(ctxPrio, {
                type: 'doughnut',
                data: {
                    labels: porPrioInit.map(function(r){ return r.nombre; }),
                    datasets: [{
                        data: porPrioInit.map(function(r){ return r.total; }),
                        backgroundColor: porPrioInit.map(function(r){ return prioColors[r.nombre] || '#94a3b8'; }),
                        borderWidth: 2, borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false, cutout: '58%',
                    plugins: {
                        legend: { position: 'bottom', labels: { font: { size: 11 }, boxWidth: 12, padding: 10 } },
                        tooltip: { callbacks: { label: function(c){ return ' ' + c.label + ': ' + c.raw + ' tickets'; } } }
                    }
                }
            });
        }

        // Línea – Resolución últimos 14 días (dinámica)
        var ctxLinea = document.getElementById('chartLinea');
        var instLinea = null;
        if (ctxLinea) {
            instLinea = new Chart(ctxLinea, {
                type: 'line',
                data: {
                    labels: diasInit.labels,
                    datasets: [{
                        label: 'Resueltos', data: diasInit.data,
                        borderColor: '#8b5cf6', backgroundColor: 'rgba(139,92,246,0.08)',
                        borderWidth: 2.5, fill: true, tension: 0.4,
                        pointRadius: 3, pointBackgroundColor: '#8b5cf6'
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: {
                            title: { display: true, text: 'Fecha (día/mes)', font: { size: 11, weight: 'bold' }, color: '#64748b' },
                            ticks: { font: { size: 10 } }, grid: { display: false }
                        },
                        y: {
                            title: { display: true, text: 'Tickets Resueltos / día', font: { size: 11, weight: 'bold' }, color: '#64748b' },
                            beginAtZero: true, ticks: { stepSize: 1, font: { size: 10 } }, grid: { color: '#f1f5f9' }
                        }
                    }
                }
            });
        }

        // ─────────────────────────────────────────────────────────────────
        // LÓGICA DE FILTRADO POWER BI — cambio automático con los selects
        // ─────────────────────────────────────────────────────────────────

        var spinner = document.getElementById('pbiSpinner');
        var badge   = document.getElementById('pbiBadge');
        var filterTimeout = null;

        // Actualiza la sección de encuestas a partir de la respuesta AJAX
        function updateEncSection(d) {
            var encSat   = d.enc_satisfechos   || 0;
            var encNoSat = d.enc_no_satisfechos || 0;
            var encSin   = d.enc_sin_responder  || 0;
            var encTotal = encSat + encNoSat + encSin;
            var encResp  = encSat + encNoSat;
            var pctEnc   = d.enc_satisfaccion_pct || 0;
            var tasaPct  = encTotal > 0 ? Math.round(encResp / encTotal * 100) : 0;

            // Hero semáforo — clase + porcentaje + icono + submensaje
            var heroEl = document.getElementById('encHeroCard');
            if (heroEl) heroEl.className = 'enc-hero h-100' + (pctEnc >= 80 ? '' : (pctEnc >= 50 ? ' amarillo' : ' rojo'));
            var pctEl = document.getElementById('encHeroPct');
            if (pctEl) pctEl.textContent = pctEnc + '%';
            var subEl = document.getElementById('encHeroSub');
            if (subEl) subEl.textContent = pctEnc >= 80 ? 'Nivel óptimo ≥ 80%' : (pctEnc >= 50 ? 'Por debajo del objetivo (80%)' : 'Nivel crítico — requiere atención');
            var iconEl = document.getElementById('encHeroIconI');
            if (iconEl) iconEl.className = 'bi ' + (pctEnc >= 80 ? 'bi-emoji-laughing-fill' : (pctEnc >= 50 ? 'bi-emoji-neutral-fill' : 'bi-emoji-frown-fill'));

            // KPI cards
            var e1 = document.getElementById('encKpiTotal');       if (e1) e1.textContent = encTotal;
            var e2 = document.getElementById('encKpiRespondidas'); if (e2) e2.textContent = encResp;
            var e3 = document.getElementById('encKpiSatisfechos'); if (e3) e3.textContent = encSat;
            var e4 = document.getElementById('encKpiNoSatisfechos'); if (e4) e4.textContent = encNoSat;

            // Badge del encabezado de sección
            var hBadge = document.getElementById('encHeaderBadge');
            if (hBadge) hBadge.textContent = encResp + '/' + encTotal + ' respondidas';

            // Donut global
            if (instEncGlobal) {
                instEncGlobal.data.datasets[0].data = [encSat, encNoSat, encSin];
                instEncGlobal.update();
            }

            // Barras por área
            if (instEncPorArea && d.enc_por_area) {
                instEncPorArea.data.labels = d.enc_por_area.map(function(r){ return r.area; });
                instEncPorArea.data.datasets[0].data = d.enc_por_area.map(function(r){ return r.satisfechos; });
                instEncPorArea.data.datasets[1].data = d.enc_por_area.map(function(r){ return r.no_satisfechos; });
                instEncPorArea.update();
            }

            // Donut tasa respuesta + texto central
            if (instEncTasa) {
                instEncTasa.data.datasets[0].data = [tasaPct, 100 - tasaPct];
                instEncTasa.update();
            }
            var tasaTxtEl = document.getElementById('encTasaText');
            if (tasaTxtEl) tasaTxtEl.textContent = tasaPct + '%';

            // Tabla por área — reconstruir filas dinamicamente
            var tbody = document.getElementById('encTableBody');
            if (tbody) {
                if (!d.enc_por_area || d.enc_por_area.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4" style="font-size:.85rem;"><i class="bi bi-inbox d-block mb-1" style="font-size:1.5rem;"></i>Sin encuestas respondidas aún</td></tr>';
                } else {
                    tbody.innerHTML = d.enc_por_area.map(function(fila) {
                        var tot = (fila.satisfechos || 0) + (fila.no_satisfechos || 0);
                        var p   = tot > 0 ? Math.round(fila.satisfechos / tot * 100) : 0;
                        var bc  = p >= 80 ? '#16a34a' : (p >= 50 ? '#f59e0b' : '#dc2626');
                        var bg  = p >= 80 ? '#dcfce7' : (p >= 50 ? '#fef3c7' : '#fee2e2');
                        var tc  = p >= 80 ? '#15803d' : (p >= 50 ? '#b45309' : '#b91c1c');
                        return '<tr>' +
                            '<td style="max-width:140px;white-space:normal;font-size:.8rem;">' + fila.area + '</td>' +
                            '<td class="text-center fw-semibold" style="color:#16a34a;">' + fila.satisfechos + '</td>' +
                            '<td class="text-center fw-semibold" style="color:#dc2626;">' + fila.no_satisfechos + '</td>' +
                            '<td class="text-center fw-semibold">' + tot + '</td>' +
                            '<td style="min-width:90px;"><div class="d-flex align-items-center gap-2">' +
                            '<div class="enc-pct-bar flex-grow-1"><div style="height:100%;border-radius:4px;width:' + p + '%;background:' + bc + ';"></div></div>' +
                            '<span class="enc-pct-chip" style="background:' + bg + ';color:' + tc + ';">' + p + '%</span>' +
                            '</div></td></tr>';
                    }).join('');
                }
            }
        }

        function updateCharts() {
            var areaId    = document.getElementById('filtroArea').value;
            var tecnicoId = document.getElementById('filtroTecnico').value;

            if (spinner) spinner.style.display = 'inline-block';
            if (badge)   badge.classList.add('d-none');

            clearTimeout(filterTimeout);
            filterTimeout = setTimeout(function() {
                var url = filterUrl + '?area_id=' + encodeURIComponent(areaId) +
                                      '&tecnico_id=' + encodeURIComponent(tecnicoId);

                fetch(url, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(function(res) {
                    if (!res.ok) throw new Error('Respuesta no válida del servidor');
                    return res.json();
                })
                .then(function(d) {
                    // Actualizar KPI cards
                    document.getElementById('dkpiTotal').textContent     = d.total;
                    document.getElementById('dkpiAbiertos').textContent  = d.abiertos;
                    document.getElementById('dkpiEnProceso').textContent = d.en_proceso;
                    document.getElementById('dkpiResueltos').textContent = d.resueltos;

                    // Actualizar gráfica por área
                    if (instArea) {
                        instArea.data.labels   = d.por_area.map(function(r){ return r.nombre; });
                        instArea.data.datasets[0].data = d.por_area.map(function(r){ return r.total; });
                        instArea.update();
                    }

                    // Actualizar donut prioridad
                    if (instPrio) {
                        instPrio.data.labels   = d.por_prioridad.map(function(r){ return r.nombre; });
                        instPrio.data.datasets[0].data = d.por_prioridad.map(function(r){ return r.total; });
                        instPrio.data.datasets[0].backgroundColor = d.por_prioridad.map(function(r){ return prioColors[r.nombre] || '#94a3b8'; });
                        instPrio.update();
                    }

                    // Actualizar línea de resolución
                    if (instLinea) {
                        instLinea.data.labels   = d.dias_labels;
                        instLinea.data.datasets[0].data = d.dias_data;
                        instLinea.update();
                    }

                    // Actualizar sección de encuestas con filtro de área
                    updateEncSection(d);

                    // Mostrar badge de filtro activo si hay filtro
                    if (badge && (areaId || tecnicoId)) {
                        badge.classList.remove('d-none');
                        badge.textContent = 'Filtro activo';
                    }
                })
                .catch(function(e) { console.warn('Error al filtrar:', e); })
                .finally(function() { if (spinner) spinner.style.display = 'none'; });
            }, 350);
        }

        var selArea    = document.getElementById('filtroArea');
        var selTecnico = document.getElementById('filtroTecnico');
        var btnReset   = document.getElementById('pbiReset');

        if (selArea)    selArea.addEventListener('change',    updateCharts);
        if (selTecnico) selTecnico.addEventListener('change', updateCharts);

        if (btnReset) {
            btnReset.addEventListener('click', function() {
                if (selArea)    selArea.value    = '';
                if (selTecnico) selTecnico.value = '';
                if (badge)      badge.classList.add('d-none');
                updateCharts();
            });
        }
    });
    </script>
    @endpush

    @endif

</div>
@endsection
