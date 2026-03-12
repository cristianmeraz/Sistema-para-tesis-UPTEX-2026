@extends('layouts.app')

@section('title', 'Panel de Administración - UPTEX')

@section('content')
<link rel="stylesheet" href="{{ asset('css/tickets-priority.css') }}">
<link rel="stylesheet" href="{{ asset('css/auto-refresh.css') }}">
{{-- comments-v2.css ya incluido por el partial de comentarios --}}
{{-- Script movido a @push('scripts') para garantizar que el DOM esté listo --}}

<style>
    body { background: #f1f5f9; }
    
    @keyframes livePulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.4; transform: scale(0.8); }
    }
    @keyframes statFlash {
        0% { background-color: transparent; }
        30% { background-color: rgba(102, 126, 234, 0.15); }
        100% { background-color: transparent; }
    }
    .stat-updated { animation: statFlash 1s ease-out; }
    
    .admin-header {
        background: linear-gradient(135deg, #1e3a5f 0%, #1d4ed8 100%);
        padding: 1.75rem 0;
        margin-bottom: 2rem;
    }
    
    /* DISEÑO ASIMÉTRICO - GRID DINÁMICO */
    .stat-cards-grid {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .stat-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border: 1px solid #e8e8ff;
        border-radius: 16px;
        padding: 1.8rem;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        grid-column: span 1;
        cursor: pointer;
    }
    
    /* Tarjetas asimétricas - col-span variable */
    .stat-card:nth-child(1) { grid-column: span 2; }
    .stat-card:nth-child(2) { grid-column: span 1; }
    .stat-card:nth-child(3) { grid-column: span 1; }
    .stat-card:nth-child(4) { grid-column: span 1; }
    .stat-card:nth-child(5) { grid-column: span 1; }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #1e3a5f, #1d4ed8);
    }
    
    .stat-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(30, 58, 95, 0.15);
        border-color: #2563eb;
        cursor: pointer;
    }

    /* Tarjeta dinámica para Tickets Abiertos */
    .stat-card.status-green {
        border-left: 4px solid #10B981;
        background: linear-gradient(135deg, #f0fdf4 0%, #e8f7f3 100%);
    }
    .stat-card.status-green .stat-value {
        background: linear-gradient(135deg, #10B981 0%, #059669 100%);
    }
    .stat-card.status-green:hover {
        border-color: #10B981;
        box-shadow: 0 20px 40px rgba(16, 185, 129, 0.15);
    }

    .stat-card.status-red {
        border-left: 4px solid #EF4444;
        background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
    }
    .stat-card.status-red .stat-value {
        background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);
    }
    .stat-card.status-red:hover {
        border-color: #EF4444;
        box-shadow: 0 20px 40px rgba(239, 68, 68, 0.15);
    }
    
    .stat-value {
        font-size: 2.5rem;
        font-weight: 700;
        background: linear-gradient(135deg, #1e3a5f 0%, #1d4ed8 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin: 0.5rem 0;
    }
    
    .stat-label {
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #96969d;
    }
    
    /* PRIORIDAD CARDS CON ASIMETRÍA */
    .priority-cards-container {
        display: grid;
        grid-template-columns: repeat(12, 1fr);
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .priority-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border: 1px solid #e8e8ff;
        border-radius: 16px;
        padding: 2rem;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }
    
    /* Distribución asimétrica — 3 columnas de igual peso */
    .priority-card:nth-child(1) { grid-column: span 4; }
    .priority-card:nth-child(2) { grid-column: span 4; }
    .priority-card:nth-child(3) { grid-column: span 4; }
    
    .priority-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 20px 50px rgba(0,0,0,0.1);
    }
    a.priority-card {
        text-decoration: none !important;
        color: inherit !important;
        display: block;
        cursor: pointer;
    }
    
    .priority-baja  { border-left: 5px solid #0d9488; }
    .priority-media { border-left: 5px solid #d97706; }
    .priority-alta  {
        background: linear-gradient(135deg, #fff5f5 0%, #fff0f0 100%);
        border-left: 5px solid #dc2626;
        box-shadow: 0 0 20px rgba(220, 38, 38, 0.07);
    }
    
    .priority-alta:hover {
        box-shadow: 0 20px 50px rgba(220, 38, 38, 0.18);
    }
    
    .priority-number {
        font-size: 2.2rem;
        font-weight: 800;
        margin: 0.5rem 0;
    }
    
    .priority-icon {
        font-size: 2.5rem;
        margin-left: auto;
        opacity: 0.3;
    }
    
    .action-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border: 1px solid #e8e8ff;
        border-radius: 16px;
        padding: 2rem;
        transition: all 0.3s ease;
    }
    
    .action-card:hover {
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        transform: translateY(-4px);
    }
    
    .action-btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        padding: 1rem 2rem;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s ease;
        width: 100%;
        margin-bottom: 1rem;
    }
    
    .action-btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
    }
    
    .action-btn-secondary {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        border: none;
        color: white;
        padding: 1rem 2rem;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s ease;
        width: 100%;
    }
    
    .action-btn-secondary:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(245, 87, 108, 0.4);
    }
    
    /* TABLA DE CRÍTICOS MEJORADA */
    .critical-section {
        background: linear-gradient(135deg, #FFEBEE 0%, #FFF8F8 100%);
        border-radius: 16px;
        padding: 2rem;
        border: 2px solid #FFCDD2;
        margin-bottom: 2rem;
    }
    
    .table-wrapper {
        background: white;
        border: 1px solid #e8e8ff;
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 1rem;
    }
    
    .table {
        width: 100%;
        margin: 0;
    }
    
    .table thead {
        background: linear-gradient(135deg, #1e3a5f 0%, #1d4ed8 100%);
        color: white;
    }
    
    .table thead th {
        padding: 1.1rem;
        font-weight: 700;
        font-size: 0.85rem;
        border: none;
        text-align: left;
    }
    
    .table tbody tr {
        border-bottom: 1px solid #e8e8ff;
        transition: background-color 0.2s ease;
    }
    
    .table tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .table tbody td {
        padding: 1rem 1.1rem;
        font-size: 0.95rem;
        color: #2d3748;
    }
    
    .ticket-id {
        font-weight: 700;
        color: #2563eb;
    }
    
    .btn-action {
        background: linear-gradient(135deg, #1e3a5f 0%, #1d4ed8 100%);
        border: none;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.8rem;
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-block;
        cursor: pointer;
    }
    
    .btn-action:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(30, 58, 95, 0.35);
        color: white;
    }
    
    .glass-effect {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 16px;
    }

    .animate-pulse-custom {
        animation: pulse-custom 2s ease-in-out infinite;
    }

    @keyframes pulse-custom {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.8; transform: scale(1.05); }
    }
    
    /* RESPONSIVE ASIMÉTRICO */
    @media (max-width: 1200px) {
        .stat-cards-grid {
            grid-template-columns: repeat(4, 1fr);
        }
        .stat-card {
            grid-column: span 2 !important;
        }
        .stat-card:nth-child(3),
        .stat-card:nth-child(4) {
            grid-column: span 2 !important;
        }
        
        .priority-cards-container {
            grid-template-columns: repeat(2, 1fr);
        }
        .priority-card {
            grid-column: span 1 !important;
        }
    }
    
    @media (max-width: 768px) {
        .stat-cards-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .stat-card {
            grid-column: span 1 !important;
            padding: 1.2rem;
        }
        
        .stat-value {
            font-size: 1.8rem;
        }
        
        .priority-cards-container {
            grid-template-columns: 1fr;
        }
        .priority-card {
            grid-column: span 1 !important;
            padding: 1.5rem;
        }
        
        .priority-number {
            font-size: 2rem;
        }
        
        .admin-header {
            padding: 1.25rem 0;
        }
        .kpi-card { padding: 1rem; }
        .kpi-value { font-size: 1.5rem; }
        .quick-btn { width: 100%; justify-content: center; }
    }

    /* ===== KPI CARDS ===== */
    a.kpi-card {
        text-decoration: none !important;
        color: inherit !important;
        display: block;
    }
    .kpi-card {
        background: #fff;
        border: 1px solid #dbeafe;
        border-radius: 14px;
        padding: 1.4rem 1.5rem;
        transition: all .3s cubic-bezier(.4,0,.2,1);
        position: relative;
        overflow: hidden;
        cursor: pointer;
        height: 100%;
    }
    .kpi-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        background: var(--kpi-color, #2563eb);
    }
    .kpi-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 28px rgba(30,58,95,.13);
        border-color: var(--kpi-color, #2563eb);
    }
    .kpi-icon {
        width: 40px; height: 40px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem;
        background: color-mix(in srgb, var(--kpi-color, #2563eb) 12%, white);
        color: var(--kpi-color, #2563eb);
        margin-bottom: .7rem;
    }
    .kpi-value {
        font-size: 2rem;
        font-weight: 800;
        color: var(--kpi-color, #1e3a5f);
        line-height: 1;
        margin-bottom: .25rem;
    }
    .kpi-label {
        font-size: .7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .07em;
        color: #94a3b8;
        margin-bottom: .4rem;
    }
    .kpi-sub {
        font-size: .77rem;
        color: #64748b;
        margin-bottom: .55rem;
        min-height: 1.1em;
    }
    .kpi-bar-track {
        height: 5px;
        background: #f1f5f9;
        border-radius: 99px;
        overflow: hidden;
    }
    .kpi-bar-fill {
        height: 100%;
        border-radius: 99px;
        background: var(--kpi-color, #2563eb);
        transition: width .6s ease;
    }
    .kpi-bar-pct {
        font-size: .65rem;
        color: #94a3b8;
        margin-top: .2rem;
    }
    @keyframes pulse-warning {
        0%, 100% { box-shadow: 0 0 0 0 rgba(239,68,68,.35); }
        50% { box-shadow: 0 0 0 8px rgba(239,68,68,0); }
    }
    .pulse-danger { animation: pulse-warning 2s infinite; }

    /* ===== SECTION HEADERS ===== */
    .section-header {
        font-size: .95rem;
        font-weight: 700;
        color: #1e3a5f;
        margin-bottom: 1.1rem;
        padding-bottom: .5rem;
        border-bottom: 2px solid #dbeafe;
        display: flex;
        align-items: center;
        gap: .5rem;
    }

    /* ===== QUICK ACTIONS ===== */
    .quick-btn {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .55rem 1.1rem;
        border-radius: 8px;
        font-size: .84rem;
        font-weight: 600;
        text-decoration: none;
        border: 1.5px solid transparent;
        transition: all .2s;
        white-space: nowrap;
    }
    .quick-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(30,58,95,.2); }
    .quick-btn-primary { background: #1e3a5f; color: #fff; }
    .quick-btn-primary:hover { background: #1d4ed8; color: #fff; }
    .quick-btn-violet { background: #7c3aed; color: #fff; }
    .quick-btn-violet:hover { background: #6d28d9; color: #fff; }
    .quick-btn-outline { background: #fff; border-color: #dbeafe; color: #1e3a5f; }
    .quick-btn-outline:hover { background: #eff6ff; border-color: #2563eb; color: #2563eb; }
</style>

<div class="admin-header">
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <div class="text-white mb-1" style="font-size:.75rem; font-weight:600; text-transform:uppercase; letter-spacing:.08em; opacity:.7;">
                    <i class="bi bi-shield-check me-1"></i>Sistema UPTEX
                </div>
                <h1 class="fw-bold text-white mb-0" style="font-size:1.7rem; line-height:1.2;">
                    Panel de Administración
                </h1>
            </div>
            <div class="d-flex gap-2 align-items-center flex-wrap">
                <div class="d-flex align-items-center gap-2 text-white px-3 py-2 rounded-pill"
                     style="background:rgba(255,255,255,.12); font-size:.82rem;" id="badgeUpdate">
                    <span style="width:8px;height:8px;border-radius:50%;background:#10B981;animation:livePulse 2s ease-in-out infinite;display:inline-block;"></span>
                    Actualizado: <span id="lastUpdate" class="fw-bold">Ahora</span>
                </div>
                <button class="btn btn-light fw-bold px-4 py-2" id="btnRefresh" style="border-radius:8px; font-size:.85rem;">
                    <i class="bi bi-arrow-clockwise me-1"></i>Actualizar
                </button>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-4 pb-5" data-user-role="{{ session('usuario_rol') }}">
    <!-- ALERT BANNER: Críticos sin asignar -->
    @php $hayActivos = ($stats['criticos_sin_asignar'] ?? 0) > 0; @endphp
    <div id="alerta-critica"
         class="mb-4 rounded-3 px-4 py-3 align-items-center justify-content-between flex-wrap gap-3 {{ $hayActivos ? 'd-flex' : 'd-none' }}"
         role="alert"
         style="background:linear-gradient(135deg,#fff5f5 0%,#fff0f0 100%); border:2px solid #dc2626;">
        <div class="d-flex align-items-center gap-3">
            <div class="d-flex align-items-center justify-content-center rounded-circle"
                 style="width:40px;height:40px;background:#dc2626;flex-shrink:0;">
                <i class="bi bi-arrow-up-circle-fill text-white" style="font-size:1rem;"></i>
            </div>
            <div>
                <div class="fw-bold" style="color:#b91c1c; font-size:.9rem; text-transform:uppercase; letter-spacing:.05em;">Alta Prioridad</div>
                <div style="font-size:.88rem; color:#dc2626; font-weight:600;">
                    Hay <strong data-stat="criticos_sin_asignar">{{ $stats['criticos_sin_asignar'] ?? 0 }}</strong> ticket(s) de alta prioridad <strong>sin técnico asignado</strong>
                </div>
            </div>
        </div>
        <a href="#tabla-criticos" class="btn btn-danger fw-bold px-4 py-2" style="border-radius:8px; font-size:.85rem; background:#C62828; border:none;">
            <i class="bi bi-arrow-down me-1"></i>Ver Tabla
        </a>
    </div>
    <!-- ===== KPI UNIFICADO (métricas principales + técnicas) ===== -->
    @php
        $kpiTotal   = max($stats['total_tickets']  ?? 1, 1);
        $kpiUsers   = max($stats['total_usuarios'] ?? 1, 1);
        $kpiBases   = max(($stats['tickets_abiertos'] ?? 0) + ($stats['tickets_pendientes'] ?? 0), 1);
        $pctAbiertos   = min((int) round(($stats['tickets_abiertos']   ?? 0) / $kpiTotal * 100), 100);
        $pctTecnicos   = min((int) round(($stats['tecnicos']           ?? 0) / $kpiUsers  * 100), 100);
        $pctEnProceso  = min((int) round(($stats['tickets_en_proceso'] ?? 0) / $kpiTotal * 100), 100);
        $pctPendientes = min((int) round(($stats['tickets_pendientes'] ?? 0) / $kpiTotal * 100), 100);
        $pctSinAsig    = min((int) round(($stats['tickets_sin_tecnico']?? 0) / $kpiBases * 100), 100);
        $pctResueltos  = min((int) round(($stats['resueltos_hoy']      ?? 0) / $kpiTotal * 100), 100);
        $pctCerrados   = min((int) round(($stats['tickets_cerrados']   ?? 0) / $kpiTotal * 100), 100);
        $colorAbiertos = ($stats['tickets_abiertos'] ?? 0) > 0 ? '#f59e0b' : '#10b981';
        $colorSinAsig  = ($stats['tickets_sin_tecnico'] ?? 0) > 0 ? '#ef4444' : '#94a3b8';
    @endphp
    <div class="mb-4">
        <div class="section-header">
            <i class="bi bi-grid-1x2-fill" style="color:#2563eb;"></i>
            Métricas del Sistema
        </div>
        <div class="row g-3">
            {{-- Tickets Abiertos --}}
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <a href="{{ route('tickets.index', ['tipo' => 'abierto']) }}" class="kpi-card" style="--kpi-color:{{ $colorAbiertos }};">
                    <div class="kpi-icon"><i class="bi bi-folder2-open"></i></div>
                    <div class="kpi-value" data-stat="tickets_abiertos">{{ $stats['tickets_abiertos'] ?? 0 }}</div>
                    <div class="kpi-label">Tickets Abiertos</div>
                    <div class="kpi-sub">{{ ($stats['tickets_abiertos'] ?? 0) == 0 ? 'Todo resuelto' : 'Requieren atención' }}</div>
                    <div class="kpi-bar-track"><div class="kpi-bar-fill" style="width:{{ $pctAbiertos }}%;"></div></div>
                    <div class="kpi-bar-pct">{{ $pctAbiertos }}% del total</div>
                </a>
            </div>
            {{-- Total Usuarios --}}
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <a href="{{ route('usuarios.index') }}" class="kpi-card" style="--kpi-color:#3b82f6;">
                    <div class="kpi-icon"><i class="bi bi-people-fill"></i></div>
                    <div class="kpi-value" data-stat="total_usuarios">{{ $stats['total_usuarios'] ?? 0 }}</div>
                    <div class="kpi-label">Total Usuarios</div>
                    <div class="kpi-sub">{{ $stats['tecnicos'] ?? 0 }} técnico(s) · {{ $stats['usuarios_normales'] ?? 0 }} normal(es)</div>
                    <div class="kpi-bar-track"><div class="kpi-bar-fill" style="width:{{ $pctTecnicos }}%;"></div></div>
                    <div class="kpi-bar-pct">{{ $pctTecnicos }}% son técnicos</div>
                </a>
            </div>
            {{-- En Proceso --}}
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <a href="{{ route('tickets.index', ['tipo' => 'en_proceso']) }}" class="kpi-card" style="--kpi-color:#1d4ed8;">
                    <div class="kpi-icon"><i class="bi bi-arrow-repeat"></i></div>
                    <div class="kpi-value" data-stat="tickets_en_proceso">{{ $stats['tickets_en_proceso'] ?? 0 }}</div>
                    <div class="kpi-label">En Proceso</div>
                    <div class="kpi-sub">Siendo trabajados ahora</div>
                    <div class="kpi-bar-track"><div class="kpi-bar-fill" style="width:{{ $pctEnProceso }}%;"></div></div>
                    <div class="kpi-bar-pct">{{ $pctEnProceso }}% del total</div>
                </a>
            </div>
            {{-- Resueltos Hoy --}}
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <a href="{{ route('tickets.index', ['tipo' => 'resuelto']) }}" class="kpi-card" style="--kpi-color:#10b981;">
                    <div class="kpi-icon"><i class="bi bi-check-circle-fill"></i></div>
                    <div class="kpi-value" data-stat="resueltos_hoy">{{ $stats['resueltos_hoy'] ?? 0 }}</div>
                    <div class="kpi-label">Resueltos Hoy</div>
                    <div class="kpi-sub">Completados satisfactoriamente</div>
                    <div class="kpi-bar-track"><div class="kpi-bar-fill" style="width:{{ $pctResueltos }}%;"></div></div>
                    <div class="kpi-bar-pct">{{ $pctResueltos }}% del total</div>
                </a>
            </div>
            {{-- Pendientes --}}
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <a href="{{ route('tickets.index', ['tipo' => 'pendiente']) }}" class="kpi-card" style="--kpi-color:#f59e0b;">
                    <div class="kpi-icon"><i class="bi bi-hourglass-split"></i></div>
                    <div class="kpi-value" data-stat="tickets_pendientes">{{ $stats['tickets_pendientes'] ?? 0 }}</div>
                    <div class="kpi-label">Pendientes</div>
                    <div class="kpi-sub">Esperando acción técnica</div>
                    <div class="kpi-bar-track"><div class="kpi-bar-fill" style="width:{{ $pctPendientes }}%;"></div></div>
                    <div class="kpi-bar-pct">{{ $pctPendientes }}% del total</div>
                </a>
            </div>
            {{-- Sin Asignar --}}
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <a href="{{ route('tickets.index', ['sin_tecnico' => '1']) }}"
                   class="kpi-card {{ ($stats['tickets_sin_tecnico'] ?? 0) > 0 ? 'pulse-danger' : '' }}"
                   style="--kpi-color:{{ $colorSinAsig }};">
                    <div class="kpi-icon"><i class="bi bi-person-x-fill"></i></div>
                    <div class="kpi-value" data-stat="tickets_sin_tecnico">{{ $stats['tickets_sin_tecnico'] ?? 0 }}</div>
                    <div class="kpi-label">Sin Asignar</div>
                    <div class="kpi-sub">{{ ($stats['tickets_sin_tecnico'] ?? 0) > 0 ? 'Acción requerida' : 'Todo asignado' }}</div>
                    <div class="kpi-bar-track"><div class="kpi-bar-fill" style="width:{{ $pctSinAsig }}%;"></div></div>
                    <div class="kpi-bar-pct">{{ $pctSinAsig }}% sin cobertura</div>
                </a>
            </div>
            {{-- Técnicos --}}
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <a href="{{ route('usuarios.index', ['id_rol' => 2]) }}" class="kpi-card" style="--kpi-color:#7c3aed;">
                    <div class="kpi-icon"><i class="bi bi-person-badge-fill"></i></div>
                    <div class="kpi-value" data-stat="tecnicos">{{ $stats['tecnicos'] ?? 0 }}</div>
                    <div class="kpi-label">Técnicos</div>
                    <div class="kpi-sub">Equipo de soporte activo</div>
                    <div class="kpi-bar-track"><div class="kpi-bar-fill" style="width:{{ $pctTecnicos }}%;"></div></div>
                    <div class="kpi-bar-pct">{{ $pctTecnicos }}% del total usuarios</div>
                </a>
            </div>
            {{-- Total Tickets --}}
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <a href="{{ route('tickets.index') }}" class="kpi-card" style="--kpi-color:#1e3a5f;">
                    <div class="kpi-icon"><i class="bi bi-ticket-detailed-fill"></i></div>
                    <div class="kpi-value" data-stat="total_tickets">{{ $stats['total_tickets'] ?? 0 }}</div>
                    <div class="kpi-label">Total Tickets</div>
                    <div class="kpi-sub">{{ $stats['tickets_cerrados'] ?? 0 }} cerrado(s)</div>
                    <div class="kpi-bar-track"><div class="kpi-bar-fill" style="width:{{ $pctCerrados }}%;"></div></div>
                    <div class="kpi-bar-pct">{{ $pctCerrados }}% cerrados</div>
                </a>
            </div>
        </div>
    </div>

    <!-- DISTRIBUCIÓN POR PRIORIDAD -->
    <div class="mb-4">
        <div class="section-header">
            <i class="bi bi-tags-fill" style="color:#2563eb;"></i>
            Distribución por Prioridad
        </div>
        
        <div class="priority-cards-container">
            <!-- BAJA -->
            <a href="{{ route('tickets.index', ['prioridad_nombre' => 'Baja']) }}" class="priority-card priority-baja">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="priority-label fw-bold text-muted small text-uppercase mb-2">Prioridad Baja</div>
                        <div class="priority-number" data-stat="prioridad_baja" style="color:#0d9488;">{{ $stats['prioridad_baja'] ?? '0' }}</div>
                        <small class="text-muted">Baja urgencia</small>
                    </div>
                    <div class="priority-icon"><i class="bi bi-arrow-down-circle-fill" style="font-size:2rem; color:#0d9488; opacity:.3;"></i></div>
                </div>
            </a>

            <!-- MEDIA -->
            <a href="{{ route('tickets.index', ['prioridad_nombre' => 'Media']) }}" class="priority-card priority-media">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="priority-label fw-bold text-muted small text-uppercase mb-2">Prioridad Media</div>
                        <div class="priority-number" data-stat="prioridad_media" style="color:#d97706;">{{ $stats['prioridad_media'] ?? '0' }}</div>
                        <small class="text-muted">Urgencia moderada</small>
                    </div>
                    <div class="priority-icon"><i class="bi bi-dash-circle-fill" style="font-size:2rem; color:#d97706; opacity:.3;"></i></div>
                </div>
            </a>

            <!-- ALTA -->
            <a href="{{ route('tickets.index', ['prioridad_nombre' => 'Alta']) }}" class="priority-card priority-alta">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="priority-label fw-bold text-danger small text-uppercase mb-2">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>Alta
                        </div>
                        <div class="priority-number" data-stat="prioridad_alta" style="color:#dc2626;">{{ $stats['prioridad_alta'] ?? '0' }}</div>
                        <small class="text-danger">Atención urgente</small>
                        @if(($stats['criticos_en_1h'] ?? 0) > 0)
                        <div class="mt-2 d-flex align-items-center gap-1 px-2 py-1 rounded-2"
                             style="background:#dc2626; display:inline-flex !important;">
                            <span style="width:7px;height:7px;border-radius:50%;background:#fff;animation: livePulse 1.5s ease-in-out infinite;display:inline-block;"></span>
                            <span style="color:#fff; font-size:.72rem; font-weight:800; text-transform:uppercase;">CRÍTICO</span>
                            <span style="color:#fca5a5; font-size:.72rem; font-weight:700;">{{ $stats['criticos_en_1h'] }} &gt;1h sin técnico</span>
                        </div>
                        @endif
                    </div>
                    <div class="priority-icon"><i class="bi bi-arrow-up-circle-fill" style="font-size:2rem; color:#dc2626; opacity:.4;"></i></div>
                </div>
            </a>
        </div>
    </div>

    <!-- TICKETS DE ALTA PRIORIDAD SIN TÉCNICO -->
    <div class="mb-4" id="tabla-criticos">
        <div class="section-header" style="border-bottom-color:#fca5a5;">
            <i class="bi bi-arrow-up-circle-fill" style="color:#dc2626;"></i>
            Tickets de Alta Prioridad Sin Técnico Asignado
        </div>
        <div class="table-wrapper">
            <table class="table" style="margin: 0;">
                <thead>
                    <tr style="background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%); color: white;">
                        <th style="border-top-left-radius: 8px;">ID</th>
                        <th>Título</th>
                        <th>Usuario</th>
                        <th>Técnico Asignado</th>
                        <th>Estado</th>
                        <th>Tiempo</th>
                        <th style="border-top-right-radius: 8px;">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($critical_tickets ?? [] as $ticket)
                    @php
                        $horasTranscurridas = $ticket['fecha_creacion'] ? \Carbon\Carbon::parse($ticket['fecha_creacion'])->diffInMinutes(now()) : 0;
                        $esCritico = ($ticket['es_critico'] ?? false) || $horasTranscurridas >= 60;
                        $tiempoLabel = $horasTranscurridas >= 60
                            ? floor($horasTranscurridas / 60) . 'h ' . ($horasTranscurridas % 60) . 'm'
                            : $horasTranscurridas . ' min';
                    @endphp
                    <tr class="row-prioridad-alta">
                        <td><span class="ticket-id" style="color:#dc2626;">#{{ $ticket['id_ticket'] }}</span></td>
                        <td>
                            <strong>{{ Str::limit($ticket['titulo'], 38) }}</strong>
                            @if($esCritico)
                            <span class="ms-1 badge" style="background:#dc2626; color:#fff; font-size:.68rem; padding:.18rem .45rem; border-radius:4px; vertical-align:middle; animation: livePulse 1.8s infinite;">CRÍTICO</span>
                            @endif
                        </td>
                        <td>{{ $ticket['usuario']['nombre_completo'] ?? 'N/A' }}</td>
                        <td>
                            @if(isset($ticket['tecnico_asignado']))
                                <span style="color: #1B5E20; font-weight: 600;">{{ $ticket['tecnico_asignado']['nombre_completo'] }}</span>
                            @else
                                <span style="color: #dc2626; font-weight: 600;">⚠️ SIN ASIGNAR</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-estado-{{ $ticket['estado']['tipo'] ?? 'abierto' }}">
                                {{ $ticket['estado']['nombre'] ?? 'N/A' }}
                            </span>
                        </td>
                        <td>
                            <small class="{{ $esCritico ? 'text-danger fw-bold' : 'text-muted' }}">{{ $tiempoLabel }}</small>
                        </td>
                        <td>
                            <a href="{{ route('tickets.show', $ticket['id_ticket']) }}" class="btn-action" style="background:#dc2626;">
                                <i class="bi bi-arrow-right me-1"></i>Atender
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 2rem; color: #0d9488;">
                            <i class="bi bi-check-circle-fill" style="font-size: 2rem;"></i>
                            <p class="mb-0 mt-2 fw-bold">¡Todos los tickets de alta prioridad tienen técnico asignado!</p>
                            <small class="text-muted">El equipo técnico tiene cobertura total.</small>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ACCIONES RÁPIDAS — fila compacta -->
    <div class="mb-5">
        <div class="section-header">
            <i class="bi bi-lightning-charge-fill" style="color:#f59e0b;"></i>
            Acciones Rápidas
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.tecnicos.create') }}" class="quick-btn quick-btn-primary">
                <i class="bi bi-person-plus-fill"></i> Crear Técnico
            </a>
            <a href="{{ route('admin.usuarios.create') }}" class="quick-btn quick-btn-violet">
                <i class="bi bi-people-fill"></i> Crear Usuario
            </a>
            <a href="{{ route('usuarios.index') }}" class="quick-btn quick-btn-outline">
                <i class="bi bi-person-check-fill"></i> Gestionar Usuarios
                <span class="badge bg-primary ms-1" style="font-size:.65rem;">{{ $stats['total_usuarios'] ?? 0 }}</span>
            </a>
            <a href="{{ route('tickets.index') }}" class="quick-btn quick-btn-outline">
                <i class="bi bi-ticket-detailed"></i> Ver Tickets
                <span class="badge bg-secondary ms-1" style="font-size:.65rem;">{{ $stats['total_tickets'] ?? 0 }}</span>
            </a>
            <a href="{{ route('reportes.index') }}" class="quick-btn quick-btn-outline">
                <i class="bi bi-chart-bar-fill"></i> Reportes
            </a>
        </div>
    </div>

</div>

{{-- ════════════════════════════════════════════════════════════════ --}}
{{-- SECCIÓN: ENCUESTA DE SATISFACCIÓN — 4 gráficas                --}}
{{-- ════════════════════════════════════════════════════════════════ --}}
@if(isset($satisfaccionStats))
<div class="container-fluid px-4 pb-5" style="max-width:1400px; margin:0 auto;">

    <div class="section-header mb-4" style="border-left:4px solid #16a34a; padding-left:.75rem;">
        <i class="bi bi-emoji-smile-fill" style="color:#16a34a;"></i>
        Encuestas de Satisfacción
        <span class="badge bg-success ms-2" style="font-size:.73rem; font-weight:600;">
            {{ $satisfaccionStats['respondidas'] }}/{{ $satisfaccionStats['total'] }} respondidas
        </span>
    </div>

    <div class="row g-4">

        {{-- Gráfica 1: Satisfacción global --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius:12px;">
                <div class="card-body p-3">
                    <h6 class="fw-bold text-muted small text-uppercase mb-3">
                        <i class="bi bi-pie-chart-fill text-success me-1"></i>Satisfacción global
                    </h6>
                    <canvas id="chartSatisfaccionGlobal" height="220"></canvas>
                    <div class="d-flex justify-content-center gap-3 mt-2 flex-wrap" style="font-size:.78rem;">
                        <span><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#16a34a;margin-right:4px;"></span>Satisfechos ({{ $satisfaccionStats['satisfechos'] }})</span>
                        <span><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#dc2626;margin-right:4px;"></span>No satisfechos ({{ $satisfaccionStats['no_satisfechos'] }})</span>
                        <span><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#94a3b8;margin-right:4px;"></span>Sin responder ({{ $satisfaccionStats['sin_responder'] }})</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Gráfica 2: Satisfacción por área --}}
        <div class="col-md-5">
            <div class="card border-0 shadow-sm h-100" style="border-radius:12px;">
                <div class="card-body p-3">
                    <h6 class="fw-bold text-muted small text-uppercase mb-3">
                        <i class="bi bi-bar-chart-fill text-primary me-1"></i>Satisfacción por área
                    </h6>
                    <canvas id="chartSatisfaccionPorArea" height="220"></canvas>
                </div>
            </div>
        </div>

        {{-- Gráfica 3: Tasa de respuesta + Gráfica 4: Tickets resueltos --}}
        <div class="col-md-3 d-flex flex-column gap-4">

            {{-- Tasa de respuesta --}}
            <div class="card border-0 shadow-sm" style="border-radius:12px; flex:1;">
                <div class="card-body p-3">
                    <h6 class="fw-bold text-muted small text-uppercase mb-2">
                        <i class="bi bi-reply-fill text-warning me-1"></i>Tasa de respuesta
                    </h6>
                    <canvas id="chartTasaRespuesta" height="160"></canvas>
                    @php
                        $tasaPct = $satisfaccionStats['total'] > 0
                            ? round($satisfaccionStats['respondidas'] / $satisfaccionStats['total'] * 100, 1)
                            : 0;
                    @endphp
                    <div class="text-center mt-1" style="font-size:1.4rem; font-weight:800; color:#1e3a5f;">{{ $tasaPct }}%</div>
                    <div class="text-center text-muted" style="font-size:.75rem;">encuestas respondidas</div>
                </div>
            </div>

            {{-- Tickets resueltos por día --}}
            <div class="card border-0 shadow-sm" style="border-radius:12px; flex:1;">
                <div class="card-body p-3">
                    <h6 class="fw-bold text-muted small text-uppercase mb-2">
                        <i class="bi bi-graph-up text-info me-1"></i>Resueltos (14 días)
                    </h6>
                    <canvas id="chartResueltoPorDia" height="130"></canvas>
                </div>
            </div>

        </div>
    </div>

</div>

<script>
(function() {
    const stats = @json($satisfaccionStats);

    // Cargar Chart.js si no está cargado
    function initCharts() {
        // Gráfica 1: Donut satisfacción global
        const ctx1 = document.getElementById('chartSatisfaccionGlobal');
        if (ctx1) {
            new Chart(ctx1, {
                type: 'doughnut',
                data: {
                    labels: ['Satisfechos', 'No satisfechos', 'Sin responder'],
                    datasets: [{
                        data: [stats.satisfechos, stats.no_satisfechos, stats.sin_responder],
                        backgroundColor: ['#16a34a', '#dc2626', '#94a3b8'],
                        borderWidth: 2,
                        borderColor: '#fff',
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false } }
                }
            });
        }

        // Gráfica 2: Barras por área
        const ctx2 = document.getElementById('chartSatisfaccionPorArea');
        if (ctx2 && stats.por_area && stats.por_area.length > 0) {
            new Chart(ctx2, {
                type: 'bar',
                data: {
                    labels: stats.por_area.map(r => r.area),
                    datasets: [
                        { label: 'Satisfechos', data: stats.por_area.map(r => r.satisfechos), backgroundColor: '#16a34a', borderRadius: 4 },
                        { label: 'No satisfechos', data: stats.por_area.map(r => r.no_satisfechos), backgroundColor: '#dc2626', borderRadius: 4 },
                    ]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom', labels: { font: { size: 11 } } } },
                    scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
                }
            });
        } else if (ctx2) {
            ctx2.closest('.card-body').insertAdjacentHTML('beforeend', '<p class="text-center text-muted small mt-3">Sin datos por área aún</p>');
        }

        // Gráfica 3: Tasa de respuesta
        const ctx3 = document.getElementById('chartTasaRespuesta');
        if (ctx3) {
            const pct = stats.total > 0 ? Math.round(stats.respondidas / stats.total * 100) : 0;
            new Chart(ctx3, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [pct, 100 - pct],
                        backgroundColor: ['#1d4ed8', '#e2e8f0'],
                        borderWidth: 0,
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false, cutout: '75%',
                    plugins: { legend: { display: false }, tooltip: { enabled: false } }
                }
            });
        }

        // Gráfica 4: Línea tickets resueltos por día
        const ctx4 = document.getElementById('chartResueltoPorDia');
        if (ctx4) {
            new Chart(ctx4, {
                type: 'line',
                data: {
                    labels: stats.dias_labels,
                    datasets: [{
                        label: 'Resueltos',
                        data: stats.dias_data,
                        borderColor: '#0891b2',
                        backgroundColor: 'rgba(8,145,178,0.12)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.35,
                        pointRadius: 3,
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 10 } } },
                        x: { ticks: { font: { size: 10 } } }
                    }
                }
            });
        }
    }

    if (typeof Chart !== 'undefined') {
        initCharts();
    } else {
        const s = document.createElement('script');
        s.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js';
        s.onload = initCharts;
        document.head.appendChild(s);
    }
})();
</script>
@endif

<!-- JAVASCRIPT PARA FUNCIONALIDADES AJAX -->
<script data-refresh-url="{{ route('reportes.refresh-stats') }}">
document.addEventListener('DOMContentLoaded', function() {
    const REFRESH_URL = '{{ route("reportes.refresh-stats") }}';

    // Botón Refresh Manual - Actualiza en caliente SIN recargar página
    const btnRefresh = document.getElementById('btnRefresh');
    if (btnRefresh) {
        btnRefresh.addEventListener('click', async function() {
            this.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Actualizando...';
            this.disabled = true;
            
            try {
                const response = await fetch(REFRESH_URL, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await response.json();
                
                if (data.success && data.stats) {
                    // Actualizar todas las tarjetas en caliente
                    for (const [key, value] of Object.entries(data.stats)) {
                        document.querySelectorAll('[data-stat="' + key + '"]').forEach(el => {
                            el.textContent = String(value);
                            el.style.transition = 'transform 0.3s ease';
                            el.style.transform = 'scale(1.15)';
                            setTimeout(() => { el.style.transform = 'scale(1)'; }, 400);
                        });
                    }
                    // Alerta crítica show/hide
                    const alerta = document.getElementById('alerta-critica');
                    if (alerta) {
                        const count = parseInt(data.stats.criticos_sin_asignar) || 0;
                        if (count > 0) {
                            alerta.classList.remove('d-none');
                            alerta.classList.add('d-flex');
                        } else {
                            alerta.classList.remove('d-flex');
                            alerta.classList.add('d-none');
                        }
                    }

                    // Timestamp
                    const ts = document.getElementById('lastUpdate');
                    if (ts && data.timestamp) ts.textContent = data.timestamp;

                    this.innerHTML = '<i class="bi bi-check-circle me-2"></i>¡Actualizado!';
                    setTimeout(() => {
                        this.innerHTML = '<i class="bi bi-arrow-clockwise me-2"></i>Actualizar';
                        this.disabled = false;
                    }, 2000);
                } else {
                    throw new Error('Respuesta inválida');
                }
            } catch (error) {
                console.error('Error:', error);
                this.innerHTML = '<i class="bi bi-arrow-clockwise me-2"></i>Actualizar';
                this.disabled = false;
            }
        });
    }

    // Drill-down en tarjetas
    window.filterTickets = function(estado) {
        window.location.href = '{{ route("tickets.index") }}?estado=' + estado;
    };

    window.filterByTechnician = function() {
        window.location.href = '{{ route("usuarios.index") }}';
    };
});
</script>

@push('scripts')
<script src="{{ asset('js/auto-refresh-tickets-enhanced.js') }}?v={{ time() }}"></script>
@endpush

@endsection