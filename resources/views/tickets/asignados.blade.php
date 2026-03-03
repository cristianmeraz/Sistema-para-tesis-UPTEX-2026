@extends('layouts.app')

@section('title', 'Panel del Técnico')
@section('no_header_title', true)

@section('content')
<style>
    /* ══════ BANNER ══════ */
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
    .tec-banner-icon { width:54px; height:54px; border-radius:14px; background:rgba(255,255,255,.18); display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:1.5rem; color:#fff; position:relative; z-index:1; }
    .tec-banner-title { color:#fff; font-size:1.5rem; font-weight:700; margin:0; position:relative; z-index:1; }
    .tec-banner-sub   { color:rgba(255,255,255,.75); font-size:.88rem; margin:.1rem 0 0; position:relative; z-index:1; }

    /* ══════ SECCIÓN TÍTULO ══════ */
    .tec-section-title { font-size:.78rem; font-weight:800; color:#64748b; text-transform:uppercase; letter-spacing:.08em; margin-bottom:.9rem; display:flex; align-items:center; gap:.5rem; }
    .tec-section-title::after { content:''; flex:1; height:1px; background:#e2e8f0; }

    /* ══════ KPI CARDS ══════ */
    .kpi-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #e8edf5;
        box-shadow: 0 4px 16px rgba(0,0,0,.05);
        padding: 1.3rem 1.2rem 1.1rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: transform .2s, box-shadow .2s;
    }
    .kpi-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,.10); }
    .kpi-icon { width:50px; height:50px; border-radius:13px; display:flex; align-items:center; justify-content:center; font-size:1.35rem; flex-shrink:0; }
    .kpi-label { font-size:.75rem; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.05em; margin-bottom:.15rem; }
    .kpi-value { font-size:1.9rem; font-weight:800; line-height:1; }
    .kpi-sub   { font-size:.75rem; color:#94a3b8; margin-top:.1rem; }

    /* ══════ PRIORIDAD CHIPS MINI ══════ */
    .prio-row { display:flex; gap:.6rem; flex-wrap:wrap; margin-bottom:1.5rem; }
    .prio-chip { display:flex; align-items:center; gap:.5rem; background:#fff; border:1px solid #e2e8f0; border-radius:10px; padding:.55rem .9rem; flex:1; min-width:120px; box-shadow:0 2px 8px rgba(0,0,0,.04); }
    .prio-chip-dot { width:10px; height:10px; border-radius:50%; flex-shrink:0; }
    .prio-chip-label { font-size:.78rem; color:#64748b; font-weight:600; }
    .prio-chip-num { font-size:1.15rem; font-weight:800; margin-left:auto; }

    /* ══════ TABLA ══════ */
    .tec-table-wrap { background:#fff; border-radius:16px; border:1px solid #e8edf5; box-shadow:0 4px 16px rgba(0,0,0,.05); overflow:hidden; }
    .tec-table-header { padding:1rem 1.5rem; display:flex; align-items:center; gap:.6rem; border-bottom:1px solid #f1f5f9; }
    .tec-table-header-title { font-weight:700; color:#1e293b; font-size:1rem; }
    .tec-table-count { background:#f0fdf4; color:#16a34a; font-size:.75rem; font-weight:700; padding:.15rem .55rem; border-radius:20px; }
    .tec-table table { margin:0; }
    .tec-table thead th { background:#f8fafc; font-size:.78rem; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.04em; border-bottom:1px solid #e2e8f0; padding:.75rem 1rem; }
    .tec-table td { padding:.85rem 1rem; border-bottom:1px solid #f1f5f9; vertical-align:middle; font-size:.9rem; }
    .tec-table tr:last-child td { border-bottom:none; }
    .tec-table tr:hover td { background:#fafafa; }

    /* CHIPS ESTADO/PRIORIDAD */
    .chip { display:inline-block; padding:.22rem .65rem; border-radius:20px; font-size:.76rem; font-weight:700; }
    .chip-abierto   { background:#dbeafe; color:#1d4ed8; }
    .chip-pendiente { background:#fef9c3; color:#854d0e; }
    .chip-en_proceso { background:#e0f2fe; color:#0369a1; }
    .chip-resuelto  { background:#dcfce7; color:#15803d; }
    .chip-cerrado   { background:#f1f5f9; color:#475569; }
    .chip-baja      { background:#dcfce7; color:#15803d; }
    .chip-media     { background:#fef9c3; color:#854d0e; }
    .chip-alta      { background:#ffedd5; color:#9a3412; }
    .chip-alta   { background:#fee2e2; color:#dc2626; }

    .btn-gestionar { background:linear-gradient(135deg,#15803d,#16a34a); color:#fff; border:none; border-radius:8px; padding:.38rem .85rem; font-size:.82rem; font-weight:600; text-decoration:none; display:inline-flex; align-items:center; gap:.35rem; transition:filter .18s, transform .18s; box-shadow:0 3px 10px rgba(22,163,74,.28); }
    .btn-gestionar:hover { filter:brightness(1.08); transform:translateY(-1px); color:#fff; }

    /* ══════ REFRESH WIDGET ══════ */
    .refresh-badge { background:rgba(255,255,255,.12); font-size:.82rem; border-radius:20px; display:flex; align-items:center; gap:.5rem; padding:.4rem .85rem; color:#fff; white-space:nowrap; }
    .live-dot { width:8px; height:8px; border-radius:50%; background:#10B981; display:inline-block; animation:livePulse 2s ease-in-out infinite; }
    .btn-refresh { background:rgba(255,255,255,.92); border:none; border-radius:8px; padding:.42rem .9rem; font-size:.82rem; font-weight:700; color:#15803d; display:inline-flex; align-items:center; gap:.35rem; cursor:pointer; transition:background .18s; white-space:nowrap; }
    .btn-refresh:hover { background:#fff; }
    @keyframes livePulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.4;transform:scale(.85)} }

    @media(max-width:768px) {
        .tec-banner { padding:1.3rem 1.2rem; }
        .tec-banner-title { font-size:1.2rem; }
        .prio-chip { min-width:calc(50% - .3rem); }
    }
</style>

<div class="container-fluid">

    {{-- ══════ BANNER ══════ --}}
    <div class="tec-banner" style="justify-content:space-between;">
        <div style="display:flex;align-items:center;gap:1.2rem;position:relative;z-index:1;">
            <div class="tec-banner-icon"><i class="bi bi-person-workspace"></i></div>
            <div>
                <h1 class="tec-banner-title">Panel del Técnico</h1>
                <p class="tec-banner-sub">Gestiona tus tickets asignados y monitorea tu desempeño</p>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:.6rem;position:relative;z-index:1;flex-wrap:wrap;">
            <div class="refresh-badge" id="badgeUpdate">
                <span class="live-dot"></span>
                Actualizado: <strong id="lastUpdate">Ahora</strong>
            </div>
            <button class="btn-refresh" id="btnRefresh">
                <i class="bi bi-arrow-clockwise"></i> Actualizar
            </button>
        </div>
    </div>

    {{-- ══════ KPI CARDS ══════ --}}
    <div class="tec-section-title"><i class="bi bi-bar-chart-fill"></i> Resumen de tickets</div>
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="kpi-card">
                <div class="kpi-icon" style="background:#f0fdf4;"><i class="bi bi-folder2-open" style="color:#16a34a;"></i></div>
                <div>
                    <div class="kpi-label">Total</div>
                    <div class="kpi-value" style="color:#16a34a;">{{ $stats['totales'] }}</div>
                    <div class="kpi-sub">Asignados</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card">
                <div class="kpi-icon" style="background:#e0f2fe;"><i class="bi bi-gear-wide-connected" style="color:#0369a1;"></i></div>
                <div>
                    <div class="kpi-label">En proceso</div>
                    <div class="kpi-value" style="color:#0369a1;">{{ $stats['en_proceso'] }}</div>
                    <div class="kpi-sub">Activos</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card">
                <div class="kpi-icon" style="background:#fef9c3;"><i class="bi bi-pause-circle" style="color:#854d0e;"></i></div>
                <div>
                    <div class="kpi-label">Pendientes</div>
                    <div class="kpi-value" style="color:#854d0e;">{{ $stats['pendientes'] }}</div>
                    <div class="kpi-sub">En espera</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card">
                <div class="kpi-icon" style="background:#dcfce7;"><i class="bi bi-check2-all" style="color:#15803d;"></i></div>
                <div>
                    <div class="kpi-label">Resueltos</div>
                    <div class="kpi-value" style="color:#15803d;">{{ $stats['resueltos'] }}</div>
                    <div class="kpi-sub">Completados</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('tickets.index', ['prioridad_nombre' => 'Alta']) }}" class="kpi-card" style="text-decoration:none; border:1px solid #fecaca; background:linear-gradient(135deg,#fff5f5,#fff0f0);">
                <div class="kpi-icon" style="background:#fee2e2;"><i class="bi bi-exclamation-triangle-fill" style="color:#dc2626;"></i></div>
                <div>
                    <div class="kpi-label">Crítico</div>
                    <div class="kpi-value" style="color:#dc2626;">{{ $stats['critico'] }}</div>
                    <div class="kpi-sub">Alta prioridad</div>
                </div>
            </a>
        </div>
    </div>

    {{-- ══════ CARGA POR PRIORIDAD ══════ --}}
    <div class="tec-section-title"><i class="bi bi-exclamation-triangle"></i> Carga por prioridad</div>
    <div class="prio-row mb-4">
        <div class="prio-chip">
            <div class="prio-chip-dot" style="background:#0d9488;"></div>
            <span class="prio-chip-label">Baja</span>
            <span class="prio-chip-num" style="color:#0d9488;">{{ $stats['baja'] }}</span>
        </div>
        <div class="prio-chip">
            <div class="prio-chip-dot" style="background:#d97706;"></div>
            <span class="prio-chip-label">Media</span>
            <span class="prio-chip-num" style="color:#d97706;">{{ $stats['media'] }}</span>
        </div>
        <div class="prio-chip">
            <div class="prio-chip-dot" style="background:#dc2626;"></div>
            <span class="prio-chip-label">Alta</span>
            <span class="prio-chip-num" style="color:#dc2626;">{{ $stats['alta'] }}</span>
        </div>
        <div class="prio-chip" style="border-color:#fee2e2; background:#fff5f5;">
            <div class="prio-chip-dot" style="background:#991b1b; animation:livePulse 2s ease-in-out infinite;"></div>
            <span class="prio-chip-label" style="color:#991b1b;">Crítico</span>
            <span class="prio-chip-num" style="color:#991b1b;">{{ $stats['critico'] }}</span>
        </div>
    </div>

    {{-- ══════ TABLA DE TICKETS ACTIVOS ══════ --}}
    <div class="tec-table-wrap tec-table">
        <div class="tec-table-header">
            <i class="bi bi-list-task" style="color:#16a34a; font-size:1.1rem;"></i>
            <span class="tec-table-header-title">Tickets por Atender</span>
            <span class="tec-table-count">{{ count($tickets) }}</span>
        </div>

        @if(count($tickets) > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width:80px;">Folio</th>
                        <th>Título del Ticket</th>
                        <th>Prioridad</th>
                        <th>Estado</th>
                        <th>Fecha creación</th>
                        <th class="text-center" style="width:120px;">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tickets as $ticket)
                    @php
                        $prioNivel = strtolower(str_replace(
                            ['á','é','í','ó','ú','ü','ñ',' '],
                            ['a','e','i','o','u','u','n','_'],
                            $ticket->prioridad->nombre ?? 'media'
                        ));
                        $estadoTipo = str_replace(' ','_', strtolower($ticket->estado->tipo ?? 'abierto'));
                        $esCritico  = ($ticket->prioridad?->nombre === 'Alta')
                                   && $ticket->fecha_creacion
                                   && $ticket->fecha_creacion->lte(now()->subHour());
                    @endphp
                    <tr>
                        <td><strong class="text-muted">#{{ $ticket->id_ticket }}</strong></td>
                        <td>
                            <div class="fw-600 text-dark" style="font-weight:600;">
                                {{ $ticket->titulo }}
                                @if($esCritico)
                                <span style="display:inline-block; background:#dc2626; color:#fff; font-size:.65rem; font-weight:800; padding:.12rem .4rem; border-radius:4px; vertical-align:middle; text-transform:uppercase; letter-spacing:.04em; animation:livePulse 1.8s infinite;">CRÍTICO</span>
                                @endif
                            </div>
                            <small class="text-muted">{{ $ticket->usuario->nombre ?? 'N/A' }} {{ $ticket->usuario->apellido ?? '' }}</small>
                        </td>
                        <td><span class="chip chip-{{ $prioNivel }}">{{ $ticket->prioridad->nombre ?? 'N/A' }}</span></td>
                        <td><span class="chip chip-{{ $estadoTipo }}">{{ $ticket->estado->nombre ?? 'N/A' }}</span></td>
                        <td><small class="text-muted">{{ $ticket->fecha_creacion ? $ticket->fecha_creacion->format('d/m/Y H:i') : 'N/A' }}</small></td>
                        <td class="text-center">
                            <a href="{{ route('tickets.show', $ticket->id_ticket) }}" class="btn-gestionar">
                                <i class="bi bi-pencil-square"></i> Gestionar
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-5">
            <div style="width:70px;height:70px;border-radius:50%;background:#f0fdf4;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                <i class="bi bi-check2-all" style="font-size:2rem;color:#16a34a;"></i>
            </div>
            <h6 class="fw-700" style="color:#1e293b;">¡Todo al día!</h6>
            <p class="text-muted small mb-0">No tienes tickets pendientes por atender.</p>
        </div>
        @endif
    </div>

</div>

@push('scripts')
<script>
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
</script>
@endpush
@endsection