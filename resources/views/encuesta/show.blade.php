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
        .survey-card { max-width: 660px; margin: 2.5rem auto 3rem; background: #fff; border-radius: 12px; box-shadow: 0 2px 16px rgba(0,0,0,0.08); overflow: hidden; }
        .survey-header { background-color: #1e3a5f; padding: 1.75rem 2rem; text-align: center; color: #fff; }
        .survey-header h2 { font-size: 1.25rem; font-weight: 700; margin-bottom: 4px; }
        .survey-header p  { font-size: .85rem; opacity: .85; margin: 0; }
        .survey-body { padding: 1.75rem 2rem; }
        .ticket-chip { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: .75rem 1rem; margin-bottom: 1.5rem; }
        .t-id    { font-weight: 800; color: #1e3a5f; font-size: .93rem; }
        .t-title { font-size: .87rem; color: #475569; margin-top: 2px; }
        /* Escala de satisfacción */
        .escala-group { margin-bottom: 1.4rem; }
        .escala-label { font-size: .88rem; font-weight: 600; color: #334155; margin-bottom: .55rem; display: block; }
        .escala-num   { font-size: .78rem; color: #64748b; font-weight: 400; }
        .escala-opts  { display: -ms-flexbox; display: flex; gap: .5rem; flex-wrap: wrap; }
        .escala-btn   { -webkit-box-flex: 1; -ms-flex: 1 1 auto; flex: 1 1 auto; }
        .escala-btn input[type=radio] { display: none; }
        .escala-btn label {
            display: block; text-align: center; padding: .55rem .4rem;
            border: 2px solid #e2e8f0; border-radius: 8px; font-size: .78rem;
            font-weight: 600; cursor: pointer; line-height: 1.35;
            color: #475569; background: #fff;
            -webkit-transition: all .15s; transition: all .15s;
            white-space: nowrap;
        }
        .escala-btn input:checked + label { border-color: #1d4ed8; background: #eff6ff; color: #1d4ed8; }
        .escala-btn label:hover { border-color: #93c5fd; background: #f0f9ff; }
        /* Colores de escala */
        .opt-4 input:checked + label { border-color: #16a34a; background: #f0fdf4; color: #15803d; }
        .opt-3 input:checked + label { border-color: #0ea5e9; background: #f0f9ff; color: #0369a1; }
        .opt-2 input:checked + label { border-color: #f59e0b; background: #fffbeb; color: #b45309; }
        .opt-1 input:checked + label { border-color: #dc2626; background: #fef2f2; color: #b91c1c; }
        @media (max-width: 480px) {
            .escala-opts { -ms-flex-wrap: wrap; flex-wrap: wrap; }
            .escala-btn  { -webkit-box-flex: 0; -ms-flex: 0 0 48%; flex: 0 0 48%; }
        }
    </style>
</head>
<body>
<div class="survey-card">
    <div class="survey-header">
        <h2>&#11088; Encuesta de Satisfacción</h2>
        <p>Universidad Politécnica de Texcoco &middot; Sistema de Soporte &middot; Área de IT</p>
    </div>
    <div class="survey-body">

        @if($encuesta->estaRespondida())
            <div class="alert alert-info text-center mb-0">
                <i class="bi bi-check-circle-fill me-2"></i>
                Ya respondiste esta encuesta. <strong>¡Gracias por tu opinión!</strong>
            </div>
        @else
            <p class="text-muted mb-2" style="font-size:.9rem;">
                Hola <strong style="color:#1e3a5f;">{{ trim(($encuesta->usuario->nombre ?? '') . ' ' . ($encuesta->usuario->apellido ?? '')) ?: 'usuario' }}</strong>,
                tu ticket de soporte ha sido resuelto. Por favor califica el servicio del área de IT respondiendo las siguientes preguntas.
            </p>
            <div class="alert alert-warning py-2 px-3 mb-3" style="font-size:.82rem;">
                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                <strong>Importante:</strong> Si no completas esta encuesta, no podrás generar un nuevo ticket.
            </div>

            <div class="ticket-chip">
                <div class="t-id">#{{ str_pad($encuesta->ticket->id_ticket, 5, '0', STR_PAD_LEFT) }}</div>
                <div class="t-title">{{ $encuesta->ticket->titulo }}</div>
            </div>

            @if(session('error'))
                <div class="alert alert-danger mb-3">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('encuesta.responder', $encuesta->token) }}">
                @csrf

                @php
                    $preguntas = [
                        1 => '¿Está satisfecho con el trabajo realizado por el servicio de IT en la universidad?',
                        2 => '¿El personal de IT atiende adecuadamente sus solicitudes técnicas?',
                        3 => '¿El servicio de IT soluciona su problema en un tiempo adecuado?',
                        4 => '¿El personal de IT demuestra los conocimientos suficientes para atender sus solicitudes?',
                        5 => '¿Se encuentra satisfecho con la atención recibida por el personal del servicio de IT?',
                    ];
                    $opciones = [
                        4 => ['icono' => '&#128512;', 'texto' => 'Muy Satisfecho'],
                        3 => ['icono' => '&#128578;', 'texto' => 'Satisfecho'],
                        2 => ['icono' => '&#128528;', 'texto' => 'Poco Satisfecho'],
                        1 => ['icono' => '&#128544;', 'texto' => 'Nada Satisfecho'],
                    ];
                @endphp

                @foreach($preguntas as $num => $texto)
                <div class="escala-group">
                    <span class="escala-label">
                        <span class="text-muted escala-num">{{ $num }}.</span> {{ $texto }}
                        <span class="text-danger">*</span>
                    </span>
                    <div class="escala-opts">
                        @foreach($opciones as $valor => $op)
                        <div class="escala-btn opt-{{ $valor }}">
                            <input type="radio"
                                   name="pregunta_{{ $num }}"
                                   id="p{{ $num }}v{{ $valor }}"
                                   value="{{ $valor }}"
                                   required>
                            <label for="p{{ $num }}v{{ $valor }}">
                                {!! $op['icono'] !!}<br>{{ $op['texto'] }}
                            </label>
                        </div>
                        @endforeach
                    </div>
                    @error("pregunta_{$num}")
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
                @endforeach

                <div class="mb-4 mt-2">
                    <label class="form-label fw-semibold text-secondary small" for="comentario">
                        Comentario adicional (opcional)
                    </label>
                    <textarea class="form-control" id="comentario" name="comentario" rows="3"
                        placeholder="¿Alguna sugerencia o comentario para mejorar el servicio?"
                        maxlength="500" style="font-size:.9rem;">{{ old('comentario') }}</textarea>
                </div>

                <button type="submit" class="btn w-100 fw-bold py-3"
                    style="background-color:#1e3a5f;color:#fff;border:none;border-radius:8px;font-size:1rem;">
                    <i class="bi bi-send me-2"></i>Enviar Encuesta
                </button>
            </form>
        @endif
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
