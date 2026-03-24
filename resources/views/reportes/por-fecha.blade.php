@extends('layouts.app')

@section('title', 'Reporte por Rango de Fechas')
@section('no_header_title', true)

@section('content')
<style>
/* BANNER */
.rep-banner {
    background: linear-gradient(135deg, #1e3a5f 0%, #1d4ed8 100%);
    border-radius: 18px; padding: 1.4rem 2rem; margin-bottom: 1.8rem;
    display: flex; align-items: center; gap: 1.2rem;
    position: relative; overflow: hidden;
    box-shadow: 0 8px 30px rgba(29,78,216,.25);
}
.rep-banner::before { content:''; position:absolute; top:-40px; right:-40px; width:180px; height:180px; border-radius:50%; background:rgba(255,255,255,.06); }
.rep-banner-logo { width:48px; height:48px; background:rgba(255,255,255,.92); border-radius:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0; padding:6px; }
.rep-banner-logo img { width:100%; height:100%; object-fit:contain; }
.rep-banner-title { color:#fff; font-size:1.3rem; font-weight:700; line-height:1.2; margin:0; }
.rep-banner-sub   { color:rgba(255,255,255,.72); font-size:.82rem; margin:.1rem 0 0; }

/* FILTRO */
.filtro-card { background:#fff; border-radius:16px; border:1px solid #e8edf5; box-shadow:0 2px 12px rgba(0,0,0,.05); padding:1.4rem 1.6rem; margin-bottom:1.6rem; }
.filtro-label { font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:#475569; margin-bottom:.4rem; display:block; }
.filtro-card .form-control { border-radius:10px; border:1.5px solid #e2e8f0; font-size:.9rem; padding:.55rem .9rem; }
.filtro-card .form-control:focus { border-color:#1d4ed8; box-shadow:0 0 0 3px rgba(29,78,216,.10); }
.btn-filtrar { background:linear-gradient(135deg,#1e3a5f,#1d4ed8); color:#fff; border:none; border-radius:10px; padding:.62rem 1.4rem; font-weight:600; font-size:.9rem; display:flex; align-items:center; gap:.5rem; box-shadow:0 4px 14px rgba(29,78,216,.28); transition:filter .18s,transform .18s; cursor:pointer; }
.btn-filtrar:hover { filter:brightness(1.1); transform:translateY(-1px); color:#fff; }
.btn-print { background:linear-gradient(135deg,#15803d,#16a34a); color:#fff; border:none; border-radius:10px; padding:.62rem 1.4rem; font-weight:600; font-size:.9rem; display:flex; align-items:center; gap:.5rem; box-shadow:0 4px 14px rgba(22,163,74,.28); transition:filter .18s,transform .18s; cursor:pointer; }
.btn-print:hover { filter:brightness(1.1); transform:translateY(-1px); color:#fff; }
.btn-volver { background:#f1f5f9; color:#475569; border:none; border-radius:10px; padding:.62rem 1.2rem; font-weight:600; font-size:.9rem; display:flex; align-items:center; gap:.5rem; transition:background .18s; text-decoration:none; }
.btn-volver:hover { background:#e2e8f0; color:#1e293b; }

/* ACCESOS RÁPIDOS */
.quick-btns { display:flex; flex-wrap:wrap; gap:.5rem; margin-top:1rem; padding-top:1rem; border-top:1px solid #f1f5f9; }
.quick-btns span { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#94a3b8; align-self:center; margin-right:.25rem; }
.quick-btn {
    font-size:.75rem; font-weight:600; padding:.3rem .75rem; border-radius:8px;
    border:1.5px solid #e2e8f0; background:#f8fafc; color:#475569; cursor:pointer;
    transition:all .15s; white-space:nowrap; line-height:1.4;
}
.quick-btn:hover, .quick-btn.active { background:#1d4ed8; border-color:#1d4ed8; color:#fff; }

/* RANGO ACTIVO */
.rango-activo {
    display:inline-flex; align-items:center; gap:.5rem;
    background:#eff6ff; border:1.5px solid #bfdbfe; border-radius:10px;
    padding:.45rem 1rem; font-size:.82rem; color:#1d4ed8; font-weight:600;
    margin-bottom:1.2rem;
}
.rango-activo i { color:#3b82f6; }

/* KPI CARDS */
.kpi-row { display:flex; gap:.9rem; flex-wrap:wrap; margin-bottom:1.6rem; }
.kpi-box { flex:1; min-width:130px; background:#fff; border-radius:14px; border:1px solid #e8edf5; box-shadow:0 2px 8px rgba(0,0,0,.04); padding:1rem 1.2rem; text-align:center; }
.kpi-box .kpi-val { font-size:2rem; font-weight:800; line-height:1; }
.kpi-box .kpi-lbl { font-size:.75rem; color:#64748b; margin-top:.25rem; }

/* TABLA */
.rep-table-wrap { background:#fff; border-radius:16px; border:1px solid #e8edf5; box-shadow:0 2px 12px rgba(0,0,0,.04); overflow:hidden; }
.rep-table-header { background:linear-gradient(135deg,#1e3a5f,#1d4ed8); padding:.9rem 1.4rem; display:flex; align-items:center; justify-content:space-between; }
.rep-table-header span { color:#fff; font-size:.82rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; }
.rep-table-wrap table { margin:0; font-size:.85rem; }
.rep-table-wrap thead th { background:#f8fafc; color:#475569; font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em; padding:.65rem 1rem; border-bottom:2px solid #e2e8f0; white-space:nowrap; }
.rep-table-wrap tbody td { padding:.7rem 1rem; color:#334155; border-bottom:1px solid #f1f5f9; vertical-align:middle; }
.rep-table-wrap tbody tr:last-child td { border-bottom:none; }
.rep-table-wrap tbody tr:hover td { background:#f8fafc; }

/* SEPARADOR DE DÍA */
.dia-row td { background:linear-gradient(90deg,#eff6ff,#f0f9ff) !important; border-bottom:1px solid #bfdbfe !important; border-top:2px solid #bfdbfe !important; padding:.5rem 1rem !important; }
.dia-row:hover td { background:linear-gradient(90deg,#eff6ff,#f0f9ff) !important; }
.dia-label { font-size:.8rem; font-weight:700; color:#1d4ed8; display:flex; align-items:center; gap:.5rem; }
.dia-badge { font-size:.68rem; font-weight:600; padding:.18rem .55rem; border-radius:20px; background:#dbeafe; color:#1d4ed8; }

/* badges estado/prio */
.badge-prio-alta  { background:#fee2e2; color:#b91c1c; font-size:.72rem; font-weight:700; padding:.25rem .6rem; border-radius:20px; }
.badge-prio-media { background:#fef3c7; color:#b45309; font-size:.72rem; font-weight:700; padding:.25rem .6rem; border-radius:20px; }
.badge-prio-baja  { background:#dcfce7; color:#15803d; font-size:.72rem; font-weight:700; padding:.25rem .6rem; border-radius:20px; }
.badge-est-abierto   { background:#dbeafe; color:#1d4ed8; font-size:.72rem; font-weight:700; padding:.25rem .6rem; border-radius:20px; }
.badge-est-pendiente { background:#fef9c3; color:#b45309; font-size:.72rem; font-weight:700; padding:.25rem .6rem; border-radius:20px; }
.badge-est-en_proceso { background:#fce7f3; color:#9d174d; font-size:.72rem; font-weight:700; padding:.25rem .6rem; border-radius:20px; }
.badge-est-resuelto  { background:#dcfce7; color:#15803d; font-size:.72rem; font-weight:700; padding:.25rem .6rem; border-radius:20px; }
.badge-est-cerrado   { background:#f1f5f9; color:#475569; font-size:.72rem; font-weight:700; padding:.25rem .6rem; border-radius:20px; }

/* PRINT */
@media print {
    body * { visibility: hidden; }
    #print-area, #print-area * { visibility: visible; }
    #print-area { position: fixed; left: 0; top: 0; width: 100%; }
    .no-print { display: none !important; }
    .print-header { display: block !important; }
    #print-footer  { display: block !important; }
    nav, .sidebar, header, footer, .no-print { display: none !important; }
    body { font-size: 10pt; }
    .rep-table-wrap { box-shadow: none; border: 1px solid #cbd5e1; }
    .rep-table-wrap thead th { background:#1e3a5f !important; color:#fff !important; -webkit-print-color-adjust:exact; print-color-adjust:exact; }
    .rep-table-wrap tbody tr:nth-child(even) td { background:#f8fafc !important; -webkit-print-color-adjust:exact; print-color-adjust:exact; }
    .dia-row td { -webkit-print-color-adjust:exact; print-color-adjust:exact; }
    .badge-prio-alta,.badge-prio-media,.badge-prio-baja,
    .badge-est-abierto,.badge-est-pendiente,.badge-est-en_proceso,
    .badge-est-resuelto,.badge-est-cerrado { -webkit-print-color-adjust:exact; print-color-adjust:exact; }
    @page { margin: 15mm 12mm; }
}
</style>

<div id="print-area">

{{-- Cabecera de impresión --}}
<div class="print-header" style="display:none;">
    <div style="display:flex;align-items:center;gap:16px;border-bottom:3px solid #1d4ed8;padding-bottom:14px;margin-bottom:20px;">
        <img src="{{ asset('images/logo-uptex.png') }}" alt="UPTEX" style="width:60px;height:60px;object-fit:contain;">
        <div>
            <div style="font-size:14pt;font-weight:700;color:#1e3a5f;">Universidad Politécnica de Texcoco</div>
            <div style="font-size:10pt;color:#475569;">Sistema de Gestión de Tickets de Soporte TI</div>
            <div style="font-size:9pt;color:#64748b;margin-top:2px;">
                Reporte por Rango de Fechas &nbsp;·&nbsp; Generado el {{ now()->format('d/m/Y H:i') }}
            </div>
        </div>
    </div>
    <div style="display:flex;gap:16px;margin-bottom:16px;">
        <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;padding:8px 14px;">
            <span style="font-size:8pt;font-weight:700;text-transform:uppercase;color:#1d4ed8;">Período solicitado</span><br>
            <span style="font-size:11pt;font-weight:800;color:#1e3a5f;">
                {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}
            </span>
        </div>
        <div style="background:#f1f5f9;border-radius:8px;padding:8px 14px;">
            <span style="font-size:8pt;font-weight:700;text-transform:uppercase;color:#64748b;">Total Tickets</span><br>
            <span style="font-size:10pt;font-weight:700;color:#1e3a5f;">{{ $resumen['total'] }}</span>
        </div>
        <div style="background:#dcfce7;border-radius:8px;padding:8px 14px;">
            <span style="font-size:8pt;font-weight:700;text-transform:uppercase;color:#15803d;">Efectividad</span><br>
            @php $ef = $resumen['total'] > 0 ? round($resumen['cerrados'] / $resumen['total'] * 100) : 0; @endphp
            <span style="font-size:10pt;font-weight:700;color:#15803d;">{{ $ef }}%</span>
        </div>
    </div>
</div>

{{-- BANNER --}}
<div class="rep-banner no-print">
    <div class="rep-banner-logo">
        <img src="{{ asset('images/logo-uptex.png') }}" alt="UPTEX">
    </div>
    <div class="flex-grow-1">
        <h1 class="rep-banner-title">Tickets por Rango de Fechas</h1>
        <p class="rep-banner-sub">
            Muestra los tickets creados exactamente entre las fechas que el administrador seleccione — UPTEX
        </p>
    </div>
    <a href="{{ route('reportes.index') }}" class="btn-volver no-print">
        <i class="bi bi-arrow-left"></i> Estadísticas
    </a>
</div>

{{-- FILTRO --}}
<div class="filtro-card no-print">
    <form method="GET" action="{{ route('reportes.por-fecha') }}" id="formFiltro">
        <div class="row g-3 align-items-end">
            <div class="col-12 col-sm-4">
                <label class="filtro-label"><i class="bi bi-calendar-event me-1"></i>Fecha inicio</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control"
                       value="{{ $fechaInicio }}" max="{{ now()->format('Y-m-d') }}">
            </div>
            <div class="col-12 col-sm-4">
                <label class="filtro-label"><i class="bi bi-calendar-check me-1"></i>Fecha fin</label>
                <input type="date" id="fecha_fin" name="fecha_fin" class="form-control"
                       value="{{ $fechaFin }}" max="{{ now()->format('Y-m-d') }}">
            </div>
            <div class="col-12 col-sm-4">
                <div class="d-flex gap-2 flex-wrap">
                    <button type="submit" class="btn-filtrar">
                        <i class="bi bi-search"></i> Ver tickets
                    </button>
                    <button type="button" class="btn-print" onclick="imprimirReporte()">
                        <i class="bi bi-download"></i> PDF
                    </button>
                </div>
            </div>
        </div>

        {{-- ACCESOS RÁPIDOS --}}
        <div class="quick-btns">
            <span>Acceso rápido:</span>
            <button type="button" class="quick-btn" onclick="setRango('hoy')">Hoy</button>
            <button type="button" class="quick-btn" onclick="setRango('ayer')">Ayer</button>
            <button type="button" class="quick-btn" onclick="setRango('7dias')" id="qb-7dias">Últimos 7 días</button>
            <button type="button" class="quick-btn" onclick="setRango('15dias')">Últimos 15 días</button>
            <button type="button" class="quick-btn" onclick="setRango('30dias')">Últimos 30 días</button>
            <button type="button" class="quick-btn" onclick="setRango('mes_actual')">Este mes</button>
            <button type="button" class="quick-btn" onclick="setRango('mes_anterior')">Mes anterior</button>
        </div>
    </form>
</div>

{{-- RANGO ACTIVO --}}
<div class="rango-activo no-print">
    <i class="bi bi-calendar-range-fill"></i>
    Mostrando tickets del
    <strong>{{ \Carbon\Carbon::parse($fechaInicio)->locale('es')->isoFormat('D [de] MMMM YYYY') }}</strong>
    al
    <strong>{{ \Carbon\Carbon::parse($fechaFin)->locale('es')->isoFormat('D [de] MMMM YYYY') }}</strong>
    @php $dias = \Carbon\Carbon::parse($fechaInicio)->diffInDays(\Carbon\Carbon::parse($fechaFin)) + 1; @endphp
    <span style="background:#dbeafe;color:#1d4ed8;font-size:.72rem;font-weight:600;padding:.15rem .55rem;border-radius:20px;">{{ $dias }} {{ $dias === 1 ? 'día' : 'días' }}</span>
</div>

{{-- KPIs --}}
@php $efectividad = $resumen['total'] > 0 ? round($resumen['cerrados'] / $resumen['total'] * 100) : 0; @endphp
<div class="kpi-row no-print">
    <div class="kpi-box">
        <div class="kpi-val" style="color:#1d4ed8;">{{ $resumen['total'] }}</div>
        <div class="kpi-lbl">Total</div>
    </div>
    <div class="kpi-box">
        <div class="kpi-val" style="color:#d97706;">{{ $resumen['abiertos'] }}</div>
        <div class="kpi-lbl">Abiertos / Pendientes</div>
    </div>
    <div class="kpi-box">
        <div class="kpi-val" style="color:#9d174d;">{{ $resumen['en_proceso'] }}</div>
        <div class="kpi-lbl">En Proceso</div>
    </div>
    <div class="kpi-box">
        <div class="kpi-val" style="color:#15803d;">{{ $resumen['cerrados'] }}</div>
        <div class="kpi-lbl">Resueltos / Cerrados</div>
    </div>
    <div class="kpi-box">
        <div class="kpi-val" style="color:{{ $efectividad >= 70 ? '#15803d' : ($efectividad >= 40 ? '#b45309' : '#b91c1c') }};">{{ $efectividad }}%</div>
        <div class="kpi-lbl">Efectividad</div>
    </div>
</div>

{{-- TABLA AGRUPADA POR DÍA --}}
@php
    $ticketsByDay = collect($tickets)->groupBy(function($t) {
        return \Carbon\Carbon::parse($t['fecha_creacion'])->format('Y-m-d');
    })->sortKeysDesc();
@endphp

<div class="rep-table-wrap">
    <div class="rep-table-header no-print">
        <span>
            <i class="bi bi-table me-2"></i>Detalle por Día
            &nbsp;·&nbsp;
            {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}
        </span>
        <span style="color:rgba(255,255,255,.7);font-size:.75rem;">{{ count($tickets) }} ticket(s)</span>
    </div>
    <div class="table-responsive">
        <table class="table table-sm mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Título</th>
                    <th>Solicitante</th>
                    <th>Área</th>
                    <th>Técnico</th>
                    <th>Prioridad</th>
                    <th>Estado</th>
                    <th>Hora creación</th>
                    <th>Fecha cierre</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ticketsByDay as $dia => $grupoTickets)
                {{-- Separador de día --}}
                <tr class="dia-row">
                    <td colspan="9">
                        <div class="dia-label">
                            <i class="bi bi-calendar3"></i>
                            {{ \Carbon\Carbon::parse($dia)->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
                            <span class="dia-badge">{{ $grupoTickets->count() }} ticket(s)</span>
                        </div>
                    </td>
                </tr>
                {{-- Tickets del día --}}
                @foreach($grupoTickets as $ticket)
                @php
                    $prioCls = match(strtolower($ticket['prioridad']['nombre'] ?? '')) {
                        'alta'  => 'badge-prio-alta',
                        'media' => 'badge-prio-media',
                        default => 'badge-prio-baja',
                    };
                    $estTipo = $ticket['estado']['tipo'] ?? 'abierto';
                    $estCls  = 'badge-est-' . $estTipo;
                @endphp
                <tr>
                    <td class="fw-semibold" style="color:#1d4ed8;">#{{ $ticket['id_ticket'] }}</td>
                    <td style="max-width:200px;">{{ Str::limit($ticket['titulo'], 45) }}</td>
                    <td>{{ $ticket['usuario']['nombre_completo'] ?? 'N/A' }}</td>
                    <td>{{ $ticket['area']['nombre'] ?? 'N/A' }}</td>
                    <td style="font-size:.8rem;">{{ $ticket['tecnico_asignado'] ?? 'Sin asignar' }}</td>
                    <td><span class="{{ $prioCls }}">{{ $ticket['prioridad']['nombre'] ?? 'N/A' }}</span></td>
                    <td><span class="{{ $estCls }}">{{ $ticket['estado']['nombre'] ?? 'N/A' }}</span></td>
                    <td style="font-size:.8rem;white-space:nowrap;">
                        {{ $ticket['fecha_creacion'] ? \Carbon\Carbon::parse($ticket['fecha_creacion'])->format('H:i') : '—' }}
                    </td>
                    <td style="font-size:.8rem;white-space:nowrap;">
                        {{ $ticket['fecha_cierre'] ? \Carbon\Carbon::parse($ticket['fecha_cierre'])->format('d/m/Y') : '—' }}
                    </td>
                </tr>
                @endforeach
                @empty
                <tr>
                    <td colspan="9" class="text-center text-muted py-5">
                        <i class="bi bi-inbox d-block mb-2" style="font-size:2rem;"></i>
                        No hay tickets en el período seleccionado<br>
                        <small>Ajusta las fechas del filtro y vuelve a buscar.</small>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Pie de impresión --}}
<div id="print-footer" style="display:none;" class="mt-4 text-center">
    <hr style="border-color:#e2e8f0;">
    <span style="font-size:8pt;color:#94a3b8;">
        Universidad Politécnica de Texcoco &nbsp;·&nbsp; Sistema de Soporte TI &nbsp;·&nbsp; Documento generado automáticamente
    </span>
</div>

</div>{{-- /print-area --}}

@push('scripts')
<script>
/* ── Accesos rápidos de fecha ── */
function setRango(rango) {
    var hoy   = new Date();
    var ini   = document.getElementById('fecha_inicio');
    var fin   = document.getElementById('fecha_fin');
    var fmt   = function(d) {
        return d.getFullYear() + '-' +
               String(d.getMonth()+1).padStart(2,'0') + '-' +
               String(d.getDate()).padStart(2,'0');
    };

    document.querySelectorAll('.quick-btn').forEach(function(b){ b.classList.remove('active'); });
    event.currentTarget.classList.add('active');

    if (rango === 'hoy') {
        ini.value = fmt(hoy); fin.value = fmt(hoy);
    } else if (rango === 'ayer') {
        var ayer = new Date(hoy); ayer.setDate(hoy.getDate() - 1);
        ini.value = fmt(ayer); fin.value = fmt(ayer);
    } else if (rango === '7dias') {
        var d7 = new Date(hoy); d7.setDate(hoy.getDate() - 6);
        ini.value = fmt(d7); fin.value = fmt(hoy);
    } else if (rango === '15dias') {
        var d15 = new Date(hoy); d15.setDate(hoy.getDate() - 14);
        ini.value = fmt(d15); fin.value = fmt(hoy);
    } else if (rango === '30dias') {
        var d30 = new Date(hoy); d30.setDate(hoy.getDate() - 29);
        ini.value = fmt(d30); fin.value = fmt(hoy);
    } else if (rango === 'mes_actual') {
        var mesIni = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
        ini.value = fmt(mesIni); fin.value = fmt(hoy);
    } else if (rango === 'mes_anterior') {
        var mesPIni = new Date(hoy.getFullYear(), hoy.getMonth() - 1, 1);
        var mesPFin = new Date(hoy.getFullYear(), hoy.getMonth(), 0);
        ini.value = fmt(mesPIni); fin.value = fmt(mesPFin);
    }
    document.getElementById('formFiltro').submit();
}

/* ── Imprimir ── */
function imprimirReporte() {
    document.querySelector('.print-header').style.display = 'block';
    document.getElementById('print-footer').style.display = 'block';
    window.print();
    document.querySelector('.print-header').style.display = 'none';
    document.getElementById('print-footer').style.display = 'none';
}
</script>
@endpush
@endsection
