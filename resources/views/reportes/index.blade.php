@extends('layouts.app')

@section('title', 'Estadísticas')
@section('no_header_title', true)

@section('content')
<style>
    /* ══════ BANNER ══════ */
    .rep-banner {
        background: linear-gradient(135deg, #1e3a5f 0%, #1d4ed8 100%);
        border-radius: 18px;
        padding: 2rem 2.2rem;
        margin-bottom: 2rem;
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
    .rep-banner-icon {
        width: 56px; height: 56px;
        border-radius: 14px;
        background: rgba(255,255,255,.15);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        font-size: 1.6rem;
        color: #fff;
    }
    .rep-banner-title {
        color: #fff;
        font-size: 1.55rem;
        font-weight: 700;
        line-height: 1.2;
        margin: 0;
    }
    .rep-banner-sub {
        color: rgba(255,255,255,.72);
        font-size: .88rem;
        margin: .15rem 0 0;
    }

    /* ══════ CARDS ══════ */
    .rep-card {
        background: #fff;
        border-radius: 18px;
        border: 1px solid #e8edf5;
        box-shadow: 0 4px 18px rgba(0,0,0,.05);
        padding: 2rem 1.8rem 1.6rem;
        display: flex;
        flex-direction: column;
        height: 100%;
        transition: transform .22s, box-shadow .22s;
    }
    .rep-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 32px rgba(0,0,0,.10);
    }
    .rep-card-icon-wrap {
        width: 68px; height: 68px;
        border-radius: 18px;
        display: flex; align-items: center; justify-content: center;
        font-size: 2rem;
        margin-bottom: 1.3rem;
    }
    .rep-card-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: .45rem;
    }
    .rep-card-desc {
        font-size: .88rem;
        color: #64748b;
        line-height: 1.55;
        flex-grow: 1;
        margin-bottom: 1.4rem;
    }
    .rep-card-badge {
        display: inline-block;
        font-size: .72rem;
        font-weight: 600;
        padding: .18rem .65rem;
        border-radius: 20px;
        margin-bottom: 1rem;
        letter-spacing: .03em;
    }
    .rep-btn {
        display: flex; align-items: center; justify-content: center; gap: .45rem;
        border: none;
        border-radius: 10px;
        padding: .72rem 1rem;
        font-size: .93rem;
        font-weight: 600;
        width: 100%;
        text-decoration: none;
        transition: filter .18s, transform .18s, box-shadow .18s;
    }
    .rep-btn:hover {
        filter: brightness(1.08);
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(0,0,0,.15);
    }

    /* Variantes de color */
    .rep-icon-blue   { background: #dbeafe; color: #1d4ed8; }
    .rep-badge-blue  { background: #dbeafe; color: #1d4ed8; }
    .rep-btn-blue    { background: linear-gradient(135deg, #1e3a5f, #1d4ed8); color: #fff;
                       box-shadow: 0 4px 14px rgba(29,78,216,.28); }

    .rep-icon-amber  { background: #fef9c3; color: #b45309; }
    .rep-badge-amber { background: #fef3c7; color: #b45309; }
    .rep-btn-amber   { background: linear-gradient(135deg, #d97706, #f59e0b); color: #fff;
                       box-shadow: 0 4px 14px rgba(245,158,11,.30); }

    .rep-icon-green  { background: #dcfce7; color: #16a34a; }
    .rep-badge-green { background: #dcfce7; color: #16a34a; }
    .rep-btn-green   { background: linear-gradient(135deg, #15803d, #16a34a); color: #fff;
                       box-shadow: 0 4px 14px rgba(22,163,74,.28); }

    @media (max-width: 768px) {
        .rep-banner { padding: 1.4rem 1.2rem; }
        .rep-banner-title { font-size: 1.2rem; }
        .rep-card { padding: 1.5rem 1.3rem 1.3rem; }
    }

    /* ══════ ENCUESTA SECTION ══════ */
    .enc-divider {
        display: flex; align-items: center; gap: .6rem;
        margin: 2.5rem 0 1.4rem;
        padding-bottom: .7rem;
        border-bottom: 2px solid #e8edf5;
    }
    .enc-divider-bar {
        width: 4px; height: 1.3rem; border-radius: 2px;
        background: #16a34a; flex-shrink: 0;
    }
    .enc-kpi {
        background: #fff; border-radius: 14px;
        border: 1px solid #e8edf5;
        box-shadow: 0 2px 10px rgba(0,0,0,.04);
        padding: 1rem 1.2rem;
        display: flex; align-items: center; gap: .9rem;
        transition: transform .2s, box-shadow .2s;
    }
    .enc-kpi:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,.08); }
    .enc-kpi-icon {
        width: 46px; height: 46px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.3rem; flex-shrink: 0;
    }
    .enc-kpi-val { font-size: 1.6rem; font-weight: 700; line-height: 1; color: #1e293b; }
    .enc-kpi-lbl { font-size: .76rem; color: #64748b; margin-top: .15rem; }
    .enc-chart-lbl {
        font-size: .75rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .04em; color: #64748b; margin-bottom: .75rem;
        display: flex; align-items: center; gap: .35rem;
    }
</style>

<div class="container-fluid">

    {{-- ══════ BANNER ══════ --}}
    <div class="rep-banner">
        <div class="rep-banner-icon">
            <i class="bi bi-graph-up-arrow"></i>
        </div>
        <div>
            <h1 class="rep-banner-title">Estadísticas</h1>
            <p class="rep-banner-sub">Genera, analiza y exporta datos del sistema de soporte</p>
        </div>
    </div>

    {{-- ══════ CARDS ══════ --}}
    <div class="row g-4 justify-content-center">

        {{-- Tickets por Fecha --}}
        <div class="col-12 col-md-5">
            <div class="rep-card">
                <div class="rep-card-icon-wrap rep-icon-blue">
                    <i class="bi bi-calendar-range"></i>
                </div>
                <span class="rep-card-badge rep-badge-blue">Filtro temporal</span>
                <div class="rep-card-title">Tickets por Fecha</div>
                <div class="rep-card-desc">
                    Genera un reporte detallado de tickets creados dentro de un rango de fechas personalizado.
                </div>
                <a href="{{ route('reportes.por-fecha') }}" class="rep-btn rep-btn-blue">
                    <i class="bi bi-bar-chart-line"></i> Ver Reporte
                </a>
            </div>
        </div>

        {{-- Rendimiento de Técnicos --}}
        <div class="col-12 col-md-5">
            <div class="rep-card">
                <div class="rep-card-icon-wrap rep-icon-amber">
                    <i class="bi bi-people-fill"></i>
                </div>
                <span class="rep-card-badge rep-badge-amber">Desempeño</span>
                <div class="rep-card-title">Rendimiento de Técnicos</div>
                <div class="rep-card-desc">
                    Consulta las estadísticas de resolución y desempeño individual de cada técnico de soporte.
                </div>
                <a href="{{ route('reportes.rendimiento') }}" class="rep-btn rep-btn-amber">
                    <i class="bi bi-eye-fill"></i> Ver Reporte
                </a>
            </div>
        </div>

    </div>

    {{-- ══════ ENCUESTAS DE SATISFACCIÓN ══════ --}}
    @if(isset($satisfaccionStats))
    @php
        $tasaPct = $satisfaccionStats['total'] > 0
            ? round($satisfaccionStats['respondidas'] / $satisfaccionStats['total'] * 100, 1)
            : 0;
    @endphp

    {{-- Separador de sección --}}
    <div class="enc-divider">
        <span class="enc-divider-bar"></span>
        <i class="bi bi-emoji-smile-fill text-success" style="font-size:1.15rem;"></i>
        <span class="fw-bold text-secondary" style="font-size:.88rem; letter-spacing:.04em; text-transform:uppercase;">
            Encuestas de Satisfacción
        </span>
        <span class="badge bg-success ms-1" style="font-size:.75rem;">
            {{ $satisfaccionStats['respondidas'] }}/{{ $satisfaccionStats['total'] }} respondidas
        </span>
    </div>

    {{-- KPI Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-sm-3">
            <div class="enc-kpi">
                <div class="enc-kpi-icon" style="background:#dbeafe; color:#1d4ed8;">
                    <i class="bi bi-clipboard-data-fill"></i>
                </div>
                <div>
                    <div class="enc-kpi-val">{{ $satisfaccionStats['total'] }}</div>
                    <div class="enc-kpi-lbl">Total Enviadas</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-3">
            <div class="enc-kpi">
                <div class="enc-kpi-icon" style="background:#ede9fe; color:#7c3aed;">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div>
                    <div class="enc-kpi-val">{{ $satisfaccionStats['respondidas'] }}</div>
                    <div class="enc-kpi-lbl">Respondidas</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-3">
            <div class="enc-kpi">
                <div class="enc-kpi-icon" style="background:#dcfce7; color:#16a34a;">
                    <i class="bi bi-emoji-smile-fill"></i>
                </div>
                <div>
                    <div class="enc-kpi-val" style="color:#16a34a;">{{ $satisfaccionStats['satisfechos'] }}</div>
                    <div class="enc-kpi-lbl">Satisfechos</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-3">
            <div class="enc-kpi">
                <div class="enc-kpi-icon" style="background:#fee2e2; color:#dc2626;">
                    <i class="bi bi-emoji-frown-fill"></i>
                </div>
                <div>
                    <div class="enc-kpi-val" style="color:#dc2626;">{{ $satisfaccionStats['no_satisfechos'] }}</div>
                    <div class="enc-kpi-lbl">No Satisfechos</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Gráficas --}}
    <div class="row g-4">

        {{-- Donut Global --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="rep-card">
                <div class="enc-chart-lbl">
                    <i class="bi bi-pie-chart-fill text-success"></i> Global
                </div>
                <div style="height:170px; position:relative;">
                    <canvas id="encChartGlobal"></canvas>
                </div>
                <div class="mt-3 d-flex flex-column gap-1" style="font-size:.8rem; color:#64748b;">
                    <span><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#16a34a;margin-right:6px;"></span>{{ $satisfaccionStats['satisfechos'] }} satisfechos</span>
                    <span><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#dc2626;margin-right:6px;"></span>{{ $satisfaccionStats['no_satisfechos'] }} no satisfechos</span>
                    <span><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#94a3b8;margin-right:6px;"></span>{{ $satisfaccionStats['sin_responder'] }} sin responder</span>
                </div>
            </div>
        </div>

        {{-- Barras Por Área --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="rep-card">
                <div class="enc-chart-lbl">
                    <i class="bi bi-bar-chart-fill text-primary"></i> Por Área
                </div>
                <div style="height:210px; position:relative;">
                    <canvas id="encChartPorArea"></canvas>
                </div>
                @if(empty($satisfaccionStats['por_area']) || count($satisfaccionStats['por_area']) === 0)
                    <p class="text-center text-muted mt-2 mb-0" style="font-size:.8rem;">Sin datos aún</p>
                @endif
            </div>
        </div>

        {{-- Donut Tasa de Respuesta --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="rep-card text-center">
                <div class="enc-chart-lbl justify-content-center">
                    <i class="bi bi-reply-fill text-warning"></i> Tasa de Respuesta
                </div>
                <div style="height:170px; position:relative; display:flex; align-items:center; justify-content:center;">
                    <canvas id="encChartTasa"></canvas>
                    <div style="position:absolute; text-align:center; pointer-events:none;">
                        <div class="fw-bold" style="font-size:2rem; color:#1e3a5f; line-height:1;">{{ $tasaPct }}%</div>
                        <div style="font-size:.75rem; color:#64748b;">respondidas</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Línea Resueltos 14 días --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="rep-card">
                <div class="enc-chart-lbl">
                    <i class="bi bi-graph-up text-info"></i> Resueltos (14 días)
                </div>
                <div style="height:210px; position:relative;">
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

        var c1 = document.getElementById('encChartGlobal');
        if (c1) {
            new Chart(c1, {
                type: 'doughnut',
                data: {
                    labels: ['Satisfechos','No satisfechos','Sin responder'],
                    datasets: [{ data: [stats.satisfechos, stats.no_satisfechos, stats.sin_responder], backgroundColor: ['#16a34a','#dc2626','#94a3b8'], borderWidth: 2, borderColor: '#fff' }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
            });
        }

        var c2 = document.getElementById('encChartPorArea');
        if (c2 && stats.por_area && stats.por_area.length > 0) {
            new Chart(c2, {
                type: 'bar',
                data: {
                    labels: stats.por_area.map(function(r){ return r.area; }),
                    datasets: [
                        { label: 'Satisfechos',    data: stats.por_area.map(function(r){ return r.satisfechos; }),    backgroundColor: '#16a34a', borderRadius: 4 },
                        { label: 'No satisfechos', data: stats.por_area.map(function(r){ return r.no_satisfechos; }), backgroundColor: '#dc2626', borderRadius: 4 }
                    ]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom', labels: { font: { size: 11 }, boxWidth: 12 } } },
                    scales: { y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 10 } } }, x: { ticks: { font: { size: 10 }, maxRotation: 45 } } }
                }
            });
        }

        var c3 = document.getElementById('encChartTasa');
        if (c3) {
            var pct = stats.total > 0 ? Math.round(stats.respondidas / stats.total * 100) : 0;
            new Chart(c3, {
                type: 'doughnut',
                data: { datasets: [{ data: [pct, 100 - pct], backgroundColor: ['#1d4ed8','#e2e8f0'], borderWidth: 0 }] },
                options: { responsive: true, maintainAspectRatio: false, cutout: '72%', plugins: { legend: { display: false }, tooltip: { enabled: false } } }
            });
        }

        var c4 = document.getElementById('encChartResueltos');
        if (c4) {
            new Chart(c4, {
                type: 'line',
                data: {
                    labels: stats.dias_labels,
                    datasets: [{ label: 'Resueltos', data: stats.dias_data, borderColor: '#0891b2', backgroundColor: 'rgba(8,145,178,0.1)', borderWidth: 2, fill: true, tension: 0.35, pointRadius: 3 }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 10 } } }, x: { ticks: { font: { size: 10 } } } }
                }
            });
        }
    });
    </script>
    @endpush

    @endif

</div>
@endsection