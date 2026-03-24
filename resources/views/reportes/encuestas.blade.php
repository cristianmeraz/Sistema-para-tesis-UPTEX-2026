@extends('layouts.app')

@section('title', 'Respuestas de Encuestas')
@section('no_header_title', true)

@section('content')
<style>
    /* ══ BANNER ══ */
    .enc-d-banner {
        background: linear-gradient(135deg, #065f46 0%, #059669 60%, #0d9488 100%);
        border-radius: 18px;
        padding: 1.6rem 2rem;
        margin-bottom: 1.6rem;
        display: flex; align-items: center; gap: 1.2rem;
        position: relative; overflow: hidden;
        box-shadow: 0 8px 30px rgba(5,150,105,.25);
    }
    .enc-d-banner::before {
        content:''; position:absolute; top:-40px; right:-40px;
        width:180px; height:180px; border-radius:50%;
        background:rgba(255,255,255,.06);
    }
    .enc-d-banner-logo {
        width:52px; height:52px; background:rgba(255,255,255,.92);
        border-radius:12px; display:flex; align-items:center; justify-content:center;
        flex-shrink:0; padding:6px;
    }
    .enc-d-banner-logo img { width:100%; height:100%; object-fit:contain; }
    .enc-d-banner h1 { color:#fff; font-size:1.4rem; font-weight:700; margin:0 0 .1rem; }
    .enc-d-banner p  { color:rgba(255,255,255,.75); font-size:.84rem; margin:0; }

    /* ══ KPI CARDS ══ */
    .ekpi-card {
        background:#fff; border-radius:14px;
        border:1px solid #e8edf5;
        box-shadow:0 2px 10px rgba(0,0,0,.04);
        padding:1.1rem 1.3rem;
        display:flex; align-items:center; gap:.85rem;
        height:100%;
    }
    .ekpi-icon {
        width:44px; height:44px; border-radius:12px;
        display:flex; align-items:center; justify-content:center;
        font-size:1.2rem; flex-shrink:0;
    }
    .ekpi-val { font-size:2rem; font-weight:800; line-height:1; color:#1e293b; }
    .ekpi-lbl { font-size:.73rem; color:#64748b; margin-top:.15rem; }

    /* ══ TABS ══ */
    .nav-tabs .nav-link {
        color:#64748b; font-size:.87rem; font-weight:600;
        border:none; border-bottom:3px solid transparent;
        padding:.65rem 1.1rem; border-radius:0;
    }
    .nav-tabs .nav-link.active {
        color:#059669; border-bottom-color:#059669;
        background:none;
    }
    .nav-tabs { border-bottom:2px solid #e8edf5; }

    /* ══ QUESTION CHART CARD ══ */
    .q-card {
        background:#fff; border-radius:16px;
        border:1px solid #e8edf5;
        box-shadow:0 2px 10px rgba(0,0,0,.04);
        overflow:hidden; height:100%;
    }
    .q-card-header {
        padding:.85rem 1.15rem .6rem;
        border-bottom:1px solid #f1f5f9;
    }
    .q-num {
        display:inline-flex; align-items:center; justify-content:center;
        width:28px; height:28px; border-radius:8px;
        background:#dcfce7; color:#15803d;
        font-size:.78rem; font-weight:800;
        margin-right:.5rem; flex-shrink:0;
    }
    .q-texto { font-size:.82rem; font-weight:600; color:#334155; line-height:1.35; }
    .q-prom-badge {
        display:inline-block; font-size:.72rem; font-weight:700;
        padding:.18rem .65rem; border-radius:20px;
        background:#f0fdf4; color:#15803d;
        border:1px solid #bbf7d0; white-space:nowrap;
    }
    .q-chart-body { padding:.8rem 1.1rem 1rem; }

    /* ══ TABLE WRAPPER ══ */
    .enc-table-card {
        background:#fff; border-radius:16px;
        border:1px solid #e8edf5;
        box-shadow:0 2px 10px rgba(0,0,0,.04);
        overflow:hidden;
    }
    .enc-table-card .t-head-bar {
        background:linear-gradient(135deg,#065f46,#059669);
        padding:.85rem 1.3rem;
        display:flex; align-items:center; gap:.5rem;
        color:#fff; font-size:.8rem; font-weight:700;
        text-transform:uppercase; letter-spacing:.05em;
    }
    .enc-table-wrap { overflow-x:auto; }
    .enc-table-wrap table { font-size:.82rem; margin:0; }
    .enc-table-wrap thead th {
        background:#f8fafc; color:#475569;
        font-size:.72rem; font-weight:700;
        text-transform:uppercase; letter-spacing:.04em;
        padding:.6rem .9rem;
        border-bottom:2px solid #e2e8f0; white-space:nowrap;
    }
    .enc-table-wrap tbody td {
        padding:.68rem .9rem; color:#334155;
        border-bottom:1px solid #f1f5f9;
        vertical-align:middle;
    }
    .enc-table-wrap tbody tr:last-child td { border-bottom:none; }
    .enc-table-wrap tbody tr:hover td { background:#f8fafc; }

    /* ══ SCORE CHIPS ══ */
    .sc { display:inline-flex; align-items:center; justify-content:center;
          width:28px; height:28px; border-radius:8px;
          font-size:.8rem; font-weight:800; }
    .sc-1 { background:#fee2e2; color:#b91c1c; }
    .sc-2 { background:#fef3c7; color:#92400e; }
    .sc-3 { background:#dbeafe; color:#1d4ed8; }
    .sc-4 { background:#dcfce7; color:#15803d; }

    /* ══ URGENCY BADGE ══ */
    .urge-low  { background:#dcfce7; color:#15803d; border-radius:20px; padding:.18rem .7rem; font-size:.72rem; font-weight:700; }
    .urge-mid  { background:#fef3c7; color:#92400e; border-radius:20px; padding:.18rem .7rem; font-size:.72rem; font-weight:700; }
    .urge-high { background:#fee2e2; color:#b91c1c; border-radius:20px; padding:.18rem .7rem; font-size:.72rem; font-weight:700; }

    /* ══ SEARCH BAR ══ */
    .search-bar {
        display:flex; align-items:center; gap:.6rem;
        padding:.8rem 1.2rem;
        background:#f8fafc; border-bottom:1px solid #e8edf5;
    }
    .search-bar input {
        flex:1; border:1px solid #e2e8f0; border-radius:8px;
        padding:.45rem .9rem; font-size:.83rem; color:#334155;
        outline:none;
    }
    .search-bar input:focus { border-color:#059669; box-shadow:0 0 0 3px rgba(5,150,105,.12); }

    /* ══ RESPONSIVE ══ */
    @media (max-width:768px) {
        .enc-d-banner { padding:1.1rem 1rem; }
        .enc-d-banner h1 { font-size:1.15rem; }
    }
</style>

<div class="container-fluid">

    {{-- ══ BANNER ══ --}}
    <div class="enc-d-banner">
        <div class="enc-d-banner-logo">
            <img src="{{ asset('images/logo-uptex.png') }}" alt="UPTEX">
        </div>
        <div class="flex-grow-1">
            <h1>Encuestas de Satisfacción — Detalle</h1>
            <p>Distribución por pregunta · Respondidas · Pendientes &mdash; Universidad Politécnica de Texcoco</p>
        </div>
        <a href="{{ route('reportes.index') }}" class="btn btn-light btn-sm d-none d-md-inline-flex align-items-center gap-1">
            <i class="bi bi-arrow-left"></i> Estadísticas
        </a>
    </div>

    {{-- ══ KPI CARDS ══ --}}
    @php
        $pctSat = $totales['respondidas'] > 0 ? round($totales['satisfechos'] / $totales['respondidas'] * 100) : 0;
        $tasaResp = $totales['total'] > 0 ? round($totales['respondidas'] / $totales['total'] * 100) : 0;
        $satColor = $pctSat >= 80 ? '#16a34a' : ($pctSat >= 50 ? '#d97706' : '#dc2626');
    @endphp
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="ekpi-card">
                <div class="ekpi-icon" style="background:#dbeafe;color:#1d4ed8;">
                    <i class="bi bi-clipboard-data-fill"></i>
                </div>
                <div>
                    <div class="ekpi-val">{{ $totales['total'] }}</div>
                    <div class="ekpi-lbl">Total Enviadas</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="ekpi-card">
                <div class="ekpi-icon" style="background:#dcfce7;color:#16a34a;">
                    <i class="bi bi-check2-circle"></i>
                </div>
                <div>
                    <div class="ekpi-val" style="color:#16a34a;">{{ $totales['respondidas'] }}</div>
                    <div class="ekpi-lbl">Respondidas ({{ $tasaResp }}% tasa)</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="ekpi-card">
                <div class="ekpi-icon" style="background:#fef3c7;color:#d97706;">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div>
                    <div class="ekpi-val" style="color:#d97706;">{{ $totales['pendientes'] }}</div>
                    <div class="ekpi-lbl">Sin Responder</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="ekpi-card">
                <div class="ekpi-icon" style="background:#f0fdf4;color:#16a34a;">
                    <i class="bi bi-emoji-smile-fill"></i>
                </div>
                <div>
                    <div class="ekpi-val" style="color:{{ $satColor }};">{{ $pctSat }}%</div>
                    <div class="ekpi-lbl">Índice de Satisfacción</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ TABS ══ --}}
    <ul class="nav nav-tabs mb-3" id="encTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" data-bs-target="#tabResumen" data-bs-toggle="tab" role="tab">
                <i class="bi bi-bar-chart-fill me-1"></i>Resumen por Pregunta
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" data-bs-target="#tabRespondidas" data-bs-toggle="tab" role="tab">
                <i class="bi bi-check-circle-fill me-1"></i>Respondidas
                <span class="badge ms-1" style="background:#dcfce7;color:#15803d;font-size:.68rem;">{{ $totales['respondidas'] }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" data-bs-target="#tabPendientes" data-bs-toggle="tab" role="tab">
                <i class="bi bi-hourglass-split me-1"></i>Pendientes
                <span class="badge ms-1" style="background:#fef3c7;color:#92400e;font-size:.68rem;">{{ $totales['pendientes'] }}</span>
            </button>
        </li>
    </ul>

    <div class="tab-content" id="encTabContent">

        {{-- ══════════════════ TAB 1: RESUMEN POR PREGUNTA ══════════════════ --}}
        <div class="tab-pane fade show active" id="tabResumen" role="tabpanel">

            {{-- Leyenda de escala --}}
            <div class="d-flex flex-wrap align-items-center gap-3 mb-3 p-3"
                 style="background:#f8fafc; border-radius:12px; border:1px solid #e8edf5; font-size:.78rem; color:#475569;">
                <span class="fw-bold text-dark"><i class="bi bi-info-circle me-1"></i>Escala de valores:</span>
                <span><span class="sc sc-1 me-1">1</span> Nada Satisfecho</span>
                <span><span class="sc sc-2 me-1">2</span> Poco Satisfecho</span>
                <span><span class="sc sc-3 me-1">3</span> Satisfecho</span>
                <span><span class="sc sc-4 me-1">4</span> Muy Satisfecho</span>
            </div>

            <div class="row g-3">
                @foreach($preguntasStats as $num => $stat)
                <div class="col-12 col-lg-6">
                    <div class="q-card">
                        <div class="q-card-header">
                            <div class="d-flex align-items-start gap-2">
                                <span class="q-num">P{{ $num }}</span>
                                <div class="flex-grow-1">
                                    <div class="q-texto">{{ $stat['texto'] }}</div>
                                    <div class="mt-1 d-flex align-items-center gap-2" style="font-size:.72rem; color:#64748b;">
                                        <span>{{ $stat['total'] }} respuestas</span>
                                        <span class="q-prom-badge">Promedio: {{ $stat['promedio'] }} / 4.0</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="q-chart-body">
                            @if($stat['total'] > 0)
                                <div style="height:190px; position:relative;">
                                    <canvas id="chartP{{ $num }}"></canvas>
                                </div>
                            @else
                                <div class="text-center text-muted py-5" style="font-size:.85rem;">
                                    <i class="bi bi-inbox d-block mb-2" style="font-size:1.5rem;"></i>
                                    Sin respuestas registradas aún
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

        </div>

        {{-- ══════════════════ TAB 2: RESPONDIDAS ══════════════════ --}}
        <div class="tab-pane fade" id="tabRespondidas" role="tabpanel">
            @if($respondidas->count() > 0)
            <div class="enc-table-card">
                <div class="t-head-bar">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>Encuestas Respondidas — {{ $respondidas->count() }} registros</span>
                    <span class="ms-auto" style="font-weight:400; font-size:.75rem; opacity:.8;">
                        P1–P5: escala 1 (Nada) → 4 (Muy Satisfecho)
                    </span>
                </div>
                <div class="search-bar">
                    <i class="bi bi-search text-muted"></i>
                    <input type="text" id="searchRespondidas" placeholder="Buscar por usuario, área, ticket, comentario...">
                    <span id="countRespondidas" class="text-muted" style="font-size:.78rem; white-space:nowrap;">
                        {{ $respondidas->count() }} resultados
                    </span>
                </div>
                <div class="enc-table-wrap">
                    <table class="table" id="tablaRespondidas">
                        <thead>
                            <tr>
                                <th>#Ticket</th>
                                <th>Usuario</th>
                                <th>Área</th>
                                <th class="text-center" title="¿Satisfecho con el trabajo de IT?">P1</th>
                                <th class="text-center" title="¿Atienden sus solicitudes?">P2</th>
                                <th class="text-center" title="¿Resuelven en tiempo adecuado?">P3</th>
                                <th class="text-center" title="¿Conocimientos suficientes?">P4</th>
                                <th class="text-center" title="¿Satisfecho con la atención?">P5</th>
                                <th class="text-center">Promedio</th>
                                <th class="text-center">Resultado</th>
                                <th>Respondida</th>
                                <th>Comentario</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($respondidas as $r)
                            <tr>
                                <td>
                                    <span class="badge bg-secondary">#{{ str_pad($r['ticket_id'], 4, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td style="white-space:nowrap;">{{ $r['usuario'] ?: 'N/A' }}</td>
                                <td>{{ $r['area'] }}</td>
                                @foreach([1,2,3,4,5] as $p)
                                    @php $val = $r["pregunta_$p"]; @endphp
                                    <td class="text-center">
                                        @if($val)
                                            <span class="sc sc-{{ $val }}" title="{{ ['','Nada Satisfecho','Poco Satisfecho','Satisfecho','Muy Satisfecho'][$val] }}">{{ $val }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                @endforeach
                                <td class="text-center">
                                    @php
                                        $prom = $r['promedio'];
                                        $promColor = $prom >= 3.5 ? '#15803d' : ($prom >= 2.5 ? '#1d4ed8' : ($prom >= 1.5 ? '#92400e' : '#b91c1c'));
                                    @endphp
                                    <span class="fw-bold" style="font-size:.9rem; color:{{ $promColor }};">{{ $prom }}</span>
                                    <span style="font-size:.7rem; color:#94a3b8;">/4.0</span>
                                </td>
                                <td class="text-center">
                                    @if($r['satisfecho'])
                                        <span class="badge" style="background:#dcfce7;color:#15803d;">😊 Satisfecho</span>
                                    @else
                                        <span class="badge" style="background:#fee2e2;color:#b91c1c;">😞 No Satisfecho</span>
                                    @endif
                                </td>
                                <td style="white-space:nowrap; font-size:.78rem; color:#64748b;">
                                    {{ $r['respondida_at'] ? \Carbon\Carbon::parse($r['respondida_at'])->format('d/m/Y H:i') : '—' }}
                                </td>
                                <td style="max-width:200px; font-size:.78rem; color:#475569;">
                                    @if($r['comentario'])
                                        <span title="{{ $r['comentario'] }}">{{ Str::limit($r['comentario'], 60) }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @else
            <div class="text-center py-5 text-muted">
                <i class="bi bi-clipboard-x d-block mb-2" style="font-size:2.5rem; color:#94a3b8;"></i>
                <div style="font-size:.95rem;">No hay encuestas respondidas todavía.</div>
                <div style="font-size:.8rem; margin-top:.3rem;">Las respuestas aparecerán aquí cuando los usuarios completen sus encuestas.</div>
            </div>
            @endif
        </div>

        {{-- ══════════════════ TAB 3: PENDIENTES ══════════════════ --}}
        <div class="tab-pane fade" id="tabPendientes" role="tabpanel">
            @if($pendientes->count() > 0)
            <div class="enc-table-card">
                <div class="t-head-bar" style="background:linear-gradient(135deg,#78350f,#d97706);">
                    <i class="bi bi-hourglass-split"></i>
                    <span>Encuestas Pendientes — {{ $pendientes->count() }} usuarios sin responder</span>
                </div>
                <div class="enc-table-wrap">
                    <table class="table" id="tablaPendientes">
                        <thead>
                            <tr>
                                <th>#Ticket</th>
                                <th>Título del Ticket</th>
                                <th>Usuario</th>
                                <th>Área</th>
                                <th>Encuesta Enviada</th>
                                <th class="text-center">Días de Espera</th>
                                <th class="text-center">Urgencia</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendientes as $p)
                            @php
                                $dias = $p['dias_espera'];
                                $urgClass = $dias >= 7 ? 'high' : ($dias >= 3 ? 'mid' : 'low');
                                $urgText  = $dias >= 7 ? 'Alta' : ($dias >= 3 ? 'Media' : 'Baja');
                                $diasColor = $dias >= 7 ? '#b91c1c' : ($dias >= 3 ? '#92400e' : '#15803d');
                            @endphp
                            <tr>
                                <td>
                                    <span class="badge bg-secondary">#{{ str_pad($p['ticket_id'], 4, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td style="max-width:200px; font-size:.82rem;">{{ Str::limit($p['titulo'], 50) }}</td>
                                <td style="white-space:nowrap; font-size:.82rem;">{{ $p['usuario'] ?: 'N/A' }}</td>
                                <td style="font-size:.82rem;">{{ $p['area'] }}</td>
                                <td style="white-space:nowrap; font-size:.78rem; color:#64748b;">
                                    {{ $p['creado_at'] ? \Carbon\Carbon::parse($p['creado_at'])->format('d/m/Y H:i') : '—' }}
                                </td>
                                <td class="text-center fw-bold" style="color:{{ $diasColor }};">
                                    {{ $dias }} {{ $dias === 1 ? 'día' : 'días' }}
                                </td>
                                <td class="text-center">
                                    <span class="urge-{{ $urgClass }}">{{ $urgText }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @else
            <div class="text-center py-5" style="color:#15803d;">
                <i class="bi bi-check2-all d-block mb-2" style="font-size:2.5rem;"></i>
                <div style="font-size:.95rem; font-weight:600;">¡Todas las encuestas han sido respondidas!</div>
                <div style="font-size:.8rem; color:#64748b; margin-top:.3rem;">No hay encuestas pendientes en este momento.</div>
            </div>
            @endif
        </div>

    </div>{{-- /tab-content --}}
</div>{{-- /container-fluid --}}

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    var preguntasData = @json($preguntasStats);
    var etiquetas = ['Nada\nSatisfecho', 'Poco\nSatisfecho', 'Satisfecho', 'Muy\nSatisfecho'];
    var etiquetasCompletas = ['Nada Satisfecho', 'Poco Satisfecho', 'Satisfecho', 'Muy Satisfecho'];
    var barColors = ['#ef4444', '#f59e0b', '#3b82f6', '#22c55e'];
    var borderColors = ['#dc2626', '#d97706', '#2563eb', '#16a34a'];

    // 5 gráficas de distribución — una por pregunta
    for (var i = 1; i <= 5; i++) {
        var el = document.getElementById('chartP' + i);
        if (!el) continue;
        var stat = preguntasData[i];
        if (!stat || stat.total === 0) continue;

        (function(canvas, stat, idx) {
            var ctx = canvas.getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: etiquetasCompletas,
                    datasets: [{
                        label: 'Nº de Respuestas',
                        data: [stat.dist[1], stat.dist[2], stat.dist[3], stat.dist[4]],
                        backgroundColor: barColors,
                        borderColor: borderColors,
                        borderWidth: 1.5,
                        borderRadius: 7,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(ctx) {
                                    var total = stat.total || 1;
                                    var pct = Math.round(ctx.raw / total * 100);
                                    return ' ' + ctx.raw + ' respuestas (' + pct + '%)';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Nivel de Satisfacción',
                                font: { size: 11, weight: 'bold' },
                                color: '#64748b',
                                padding: { top: 6 }
                            },
                            ticks: { font: { size: 10 }, color: '#475569' },
                            grid: { display: false }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Nº de Respuestas',
                                font: { size: 11, weight: 'bold' },
                                color: '#64748b',
                            },
                            beginAtZero: true,
                            ticks: { stepSize: 1, font: { size: 10 }, color: '#475569' },
                            grid: { color: '#f1f5f9' }
                        }
                    }
                }
            });
        })(el, stat, i);
    }

    // Filtro de búsqueda en tabla de respondidas
    var searchInput = document.getElementById('searchRespondidas');
    var countEl = document.getElementById('countRespondidas');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            var val = this.value.toLowerCase().trim();
            var rows = document.querySelectorAll('#tablaRespondidas tbody tr');
            var visible = 0;
            rows.forEach(function (tr) {
                var match = tr.textContent.toLowerCase().includes(val);
                tr.style.display = match ? '' : 'none';
                if (match) visible++;
            });
            if (countEl) countEl.textContent = visible + ' resultados';
        });
    }
});
</script>
@endpush
@endsection
