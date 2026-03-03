<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualización de Ticket - UPTEX Soporte</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, Helvetica, sans-serif; background: #F1F5F9; color: #1e293b; }
        .wrapper { max-width: 620px; margin: 32px auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.10); }

        /* ── HEADER ── */
        .header { padding: 2rem 2.2rem; position: relative; overflow: hidden; }
        .header::before { content: ''; position: absolute; top: -40px; right: -40px; width: 160px; height: 160px; border-radius: 50%; background: rgba(255,255,255,0.06); }
        .header::after  { content: ''; position: absolute; bottom: -50px; right: 100px; width: 110px; height: 110px; border-radius: 50%; background: rgba(255,255,255,0.04); }
        .header-inner { position: relative; z-index: 1; display: flex; align-items: center; gap: 1rem; }
        .header-icon { width: 50px; height: 50px; background: rgba(255,255,255,0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0; }
        .header h1 { color: #fff; font-size: 1.25rem; font-weight: 700; }
        .header p  { color: rgba(255,255,255,0.78); font-size: 0.85rem; margin-top: 2px; }

        /* ── BODY ── */
        .body { padding: 1.5rem 1.8rem 1.8rem; }
        .greeting { font-size: 1rem; color: #334155; margin-bottom: 0.8rem; line-height: 1.5; }
        .greeting strong { color: #1e3a5f; }
        .subtitle { font-size: .9rem; color: #475569; line-height: 1.55; margin-bottom: 1.2rem; }

        /* ── TICKET BADGE ── */
        .ticket-ref { display: flex; align-items: center; gap: .7rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: .8rem 1rem; margin-bottom: 1.4rem; }
        .ticket-ref-num { font-weight: 800; color: #1e3a5f; font-size: .95rem; }
        .ticket-ref-title { font-size: .88rem; color: #475569; }

        /* ── TRANSICIÓN DE ESTADO ── */
        .estado-cambio { display: flex; align-items: center; justify-content: center; gap: .7rem; margin: 1.2rem 0; flex-wrap: wrap; }
        .estado-box { display: inline-block; padding: .35rem .9rem; border-radius: 20px; font-size: .82rem; font-weight: 700; }
        .arrow { color: #94a3b8; font-size: 1.1rem; font-weight: 700; }

        /* ── INFO GRID ── */
        .info-grid { display: table; width: 100%; border-collapse: collapse; margin: 1.2rem 0; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; }
        .info-row { display: table-row; }
        .info-label { display: table-cell; width: 38%; background: #f8fafc; padding: .6rem .8rem; font-size: .79rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: .04em; border-bottom: 1px solid #e2e8f0; vertical-align: top; }
        .info-value { display: table-cell; padding: .6rem .8rem; font-size: .88rem; color: #1e293b; border-bottom: 1px solid #e2e8f0; vertical-align: top; }
        .info-row:last-child .info-label,
        .info-row:last-child .info-value { border-bottom: none; }

        /* ── CHIPS ── */
        .chip { display: inline-block; padding: .2rem .65rem; border-radius: 20px; font-size: .78rem; font-weight: 700; }
        .chip-abierto   { background: #dbeafe; color: #1d4ed8; }
        .chip-pendiente { background: #fef9c3; color: #854d0e; }
        .chip-proceso   { background: #e0f2fe; color: #0369a1; }
        .chip-resuelto  { background: #dcfce7; color: #15803d; }
        .chip-cerrado   { background: #f1f5f9; color: #475569; }
        .chip-baja      { background: #dcfce7; color: #15803d; }
        .chip-media     { background: #fef9c3; color: #854d0e; }
        .chip-alta      { background: #ffedd5; color: #9a3412; }
        .chip-alta    { background: #fee2e2; color: #dc2626; }

        /* ── SECCIÓN COMENTARIO / AVANCE ── */
        .comentario-section { margin: 1.4rem 0; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; }
        .comentario-header { background: #1e3a5f; color: #fff; padding: .65rem 1rem; font-size: .8rem; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; display: flex; align-items: center; gap: .5rem; }
        .comentario-header span { opacity: .8; }
        .comentario-body { background: #f8fafc; padding: 1rem 1.1rem; font-size: .9rem; color: #334155; line-height: 1.65; white-space: pre-line; }
        .comentario-footer-note { background: #fff; border-top: 1px solid #e2e8f0; padding: .55rem 1rem; font-size: .78rem; color: #64748b; display: flex; align-items: center; gap: .4rem; }

        /* ── OPERADOR ── */
        .operador-wrap { display: flex; align-items: center; gap: .8rem; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: .8rem 1rem; margin: 1.2rem 0; }
        .operador-avatar { width: 38px; height: 38px; border-radius: 9px; background: #16a34a; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: .9rem; flex-shrink: 0; }
        .operador-avatar.admin { background: #1d4ed8; }
        .operador-name { font-weight: 700; color: #15803d; font-size: .9rem; }
        .operador-name.admin { color: #1d4ed8; }
        .operador-role { font-size: .78rem; color: #64748b; }

        /* ── CTA CIERRE (cuando es resuelto/cerrado) ── */
        .cierre-banner { background: linear-gradient(135deg, #15803d, #16a34a); border-radius: 12px; padding: 1.2rem; margin: 1.2rem 0; text-align: center; color: #fff; }
        .cierre-banner h3 { font-size: 1.05rem; margin-bottom: .3rem; }
        .cierre-banner p { font-size: .85rem; opacity: .88; }

        /* ── CTA ── */
        .cta-wrap { text-align: center; margin: 1.4rem 0 .5rem; }
        .cta-btn { display: inline-block; background: linear-gradient(135deg, #1e3a5f, #1d4ed8); color: #fff; text-decoration: none; padding: 1rem 2.5rem; border-radius: 12px; font-weight: 800; font-size: 1.05rem; box-shadow: 0 6px 22px rgba(29,78,216,.40); letter-spacing:.02em; }

        /* ── FOOTER ── */
        .footer { background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 1.2rem 1.8rem; text-align: center; font-size: .78rem; color: #94a3b8; line-height: 1.6; }
        .footer a { color: #1d4ed8; text-decoration: none; }

        @media (max-width: 480px) {
            .wrapper { margin: 0; border-radius: 0; }
            .body { padding-left: 1.2rem; padding-right: 1.2rem; }
        }
    </style>
</head>
<body>
<div class="wrapper">

@php
    $ticketId = str_pad($ticket->id_ticket, 5, '0', STR_PAD_LEFT);

    $prioNombre = strtolower($ticket->prioridad->nombre ?? 'media');
    $prioChip   = match(true) {
        str_contains($prioNombre, 'baja')  => 'chip-baja',
        str_contains($prioNombre, 'media') => 'chip-media',
        str_contains($prioNombre, 'alta')  => 'chip-alta',
        default => 'chip-media',
    };

    $chipMap = [
        'abierto'    => 'chip-abierto',
        'pendiente'  => 'chip-pendiente',
        'en_proceso' => 'chip-proceso',
        'resuelto'   => 'chip-resuelto',
        'cerrado'    => 'chip-cerrado',
    ];
    $chipAnterior = $chipMap[strtolower($estadoAnteriorTipo)] ?? 'chip-abierto';
    $chipNuevo    = $chipMap[strtolower($estadoNuevoTipo)]    ?? 'chip-abierto';

    $esCierre     = in_array(strtolower($estadoNuevoTipo), ['resuelto', 'cerrado']);
    $esAdmin      = $tipoDestinatario === 'admin';
    $esTecnico    = $tipoDestinatario === 'tecnico';

    $headerColor  = $esTecnico ? 'linear-gradient(135deg,#15803d,#16a34a)' : 'linear-gradient(135deg,#1e3a5f,#1d4ed8)';
    $avatarClass  = $esTecnico ? '' : 'admin';
    $nombreClass  = $esTecnico ? '' : 'admin';

    $usuarioNombre = trim(($ticket->usuario->nombre ?? '') . ' ' . ($ticket->usuario->apellido ?? ''));

    // Iniciales del operador
    $partes  = explode(' ', $operadorNombre);
    $iniciales = strtoupper(substr($partes[0] ?? 'O', 0, 1) . substr($partes[1] ?? '', 0, 1));
@endphp

    {{-- HEADER dinámico según quien opera --}}
    <div class="header" style="background: {{ $headerColor }};">
        <div class="header-inner">
            <div class="header-icon">
                @if($esCierre) ✅ @else 🔄 @endif
            </div>
            <div>
                <h1>@if($estadoNuevoTipo === 'cerrado') Ticket Cerrado @elseif($esCierre) Ticket Resuelto @else Actualización de Ticket @endif</h1>
                <p>Universidad Politécnica de Texcoco · Sistema de Soporte</p>
            </div>
        </div>
    </div>

    <div class="body">

        {{-- SALUDO --}}
        @if($esAdmin)
            <p class="greeting">Hola <strong>Equipo Administrador</strong>,</p>
            <p class="subtitle">El ticket <strong>#{{ $ticketId }}</strong> ha sido actualizado por un miembro del equipo.</p>
        @elseif($esTecnico)
            <p class="greeting">Hola <strong>{{ $operadorNombre }}</strong>,</p>
            <p class="subtitle">Se ha registrado una actualización en el ticket <strong>#{{ $ticketId }}</strong> que tienes asignado.</p>
        @else
            <p class="greeting">Hola <strong>{{ $usuarioNombre }}</strong>,</p>
            @if($esCierre)
                @if($estadoNuevoTipo === 'cerrado')
                    <p class="subtitle">Tu ticket <strong>#{{ $ticketId }}</strong> ha sido <strong>cerrado</strong> y archivado en el sistema.</p>
                @else
                    <p class="subtitle">¡Tu ticket ha sido <strong>resuelto</strong>. El equipo técnico ha completado el trabajo.</p>
                @endif
            @else
                <p class="subtitle">Tu ticket de soporte <strong>#{{ $ticketId }}</strong> ha recibido una actualización.</p>
            @endif
        @endif

        {{-- REFERENCIA DE TICKET --}}
        <div class="ticket-ref">
            <div>
                <div class="ticket-ref-num">#{{ $ticketId }}</div>
                <div class="ticket-ref-title">{{ $ticket->titulo }}</div>
            </div>
        </div>

        {{-- TRANSICIÓN DE ESTADO --}}
        <div class="estado-cambio">
            <span class="estado-box {{ $chipAnterior }}">{{ $estadoAnterior }}</span>
            <span class="arrow">→</span>
            <span class="estado-box {{ $chipNuevo }}">{{ $estadoNuevo }}</span>
        </div>

        {{-- INFO GRID --}}
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">N° Ticket</div>
                <div class="info-value"><strong>#{{ $ticketId }}</strong></div>
            </div>
            <div class="info-row">
                <div class="info-label">Creado por</div>
                <div class="info-value">{{ $usuarioNombre }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Departamento</div>
                <div class="info-value">{{ $ticket->area->nombre ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Prioridad</div>
                <div class="info-value"><span style="color:#9ca3af; font-style:italic;">N/A</span></div>
            </div>
            <div class="info-row">
                <div class="info-label">Estado actual</div>
                <div class="info-value"><span class="chip {{ $chipNuevo }}">{{ $estadoNuevo }}</span></div>
            </div>
            <div class="info-row">
                <div class="info-label">Actualizado</div>
                <div class="info-value">{{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</div>
            </div>
        </div>

        {{-- QUIÉN REALIZÓ EL CAMBIO --}}
        <div class="operador-wrap">
            <div class="operador-avatar {{ $avatarClass }}">{{ $iniciales }}</div>
            <div>
                <div class="operador-name {{ $nombreClass }}">{{ $operadorNombre }}</div>
                <div class="operador-role">
                    @if($esTecnico) Técnico de Soporte @else Administrador del Sistema @endif
                    &nbsp;·&nbsp; Actualizó el estado
                </div>
            </div>
        </div>

        {{-- COMENTARIO / AVANCE --}}
        <div class="comentario-section">
            <div class="comentario-header" style="background: {{ $esTecnico ? '#15803d' : '#1e3a5f' }};">
                ✏️ <span>Comentario / Avance registrado</span>
            </div>
            <div class="comentario-body">{{ $comentario }}</div>
            <div class="comentario-footer-note">
                📋 <span>Este comentario quedará registrado en el historial del ticket</span>
            </div>
        </div>

        {{-- BANNER ESPECIAL SI ES CIERRE --}}
        @if($esCierre)
        <div class="cierre-banner">
            <h3 style="text-align:center; margin-bottom:.4rem;">@if($estadoNuevoTipo === 'cerrado') 🔒 @else ✅ @endif Ticket #{{ $ticketId }} {{ $estadoNuevo }}</h3>
            <p style="text-align:center;">Si el problema persiste o tienes dudas, puedes abrir un nuevo ticket de soporte.</p>
        </div>
        @endif

        {{-- CTA: lleva directo al ticket. El controlador protege el acceso por rol --}}
        <div class="cta-wrap">
            @if($esTecnico)
            <a href="{{ route('tickets.asignados') }}" class="cta-btn">
                📂 Ver mi Panel de Técnico &rarr;
            </a>
            @else
            <a href="{{ route('tickets.show', $ticket->id_ticket) }}" class="cta-btn">
                🎫 Ver Ticket #{{ $ticketId }} &rarr;
            </a>
            @endif
        </div>

    </div>

    {{-- FOOTER --}}
    <div class="footer">
        Este correo fue generado automáticamente por el <strong>Sistema de Soporte UPTEX</strong>.<br>
        Por favor no respondas a este mensaje.<br>
        <a href="{{ config('app.url') }}">{{ config('app.url') }}</a>
    </div>

</div>
</body>
</html>
