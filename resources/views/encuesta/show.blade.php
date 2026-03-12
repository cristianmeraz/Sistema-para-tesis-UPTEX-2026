<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encuesta de Satisfacción - UPTEX</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background: #f1f5f9; font-family: Arial, sans-serif; }
        .survey-card { max-width: 560px; margin: 3rem auto; background: #fff; border-radius: 12px; box-shadow: 0 2px 16px rgba(0,0,0,0.08); overflow: hidden; }
        .survey-header { background-color: #1e3a5f; padding: 2rem; text-align: center; color: #fff; }
        .survey-header h2 { font-size: 1.3rem; font-weight: 700; margin-bottom: 4px; }
        .survey-header p  { font-size: .88rem; opacity: .85; margin: 0; }
        .survey-body { padding: 2rem 2.2rem; }
        .ticket-chip { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: .75rem 1rem; margin-bottom: 1.5rem; }
        .t-id    { font-weight: 800; color: #1e3a5f; font-size: .93rem; }
        .t-title { font-size: .87rem; color: #475569; margin-top: 2px; }
        .btn-survey { width: 100%; padding: .9rem; font-size: 1rem; font-weight: 700; border-radius: 8px; border: none; cursor: pointer; transition: opacity .2s; }
        .btn-survey:hover { opacity: .88; }
        .btn-yes { background-color: #16a34a; color: #fff; }
        .btn-no  { background-color: #dc2626; color: #fff; }
    </style>
</head>
<body>
<div class="survey-card">
    <div class="survey-header">
        <h2>⭐ Encuesta de Satisfacción</h2>
        <p>Universidad Politécnica de Texcoco · Sistema de Soporte</p>
    </div>
    <div class="survey-body">

        @if($encuesta->estaRespondida())
            <div class="alert alert-info text-center mb-0">
                <i class="bi bi-check-circle-fill me-2"></i>
                Ya respondiste esta encuesta. <strong>¡Gracias por tu opinión!</strong>
            </div>
        @else
            <p class="text-muted mb-3" style="font-size:.9rem;">
                Hola <strong>{{ trim(($encuesta->usuario->nombre ?? '') . ' ' . ($encuesta->usuario->apellido ?? '')) }}</strong>,
                tu ticket de soporte ha sido resuelto. Por favor indícanos si quedaste satisfecho con la atención.
            </p>

            <div class="ticket-chip">
                <div class="t-id">#{{ str_pad($encuesta->ticket->id_ticket, 5, '0', STR_PAD_LEFT) }}</div>
                <div class="t-title">{{ $encuesta->ticket->titulo }}</div>
            </div>

            @if(session('error'))
                <div class="alert alert-danger mb-3">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('encuesta.responder', $encuesta->token) }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold text-secondary small">¿Quedaste satisfecho con la atención? <span class="text-danger">*</span></label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="satisfecho" id="rSi" value="1" required>
                            <label class="form-check-label text-success fw-semibold" for="rSi">✅ Sí, satisfecho</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="satisfecho" id="rNo" value="0" required>
                            <label class="form-check-label text-danger fw-semibold" for="rNo">❌ No satisfecho</label>
                        </div>
                    </div>
                    @error('satisfecho')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold text-secondary small" for="comentario">Comentario adicional (opcional)</label>
                    <textarea class="form-control" id="comentario" name="comentario" rows="3"
                        placeholder="¿Cómo podemos mejorar? Cualquier sugerencia es bienvenida..."
                        maxlength="500">{{ old('comentario') }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary w-100 fw-bold" style="background-color:#1e3a5f;border:none;">
                    <i class="bi bi-send me-2"></i>Enviar encuesta
                </button>
            </form>
        @endif
    </div>
</div>
</body>
</html>
