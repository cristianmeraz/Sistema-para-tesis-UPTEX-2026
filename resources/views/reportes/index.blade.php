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

    @media (max-width: 768px) {
        .rep-banner { padding: 1.2rem; }
        .rep-banner-title { font-size: 1.2rem; }
        .enc-hero-pct { font-size: 3rem; }
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
        <span class="badge bg-success" style="font-size:.7rem; font-weight:600;">
            {{ $respondidas }}/{{ $total }} respondidas
        </span>
        <span class="ms-auto text-muted" style="font-size:.75rem;">
            <i class="bi bi-clock-history me-1"></i>Indicadores Mantenimiento TI — Periodo {{ date('Y') }}
        </span>
    </div>

    {{-- Fila 1: Hero KPI + KPIs secundarios --}}
    <div class="row g-3 mb-3">

        {{-- HERO: % de satisfacción --}}
        <div class="col-12 col-md-4">
            <div class="enc-hero {{ $heroClass }} h-100">
                <div class="enc-hero-icon">
                    <i class="bi {{ $heroIcon }}"></i>
                </div>
                <div>
                    <div class="enc-hero-pct">{{ $pctSatisfaccion }}%</div>
                    <div class="enc-hero-label">de satisfacción global</div>
                    <div class="enc-hero-sub">{{ $heroMsg }}</div>
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
                            <div class="enc-kpi-val">{{ $total }}</div>
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
                            <div class="enc-kpi-val">{{ $respondidas }}</div>
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
                            <div class="enc-kpi-val" style="color:#16a34a;">{{ $satisfechos }}</div>
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
                            <div class="enc-kpi-val" style="color:#dc2626;">{{ $noSatisfechos }}</div>
                            <div class="enc-kpi-lbl">No Satisfechos</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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
                        <tbody>
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
                        <div class="fw-bold" style="font-size:1.9rem; color:#1e3a5f; line-height:1;">{{ $tasaPct }}%</div>
                        <div style="font-size:.7rem; color:#64748b;">respondidas</div>
                    </div>
                </div>
                <div class="text-muted mt-2" style="font-size:.78rem;">
                    <strong style="color:#1e293b;">{{ $respondidas }}</strong> de <strong style="color:#1e293b;">{{ $total }}</strong> encuestas contestadas
                </div>
            </div>
        </div>

        {{-- Resueltos 14 días --}}
        <div class="col-12 col-sm-4">
            <div class="enc-chart-card h-100">
                <div class="enc-chart-title">
                    <i class="bi bi-graph-up text-info"></i> Tickets Resueltos (14 días)
                </div>
                <div style="height:190px; position:relative;">
                    <canvas id="encChartResueltos"></canvas>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var stats = @json($satisfaccionStats);

        // Donut global
        var c1 = document.getElementById('encChartGlobal');
        if (c1) {
            new Chart(c1, {
                type: 'doughnut',
                data: {
                    labels: ['Satisfechos','No satisfechos','Sin responder'],
                    datasets: [{ data: [stats.satisfechos, stats.no_satisfechos, stats.sin_responder],
                        backgroundColor: ['#16a34a','#dc2626','#94a3b8'], borderWidth: 2, borderColor: '#fff' }]
                },
                options: { responsive: true, maintainAspectRatio: false, cutout: '65%',
                    plugins: { legend: { display: false } } }
            });
        }

        // Barras por área
        var c2 = document.getElementById('encChartPorArea');
        if (c2 && stats.por_area && stats.por_area.length > 0) {
            new Chart(c2, {
                type: 'bar',
                data: {
                    labels: stats.por_area.map(function(r){ return r.area; }),
                    datasets: [
                        { label: 'Satisfechos',    data: stats.por_area.map(function(r){ return r.satisfechos; }),    backgroundColor: '#16a34a', borderRadius: 5 },
                        { label: 'No satisfechos', data: stats.por_area.map(function(r){ return r.no_satisfechos; }), backgroundColor: '#dc2626', borderRadius: 5 }
                    ]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom', labels: { font: { size: 11 }, boxWidth: 12, padding: 12 } } },
                    scales: {
                        y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 10 } }, grid: { color: '#f1f5f9' } },
                        x: { ticks: { font: { size: 10 }, maxRotation: 40 }, grid: { display: false } }
                    }
                }
            });
        }

        // Donut tasa de respuesta
        var c3 = document.getElementById('encChartTasa');
        if (c3) {
            var pct = stats.total > 0 ? Math.round(stats.respondidas / stats.total * 100) : 0;
            new Chart(c3, {
                type: 'doughnut',
                data: { datasets: [{ data: [pct, 100 - pct], backgroundColor: ['#1d4ed8','#e2e8f0'], borderWidth: 0 }] },
                options: { responsive: true, maintainAspectRatio: false, cutout: '75%',
                    plugins: { legend: { display: false }, tooltip: { enabled: false } } }
            });
        }

        // Línea resueltos por día
        var c4 = document.getElementById('encChartResueltos');
        if (c4) {
            new Chart(c4, {
                type: 'line',
                data: {
                    labels: stats.dias_labels,
                    datasets: [{ label: 'Resueltos', data: stats.dias_data,
                        borderColor: '#0891b2', backgroundColor: 'rgba(8,145,178,0.08)',
                        borderWidth: 2.5, fill: true, tension: 0.4, pointRadius: 3,
                        pointBackgroundColor: '#0891b2' }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 10 } }, grid: { color: '#f1f5f9' } },
                        x: { ticks: { font: { size: 10 } }, grid: { display: false } }
                    }
                }
            });
        }
    });
    </script>
    @endpush

    @endif

</div>
@endsection
