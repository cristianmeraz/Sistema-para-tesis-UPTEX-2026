<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cuenta Bloqueada - UPTEX Tickets</title>
    <style>
        body { margin: 0; padding: 0; font-family: 'Inter', Arial, sans-serif; background: #F8FAFC; }
        .wrapper { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0; }
        .header { background-color: #4F46E5; padding: 2rem; text-align: center; }
        .header h1 { color: white; font-size: 1.5rem; margin: 0; }
        .header p { color: rgba(255,255,255,0.85); margin: 0.5rem 0 0; font-size: 0.95rem; }
        .body { padding: 2rem 2.5rem; }
        .alert-icon { font-size: 3rem; text-align: center; margin-bottom: 1rem; }
        h2 { color: #1E293B; font-size: 1.3rem; margin-bottom: 0.75rem; }
        p { color: #475569; line-height: 1.6; margin: 0 0 1rem; }
        .btn { display: block; width: fit-content; margin: 1.5rem auto; padding: 0.85rem 2.5rem; background-color: #4F46E5; color: white; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 1rem; text-align: center; }
        .note { background: #F1F5F9; border-left: 4px solid #4F46E5; padding: 1rem; border-radius: 0 8px 8px 0; font-size: 0.875rem; color: #64748B; margin-top: 1.5rem; }
        .url-text { word-break: break-all; color: #4F46E5; font-size: 0.8rem; }
        .footer { background: #F8FAFC; padding: 1.5rem; text-align: center; border-top: 1px solid #E2E8F0; font-size: 0.8rem; color: #94A3B8; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>🎟 Sistema de Tickets</h1>
            <p>Universidad Politécnica de Texcoco</p>
        </div>
        <div class="body">
            <div class="alert-icon">🔒</div>
            <h2>Tu cuenta ha sido bloqueada</h2>
            <p>Hola <strong>{{ $nombreUsuario }}</strong>,</p>
            <p>Detectamos <strong>5 intentos de inicio de sesión fallidos</strong> consecutivos en tu cuenta. Por seguridad, el acceso ha sido bloqueado temporalmente.</p>
            <p>Si fuiste tú quien intentó ingresar y olvidaste tu contraseña, usa el siguiente enlace para restablecerla y desbloquear tu cuenta:</p>

            <a href="{{ $resetUrl }}" class="btn">🔑 Restablecer contraseña y desbloquear</a>

            <div class="note">
                <strong>⏱ Este enlace expira en 60 minutos.</strong><br>
                Si no reconoces esta actividad o no fuiste tú, ignora este correo. Tu cuenta permanecerá bloqueada hasta que uses el enlace o contactes al administrador.<br><br>
                Si el botón no funciona, copia y pega esta URL en tu navegador:<br>
                <span class="url-text">{{ $resetUrl }}</span>
            </div>
        </div>
        <div class="footer">
            Este correo fue enviado automáticamente por el Sistema de Tickets UPTEX.<br>
            No responder a este mensaje.
        </div>
    </div>
</body>
</html>
