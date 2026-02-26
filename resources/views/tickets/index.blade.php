@extends('layouts.app')

@section('title', 'Tickets - Sistema de Tickets')

@section('content')
<link rel="stylesheet" href="{{ asset('css/tickets-priority.css') }}?v={{ filemtime(public_path('css/tickets-priority.css')) }}">
<link rel="stylesheet" href="{{ asset('css/comments-premium.css') }}">
<script src="{{ asset('js/auto-refresh-tickets-enhanced.js') }}"></script>

<style>
    body { background: #f3f4f6; }
    .page-container { background: #f3f4f6; padding: 0 0 2.5rem; }

    /* ── Cabecera ───────────────────────────────────── */
    .page-header {
        background: linear-gradient(135deg, #1e3a5f 0%, #1d4ed8 100%);
        padding: 2rem 2.5rem;
        margin-bottom: 1.8rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .page-header h1 { color: #fff; font-size: 1.7rem; font-weight: 700; margin: 0; }
    .page-header p  { color: rgba(255,255,255,.75); font-size: .93rem; margin: .25rem 0 0; }
    .btn-header-white {
        background: rgba(255,255,255,.18);
        border: 1.5px solid rgba(255,255,255,.55);
        color: #fff;
        padding: .6rem 1.4rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: .9rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        transition: background .2s;
        cursor: pointer;
        white-space: nowrap;
    }
    .btn-header-white:hover { background: rgba(255,255,255,.32); color: #fff; }

    /* ── KPI cards ──────────────────────────────────── */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    @media(max-width:900px){ .kpi-grid{ grid-template-columns: repeat(2,1fr); } }
    @media(max-width:540px){ .kpi-grid{ grid-template-columns: 1fr; } }
    a.kpi-card { text-decoration: none !important; color: inherit !important; display: block; }
    .kpi-card {
        background: #fff;
        border-radius: 12px;
        padding: 1.3rem 1.5rem;
        border-left: 4px solid var(--kpi-color, #1e3a5f);
        box-shadow: 0 2px 8px rgba(30,58,95,.08);
        transition: transform .2s, box-shadow .2s;
        cursor: pointer;
        overflow: hidden;
    }
    .kpi-card:hover { transform: translateY(-3px); box-shadow: 0 6px 18px rgba(30,58,95,.15); }
    .kpi-number { font-size: 2rem; font-weight: 800; color: var(--kpi-color, #1e3a5f); line-height: 1; }
    .kpi-label  { font-size: .8rem; font-weight: 600; color: #6b7280; margin-top: .3rem; text-transform: none; }
    .kpi-icon   { font-size: 1.6rem; opacity: .18; float: right; margin-top: -.3rem; color: var(--kpi-color, #1e3a5f); }

    /* ── Filtros ────────────────────────────────────── */
    .filter-box {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 1.3rem 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 1px 4px rgba(30,58,95,.05);
    }
    .filter-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
        gap: .9rem;
        align-items: flex-end;
    }
    .filter-group { display: flex; flex-direction: column; }
    .filter-label { font-weight: 600; color: #374151; font-size: .85rem; margin-bottom: .4rem; }
    .form-select {
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: .6rem .9rem;
        font-size: .9rem;
        background: #fff;
        color: #374151;
        transition: border-color .2s, box-shadow .2s;
    }
    .form-select:focus { border-color: #1d4ed8; box-shadow: 0 0 0 3px rgba(29,78,216,.1); outline: none; }
    .btn-filter {
        background: #1e3a5f; color: #fff; border: none;
        padding: .6rem 1.2rem; border-radius: 8px; font-weight: 600; font-size: .87rem;
        display: inline-flex; align-items: center; gap: .4rem; cursor: pointer;
        text-decoration: none; transition: background .2s;
    }
    .btn-filter:hover { background: #1d4ed8; color: #fff; }
    .btn-filter-clear {
        background: #6b7280; color: #fff; border: none;
        padding: .6rem 1.2rem; border-radius: 8px; font-weight: 600; font-size: .87rem;
        display: inline-flex; align-items: center; gap: .4rem;
        cursor: pointer; text-decoration: none; transition: background .2s;
    }
    .btn-filter-clear:hover { background: #4b5563; color: #fff; }

    /* ── Tabla ──────────────────────────────────────── */
    .table-wrapper {
        background: #fff;
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 1rem;
        box-shadow: 0 2px 8px rgba(30,58,95,.07);
    }
    .table { width: 100%; margin: 0; }
    .table thead { background: #1e3a5f; }
    .table thead th {
        padding: 1rem 1.1rem;
        font-weight: 700;
        font-size: .82rem;
        border: none;
        text-align: left;
        color: #fff;
        text-transform: none;
        letter-spacing: .02em;
    }
    .table tbody tr { border-bottom: 1px solid #f3f4f6; transition: background .15s; }
    .table tbody tr:last-child { border-bottom: none; }
    /* El hover por prioridad se gestiona en tickets-priority.css */
    .table tbody td { padding: .9rem 1.1rem; font-size: .9rem; color: #374151; }

    .ticket-id   { font-weight: 700; color: #1e3a5f; font-size: .9rem; }
    .ticket-title {
        color: #1f2937; text-decoration: none; font-weight: 500; font-size: .9rem;
    }
    .ticket-title:hover { color: #1d4ed8; text-decoration: underline; }

    .badge { display: inline-block; padding: .3rem .7rem; border-radius: 14px; font-size: .75rem; font-weight: 700; }
    .tech-name       { font-weight: 500; font-size: .88rem; }
    .tech-unassigned { color: #9ca3af; font-size: .85rem; }

    .btn-action {
        background: #1e3a5f; border: none; color: #fff;
        padding: .42rem .9rem; border-radius: 6px; font-weight: 600; font-size: .78rem;
        text-decoration: none; transition: background .2s, transform .15s;
        display: inline-block; cursor: pointer;
    }
    .btn-action:hover { background: #1d4ed8; transform: translateY(-1px); color: #fff; }

    .empty-state { text-align: center; padding: 3.5rem 2rem; }
    .empty-icon  { font-size: 3rem; color: #d1d5db; margin-bottom: 1rem; }
    .empty-text  { color: #9ca3af; font-size: 1.05rem; margin-bottom: 1.5rem; }

    /* ── Paginacion ─────────────────────────────────── */
    .pag-wrap {
        display: flex; justify-content: center; gap: .4rem;
        flex-wrap: wrap; margin-top: 1.2rem;
    }
    .pag-wrap a, .pag-wrap span {
        padding: .45rem .8rem; border-radius: 7px; font-size: .83rem;
        text-decoration: none; transition: all .2s; border: 1px solid #e5e7eb;
    }
    .pag-wrap a  { background: #fff; color: #1e3a5f; }
    .pag-wrap a:hover { background: #1e3a5f; color: #fff; border-color: #1e3a5f; }
    .pag-wrap .pag-active { background: #1e3a5f; color: #fff; border-color: #1e3a5f; font-weight: 700; }
    .pag-wrap .pag-disabled { color: #9ca3af; background: #f9fafb; }
</style>

<div class="page-container" data-user-role="{{ session('usuario_rol') }}">

    {{-- ── CABECERA ─────────────────────────────── --}}
    <div class="page-header">
        <div>
            <h1><i class="bi bi-ticket-detailed" style="margin-right:.5rem;"></i>Gestion de Tickets</h1>
            <p>Ver y administrar todos los tickets del sistema</p>
        </div>
        @if(session('usuario_rol') === 'Administrador')
        <a href="{{ route('tickets.create') }}" class="btn-header-white">
            <i class="bi bi-plus-circle"></i> Nuevo Ticket
        </a>
        @endif
    </div>

    <div class="container-fluid">

        {{-- ── KPI CARDS ────────────────────────── --}}
        @php $ts = $ticketStats ?? ['abiertos'=>0,'en_proceso'=>0,'criticos'=>0,'total'=>0]; @endphp
        <div class="kpi-grid">

            <a href="{{ route('tickets.index', ['tipo'=>'abierto']) }}" class="kpi-card" style="--kpi-color:#1e3a5f;">
                <div class="kpi-icon"><i class="bi bi-folder2-open"></i></div>
                <div class="kpi-number">{{ $ts['abiertos'] }}</div>
                <div class="kpi-label">Abiertos</div>
            </a>

            <a href="{{ route('tickets.index', ['tipo'=>'en_proceso']) }}" class="kpi-card" style="--kpi-color:#0369a1;">
                <div class="kpi-icon"><i class="bi bi-gear-fill"></i></div>
                <div class="kpi-number">{{ $ts['en_proceso'] }}</div>
                <div class="kpi-label">En Proceso</div>
            </a>

            <a href="{{ route('tickets.index') . '?prioridad_nombre=Cr' . rawurlencode("\xc3\xad") . 'tica' }}" class="kpi-card" style="--kpi-color:#dc2626;">
                <div class="kpi-icon"><i class="bi bi-exclamation-triangle-fill"></i></div>
                <div class="kpi-number">{{ $ts['criticos'] }}</div>
                <div class="kpi-label">Criticos</div>
            </a>

            <a href="{{ route('tickets.index') }}" class="kpi-card" style="--kpi-color:#7c3aed;">
                <div class="kpi-icon"><i class="bi bi-collection-fill"></i></div>
                <div class="kpi-number">{{ $ts['total'] }}</div>
                <div class="kpi-label">Total</div>
            </a>

        </div>

        {{-- ── BANNER FILTROS ACTIVOS ───────────── --}}
        @php
            $filtrosActivos = [];
            if (request('tipo')) {
                $tipoLabels = [
                    'abierto'    => 'Abierto',
                    'en_proceso' => 'En Proceso',
                    'resuelto'   => 'Resuelto',
                    'pendiente'  => 'Pendiente',
                    'cerrado'    => 'Cerrado',
                ];
                $filtrosActivos[] = 'Estado: ' . ($tipoLabels[request('tipo')] ?? request('tipo'));
            }
            if (request('estado_id')) {
                $enObj = collect($estados ?? [])->firstWhere('id_estado', (int) request('estado_id'));
                if ($enObj) $filtrosActivos[] = 'Estado: ' . $enObj['nombre'];
            }
            if (request('prioridad_nombre')) {
                $filtrosActivos[] = 'Prioridad: ' . request('prioridad_nombre');
            }
            if (request('prioridad_id')) {
                $pObj = collect($prioridades ?? [])->firstWhere('id_prioridad', (int) request('prioridad_id'));
                if ($pObj) $filtrosActivos[] = 'Prioridad: ' . $pObj['nombre'];
            }
            if (request('area_id')) {
                $aObj = collect($areas ?? [])->firstWhere('id_area', (int) request('area_id'));
                if ($aObj) $filtrosActivos[] = 'Depto: ' . $aObj['nombre'];
            }
            if (request('sin_tecnico') === '1') {
                $filtrosActivos[] = 'Sin tecnico asignado';
            }
        @endphp

        @if(count($filtrosActivos) > 0)
        <div style="background:#eff6ff; border:1.5px solid #bfdbfe; border-radius:10px; padding:.75rem 1.2rem; margin-bottom:1rem; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.5rem;">
            <div style="display:flex; align-items:center; gap:.6rem; flex-wrap:wrap;">
                <i class="bi bi-funnel-fill" style="color:#2563eb;"></i>
                <span style="font-weight:600; color:#1e3a5f; font-size:.9rem;">Filtrando por:</span>
                @foreach($filtrosActivos as $f)
                <span style="background:#2563eb; color:#fff; padding:.25rem .75rem; border-radius:20px; font-size:.82rem; font-weight:600;">{{ $f }}</span>
                @endforeach
            </div>
            <a href="{{ route('tickets.index') }}"
               style="color:#dc2626; font-size:.85rem; font-weight:600; text-decoration:none; display:flex; align-items:center; gap:.3rem;">
                <i class="bi bi-x-circle-fill"></i> Quitar filtros
            </a>
        </div>
        @endif

        {{-- ── FILTROS ──────────────────────────── --}}
        <div class="filter-box">
            <form method="GET" action="{{ route('tickets.index') }}" id="filterForm">
                @if(request('tipo') && !request('estado_id'))
                <input type="hidden" name="tipo" value="{{ request('tipo') }}">
                @endif
                @if(request('prioridad_nombre') && !request('prioridad_id'))
                <input type="hidden" name="prioridad_nombre" value="{{ request('prioridad_nombre') }}">
                @endif
                <div class="filter-row">
                    <div class="filter-group">
                        <label class="filter-label">Estado</label>
                        <select name="estado_id" class="form-select" onchange="this.form.querySelector('[name=tipo]') && (this.form.querySelector('[name=tipo]').remove())">
                            <option value="">Todos</option>
                            @foreach($estados ?? [] as $estado)
                            <option value="{{ $estado['id_estado'] }}" {{ request('estado_id') == $estado['id_estado'] ? 'selected' : '' }}>
                                {{ $estado['nombre'] }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-group">
                        <label class="filter-label">Prioridad</label>
                        <select name="prioridad_id" class="form-select">
                            <option value="">Todas</option>
                            @foreach($prioridades ?? [] as $prioridad)
                            <option value="{{ $prioridad['id_prioridad'] }}" {{ request('prioridad_id') == $prioridad['id_prioridad'] ? 'selected' : '' }}>
                                {{ $prioridad['nombre'] }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-group">
                        <label class="filter-label">Departamento</label>
                        <select name="area_id" class="form-select">
                            <option value="">Todos</option>
                            @foreach($areas ?? [] as $area)
                            <option value="{{ $area['id_area'] }}" {{ request('area_id') == $area['id_area'] ? 'selected' : '' }}>
                                {{ $area['nombre'] }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-group">
                        <label class="filter-label">Tecnico Asignado</label>
                        <select name="sin_tecnico" class="form-select">
                            <option value="">Todos</option>
                            <option value="1" {{ request('sin_tecnico') === '1' ? 'selected' : '' }}>Sin tecnico</option>
                        </select>
                    </div>

                    <div class="filter-group" style="display:flex; flex-direction:column; gap:.45rem;">
                        <label class="filter-label">&nbsp;</label>
                        <button type="submit" class="btn-filter">
                            <i class="bi bi-search"></i> Filtrar
                        </button>
                        <a href="{{ route('tickets.index') }}" class="btn-filter-clear">
                            <i class="bi bi-x-lg"></i> Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- ── TABLA ───────────────────────────── --}}
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Titulo</th>
                        <th>Usuario</th>
                        <th>Departamento</th>
                        <th>Prioridad</th>
                        <th>Estado</th>
                        <th>Tecnico</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                    @php
                        $pc = strtolower($ticket['prioridad']['nombre'] ?? 'media');
                        $pc = str_replace(['á','é','í','ó','ú','ü','ñ'], ['a','e','i','o','u','u','n'], $pc);
                        $pc = str_replace(' ', '_', $pc);
                    @endphp
                    <tr class="row-prioridad-{{ $pc }}">
                        <td><span class="ticket-id">#{{ $ticket['id_ticket'] }}</span></td>
                        <td>
                            <a href="{{ route('tickets.show', $ticket['id_ticket']) }}" class="ticket-title">
                                {{ Str::limit($ticket['titulo'], 45) }}
                            </a>
                        </td>
                        <td>{{ $ticket['usuario']['nombre_completo'] ?? 'N/A' }}</td>
                        <td>{{ $ticket['area']['nombre'] ?? 'N/A' }}</td>
                        <td>
                            <span class="badge badge-prioridad-{{ $pc }}">
                                {{ $ticket['prioridad']['nombre'] ?? 'N/A' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-estado-{{ $ticket['estado']['tipo'] ?? 'abierto' }}">
                                {{ $ticket['estado']['nombre'] ?? 'N/A' }}
                            </span>
                        </td>
                        <td>
                            @if(isset($ticket['tecnico_asignado']))
                                <span class="tech-name">{{ $ticket['tecnico_asignado']['nombre_completo'] }}</span>
                            @else
                                <span class="tech-unassigned">Sin asignar</span>
                            @endif
                        </td>
                        <td style="font-size:.87rem; color:#9ca3af;">
                            {{ \Carbon\Carbon::parse($ticket['fecha_creacion'])->format('d/m/Y') }}
                        </td>
                        <td>
                            <a href="{{ route('tickets.show', $ticket['id_ticket']) }}" class="btn-action">
                                Gestionar
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9">
                            <div class="empty-state">
                                <div class="empty-icon"><i class="bi bi-inbox"></i></div>
                                <p class="empty-text">No hay tickets para mostrar</p>
                                @if(session('usuario_rol') === 'Administrador')
                                <a href="{{ route('tickets.create') }}" class="btn-filter">
                                    <i class="bi bi-plus-circle"></i> Crear Ticket
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ── PAGINACION ───────────────────────── --}}
        @if($tickets && $tickets->lastPage() > 1)
        <div class="pag-wrap">
            @if($tickets->onFirstPage())
                <span class="pag-disabled">&#8592; Anterior</span>
            @else
                <a href="{{ $tickets->previousPageUrl() }}">&#8592; Anterior</a>
            @endif

            @for($p = 1; $p <= $tickets->lastPage(); $p++)
                @if($p == $tickets->currentPage())
                    <span class="pag-active">{{ $p }}</span>
                @else
                    <a href="{{ $tickets->url($p) }}">{{ $p }}</a>
                @endif
            @endfor

            @if($tickets->hasMorePages())
                <a href="{{ $tickets->nextPageUrl() }}">Siguiente &#8594;</a>
            @else
                <span class="pag-disabled">Siguiente &#8594;</span>
            @endif
        </div>
        @endif

    </div>{{-- /container-fluid --}}
</div>{{-- /page-container --}}

@endsection
