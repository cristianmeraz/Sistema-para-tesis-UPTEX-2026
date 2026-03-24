<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encuesta de Satisfacción - UPTEX Soporte</title>
</head>
<body style="margin:0;padding:0;background-color:#f1f5f9;font-family:Arial,Helvetica,sans-serif;color:#1e293b;">

@php
    $ticket   = $encuesta->ticket;
    $usuario  = $encuesta->usuario ?? $ticket->usuario;
    $ticketId = str_pad($ticket->id_ticket, 5, '0', STR_PAD_LEFT);
    $nombre   = trim(($usuario->nombre ?? '') . ' ' . ($usuario->apellido ?? ''));
    $urlEncuesta = url('/encuesta/' . $encuesta->token);
@endphp

<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f1f5f9;padding:32px 0;">
  <tr>
    <td align="center">
      <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background-color:#ffffff;border-radius:8px;overflow:hidden;border:1px solid #e2e8f0;">

        {{-- HEADER --}}
        <tr>
          <td style="background-color:#1e3a5f;padding:28px 32px;">
            <h1 style="margin:0 0 4px;color:#ffffff;font-size:18px;font-weight:700;">&#11088; Encuesta de satisfacción</h1>
            <p style="margin:0;color:rgba(255,255,255,0.80);font-size:13px;">Universidad Politécnica de Texcoco &middot; Sistema de Soporte</p>
          </td>
        </tr>

        {{-- CUERPO --}}
        <tr>
          <td style="padding:28px 32px;">

            <p style="margin:0 0 16px;font-size:15px;color:#334155;line-height:1.6;">
              Hola <strong style="color:#1e3a5f;">{{ $nombre ?: 'usuario' }}</strong>,
            </p>

            <p style="margin:0 0 20px;font-size:14px;color:#475569;line-height:1.6;">
              Tu ticket de soporte ha sido marcado como <strong>resuelto</strong>.
              Tu opinión es muy importante para mejorar el servicio del área de IT.
              Por favor, toma un momento para responder <strong>5 preguntas rápidas</strong>.
            </p>

            {{-- AVISO IMPORTANTE --}}
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:20px;">
              <tr>
                <td style="background-color:#fff7ed;border-left:4px solid #f59e0b;border-radius:0 6px 6px 0;padding:12px 16px;">
                  <p style="margin:0;font-size:13px;color:#92400e;line-height:1.5;">
                    <strong>&#9888;&#65039; Importante:</strong> Si no respondes la encuesta,
                    <strong>no podrás generar un nuevo ticket</strong> hasta completarla.
                  </p>
                </td>
              </tr>
            </table>

            {{-- REFERENCIA DEL TICKET --}}
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
              <tr>
                <td style="background-color:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:14px 18px;">
                  <p style="margin:0 0 2px;font-weight:800;color:#1e3a5f;font-size:15px;">#{{ $ticketId }}</p>
                  <p style="margin:0;font-size:13px;color:#475569;">{{ $ticket->titulo }}</p>
                </td>
              </tr>
            </table>

            {{-- BOTÓN PRINCIPAL --}}
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:20px;">
              <tr>
                <td align="center">
                  <a href="{{ $urlEncuesta }}"
                     style="display:inline-block;background-color:#1d4ed8;color:#ffffff;text-decoration:none;padding:14px 36px;border-radius:8px;font-weight:700;font-size:15px;letter-spacing:0.3px;">
                    &#128203; Responder Encuesta
                  </a>
                </td>
              </tr>
            </table>

            {{-- ENLACE ALTERNATIVO --}}
            <table width="100%" cellpadding="0" cellspacing="0">
              <tr>
                <td style="background-color:#f0f9ff;border-left:3px solid #3b82f6;border-radius:0 6px 6px 0;padding:10px 14px;">
                  <p style="margin:0;font-size:12px;color:#1e40af;line-height:1.5;">
                    Si el botón no funciona, copia y pega este enlace en tu navegador:<br>
                    <span style="word-break:break-all;color:#1d4ed8;">{{ $urlEncuesta }}</span>
                  </p>
                </td>
              </tr>
            </table>

          </td>
        </tr>

        {{-- FOOTER --}}
        <tr>
          <td style="background-color:#f8fafc;border-top:1px solid #e2e8f0;padding:16px 24px;text-align:center;">
            <p style="margin:0;font-size:12px;color:#94a3b8;line-height:1.5;">
              Este correo fue enviado automáticamente por el Sistema de Tickets UPTEX.<br>
              No responder a este mensaje.
            </p>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>

</body>
</html>
