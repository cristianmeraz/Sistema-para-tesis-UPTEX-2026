@extends('layouts.app')

@section('title', 'Detalle del Usuario')

@section('content')
<style>
    body { background: #f1f5f9; }

    /* === HEADER BANNER === */
    .page-header {
        background: linear-gradient(135deg, #1e3a5f 0%, #1d4ed8 100%);
        border-radius: 16px;
        padding: 1.75rem 2rem;
        margin-bottom: 1.5rem;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        box-shadow: 0 8px 24px rgba(30,58,95,.25);
    }
    .page-header-title { font-size: 1.65rem; font-weight: 800; margin: 0; }
    .page-header-sub   { font-size: .9rem; opacity: .8; margin: .25rem 0 0; }

    .btn-hdr {
        padding: .6rem 1.3rem; border-radius: 8px; font-weight: 700;
        font-size: .88rem; display: inline-flex; align-items: center;
        gap: .4rem; text-decoration: none; transition: all .2s ease;
        border: none; cursor: pointer; white-space: nowrap;
    }
    .btn-hdr-white  { background: white; color: #1e3a5f; }
    .btn-hdr-white:hover  { background: #dbeafe; color: #1e3a5f; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,.15); }
    .btn-hdr-amber  { background: #f59e0b; color: #1e3a5f; }
    .btn-hdr-amber:hover  { background: #d97706; color: white; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(245,158,11,.4); }

    /* === PROFILE BANNER === */
    .profile-banner {
        background: white; border: 1px solid #dbeafe; border-radius: 14px;
        overflow: hidden; margin-bottom: 1.5rem;
    }
    .profile-banner-top {
        background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 100%);
        padding: 2rem 2rem 3.5rem; position: relative;
    }
    .profile-banner-body {
        padding: 0 2rem 1.5rem; margin-top: -2.5rem;
        display: flex; align-items: flex-end; gap: 1.5rem; flex-wrap: wrap;
    }

    /* === AVATAR === */
    .avatar-xl {
        width: 80px; height: 80px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; color: white; font-size: 2rem;
        border: 4px solid white; box-shadow: 0 4px 12px rgba(0,0,0,.15);
        flex-shrink: 0;
    }
    .av-admin   { background: linear-gradient(135deg, #1e3a5f, #1d4ed8); }
    .av-tecnico { background: linear-gradient(135deg, #4f46e5, #7c3aed); }
    .av-normal  { background: linear-gradient(135deg, #0891b2, #06b6d4); }
    .av-otro    { background: linear-gradient(135deg, #475569, #64748b); }

    .profile-info { flex: 1; min-width: 0; padding-bottom: .25rem; }
    .profile-name { font-size: 1.35rem; font-weight: 800; color: #1e293b; margin: 0; }
    .profile-email { font-size: .9rem; color: #64748b; margin: .15rem 0 0; }

    .profile-badges { display: flex; gap: .5rem; flex-wrap: wrap; align-items: center; padding-bottom: .5rem; }

    /* === ROLE BADGE === */
    .role-badge {
        display: inline-flex; align-items: center; gap: .35rem;
        padding: .35rem .85rem; border-radius: 20px;
        font-size: .78rem; font-weight: 700; white-space: nowrap;
    }
    .rb-admin   { background: #1e3a5f; color: white; }
    .rb-tecnico { background: #4f46e5; color: white; }
    .rb-normal  { background: #0891b2; color: white; }
    .rb-otro    { background: #475569; color: white; }

    /* === STATUS BADGE === */
    .status-badge {
        display: inline-flex; align-items: center; gap: .3rem;
        padding: .3rem .8rem; border-radius: 8px; font-size: .8rem; font-weight: 700;
    }
    .sb-active   { background: #d1fae5; color: #065f46; }
    .sb-inactive { background: #fee2e2; color: #991b1b; }

    /* === INFO CARDS === */
    .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem; }

    .info-card {
        background: white; border: 1px solid #dbeafe; border-radius: 14px;
        padding: 1.3rem 1.4rem; position: relative; overflow: hidden;
    }
    .info-card::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0;
        height: 3px; background: var(--info-color, #2563eb);
    }
    .info-icon {
        width: 38px; height: 38px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem; margin-bottom: .6rem;
        background: color-mix(in srgb, var(--info-color, #2563eb) 12%, white);
        color: var(--info-color, #2563eb);
    }
    .info-value { font-size: 1rem; font-weight: 700; color: #1e293b; margin-bottom: .1rem; }
    .info-label { font-size: .72rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .05em; }

    /* === KPI CARD === */
    .kpi-card {
        background: white; border: 1px solid #dbeafe; border-radius: 14px;
        padding: 1.3rem 1.4rem; transition: all .3s cubic-bezier(.4,0,.2,1);
        position: relative; overflow: hidden; height: 100%;
    }
    .kpi-card::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0;
        height: 3px; background: var(--kpi-color, #2563eb);
    }
    .kpi-card:hover { transform: translateY(-4px); box-shadow: 0 12px 28px rgba(30,58,95,.13); border-color: var(--kpi-color, #2563eb); }
    .kpi-icon {
        width: 38px; height: 38px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem;
        background: color-mix(in srgb, var(--kpi-color, #2563eb) 12%, white);
        color: var(--kpi-color, #2563eb); margin-bottom: .6rem;
    }
    .kpi-value { font-size: 2.1rem; font-weight: 800; color: var(--kpi-color, #2563eb); line-height: 1; margin-bottom: .2rem; }
    .kpi-label { font-size: .72rem; font-weight: 700; color: #94a3b8; letter-spacing: .05em; }

    /* === TABLE === */
    .table-card { background: white; border: 1px solid #dbeafe; border-radius: 14px; overflow: hidden; }
    .table-card-header {
        background: linear-gradient(135deg, #1e3a5f 0%, #1d4ed8 100%);
        color: white; padding: 1rem 1.5rem;
        display: flex; align-items: center; gap: .6rem;
        font-weight: 700; font-size: .95rem;
    }
    .tbl { width: 100%; margin: 0; border-collapse: collapse; }
    .tbl thead th {
        background: #f8fafc; padding: .85rem 1rem;
        font-size: .72rem; font-weight: 700; color: #64748b;
        text-transform: uppercase; letter-spacing: .05em;
        border-bottom: 2px solid #dbeafe; white-space: nowrap;
    }
    .tbl tbody tr { border-bottom: 1px solid #f1f5f9; transition: background .15s; }
    .tbl tbody tr:hover { background: #f8fafc; }
    .tbl tbody td { padding: .8rem 1rem; font-size: .9rem; vertical-align: middle; }
    .tbl tbody tr:last-child { border-bottom: none; }

    .ticket-id { font-weight: 800; color: #1d4ed8; }

    /* === ESTADO CHIPS === */
    .chip-estado {
        display: inline-flex; align-items: center; gap: .3rem;
        padding: .3rem .75rem; border-radius: 20px;
        font-size: .75rem; font-weight: 700;
    }
    .chip-abierto    { background: #dbeafe; color: #1e40af; }
    .chip-en_proceso { background: #fef3c7; color: #92400e; }
    .chip-pendiente  { background: #ffedd5; color: #9a3412; }
    .chip-resuelto   { background: #d1fae5; color: #065f46; }
    .chip-cerrado    { background: #e2e8f0; color: #475569; }
    .chip-cancelado  { background: #fee2e2; color: #991b1b; }

    /* === EMPTY STATE === */
    .empty-state { text-align: center; padding: 3rem 2rem; }
    .empty-state i { font-size: 3rem; color: #cbd5e1; display: block; margin-bottom: .5rem; }
    .empty-state p { color: #94a3b8; font-weight: 600; font-size: .9rem; margin: 0; }

    /* === SECTION HEADER === */
    .section-header {
        display: flex; align-items: center; gap: .6rem;
        font-size: .95rem; font-weight: 700; color: #1e3a5f;
        padding-bottom: .6rem; border-bottom: 2px solid #dbeafe;
        margin-bottom: 1rem;
    }

    @media(max-width:768px){
        .page-header { flex-direction: column; align-items: flex-start; }
        .profile-banner-body { flex-direction: column; align-items: center; text-align: center; }
        .profile-badges { justify-content: center; }
        .info-grid { grid-template-columns: 1fr; }
    }
</style>

@php
    $rolNombre = $usuario['rol']['nombre'] ?? '';
    $inicial = mb_strtoupper(mb_substr($usuario['nombre'], 0, 1));
    $avClass = match($rolNombre) {
        'Administrador' => 'av-admin',
        'Técnico'       => 'av-tecnico',
        'Normal', 'Usuario Normal' => 'av-normal',
        default         => 'av-otro',
    };
    $rbClass = match($rolNombre) {
        'Administrador' => 'rb-admin',
        'Técnico'       => 'rb-tecnico',
        'Normal', 'Usuario Normal' => 'rb-normal',
        default         => 'rb-otro',
    };
    $esTecnico = $rolNombre === 'Técnico';
    $tickets   = $esTecnico ? ($usuario['tickets_asignados'] ?? []) : ($usuario['tickets'] ?? []);
    $totalTk   = count($tickets);
@endphp

{{-- ===== HEADER ===== --}}
<div class="page-header">
    <div>
        <h1 class="page-header-title"><i class="bi bi-person-badge me-2"></i>Detalle del Usuario</h1>
        <p class="page-header-sub">Perfil completo y actividad de tickets</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('usuarios.edit', $usuario['id_usuario']) }}" class="btn-hdr btn-hdr-amber">
            <i class="bi bi-pencil-square"></i> Editar
        </a>
        <a href="{{ route('usuarios.index') }}" class="btn-hdr btn-hdr-white">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
</div>

{{-- ===== PROFILE BANNER ===== --}}
<div class="profile-banner">
    <div class="profile-banner-top"></div>
    <div class="profile-banner-body">
        <div class="avatar-xl {{ $avClass }}">{{ $inicial }}</div>
        <div class="profile-info">
            <h2 class="profile-name">{{ $usuario['nombre'] }} {{ $usuario['apellido'] }}</h2>
            <p class="profile-email"><i class="bi bi-envelope me-1"></i>{{ $usuario['correo'] }}</p>
        </div>
        <div class="profile-badges">
            <span class="role-badge {{ $rbClass }}"><i class="bi bi-shield-check"></i> {{ $rolNombre }}</span>
            @if($usuario['activo'])
                <span class="status-badge sb-active"><i class="bi bi-check-circle-fill"></i> Activo</span>
            @else
                <span class="status-badge sb-inactive"><i class="bi bi-x-circle-fill"></i> Inactivo</span>
            @endif
        </div>
    </div>
</div>

{{-- ===== INFO + KPI CARDS ===== --}}
<div class="info-grid">
    <div class="info-card" style="--info-color:#2563eb;">
        <div class="info-icon"><i class="bi bi-calendar-event"></i></div>
        <div class="info-label">Fecha de registro</div>
        <div class="info-value">{{ \Carbon\Carbon::parse($usuario['created_at'])->format('d/m/Y H:i') }}</div>
    </div>
    <div class="info-card" style="--info-color:#8b5cf6;">
        <div class="info-icon"><i class="bi bi-clock-history"></i></div>
        <div class="info-label">Última actualización</div>
        <div class="info-value">{{ \Carbon\Carbon::parse($usuario['updated_at'])->format('d/m/Y H:i') }}</div>
    </div>
    <div class="kpi-card" style="--kpi-color:{{ $esTecnico ? '#f59e0b' : '#2563eb' }};">
        <div class="kpi-icon"><i class="bi bi-{{ $esTecnico ? 'clipboard-check' : 'ticket-perforated' }}"></i></div>
        <div class="kpi-value">{{ $totalTk }}</div>
        <div class="kpi-label">{{ $esTecnico ? 'Tickets Asignados' : 'Tickets Creados' }}</div>
    </div>
</div>

{{-- ===== TICKETS TABLE ===== --}}
<div class="table-card">
    <div class="table-card-header">
        <i class="bi bi-{{ $esTecnico ? 'clipboard-check' : 'ticket-perforated' }}"></i>
        {{ $esTecnico ? 'Tickets Asignados' : 'Tickets Creados' }}
    </div>
    @if($totalTk > 0)
    <div class="table-responsive">
        <table class="tbl">
            <thead>
                <tr>
                    <th style="padding-left:1.5rem;">ID</th>
                    <th>Título</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                @foreach(collect($tickets)->take(10) as $ticket)
                <tr>
                    <td style="padding-left:1.5rem;"><span class="ticket-id">#{{ $ticket['id_ticket'] ?? 'N/A' }}</span></td>
                    <td>{{ Str::limit($ticket['titulo'] ?? 'N/A', 45) }}</td>
                    <td>
                        @php $tipo = $ticket['estado']['tipo'] ?? 'abierto'; @endphp
                        <span class="chip-estado chip-{{ $tipo }}">{{ $ticket['estado']['nombre'] ?? 'N/A' }}</span>
                    </td>
                    <td style="color:#64748b; font-size:.85rem;">{{ \Carbon\Carbon::parse($ticket['fecha_creacion'] ?? now())->format('d/m/Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="empty-state">
        <i class="bi bi-inbox"></i>
        <p>{{ $esTecnico ? 'No tiene tickets asignados' : 'No ha creado tickets aún' }}</p>
    </div>
    @endif
</div>
@endsection