<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¡Gracias! - UPTEX Soporte</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background: #f1f5f9; font-family: Arial, sans-serif; }
        .gracias-card { max-width: 480px; margin: 5rem auto; background: #fff; border-radius: 12px; box-shadow: 0 2px 16px rgba(0,0,0,0.08); padding: 3rem 2.5rem; text-align: center; }
        .icon-circle { width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 2.2rem; }
        .icon-satisfecho    { background: #dcfce7; }
        .icon-no-satisfecho { background: #fee2e2; }
        h2 { font-size: 1.4rem; font-weight: 800; color: #1e293b; margin-bottom: .5rem; }
        p  { color: #64748b; font-size: .95rem; line-height: 1.6; }
    </style>
</head>
<body>
<div class="gracias-card">
    @if(session('satisfecho') === true)
        <div class="icon-circle icon-satisfecho">😊</div>
        <h2>¡Gracias por tu respuesta!</h2>
        <p>Nos alegra saber que quedaste <strong class="text-success">satisfecho</strong> con la atención recibida.</p>
    @elseif(session('satisfecho') === false)
        <div class="icon-circle icon-no-satisfecho">😔</div>
        <h2>Gracias por tu honestidad</h2>
        <p>Lamentamos que no hayas quedado <strong class="text-danger">satisfecho</strong>. Tu comentario será revisado para mejorar nuestro servicio.</p>
    @else
        <div class="icon-circle icon-satisfecho">✅</div>
        <h2>¡Encuesta completada!</h2>
        <p>Tu respuesta fue registrada. Gracias por tomarte el tiempo de responder.</p>
    @endif

    <p class="mt-3 text-muted small">Universidad Politécnica de Texcoco · Sistema de Soporte</p>
</div>
</body>
</html>
