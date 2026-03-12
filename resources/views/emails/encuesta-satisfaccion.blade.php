<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encuesta de Satisfacción - UPTEX Soporte</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, Helvetica, sans-serif; background: #f1f5f9; color: #1e293b; }
        .wrapper { max-width: 600px; margin: 32px auto; background: #ffffff; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0; }

        /* ── HEADER ── */
        .header { background-color: #1e3a5f; padding: 2rem 2.2rem; }
        .header h1 { color: #fff; font-size: 1.2rem; font-weight: 700; margin-bottom: 4px; }
        .header p  { color: rgba(255,255,255,0.80); font-size: 0.83rem; }

        /* ── CUERPO ── */
        .body { padding: 1.8rem 2rem; }
        .greeting { font-size: 1rem; color: #334155; margin-bottom: 1rem; line-height: 1.5; }
        .greeting strong { color: #1e3a5f; }
        .subtitle { font-size: .9rem; color: #475569; line-height: 1.55; margin-bottom: 1.5rem; }

        /* ── TICKET REF ── */
        .ticket-ref { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: .9rem 1.1rem; margin-bottom: 1.6rem; }
        .ticket-ref-num   { font-weight: 800; color: #1e3a5f; font-size: .95rem; margin-bottom: 2px; }
        .ticket-ref-title { font-size: .88rem; color: #475569; }

        /* ── BOTONES RESPUESTA ── */
        .btn-row { text-align: center; margin: 1.5rem 0; }
        .btn-satisfecho { display: inline-block; background-color: #16a34a; color: #ffffff; text-decoration: none; padding: .85rem 2rem; border-radius: 8px; font-weight: 700; font-size: .95rem; margin: .4rem; }
        .btn-no-satisfecho { display: inline-block; background-color: #dc2626; color: #ffffff; text-decoration: none; padding: .85rem 2rem; border-radius: 8px; font-weight: 700; font-size: .95rem; margin: .4rem; }

        /* ── NOTA ── */
        .note { background: #fff7ed; border-left: 3px solid #f59e0b; padding: .8rem 1rem; font-size: .82rem; color: #92400e; border-radius: 0 6px 6px 0; margin-top: 1.2rem; }

        /* ── FOOTER ── */
        .footer { background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 1rem 1.5rem; text-align: center; font-size: .77rem; color: #94a3b8; line-height: 1.5; }
    </style>
</head>
<body>
<div class="wrapper">

    @php
        $encuesta  = $encuesta;
        $ticket    = $encuesta->ticket;
        $usuario   = $encuesta->usuario ?? $ticket->usuario;
        $ticketId  = str_pad($ticket->id_ticket, 5, '0', STR_PAD_LEFT);
        $nombre    = trim(($usuario->nombre ?? '') . ' ' . ($usuario->apellido ?? ''));
        $urlSi     = url('/encuesta/' . $encuesta->token . '?r=si');
        $urlNo     = url('/encuesta/' . $encuesta->token . '?r=no');
    @endphp

    <div class="header">
        <h1>⭐ Encuesta de satisfacción</h1>
        <p>Universidad Politécnica de Texcoco · Sistema de Soporte</p>
    </div>

    <div class="body">
        <p class="greeting">Hola <strong>{{ $nombre ?: 'usuario' }}</strong>,</p>
        <p class="subtitle">
            Tu ticket de soporte ha sido marcado como <strong>resuelto</strong>.
            Tu opinión es muy importante para que podamos seguir mejorando.
            ¿Quedaste satisfecho con la atención recibida?
        </p>

        <div class="ticket-ref">
            <div class="ticket-ref-num">#{{ $ticketId }}</div>
            <div class="ticket-ref-title">{{ $ticket->titulo }}</div>
        </div>

        <div class="btn-row">
            <a href="{{ $urlSi }}" class="btn-satisfecho">✅ Sí, quedé satisfecho</a>
            <a href="{{ $urlNo }}" class="btn-no-satisfecho">❌ No, no quedé satisfecho</a>
        </div>

        <div class="note">
            <strong>Nota:</strong> Si los botones no funcionan, copia y pega cualquiera de estos enlaces en tu navegador:<br>
            Satisfecho: <span style="word-break:break-all; color:#1d4ed8;">{{ $urlSi }}</span><br>
            No satisfecho: <span style="word-break:break-all; color:#dc2626;">{{ $urlNo }}</span>
        </div>
    </div>

    <div class="footer">
        Este correo fue enviado automáticamente por el Sistema de Tickets UPTEX.<br>
        No responder a este mensaje.
    </div>

</div>
</body>
</html>
