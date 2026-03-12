@extends('layouts.app')

@section('title', 'Detalle del Ticket #' . $ticket['id_ticket'])
@section('no_header_title', true)

@section('content')
<link rel="stylesheet" href="{{ asset('css/comments-v2.css') }}">
@php
    // Colores de panel según rol ($esAdmin/$esTecnico vienen del controlador)
    if ($esAdmin) {
        $rolColor1 = '#1e3a5f';
        $rolColor2 = '#1d4ed8';
        $rolGlow   = 'rgba(29,78,216,.22)';
        $chipHover = '#93c5fd';
        $chipHoverShadow = 'rgba(29,78,216,0.07)';
        $chipHoverText   = '#1d4ed8';
    } elseif ($esTecnico) {
        $rolColor1 = '#14532d';
        $rolColor2 = '#16a34a';
        $rolGlow   = 'rgba(22,163,74,.22)';
        $chipHover = '#86efac';
        $chipHoverShadow = 'rgba(22,163,74,0.07)';
        $chipHoverText   = '#15803d';
    } else {
        // Usuario Normal → teal
        $rolColor1 = '#0f766e';
        $rolColor2 = '#0891b2';
        $rolGlow   = 'rgba(8,145,178,.22)';
        $chipHover = '#99f6e4';
        $chipHoverShadow = 'rgba(8,145,178,0.07)';
        $chipHoverText   = '#0891b2';
    }
@endphp

{{-- CSS REDISEÑO SHOW --}}
<style>
    .bg-brown { background-color: #795548 !important; }
    .btn-brown { background-color: #795548; border-color: #795548; color: white; }
    .btn-brown:hover { background-color: #5d4037; border-color: #5d4037; color: white; }
    .border-brown { border-color: #795548 !important; }
    .text-brown { color: #795548 !important; }
    .modal-xl { max-width: 90%; }
    .shadow-inset { box-shadow: inset 0 2px 4px rgba(0,0,0,.06); }

    /* ── BANNER ── */
    .show-banner {
        background: linear-gradient(135deg, {{ $rolColor1 }} 0%, {{ $rolColor2 }} 100%);
        border-radius: 14px;
        padding: 1.6rem 2rem;
        margin-bottom: 1.4rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 8px 32px {{ $rolGlow }};
    }
    .show-banner::before {
        content: '';
        position: absolute; top: -40px; right: -40px;
        width: 180px; height: 180px; border-radius: 50%;
        background: rgba(255,255,255,0.05);
    }
    .show-banner::after {
        content: '';
        position: absolute; bottom: -50px; right: 60px;
        width: 120px; height: 120px; border-radius: 50%;
        background: rgba(255,255,255,0.04);
    }
    .show-banner-badge {
        display: inline-flex; align-items: center; gap: 0.4rem;
        background: rgba(255,255,255,0.15); color: rgba(255,255,255,0.9);
        border-radius: 20px; padding: 0.25rem 0.85rem;
        font-size: 0.78rem; font-weight: 700; letter-spacing: .04em;
        text-transform: uppercase; margin-bottom: 0.6rem;
        border: 1px solid rgba(255,255,255,0.2);
    }
    .show-banner-title {
        font-size: 1.45rem; font-weight: 800; color: white;
        margin: 0; line-height: 1.3;
        text-shadow: 0 1px 4px rgba(0,0,0,0.15);
    }
    .show-banner-prioridad {
        display: inline-flex; align-items: center; gap: 0.4rem;
        border-radius: 20px; padding: 0.22rem 0.75rem;
        font-size: 0.75rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: .04em; margin-top: 0.5rem;
    }
    .show-banner-actions { display: flex; gap: 0.6rem; flex-shrink: 0; align-items: flex-start; }
    .btn-banner-edit {
        background: #f59e0b; border: none; color: white;
        font-weight: 700; border-radius: 9px;
        padding: 0.55rem 1.1rem; font-size: 0.88rem;
        transition: all .2s; white-space: nowrap;
    }
    .btn-banner-edit:hover { background: #d97706; color: white; transform: translateY(-1px); }
    .btn-banner-back {
        background: rgba(255,255,255,0.15); border: 1.5px solid rgba(255,255,255,0.3);
        color: white; font-weight: 700; border-radius: 9px;
        padding: 0.55rem 1.1rem; font-size: 0.88rem;
        transition: all .2s; white-space: nowrap;
        backdrop-filter: blur(4px);
    }
    .btn-banner-back:hover { background: rgba(255,255,255,0.25); color: white; transform: translateY(-1px); }

    /* ── TARJETA PRINCIPAL ── */
    .show-main-card {
        background: white; border-radius: 14px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.07);
        overflow: hidden; margin-bottom: 1rem;
    }
    .show-card-header {
        background: linear-gradient(90deg, {{ $rolColor1 }} 0%, {{ $rolColor2 }} 100%);
        padding: 0.75rem 1.3rem;
        display: flex; align-items: center; gap: 0.6rem;
        color: white;
    }
    .show-card-header i { font-size: 1rem; opacity: 0.9; }
    .show-card-header span { font-size: 0.85rem; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; }

    /* ── INFO CHIPS GRID ── */
    .info-chips-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 0.75rem;
        padding: 1.2rem;
    }
    .info-chip {
        background: #f8fafc; border-radius: 11px;
        padding: 0.75rem 1rem;
        border: 1.5px solid #e2e8f0;
        display: flex; align-items: flex-start; gap: 0.7rem;
        transition: border-color .2s, box-shadow .2s;
    }
    .info-chip:hover { border-color: {{ $chipHover }}; box-shadow: 0 2px 8px {{ $chipHoverShadow }}; }
    .info-chip-icon {
        width: 34px; height: 34px; border-radius: 9px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; font-size: 1rem;
    }
    .info-chip-label {
        font-size: 0.68rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .05em; color: #94a3b8; margin-bottom: 2px;
    }
    .info-chip-value {
        font-size: 0.88rem; font-weight: 700; color: #1e293b; line-height: 1.3;
    }
    .info-chip-action {
        background: none; border: none; padding: 0;
        cursor: pointer; display: flex; align-items: flex-start;
        gap: 0.7rem; text-align: left; width: 100%;
    }
    .info-chip-action:hover .info-chip-value { color: {{ $chipHoverText }}; }
    .info-chip-action:hover .info-chip-icon { filter: brightness(1.1); }

    /* ── DESCRIPCIÓN ── */
    .desc-card {
        background: white; border-radius: 14px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.07);
        overflow: hidden; margin-bottom: 1rem;
    }
    .desc-body {
        padding: 1.2rem 1.3rem;
        background: #f8fafc;
        font-size: 0.92rem; color: #374151;
        line-height: 1.6; border-radius: 0 0 14px 14px;
        min-height: 60px; white-space: pre-wrap; word-break: break-word;
    }

    /* ── ESTADO INLINE (en la tarjeta info) ── */
    .estado-inline-chip {
        display: inline-flex; align-items: center; gap: 0.5rem;
        border-radius: 20px; padding: 0.3rem 0.9rem;
        font-size: 0.82rem; font-weight: 700;
    }
    .estado-dot-sm {
        width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0;
    }

    /* ── COMENTARIOS REDISEÑO ── */
    .comments-wrap {
        background: white; border-radius: 14px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.07);
        overflow: hidden;
    }
    .comments-wrap-header {
        background: linear-gradient(90deg, {{ $rolColor1 }} 0%, {{ $rolColor2 }} 100%);
        padding: 0.75rem 1.3rem;
        display: flex; align-items: center; justify-content: space-between;
        color: white;
    }
    .comments-wrap-header .ch-left {
        display: flex; align-items: center; gap: 0.6rem;
    }
    .comments-wrap-header i { font-size: 1rem; }
    .comments-wrap-header span { font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; }
    .comments-count-badge {
        background: rgba(255,255,255,0.2); color: white;
        border-radius: 50%; width: 26px; height: 26px;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.78rem; font-weight: 700;
        border: 1.5px solid rgba(255,255,255,0.35);
    }
    .comments-inner { padding: 1rem 1.2rem; }

    /* ── PRIORIDAD BADGES ── */
    .prio-baja  { background: #f0fdfa; color: #0d9488; }
    .prio-media { background: #fffbeb; color: #d97706; }
    .prio-alta  { background: #fee2e2; color: #dc2626; }

    /* ── STEPPER ── */
    .ticket-stepper {
        display: flex; align-items: flex-start;
        justify-content: space-between;
        padding: 0.25rem 0; gap: 0;
    }
    .stepper-step {
        display: flex; flex-direction: column;
        align-items: center; flex: 1;
        position: relative;
    }
    .stepper-step:not(:last-child)::after {
        content: ''; position: absolute;
        top: 13px; left: 55%; width: 90%; height: 3px;
        background: #dee2e6; z-index: 0; transition: background .3s;
    }
    .stepper-step.done:not(:last-child)::after,
    .stepper-step.active:not(:last-child)::after { background: var(--step-color, #16a34a); }
    .stepper-dot {
        width: 26px; height: 26px; border-radius: 50%;
        border: 3px solid #dee2e6; background: white;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.65rem; font-weight: 800;
        position: relative; z-index: 1;
        transition: all .3s; color: #adb5bd;
    }
    .stepper-step.done .stepper-dot {
        background: var(--step-color, #16a34a);
        border-color: var(--step-color, #16a34a); color: white;
    }
    .stepper-step.active .stepper-dot {
        background: var(--step-color, #16a34a);
        border-color: var(--step-color, #16a34a); color: white;
        animation: pulse-step 2s infinite;
    }
    @keyframes pulse-step {
        0%,100% { box-shadow: 0 0 0 4px color-mix(in srgb, var(--step-color,#16a34a) 20%, transparent); }
        50%      { box-shadow: 0 0 0 8px color-mix(in srgb, var(--step-color,#16a34a) 10%, transparent); }
    }
    .stepper-label {
        font-size: 0.58rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: .02em;
        margin-top: 5px; color: #adb5bd; text-align: center; line-height: 1.2;
    }
    .stepper-step.done .stepper-label,
    .stepper-step.active .stepper-label { color: var(--step-color, #16a34a); }

    /* ── ESTADO BADGE PANEL (derecho - no tocar) ── */
    .estado-badge-panel {
        display: flex; align-items: center; gap: 0.7rem;
        padding: 0.8rem 1rem; border-radius: 12px;
        font-weight: 700; font-size: 0.95rem;
    }
    .estado-badge-panel .estado-dot {
        width: 11px; height: 11px; border-radius: 50%;
        flex-shrink: 0; animation: pulse-step 2s infinite;
    }

    /* ── META CHIPS PANEL DERECHO ── */
    .meta-chips { display: flex; gap: 0.5rem; }
    .meta-chip {
        display: flex; align-items: center; gap: 0.4rem;
        background: white; border-radius: 8px;
        padding: 0.45rem 0.65rem; font-size: 0.78rem;
        color: #4b5563; box-shadow: 0 1px 4px rgba(0,0,0,.07);
        flex: 1; white-space: nowrap; overflow: hidden;
    }
    .meta-chip i { font-size: 0.9rem; opacity: .75; }
    .meta-chip span { overflow: hidden; text-overflow: ellipsis; }

    /* ── STICKY ── */
    @media (min-width: 992px) {
        .acciones-sticky { position: sticky; top: 1rem; }
    }
    @media (max-width: 991.98px) {
        .acciones-sticky { position: static; margin-top: 1rem; }
        .modal-xl { max-width: 95%; }
        .stepper-label { font-size: 0.52rem; }
        .info-chips-grid { grid-template-columns: repeat(2, 1fr); }
        .show-banner-title { font-size: 1.1rem; }
    }
    @media (max-width: 576px) {
        .show-banner { padding: 1.1rem 1.2rem; }
        .show-banner-actions { flex-wrap: wrap; }
        .info-chips-grid { grid-template-columns: 1fr 1fr; gap: 0.5rem; }
    }
</style>

@php
    // $esAdmin y $esTecnico son pasados explícitamente desde el controlador (NO desde session)

    // Colores de prioridad
    $prioNombre = $ticket['prioridad']['nombre'] ?? null;
    $sinPrioridad = $prioNombre === null;
    $prioNivel  = $prioNombre ? strtolower(str_replace(['\u00e1','\u00e9','\u00ed','\u00f3','\u00fa','\u00fc','\u00f1',' '], ['a','e','i','o','u','u','n','_'], $prioNombre)) : 'sin_asignar';
    $prioColors = [
        'baja'        => ['bg' => '#f0fdfa', 'color' => '#0d9488', 'icon' => 'bi-arrow-down-circle-fill'],
        'media'       => ['bg' => '#fffbeb', 'color' => '#d97706', 'icon' => 'bi-dash-circle-fill'],
        'alta'        => ['bg' => '#fee2e2', 'color' => '#dc2626', 'icon' => 'bi-arrow-up-circle-fill'],
        'sin_asignar' => ['bg' => '#eff6ff', 'color' => '#3b82f6', 'icon' => 'bi-clock-history'],
    ];
    $prioStyle = $prioColors[$prioNivel] ?? $prioColors['media'];
    $prioNombreDisplay = $prioNombre ?? 'Pendiente de asignaci\u00f3n';

    // Estado actual
    $estadoNombre = $ticket['estado']['nombre'] ?? 'Sin definir';
    $estadoTipo   = $ticket['estado']['tipo'] ?? 'abierto';
    $estadoColors = [
        'abierto'    => '#3b82f6',
        'en_proceso' => '#16a34a',
        'pendiente'  => '#f59e0b',
        'resuelto'   => '#10b981',
        'cerrado'    => '#6b7280',
        'cancelado'  => '#ef4444',
    ];
    $estadoColor = $estadoColors[$estadoTipo] ?? '#6b7280';
@endphp

{{-- ══════ BANNER SUPERIOR ══════ --}}
<div class="show-banner">
    <div class="d-flex align-items-start justify-content-between gap-3 flex-wrap">
        <div>
            <div class="show-banner-badge">
                <i class="bi bi-ticket-perforated-fill"></i>
                Ticket #{{ $ticket['id_ticket'] }}
            </div>
            <h1 class="show-banner-title">{{ $ticket['titulo'] }}</h1>
            <div class="d-flex align-items-center gap-2 mt-2 flex-wrap">
                {{-- Estado inline --}}
                <span class="show-banner-prioridad"
                      style="background: rgba(255,255,255,0.15); color: white; border: 1px solid rgba(255,255,255,0.25);">
                    <i class="bi bi-circle-fill" style="font-size:.45rem; color: {{ $estadoColor }};"></i>
                    {{ $estadoNombre }}
                </span>
                {{-- Prioridad inline --}}
                <span class="show-banner-prioridad"
                      style="background: {{ $prioStyle['bg'] }}; color: {{ $prioStyle['color'] }}; border: 1px solid {{ $prioStyle['color'] }}30;">
                    <i class="bi {{ $prioStyle['icon'] }}" style="font-size:.75rem;"></i>
                    {{ $prioNombre }}
                </span>
            </div>
        </div>
        <div class="show-banner-actions">
            @php
                $volverRuta = $esAdmin
                    ? route('tickets.index')
                    : ($esTecnico ? route('tickets.asignados') : route('tickets.mis-tickets'));
            @endphp
            <a href="{{ $volverRuta }}" class="btn btn-banner-back">
                <i class="bi bi-arrow-left-circle me-1"></i> Volver
            </a>
        </div>
    </div>
</div>

<div class="row g-3 align-items-start">
    <div class="{{ $esTecnico ? 'col-lg-9 col-12' : 'col-lg-8 col-12' }}">

        {{-- ══════ TARJETA INFO ══════ --}}
        <div class="show-main-card">
            <div class="show-card-header">
                <i class="bi bi-info-circle-fill"></i>
                <span>Información del Ticket</span>
            </div>
            <div class="info-chips-grid">

                {{-- Creado por --}}
                <div class="info-chip">
                    <div class="info-chip-icon" style="background:#eff6ff;">
                        <i class="bi bi-person-circle" style="color:#3b82f6;"></i>
                    </div>
                    <div>
                        <div class="info-chip-label">Creado por</div>
                        <div class="info-chip-value">{{ $ticket['usuario']['nombre_completo'] }}</div>
                    </div>
                </div>

                {{-- Departamento --}}
                <div class="info-chip">
                    <div class="info-chip-icon" style="background:#f0fdf4;">
                        <i class="bi bi-building" style="color:#16a34a;"></i>
                    </div>
                    <div>
                        <div class="info-chip-label">Departamento</div>
                        <div class="info-chip-value">{{ $ticket['area']['nombre'] }}</div>
                    </div>
                </div>

                {{-- Fecha de creación --}}
                <div class="info-chip">
                    <div class="info-chip-icon" style="background:#fff7ed;">
                        <i class="bi bi-calendar2-event" style="color:#ea580c;"></i>
                    </div>
                    <div>
                        <div class="info-chip-label">Fecha de creación</div>
                        <div class="info-chip-value">{{ \Carbon\Carbon::parse($ticket['fecha_creacion'])->setTimezone('America/Mexico_City')->format('d/m/Y H:i') }}</div>
                    </div>
                </div>

                {{-- Fecha de cierre --}}
                @if(!empty($ticket['fecha_cierre']))
                <div class="info-chip">
                    <div class="info-chip-icon" style="background:#f0fdf4;">
                        <i class="bi bi-calendar2-check" style="color:#16a34a;"></i>
                    </div>
                    <div>
                        <div class="info-chip-label">Fecha de cierre</div>
                        <div class="info-chip-value" style="color:#15803d; font-weight:700;">{{ \Carbon\Carbon::parse($ticket['fecha_cierre'])->setTimezone('America/Mexico_City')->format('d/m/Y H:i') }}</div>
                    </div>
                </div>
                @endif

                {{-- Prioridad --}}
                <div class="info-chip" @if($esAdmin && $sinPrioridad) style="border:1.5px dashed #93c5fd;" @endif>
                    <div class="info-chip-icon" style="background: {{ $prioStyle['bg'] }};">
                        <i class="bi {{ $prioStyle['icon'] }}" style="color: {{ $prioStyle['color'] }};"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="info-chip-label">Prioridad</div>
                        <div class="info-chip-value" style="color: {{ $prioStyle['color'] }};">
                            {{ $prioNombreDisplay }}
                        </div>
                    </div>
                </div>

                {{-- Técnico asignado --}}
                @if($esAdmin)
                {{-- Solo Admin puede abrir el modal de asignar --}}
                <div class="info-chip" style="cursor:pointer;"
                     data-bs-toggle="modal" data-bs-target="#modalAsignarTecnico"
                     title="Clic para asignar técnico">
                    <div class="info-chip-icon" style="background:#f5f3ff;">
                        <i class="bi bi-person-gear" style="color:#7c3aed;"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="info-chip-label">Técnico asignado</div>
                        <div class="info-chip-value d-flex align-items-center gap-1" style="color:#7c3aed;">
                            {{ $ticket['tecnico_asignado']['nombre_completo'] ?? 'Sin asignar' }}
                            <i class="bi bi-pencil" style="font-size:.7rem; opacity:.7;"></i>
                        </div>
                    </div>
                </div>
                @else
                {{-- Técnico y Usuario normal: solo lectura --}}
                <div class="info-chip">
                    <div class="info-chip-icon" style="background:#f5f3ff;">
                        <i class="bi bi-person-vcard" style="color:#7c3aed;"></i>
                    </div>
                    <div>
                        <div class="info-chip-label">Técnico asignado</div>
                        <div class="info-chip-value">{{ $ticket['tecnico_asignado']['nombre_completo'] ?? 'Sin asignar' }}</div>
                    </div>
                </div>
                @endif

            </div>
        </div>

        {{-- ══════ DESCRIPCIÓN ══════ --}}
        <div class="desc-card">
            <div class="show-card-header">
                <i class="bi bi-file-text-fill"></i>
                <span>Descripción del Problema</span>
            </div>
            <div class="desc-body">{{ $ticket['descripcion'] }}</div>
        </div>

        {{-- ══════ COMENTARIOS ══════ --}}
        <div class="comments-wrap">
            <div class="comments-wrap-header">
                <div class="ch-left">
                    <i class="bi bi-chat-dots-fill"></i>
                    <span>Historial de Comentarios</span>
                </div>
                <span class="comments-count-badge">{{ count($comentarios ?? []) }}</span>
            </div>
            <div class="comments-inner">
                @include('tickets.partials.comments', ['comentarios' => $comentarios ?? [], 'ticket' => $ticket])
            </div>
        </div>

    </div>

    <div class="{{ $esTecnico ? 'col-lg-3 col-12' : 'col-lg-4 col-12' }}">
        <div class="acciones-sticky">
        @php
            $stepFlow = [
                ['tipo' => 'abierto',    'label' => 'Abierto'],
                ['tipo' => 'en_proceso', 'label' => 'En Proceso'],
                ['tipo' => 'pendiente',  'label' => 'Pendiente'],
                ['tipo' => 'resuelto',   'label' => 'Resuelto'],
                ['tipo' => 'cerrado',    'label' => 'Cerrado'],
            ];
            $tipoActual = $ticket['estado']['tipo'] ?? 'abierto';
            $cancelado  = $tipoActual === 'cancelado';
            $idxActual  = collect($stepFlow)->search(fn($s) => $s['tipo'] === $tipoActual);
            if ($idxActual === false) $idxActual = 0;
            $colorMap = [
                'abierto'    => '#3b82f6',
                'en_proceso' => '#16a34a',
                'pendiente'  => '#f59e0b',
                'resuelto'   => '#10b981',
                'cerrado'    => '#6b7280',
                'cancelado'  => '#ef4444',
            ];
            $iconMap = [
                'abierto'    => 'bi-folder2-open',
                'en_proceso' => 'bi-arrow-repeat',
                'pendiente'  => 'bi-hourglass-split',
                'resuelto'   => 'bi-check-circle-fill',
                'cerrado'    => 'bi-lock-fill',
                'cancelado'  => 'bi-x-circle-fill',
            ];
            $colorActual = $colorMap[$tipoActual] ?? '#6b7280';
            $iconActual  = $iconMap[$tipoActual]  ?? 'bi-circle';

            // Tiempo relativo para última actualización
            try {
                $updatedAt = \Carbon\Carbon::createFromFormat('d/m/Y H:i', $ticket['updated_at_formatted'] ?? '');
            } catch (\Throwable $e) {
                $updatedAt = \Carbon\Carbon::now();
            }
            $diffRelativo = $updatedAt->locale('es')->diffForHumans();

            // Fecha apertura corta
            $fechaCorta = \Carbon\Carbon::parse($ticket['fecha_creacion'])->format('d/m/Y');

            // Título del header según rol
            $headerTitulo = $esTecnico ? 'Gestión Técnica' : ($esAdmin ? 'Panel Admin' : 'Estado del Ticket');
            $headerIcono  = $esTecnico ? 'bi-tools' : ($esAdmin ? 'bi-shield-lock-fill' : 'bi-info-circle-fill');
        @endphp

        <div class="card border-0 shadow overflow-hidden">

            {{-- HEADER con ícono grande + título + número --}}
            <div class="card-header border-0 px-4 py-3 text-white"
                 style="background: linear-gradient(135deg, {{ $colorActual }} 0%, color-mix(in srgb, {{ $colorActual }} 65%, #000) 100%);">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-circle bg-white bg-opacity-25"
                         style="width:42px; height:42px; flex-shrink:0;">
                        <i class="bi {{ $headerIcono }}" style="font-size:1.2rem;"></i>
                    </div>
                    <div class="flex-grow-1 overflow-hidden">
                        <div class="fw-bold text-uppercase" style="font-size:.82rem; letter-spacing:.06em; opacity:.85; line-height:1.1;">
                            {{ $headerTitulo }}
                        </div>
                        <div class="fw-bold" style="font-size:1rem; line-height:1.2;">
                            Ticket #{{ $ticket['id_ticket'] }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body px-3 pt-3 pb-3" style="background: linear-gradient(160deg,#f8f9fa 0%,#e9ecef 100%);">

                {{-- ESTADO BADGE --}}
                <div class="mb-3 px-3 py-2 rounded-3 d-flex align-items-center gap-2"
                     style="background: color-mix(in srgb, {{ $colorActual }} 10%, white); border: 2px solid color-mix(in srgb, {{ $colorActual }} 25%, transparent);"
                     data-estado-badge>
                    <div style="width:10px; height:10px; border-radius:50%; background:{{ $colorActual }}; flex-shrink:0; animation: pulse-step 2s infinite; --step-color:{{ $colorActual }};"></div>
                    <div>
                        <div style="font-size:.6rem; text-transform:uppercase; letter-spacing:.07em; color:#9ca3af; font-weight:700; margin-bottom:1px;">Estado</div>
                        <div class="fw-bold" style="color:{{ $colorActual }}; font-size:.95rem; line-height:1.2;">
                            <i class="bi {{ $iconActual }} me-1"></i>{{ $ticket['estado']['nombre'] }}
                        </div>
                    </div>
                </div>

                {{-- STEPPER --}}
                @if(!$cancelado)
                <div class="mb-3 px-1">
                    <div class="ticket-stepper">
                        @foreach($stepFlow as $i => $step)
                            @php
                                $esDone   = $i < $idxActual;
                                $esActive = $i === $idxActual;
                                $clase    = $esDone ? 'done' : ($esActive ? 'active' : '');
                                $color    = $colorMap[$step['tipo']] ?? '#6b7280';
                            @endphp
                            <div class="stepper-step {{ $clase }}" style="--step-color: {{ $color }};">
                                <div class="stepper-dot">
                                    @if($esDone)
                                        <i class="bi bi-check" style="font-size:.75rem;"></i>
                                    @else
                                        {{ $i + 1 }}
                                    @endif
                                </div>
                                <div class="stepper-label">{{ $step['label'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @else
                <div class="alert alert-danger py-2 px-3 mb-3" style="font-size:.82rem; border-radius:10px;">
                    <i class="bi bi-x-circle-fill me-1"></i> Ticket cancelado
                </div>
                @endif

                {{-- CHIPS: apilados verticalmente para evitar truncado --}}
                <div class="d-flex flex-column gap-2 mb-3">
                    <div class="d-flex align-items-center gap-2 bg-white rounded-3 px-3 py-2 shadow-sm">
                        <i class="bi bi-calendar2-check" style="color:#3b82f6; font-size:1rem; flex-shrink:0;"></i>
                        <div>
                            <div style="font-size:.6rem; text-transform:uppercase; letter-spacing:.06em; color:#9ca3af; font-weight:700;">Apertura</div>
                            <div class="fw-bold" style="font-size:.85rem; color:#374151;">{{ $fechaCorta }}</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 bg-white rounded-3 px-3 py-2 shadow-sm">
                        <i class="bi bi-clock-history" style="color:#f59e0b; font-size:1rem; flex-shrink:0;"></i>
                        <div>
                            <div style="font-size:.6rem; text-transform:uppercase; letter-spacing:.06em; color:#9ca3af; font-weight:700;">Última actualización</div>
                            <div class="fw-bold" style="font-size:.85rem; color:#374151;" data-ultima-actualizacion>{{ $diffRelativo }}</div>
                        </div>
                    </div>
                </div>

                {{-- BOTÓN PRINCIPAL --}}
                @if($esTecnico || $esAdmin)
                    <button type="button"
                            class="btn w-100 fw-bold py-3 d-flex align-items-center justify-content-center gap-2"
                            style="background: {{ $colorActual }}; color:white; border:none; border-radius:12px; font-size:1rem; box-shadow: 0 6px 18px color-mix(in srgb, {{ $colorActual }} 40%, transparent); transition: filter .2s, transform .15s;"
                            onmouseover="this.style.filter='brightness(1.08)'; this.style.transform='translateY(-1px)'"
                            onmouseout="this.style.filter='brightness(1)'; this.style.transform='translateY(0)'"
                            data-bs-toggle="modal" data-bs-target="#modalGestionTicket">
                        <i class="bi {{ $esTecnico ? 'bi-check-circle-fill' : 'bi-shield-lock-fill' }}" style="font-size:1.1rem;"></i>
                        {{ $esTecnico ? 'Actualizar Estado' : 'Modificar Estado' }}
                    </button>
                @else
                    <div class="text-center p-3 rounded-3 bg-white shadow-sm" style="border: 1.5px dashed #93c5fd; font-size:.85rem; color:#3b82f6;">
                        <i class="bi bi-pencil-square me-1"></i>Añade comentarios abajo
                    </div>
                @endif

            </div>
        </div>

        {{-- TARJETA ASIGNAR PRIORIDAD (Solo Admin) --}}
        @if($esAdmin)
        <div class="card border-0 shadow-sm overflow-hidden mt-3">
            <div class="card-header border-0 px-4 py-3 text-white"
                 style="background: linear-gradient(135deg, #1e3a5f 0%, #1d4ed8 100%);">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-flag-fill" style="font-size:1rem;"></i>
                    <span class="fw-bold" style="font-size:.9rem;">Prioridad del Ticket</span>
                </div>
            </div>
            <div class="card-body px-3 py-3" style="background: linear-gradient(160deg,#f0f9ff 0%,#e0f2fe 100%);">
                @if($sinPrioridad)
                <div class="d-flex align-items-center gap-2 mb-3 px-3 py-2 rounded-3"
                     style="background:#f0fdfa; border:1.5px dashed #5eead4;">
                    <i class="bi bi-clock-history" style="color:#0f766e; font-size:.95rem;"></i>
                    <div style="font-size:.8rem; color:#0f766e; font-weight:700; line-height:1.2;">
                        Sin prioridad asignada<br>
                        <span style="font-weight:400; color:#6b7280; font-size:.72rem;">Asigna una a continuación</span>
                    </div>
                </div>
                @else
                <div class="d-flex align-items-center gap-2 mb-3 px-3 py-2 rounded-3"
                     style="background: {{ $prioStyle['bg'] }}; border:1.5px solid color-mix(in srgb, {{ $prioStyle['color'] }} 30%, transparent);">
                    <i class="bi {{ $prioStyle['icon'] }}" style="color:{{ $prioStyle['color'] }}; font-size:.95rem;"></i>
                    <div style="font-size:.85rem; font-weight:700; color:{{ $prioStyle['color'] }}; line-height:1.2;">
                        {{ $prioNombreDisplay }}<br>
                        <span style="font-weight:400; color:#6b7280; font-size:.72rem;">Puedes modificarla abajo</span>
                    </div>
                </div>
                @endif

                <form action="{{ route('tickets.cambiar-prioridad', $ticket['id_ticket']) }}" method="POST">
                    @csrf
                    <select name="prioridad_id"
                            style="width:100%; padding:.6rem .9rem; font-size:.88rem; font-weight:700; border-radius:10px;
                                   border:2px solid #bae6fd; background:white; color:#1e293b; outline:none; cursor:pointer;
                                   transition:border-color .2s, box-shadow .2s; appearance:auto; margin-bottom:.75rem;"
                            onfocus="this.style.borderColor='#0891b2'; this.style.boxShadow='0 0 0 3px rgba(8,145,178,0.14)';"
                            onblur="this.style.borderColor='#bae6fd'; this.style.boxShadow='none';">
                        <option value="">— Selecciona prioridad —</option>
                        @foreach($prioridades as $p)
                        <option value="{{ $p->id_prioridad }}"
                            {{ ($ticket['prioridad']['id_prioridad'] ?? '') == $p->id_prioridad ? 'selected' : '' }}>
                            {{ $p->nombre }}
                        </option>
                        @endforeach
                    </select>
                    <button type="submit"
                            class="btn w-100 fw-bold py-2"
                            style="border-radius:10px; background:linear-gradient(135deg,#1e3a5f,#1d4ed8); color:white; border:none;
                                   font-size:.9rem; box-shadow:0 4px 14px rgba(29,78,216,0.30); transition:all .2s;"
                            onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 6px 20px rgba(29,78,216,0.42)';"
                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 14px rgba(29,78,216,0.30)';">
                        <i class="bi bi-check-circle-fill me-2"></i>Asignar Prioridad
                    </button>
                </form>
            </div>
        </div>
        @endif

        </div>
    </div>
</div>

<div class="modal fade" id="modalGestionTicket" tabindex="-1" aria-hidden="true">
    @if($esTecnico || $esAdmin)
    @php
        $mColor1 = $esTecnico ? '#16a34a' : '#1e3a5f';
        $mColor2 = $esTecnico ? '#15803d' : '#1d4ed8';
        $mIcon   = $esTecnico ? 'bi-sliders' : 'bi-shield-lock-fill';
        $mTitulo = $esTecnico ? 'Gestión Técnica' : 'Panel Administrador';
        $mShadow = $esTecnico ? 'rgba(22,163,74,0.35)' : 'rgba(29,78,216,0.35)';
        $mFocus  = $esTecnico ? '#16a34a' : '#1d4ed8';
        $mFocusBg= $esTecnico ? 'rgba(22,163,74,0.12)' : 'rgba(29,78,216,0.12)';
    @endphp
    <div class="modal-dialog modal-dialog-centered modal-xl" style="max-width: min(92vw, 860px);">
        <div class="modal-content border-0 shadow-lg overflow-hidden" style="border-radius: 18px;">

            {{-- HEADER --}}
            <div class="modal-header border-0 px-4 py-4 text-white"
                 style="background: linear-gradient(135deg, {{ $mColor1 }} 0%, {{ $mColor2 }} 100%); position:relative; overflow:hidden;">
                <div style="position:absolute;top:-30px;right:-30px;width:130px;height:130px;border-radius:50%;background:rgba(255,255,255,0.06);"></div>
                <div style="position:absolute;bottom:-40px;right:80px;width:90px;height:90px;border-radius:50%;background:rgba(255,255,255,0.04);"></div>
                <div class="d-flex align-items-center gap-3 position-relative">
                    <div class="d-flex align-items-center justify-content-center rounded-circle"
                         style="width:46px;height:46px;background:rgba(255,255,255,0.15);flex-shrink:0;border:1.5px solid rgba(255,255,255,0.3);">
                        <i class="bi {{ $mIcon }}" style="font-size:1.2rem;"></i>
                    </div>
                    <div>
                        <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;opacity:.8;line-height:1;">{{ $mTitulo }}</div>
                        <div style="font-size:1.1rem;font-weight:800;line-height:1.3;">Gestión de Ticket #{{ $ticket['id_ticket'] }}</div>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white position-absolute"
                        style="top:1.1rem;right:1.1rem;" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('tickets.cambiar-estado', $ticket['id_ticket']) }}" method="POST">
                @csrf
                <input type="hidden" name="tecnico_id" value="{{ auth()->id() }}">

                <div class="modal-body p-0">
                    <div class="row g-0">

                        {{-- COLUMNA IZQUIERDA --}}
                        <div class="col-md-5" style="background:#f8fafc; border-right:1.5px solid #e2e8f0; padding:1.8rem 1.6rem;">

                            {{-- Usuario operando --}}
                            <div class="mb-4">
                                <div style="font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#94a3b8;margin-bottom:8px;">
                                    Usuario operando
                                </div>
                                <div class="d-flex align-items-center gap-2 rounded-3 px-3 py-2"
                                     style="background:white; border:1.5px solid #e2e8f0; box-shadow:0 1px 4px rgba(0,0,0,0.05);">
                                    <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0"
                                         style="width:36px;height:36px;background:linear-gradient(135deg,{{ $mColor1 }},{{ $mColor2 }});color:white;font-weight:800;font-size:0.85rem;">
                                        {{ strtoupper(substr(session('usuario_nombre') ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div style="font-size:0.88rem;font-weight:700;color:#1e293b;line-height:1.2;">{{ session('usuario_nombre') }}</div>
                                        <div style="font-size:0.7rem;color:#64748b;font-weight:600;">{{ session('usuario_rol') }}</div>
                                    </div>
                                </div>
                            </div>

                            {{-- Estado actual --}}
                            <div class="mb-4">
                                <div style="font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#94a3b8;margin-bottom:8px;">
                                    Estado actual
                                </div>
                                <div class="d-flex align-items-center gap-2 rounded-3 px-3 py-2"
                                     style="background:white; border:1.5px solid #e2e8f0;">
                                    <div style="width:9px;height:9px;border-radius:50%;background:{{ $colorActual }};flex-shrink:0;"></div>
                                    <span style="font-size:0.88rem;font-weight:700;color:{{ $colorActual }};">{{ $ticket['estado']['nombre'] }}</span>
                                </div>
                            </div>

                            {{-- Cambiar estado a --}}
                            <div>
                                <div style="font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#94a3b8;margin-bottom:8px;">
                                    Cambiar estado a
                                </div>
                                <select name="estado_id"
                                        style="width:100%;padding:0.65rem 0.9rem;font-size:0.9rem;font-weight:700;border-radius:10px;
                                               border:2px solid #e2e8f0;background:white;color:#1e293b;cursor:pointer;outline:none;
                                               transition:border-color .2s,box-shadow .2s; appearance:auto;"
                                        onfocus="this.style.borderColor='{{ $mFocus }}';this.style.boxShadow='0 0 0 3px {{ $mFocusBg }}';"
                                        onblur="this.style.borderColor='#e2e8f0';this.style.boxShadow='none';">
                                    @foreach($estados ?? [] as $est)
                                        @if($esTecnico)
                                            @if(in_array($est['nombre'], ['En Proceso', 'Pendiente', 'Resuelto']))
                                                <option value="{{ $est['id_estado'] }}" {{ $ticket['estado']['id_estado'] == $est['id_estado'] ? 'selected' : '' }}>
                                                    {{ $est['nombre'] }}
                                                </option>
                                            @endif
                                        @else
                                            @if(in_array($est['nombre'], ['Abierto', 'Cerrado']))
                                                <option value="{{ $est['id_estado'] }}" {{ $ticket['estado']['id_estado'] == $est['id_estado'] ? 'selected' : '' }}>
                                                    {{ $est['nombre'] }}
                                                </option>
                                            @endif
                                        @endif
                                    @endforeach
                                </select>
                                <div class="d-flex align-items-center gap-1 mt-2" style="font-size:0.75rem;color:#94a3b8;">
                                    <i class="bi bi-info-circle"></i>
                                    {{ $esTecnico ? 'Opciones disponibles para técnico' : 'Opciones exclusivas de administrador' }}
                                </div>
                            </div>

                        </div>

                        {{-- COLUMNA DERECHA --}}
                        <div class="col-md-7" style="padding:1.8rem 1.6rem; background:white;">
                            <div style="font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#94a3b8;margin-bottom:8px;">
                                Comentario / Avance <span style="color:#ef4444;">*</span>
                            </div>
                            <textarea name="contenido" rows="9" required
                                      placeholder="Describe el motivo del cambio de estado, los avances realizados o cualquier detalle importante para el seguimiento del ticket..."
                                      style="width:100%;padding:0.85rem 1rem;font-size:0.9rem;border-radius:10px;border:2px solid #e2e8f0;
                                             resize:vertical;min-height:210px;line-height:1.6;color:#374151;outline:none;font-family:inherit;
                                             transition:border-color .2s,box-shadow .2s;"
                                      onfocus="this.style.borderColor='{{ $mFocus }}';this.style.boxShadow='0 0 0 3px {{ $mFocusBg }}';"
                                      onblur="this.style.borderColor='#e2e8f0';this.style.boxShadow='none';"></textarea>
                            <div class="d-flex align-items-center gap-1 mt-2" style="font-size:0.75rem;color:#94a3b8;">
                                <i class="bi bi-pencil-square"></i>
                                Este comentario quedará registrado en el historial del ticket
                            </div>
                        </div>

                    </div>
                </div>

                {{-- FOOTER --}}
                <div class="modal-footer border-0 px-4 py-3 gap-2" style="background:#f8fafc; border-top:1.5px solid #e2e8f0 !important;">
                    <button type="button"
                            class="btn px-4 py-2"
                            style="border-radius:10px;background:#f1f5f9;color:#475569;font-weight:700;border:none;font-size:0.9rem;transition:.2s;"
                            onmouseover="this.style.background='#e2e8f0'"
                            onmouseout="this.style.background='#f1f5f9'"
                            data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="btn px-5 py-2"
                            style="border-radius:10px;background:linear-gradient(135deg,{{ $mColor1 }},{{ $mColor2 }});color:white;font-weight:700;
                                   border:none;font-size:0.9rem;box-shadow:0 4px 14px {{ $mShadow }};transition:all .2s;"
                            onmouseover="this.style.transform='translateY(-1px)';this.style.boxShadow='0 6px 20px {{ $mShadow }}';"
                            onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 14px {{ $mShadow }}';">
                        <i class="bi {{ $esTecnico ? 'bi-check-circle-fill' : 'bi-shield-check' }} me-2"></i>
                        {{ $esTecnico ? 'Guardar Cambios' : 'Aplicar Cambios' }}
                    </button>
                </div>
            </form>

        </div>
    </div>
    @endif
</div>

{{-- MODAL: ASIGNAR TÉCNICO (Solo Administrador) --}}
@if(!$esTecnico)
<div class="modal fade" id="modalAsignarTecnico" tabindex="-1" aria-labelledby="modalAsignarTecnicoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 460px;">
        <div class="modal-content border-0 shadow-lg overflow-hidden" style="border-radius: 16px;">

            {{-- HEADER --}}
            <div class="modal-header border-0 px-4 py-4 text-white position-relative"
                 style="background: linear-gradient(135deg, #1e3a5f 0%, #1d4ed8 100%);">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-circle"
                         style="width:44px; height:44px; background: rgba(255,255,255,0.15); flex-shrink:0;">
                        <i class="bi bi-person-check-fill" style="font-size:1.2rem;"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="modalAsignarTecnicoLabel" style="font-size:1.05rem; line-height:1.2;">
                            Asignar Técnico
                        </h5>
                        <div style="font-size:0.75rem; opacity:0.75;">Ticket #{{ $ticket['id_ticket'] }}</div>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white position-absolute"
                        style="top:1rem; right:1rem;"
                        data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <form action="{{ route('tickets.asignar-tecnico', $ticket['id_ticket']) }}" method="POST">
                @csrf
                <div class="modal-body px-4 pt-4 pb-2">

                    {{-- Info actual --}}
                    @if($ticket['tecnico_asignado'])
                    <div class="d-flex align-items-center gap-2 rounded-3 px-3 py-2 mb-4"
                         style="background:#f0fdf4; border: 1.5px solid #bbf7d0;">
                        <i class="bi bi-person-badge-fill" style="color:#16a34a; font-size:1rem;"></i>
                        <div>
                            <div style="font-size:0.65rem; text-transform:uppercase; letter-spacing:.06em; font-weight:700; color:#6b7280;">Actual</div>
                            <div style="font-size:0.85rem; font-weight:700; color:#15803d;">{{ $ticket['tecnico_asignado']['nombre_completo'] }}</div>
                        </div>
                    </div>
                    @else
                    <div class="d-flex align-items-center gap-2 rounded-3 px-3 py-2 mb-4"
                         style="background:#fafafa; border: 1.5px dashed #d1d5db;">
                        <i class="bi bi-person-slash" style="color:#9ca3af; font-size:1rem;"></i>
                        <div style="font-size:0.85rem; color:#6b7280; font-weight:600;">Sin técnico asignado actualmente</div>
                    </div>
                    @endif

                    {{-- Select de técnico --}}
                    <div class="mb-3">
                        <label for="tecnico_select" class="d-block mb-2"
                               style="font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:#64748b;">
                            Seleccionar técnico
                        </label>
                        <div class="position-relative">
                            <i class="bi bi-person-gear position-absolute"
                               style="left:12px; top:50%; transform:translateY(-50%); color:#1d4ed8; font-size:1rem; z-index:5; pointer-events:none;"></i>
                            <select class="form-select" id="tecnico_select" name="tecnico_id"
                                    style="padding: 0.7rem 0.75rem 0.7rem 2.4rem; font-size:0.92rem; border-radius:10px; border:2px solid #e2e8f0; font-weight:600; cursor:pointer;
                                           transition: border-color .2s, box-shadow .2s;"
                                    onfocus="this.style.borderColor='#1d4ed8'; this.style.boxShadow='0 0 0 3px rgba(29,78,216,0.12)';"
                                    onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none';">
                                <option value="">— Sin asignar —</option>
                                @foreach($tecnicos as $tecnico)
                                    <option value="{{ $tecnico->id_usuario }}"
                                        {{ $ticket['tecnico_asignado_id'] == $tecnico->id_usuario ? 'selected' : '' }}>
                                        {{ $tecnico->nombre }} {{ $tecnico->apellido }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    @if($errors->any())
                    <div class="alert alert-danger py-2 px-3 mb-0" style="border-radius:10px; font-size:0.85rem;">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        @foreach($errors->all() as $error) {{ $error }} @endforeach
                    </div>
                    @endif

                </div>

                {{-- FOOTER --}}
                <div class="modal-footer border-0 px-4 pb-4 pt-2 gap-2">
                    <button type="button"
                            class="btn px-4 py-2 fw-700"
                            style="border-radius:10px; background:#f1f5f9; color:#475569; font-weight:700; border:none; font-size:0.9rem; transition:.2s;"
                            onmouseover="this.style.background='#e2e8f0'"
                            onmouseout="this.style.background='#f1f5f9'"
                            data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="btn px-5 py-2"
                            style="border-radius:10px; background: linear-gradient(135deg,#1e3a5f,#1d4ed8); color:white; font-weight:700; border:none; font-size:0.9rem;
                                   box-shadow: 0 4px 14px rgba(29,78,216,0.35); transition: all .2s;"
                            onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 6px 20px rgba(29,78,216,0.45)';"
                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 14px rgba(29,78,216,0.35)';">
                        <i class="bi bi-check-circle-fill me-2"></i>Asignar
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
@endif

@endsection