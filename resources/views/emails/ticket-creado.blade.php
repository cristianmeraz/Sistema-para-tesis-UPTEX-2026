<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Ticket Creado - UPTEX Soporte</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, Helvetica, sans-serif; background: #F1F5F9; color: #1e293b; }
        .wrapper { max-width: 620px; margin: 32px auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.10); }

        /* ── HEADER ── */
        .header { background: linear-gradient(135deg, #1e3a5f 0%, #1d4ed8 100%); padding: 2rem 2.2rem; position: relative; overflow: hidden; }
        .header::before { content: ''; position: absolute; top: -40px; right: -40px; width: 160px; height: 160px; border-radius: 50%; background: rgba(255,255,255,0.06); }
        .header::after  { content: ''; position: absolute; bottom: -50px; right: 100px; width: 110px; height: 110px; border-radius: 50%; background: rgba(255,255,255,0.04); }
        .header-inner { position: relative; z-index: 1; display: flex; align-items: center; gap: 1rem; }
        .header-icon { width: 50px; height: 50px; background: rgba(255,255,255,0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0; }
        .header h1 { color: #fff; font-size: 1.25rem; font-weight: 700; }
        .header p  { color: rgba(255,255,255,0.78); font-size: 0.85rem; margin-top: 2px; }

        /* ── BADGE TICKET ── */
        .ticket-badge { background: linear-gradient(135deg, #1e3a5f, #1d4ed8); border-radius: 12px; padding: 1.2rem 1.5rem; margin: 1.5rem 1.8rem 0; display: flex; align-items: center; gap: 1rem; }
        .ticket-badge-num { background: rgba(255,255,255,0.18); color: #fff; font-size: 0.78rem; font-weight: 700; padding: 0.2rem 0.6rem; border-radius: 20px; white-space: nowrap; }
        .ticket-badge-title { color: #fff; font-weight: 700; font-size: 1rem; margin: 0; }
        .ticket-badge-label { color: rgba(255,255,255,0.72); font-size: 0.8rem; margin-top: 2px; }

        /* ── BODY ── */
        .body { padding: 1.5rem 1.8rem 1.8rem; }
        .greeting { font-size: 1rem; color: #334155; margin-bottom: 0.8rem; line-height: 1.5; }
        .greeting strong { color: #1e3a5f; }

        /* ── INFO GRID ── */
        .info-grid { display: table; width: 100%; border-collapse: collapse; margin: 1.2rem 0; }
        .info-row { display: table-row; }
        .info-label { display: table-cell; width: 38%; background: #f8fafc; padding: 0.6rem 0.8rem; font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em; border-bottom: 1px solid #e2e8f0; vertical-align: top; }
        .info-value { display: table-cell; padding: 0.6rem 0.8rem; font-size: 0.88rem; color: #1e293b; border-bottom: 1px solid #e2e8f0; vertical-align: top; }

        /* ── CHIPS DE PRIORIDAD / ESTADO ── */
        .chip { display: inline-block; padding: 0.2rem 0.65rem; border-radius: 20px; font-size: 0.78rem; font-weight: 700; }
        .chip-abierto   { background: #dbeafe; color: #1d4ed8; }
        .chip-pendiente { background: #fef9c3; color: #854d0e; }
        .chip-proceso   { background: #e0f2fe; color: #0369a1; }
        .chip-resuelto  { background: #dcfce7; color: #15803d; }
        .chip-cerrado   { background: #f1f5f9; color: #475569; }
        .chip-baja      { background: #dcfce7; color: #15803d; }
        .chip-media     { background: #fef9c3; color: #854d0e; }
        .chip-alta      { background: #ffedd5; color: #9a3412; }
        .chip-alta    { background: #fee2e2; color: #dc2626; }

        /* ── DESCRIPCIÓN ── */
        .desc-box { background: #f8fafc; border-left: 4px solid #1d4ed8; border-radius: 0 10px 10px 0; padding: 1rem 1.1rem; margin: 1.2rem 0; font-size: 0.88rem; color: #475569; line-height: 1.6; }

        /* ── CTA ── */
        .cta-wrap { text-align: center; margin: 1.5rem 0 0.5rem; }
        .cta-btn { display: inline-block; background: linear-gradient(135deg, #1e3a5f, #1d4ed8); color: #fff; text-decoration: none; padding: 1rem 2.5rem; border-radius: 12px; font-weight: 800; font-size: 1.05rem; box-shadow: 0 6px 22px rgba(29,78,216,0.40); letter-spacing:.02em; }

        /* ── FOOTER ── */
        .footer { background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 1.2rem 1.8rem; text-align: center; font-size: 0.78rem; color: #94a3b8; line-height: 1.6; }
        .footer a { color: #1d4ed8; text-decoration: none; }

        @media (max-width: 480px) {
            .wrapper { margin: 0; border-radius: 0; }
            .body, .ticket-badge { padding-left: 1.2rem; padding-right: 1.2rem; }
            .ticket-badge { margin-left: 1.2rem; margin-right: 1.2rem; }
        }
    </style>
</head>
<body>
<div class="wrapper">

    {{-- HEADER --}}
    <div class="header">
        <div class="header-inner">
            <div class="header-icon">🎫</div>
            <div>
                <h1>Sistema de Soporte UPTEX</h1>
                <p>Universidad Politécnica de Texcoco</p>
            </div>
        </div>
    </div>

    {{-- BADGE DE TICKET --}}
    @php
        $ticketId   = str_pad($ticket->id_ticket, 5, '0', STR_PAD_LEFT);
        $tienePrioridad = !is_null($ticket->prioridad);
        $prioNombre = strtolower($ticket->prioridad->nombre ?? '');
        $prioChip   = match(true) {
            str_contains($prioNombre, 'baja')  => 'chip-baja',
            str_contains($prioNombre, 'media') => 'chip-media',
            str_contains($prioNombre, 'alta')  => 'chip-alta',
            default => '',
        };
        $prioDisplay = $tienePrioridad ? $ticket->prioridad->nombre : 'Sin asignar';
        $estadoTipo = strtolower($ticket->estado->tipo ?? 'abierto');
        $estadoChip = match($estadoTipo) {
            'abierto'    => 'chip-abierto',
            'pendiente'  => 'chip-pendiente',
            'en_proceso' => 'chip-proceso',
            'resuelto'   => 'chip-resuelto',
            'cerrado'    => 'chip-cerrado',
            default      => 'chip-abierto',
        };
        $usuarioNombre = trim(($ticket->usuario->nombre ?? '') . ' ' . ($ticket->usuario->apellido ?? ''));
        $esAdmin    = $tipoDestinatario === 'admin';
        $esTecnico  = $tipoDestinatario === 'tecnico';
    @endphp

    <div class="ticket-badge" style="margin-top:1.5rem; margin-left:1.8rem; margin-right:1.8rem;">
        <div>
            <span class="ticket-badge-num">TICKET #{{ $ticketId }}</span>
            <p class="ticket-badge-title" style="margin-top:6px;">{{ $ticket->titulo }}</p>
            <p class="ticket-badge-label">
                {{ $ticket->area->nombre ?? 'General' }} &nbsp;·&nbsp;
                <span style="background:#f1f5f9;color:#9ca3af;border:1px solid #e2e8f0;padding:.15rem .5rem;border-radius:12px;font-size:.76rem;font-weight:600;">N/A</span>
                &nbsp;
                <span class="chip {{ $estadoChip }}">{{ $ticket->estado->nombre ?? 'Abierto' }}</span>
            </p>
        </div>
    </div>

    {{-- BODY --}}
    <div class="body">
        @if($esAdmin)
            <p class="greeting">Hola <strong>Equipo Administrador</strong>,</p>
            <p class="greeting" style="color:#475569; font-size:.9rem;">Se ha creado un nuevo ticket en el sistema que requiere su atención.</p>
        @elseif($esTecnico)
            <p class="greeting">Hola <strong>{{ $ticket->tecnicoAsignado?->nombre ?? 'Técnico' }}</strong>,</p>
            <p class="greeting" style="color:#475569; font-size:.9rem;">Se te ha asignado el ticket <strong>#{{ $ticketId }}</strong>. Por favor revísalo a la brevedad.</p>
        @else
            <p class="greeting">Hola <strong>{{ $usuarioNombre }}</strong>,</p>
            <p class="greeting" style="color:#475569; font-size:.9rem;">Tu ticket de soporte ha sido registrado exitosamente. El equipo técnico lo revisará a la brevedad.</p>
        @endif

        {{-- INFORMACIÓN --}}
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
                <div class="info-value">
                    <span style="color:#9ca3af; font-style:italic;">N/A</span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Estado</div>
                <div class="info-value"><span class="chip {{ $estadoChip }}">{{ $ticket->estado->nombre ?? 'N/A' }}</span></div>
            </div>
            <div class="info-row">
                <div class="info-label">Fecha</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($ticket->fecha_creacion)->format('d/m/Y H:i') }}</div>
            </div>
        </div>

        {{-- DESCRIPCIÓN --}}
        <p style="font-size:.82rem; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.04em; margin-bottom:.5rem;">Descripción del problema</p>
        <div class="desc-box">
            {{ $ticket->descripcion }}
        </div>

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
