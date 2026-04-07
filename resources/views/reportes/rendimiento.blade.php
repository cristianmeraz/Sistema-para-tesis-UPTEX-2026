@extends('layouts.app')

@section('title', 'Rendimiento de Técnicos')

@section('content')
<style>
    /* ══════ BANNER ══════ */
    .rep-banner {
        background: linear-gradient(135deg, #78350f 0%, #d97706 100%);
        border-radius: 18px;
        padding: 1.4rem 2rem;
        margin-bottom: 1.8rem;
        display: flex; align-items: center; gap: 1.2rem;
        position: relative; overflow: hidden;
        box-shadow: 0 8px 30px rgba(217,119,6,.30);
    }
    .rep-banner::before { content:''; position:absolute; top:-40px; right:-40px; width:180px; height:180px; border-radius:50%; background:rgba(255,255,255,.07); }
    .rep-banner-logo { width:48px; height:48px; background:rgba(255,255,255,.92); border-radius:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0; padding:6px; }
    .rep-banner-logo img { width:100%; height:100%; object-fit:contain; }
    .rep-banner-title { color:#fff; font-size:1.3rem; font-weight:700; line-height:1.2; margin:0; }
    .rep-banner-sub { color:rgba(255,255,255,.75); font-size:.82rem; margin:.1rem 0 0; }

    /* ══════ BOTONES ══════ */
    .btn-print { background:linear-gradient(135deg,#15803d,#16a34a); color:#fff; border:none; border-radius:10px; padding:.62rem 1.4rem; font-weight:600; font-size:.9rem; display:inline-flex; align-items:center; gap:.5rem; box-shadow:0 4px 14px rgba(22,163,74,.28); transition:filter .18s,transform .18s; }
    .btn-print:hover { filter:brightness(1.1); transform:translateY(-1px); color:#fff; }
    .btn-volver { background:#f1f5f9; color:#475569; border:none; border-radius:10px; padding:.62rem 1.2rem; font-weight:600; font-size:.9rem; display:inline-flex; align-items:center; gap:.5rem; transition:background .18s; text-decoration:none; }
    .btn-volver:hover { background:#e2e8f0; color:#1e293b; }

    /* ══════ KPI CARDS ══════ */
    .kpi-row { display:flex; gap:.9rem; flex-wrap:wrap; margin-bottom:1.6rem; }
    .kpi-box { flex:1; min-width:130px; background:#fff; border-radius:14px; border:1px solid #e8edf5; box-shadow:0 2px 8px rgba(0,0,0,.04); padding:1rem 1.2rem; text-align:center; }
    .kpi-box .kpi-val { font-size:1.9rem; font-weight:800; line-height:1; }
    .kpi-box .kpi-lbl { font-size:.75rem; color:#64748b; margin-top:.25rem; }

    /* ══════ TABLA ══════ */
    .rep-table-wrap { background:#fff; border-radius:16px; border:1px solid #e8edf5; box-shadow:0 2px 12px rgba(0,0,0,.04); overflow:hidden; margin-bottom:1.4rem; }
    .rep-table-header { background:linear-gradient(135deg,#78350f,#d97706); padding:.9rem 1.4rem; display:flex; align-items:center; justify-content:space-between; }
    .rep-table-header span { color:#fff; font-size:.82rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; }
    .rep-table-wrap table { margin:0; font-size:.85rem; }
    .rep-table-wrap thead th { background:#fffbeb; color:#92400e; font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em; padding:.65rem 1rem; border-bottom:2px solid #fde68a; white-space:nowrap; }
    .rep-table-wrap tbody td { padding:.7rem 1rem; color:#334155; border-bottom:1px solid #f1f5f9; vertical-align:middle; }
    .rep-table-wrap tbody tr:last-child td { border-bottom:none; }
    .rep-table-wrap tbody tr:hover td { background:#fffbeb; }

    /* Barra de efectividad */
    .eff-bar-wrap { display:flex; align-items:center; gap:.5rem; min-width:110px; }
    .eff-bar { flex-grow:1; height:8px; border-radius:4px; background:#e2e8f0; overflow:hidden; }
    .eff-bar-fill { height:100%; border-radius:4px; transition:width .6s ease; }
    .eff-chip { font-size:.72rem; font-weight:700; padding:.2rem .55rem; border-radius:20px; white-space:nowrap; }

    /* ══════ PRINT ══════ */
    @media print {
        .sidebar, .sidebarOverlay, .main-header { display: none !important; }
        .main-content { margin-left: 0 !important; padding: 0 !important; }
        body { background: #fff; }
        #print-area { position: static !important; width: 100%; display: block !important; }
        .no-print { display: none !important; }
    }
</style>

<div id="print-area">

{{-- Cabecera de impresión --}}
<div class="print-header" style="display:none;">
<div style="display:flex; align-items:center; gap:16px; border-bottom:3px solid #d97706; padding-bottom:14px; margin-bottom:20px;">
    <img src="{{ asset('images/logo-uptex.png') }}" alt="UPTEX" style="width:60px; height:60px; object-fit:contain;">
    <div>
        <div style="font-size:14pt; font-weight:700; color:#1e3a5f;">Universidad Politécnica de Texcoco</div>
        <div style="font-size:10pt; color:#475569;">Sistema de Gestión de Tickets de Soporte TI</div>
        <div style="font-size:9pt; color:#64748b; margin-top:2px;">Reporte de Rendimiento de Técnicos &nbsp;·&nbsp; Generado el {{ now()->format('d/m/Y H:i') }}</div>
    </div>
</div>
@php
    $totalTecs  = count($tecnicos);
    $totalAsig  = collect($tecnicos)->sum('total_asignados');
    $totalCerr  = collect($tecnicos)->sum('cerrados');
    $efGlobal   = $totalAsig > 0 ? round($totalCerr / $totalAsig * 100) : 0;
@endphp
<div style="display:flex; gap:16px; margin-bottom:16px;">
    <div style="background:#f1f5f9; border-radius:8px; padding:8px 14px;">
        <span style="font-size:8pt; font-weight:700; text-transform:uppercase; color:#64748b;">Total Técnicos</span><br>
        <span style="font-size:10pt; font-weight:700; color:#1e3a5f;">{{ $totalTecs }}</span>
    </div>
    <div style="background:#f1f5f9; border-radius:8px; padding:8px 14px;">
        <span style="font-size:8pt; font-weight:700; text-transform:uppercase; color:#64748b;">Tickets Asignados</span><br>
        <span style="font-size:10pt; font-weight:700; color:#1e3a5f;">{{ $totalAsig }}</span>
    </div>
    <div style="background:#dcfce7; border-radius:8px; padding:8px 14px;">
        <span style="font-size:8pt; font-weight:700; text-transform:uppercase; color:#15803d;">Efectividad Global</span><br>
        <span style="font-size:10pt; font-weight:700; color:#15803d;">{{ $efGlobal }}%</span>
    </div>
</div>
</div>

{{-- ══ BANNER (pantalla) ══ --}}
<div class="rep-banner no-print">
    <div class="rep-banner-logo">
        <img src="{{ asset('images/logo-uptex.png') }}" alt="UPTEX">
    </div>
    <div class="flex-grow-1">
        <h1 class="rep-banner-title">Rendimiento de Técnicos</h1>
        <p class="rep-banner-sub">Estadísticas individuales de resolución por técnico de soporte — UPTEX</p>
    </div>
    <div class="d-flex gap-2 no-print">
        <button class="btn-print" onclick="imprimirReporte()">
            <i class="bi bi-download"></i> Descargar PDF
        </button>
        <a href="{{ route('reportes.index') }}" class="btn-volver">
            <i class="bi bi-arrow-left"></i> Estadísticas
        </a>
    </div>
</div>

{{-- ══ KPIs globales ══ --}}
@php
    $totalTecs  = count($tecnicos);
    $totalAsig  = collect($tecnicos)->sum('total_asignados');
    $totalCerr  = collect($tecnicos)->sum('cerrados');
    $efGlobal   = $totalAsig > 0 ? round($totalCerr / $totalAsig * 100) : 0;
    $mejorTec   = collect($tecnicos)->sortByDesc('efectividad')->first();
@endphp
<div class="kpi-row no-print">
    <div class="kpi-box">
        <div class="kpi-val" style="color:#d97706;">{{ $totalTecs }}</div>
        <div class="kpi-lbl">Técnicos Activos</div>
    </div>
    <div class="kpi-box">
        <div class="kpi-val" style="color:#1d4ed8;">{{ $totalAsig }}</div>
        <div class="kpi-lbl">Tickets Asignados</div>
    </div>
    <div class="kpi-box">
        <div class="kpi-val" style="color:#15803d;">{{ $totalCerr }}</div>
        <div class="kpi-lbl">Resueltos / Cerrados</div>
    </div>
    <div class="kpi-box">
        <div class="kpi-val" style="color:{{ $efGlobal >= 70 ? '#15803d' : ($efGlobal >= 40 ? '#b45309' : '#b91c1c') }};">{{ $efGlobal }}%</div>
        <div class="kpi-lbl">Efectividad Global</div>
    </div>
    @if($mejorTec)
    <div class="kpi-box">
        <div class="kpi-val" style="color:#d97706; font-size:1.15rem; line-height:1.3;">
            {{ explode(' ', $mejorTec['tecnico'])[0] ?? '—' }}
        </div>
        <div class="kpi-lbl">Mejor Efectividad ({{ $mejorTec['efectividad'] }}%)</div>
    </div>
    @endif
</div>

{{-- ══ TABLA ══ --}}
<div class="rep-table-wrap">
    <div class="rep-table-header no-print">
        <span><i class="bi bi-people-fill me-2"></i>Detalle por Técnico</span>
        <span style="color:rgba(255,255,255,.7); font-size:.75rem;">{{ count($tecnicos) }} técnicos</span>
    </div>
    <div class="table-responsive">
        <table class="table table-sm mb-0">
            <thead>
                <tr>
                    <th>Técnico</th>
                    <th>Correo</th>
                    <th class="text-center">Total</th>
                    <th class="text-center">Resueltos</th>
                    <th class="text-center">T. Prom. (h)</th>
                    <th style="min-width:140px;">Efectividad</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tecnicos as $t)
                @php
                    $ef     = $t['efectividad'];
                    $color  = $ef >= 70 ? '#16a34a' : ($ef >= 40 ? '#f59e0b' : '#dc2626');
                    $chipBg = $ef >= 70 ? '#dcfce7' : ($ef >= 40 ? '#fef3c7' : '#fee2e2');
                    $chipTx = $ef >= 70 ? '#15803d' : ($ef >= 40 ? '#b45309' : '#b91c1c');
                @endphp
                <tr>
                    <td>
                        <div style="display:flex; align-items:center; gap:.55rem;">
                            <div style="width:32px; height:32px; border-radius:50%; background:linear-gradient(135deg,#fef3c7,#fde68a); display:flex; align-items:center; justify-content:center; font-weight:700; font-size:.82rem; color:#b45309; flex-shrink:0;">
                                {{ strtoupper(substr($t['tecnico'], 0, 1)) }}
                            </div>
                            <span class="fw-semibold">{{ $t['tecnico'] }}</span>
                        </div>
                    </td>
                    <td style="font-size:.8rem; color:#64748b;">{{ $t['correo'] }}</td>
                    <td class="text-center fw-bold">{{ $t['total_asignados'] }}</td>
                    <td class="text-center fw-bold" style="color:#16a34a;">{{ $t['cerrados'] }}</td>
                    <td class="text-center" style="font-size:.82rem;">
                        {{ $t['tiempo_promedio'] !== null ? $t['tiempo_promedio'] . 'h' : '—' }}
                    </td>
                    <td>
                        <div class="eff-bar-wrap">
                            <div class="eff-bar">
                                <div class="eff-bar-fill" style="width:{{ $ef }}%; background:{{ $color }};"></div>
                            </div>
                            <span class="eff-chip" style="background:{{ $chipBg }}; color:{{ $chipTx }};">{{ $ef }}%</span>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-5">
                        <i class="bi bi-people d-block mb-2" style="font-size:2rem;"></i>
                        No hay técnicos registrados
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ══ TABLA TICKETS RESUELTOS ══ --}}
<div class="rep-table-wrap">
    <div class="rep-table-header no-print">
        <span><i class="bi bi-check2-circle me-2"></i>Tickets Resueltos</span>
        <span style="color:rgba(255,255,255,.7); font-size:.75rem;">{{ count($ticketsResueltos) }} tickets</span>
    </div>
    <div class="table-responsive">
        <table class="table table-sm mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Título</th>
                    <th>Técnico</th>
                    <th>Área</th>
                    <th class="text-center">Fecha Apertura</th>
                    <th class="text-center">Fecha Cierre</th>
                    <th class="text-center">Tiempo (h)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ticketsResueltos as $tr)
                <tr>
                    <td class="fw-bold" style="color:#d97706;">#{{ $tr['id_ticket'] }}</td>
                    <td style="max-width:220px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $tr['titulo'] }}</td>
                    <td style="font-size:.82rem;">{{ $tr['tecnico'] }}</td>
                    <td style="font-size:.82rem; color:#64748b;">{{ $tr['area'] }}</td>
                    <td class="text-center" style="font-size:.82rem; white-space:nowrap;">
                        {{ \Carbon\Carbon::parse($tr['fecha_apertura'])->format('d/m/Y') }}
                    </td>
                    <td class="text-center" style="font-size:.82rem; white-space:nowrap;">
                        {{ \Carbon\Carbon::parse($tr['fecha_cierre'])->format('d/m/Y') }}
                    </td>
                    <td class="text-center" style="font-size:.82rem;">
                        {{ $tr['tiempo_horas'] !== null ? $tr['tiempo_horas'] . 'h' : '—' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-5">
                        <i class="bi bi-check2-circle d-block mb-2" style="font-size:2rem;"></i>
                        No hay tickets resueltos registrados
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Pie impresión --}}
<div id="print-footer" style="display:none; margin-top:20px; text-align:center; font-size:8pt; color:#94a3b8; border-top:1px solid #e2e8f0; padding-top:8px;">
    Universidad Politécnica de Texcoco &nbsp;·&nbsp; Sistema de Soporte TI &nbsp;·&nbsp; Documento generado automáticamente
</div>

</div>{{-- /print-area --}}

@push('scripts')
<script>
function imprimirReporte() {
    document.querySelector('.print-header').style.display = 'block';
    document.getElementById('print-footer').style.display = 'block';
    window.print();
    document.querySelector('.print-header').style.display = 'none';
    document.getElementById('print-footer').style.display = 'none';
}
</script>
<style>
@media print {
    .print-header { display: block !important; }
    #print-footer { display: block !important; }
    nav, .sidebar, header, footer, .no-print { display: none !important; }
    body { font-size: 10pt; }
    .rep-table-wrap { box-shadow: none; border: 1px solid #cbd5e1; }
    .rep-table-wrap thead th {
        background: #78350f !important;
        color: #fff !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    .rep-table-wrap tbody tr:nth-child(even) td {
        background: #fffbeb !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    .eff-bar, .eff-bar-fill, .eff-chip {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    @page { margin: 15mm 12mm; }
}
</style>
@endpush
@endsection
