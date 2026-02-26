<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - UPTEX Tickets</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #1e3a5f 0%, #1d4ed8 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .fp-container {
            max-width: 1100px;
            width: 100%;
        }

        .fp-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }

        /* ── LADO IZQUIERDO ── */
        .fp-left {
            background: linear-gradient(160deg, #1e3a5f 0%, #1d4ed8 100%);
            color: white;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        .fp-left::before {
            content: '';
            position: absolute;
            top: -60px; right: -60px;
            width: 220px; height: 220px;
            border-radius: 50%;
            background: rgba(255,255,255,0.06);
        }
        .fp-left::after {
            content: '';
            position: absolute;
            bottom: -70px; left: -40px;
            width: 180px; height: 180px;
            border-radius: 50%;
            background: rgba(255,255,255,0.04);
        }
        .fp-left-inner { position: relative; z-index: 1; }

        .fp-logo-img {
            width: 180px;
            height: auto;
            background: white;
            border-radius: 14px;
            padding: 10px 18px;
            display: block;
            box-shadow: 0 4px 20px rgba(0,0,0,0.25);
            margin-bottom: 1.8rem;
        }

        .fp-left h2 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.6rem;
        }
        .fp-left > .fp-left-inner > p {
            font-size: 1.05rem;
            opacity: 0.88;
            margin-bottom: 2rem;
        }

        .fp-step {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1.1rem;
        }
        .fp-step-num {
            width: 30px; height: 30px;
            border-radius: 50%;
            background: rgba(255,255,255,0.18);
            display: flex; align-items: center; justify-content: center;
            font-size: .82rem; font-weight: 700;
            flex-shrink: 0; margin-top: 1px;
        }
        .fp-step-text { font-size: .92rem; opacity: .9; line-height: 1.5; }

        /* ── LADO DERECHO ── */
        .fp-right {
            padding: 3rem;
        }

        .fp-right-icon {
            width: 60px; height: 60px;
            background: #dbeafe;
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.6rem;
            margin-bottom: 1.5rem;
        }

        .fp-right h3 {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: .4rem;
        }
        .fp-right > p {
            color: #64748b;
            font-size: .95rem;
            margin-bottom: 2rem;
        }

        .form-label { font-weight: 600; font-size: .88rem; color: #475569; margin-bottom: .4rem; }

        .form-control {
            border-radius: 10px;
            border: 2px solid #e2e8f0;
            padding: .85rem 1rem;
            font-size: .95rem;
            transition: border-color .2s, box-shadow .2s;
        }
        .form-control:focus {
            border-color: #1d4ed8;
            box-shadow: 0 0 0 4px rgba(29,78,216,0.12);
            outline: none;
        }

        .btn-fp {
            width: 100%;
            padding: 1rem;
            border-radius: 10px;
            font-weight: 700;
            font-size: 1rem;
            background: linear-gradient(135deg, #1e3a5f 0%, #1d4ed8 100%);
            border: none;
            color: white;
            transition: all .25s;
            box-shadow: 0 4px 14px rgba(29,78,216,.30);
        }
        .btn-fp:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(29,78,216,.40);
            color: white;
        }

        .back-link {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .4rem;
            margin-top: 1.4rem;
            color: #64748b;
            font-size: .9rem;
            text-decoration: none;
            transition: color .18s;
        }
        .back-link:hover { color: #1d4ed8; }

        /* ── ALERT SUCCESS (cuando ya se envió) ── */
        .fp-success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 12px;
            padding: 1.1rem 1.2rem;
            display: flex;
            align-items: flex-start;
            gap: .8rem;
            margin-bottom: 1.4rem;
        }
        .fp-success i { color: #16a34a; font-size: 1.2rem; flex-shrink: 0; margin-top: 1px; }
        .fp-success p { margin: 0; font-size: .9rem; color: #15803d; line-height: 1.5; }

        /* ── RESPONSIVE ── */
        @media (max-width: 991.98px) {
            body { padding: 1rem; align-items: flex-start; }
            .fp-left { padding: 2rem; text-align: center; align-items: center; }
            .fp-left-inner { display: flex; flex-direction: column; align-items: center; }
            .fp-steps { display: none; }
            .fp-right { padding: 2rem; }
            .fp-logo-img { width: 150px; }
        }
        @media (max-width: 576px) {
            .fp-right { padding: 1.5rem; }
            .fp-right h3 { font-size: 1.4rem; }
        }
    </style>
</head>
<body>
<div class="fp-container">
    <div class="row g-0 fp-card">

        {{-- ── IZQUIERDA ── --}}
        <div class="col-lg-5 fp-left">
            <div class="fp-left-inner">
                <img src="{{ asset('images/logo-uptex.png') }}" alt="UPTEX" class="fp-logo-img">
                <h2>Recupera tu acceso</h2>
                <p>Te ayudamos a restablecer tu contraseña en pocos pasos.</p>
                <div class="fp-steps">
                    <div class="fp-step">
                        <div class="fp-step-num">1</div>
                        <div class="fp-step-text">Ingresa tu correo institucional registrado.</div>
                    </div>
                    <div class="fp-step">
                        <div class="fp-step-num">2</div>
                        <div class="fp-step-text">Recibirás un enlace seguro en tu bandeja de entrada.</div>
                    </div>
                    <div class="fp-step">
                        <div class="fp-step-num">3</div>
                        <div class="fp-step-text">Haz clic en el enlace y crea tu nueva contraseña.</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── DERECHA ── --}}
        <div class="col-lg-7 fp-right">

            <div class="fp-right-icon">
                <i class="bi bi-shield-lock" style="color:#1d4ed8;"></i>
            </div>

            <h3>¿Olvidaste tu contraseña?</h3>
            <p>Ingresa tu correo institucional y te enviaremos un enlace para restablecerla.</p>

            {{-- Éxito --}}
            @if(session('status'))
            <div class="fp-success">
                <i class="bi bi-check-circle-fill"></i>
                <p><strong>¡Correo enviado!</strong><br>{{ session('status') }}</p>
            </div>
            @endif

            {{-- Error --}}
            @if($errors->any())
            <div class="alert alert-danger d-flex align-items-center gap-2 rounded-3 mb-3">
                <i class="bi bi-exclamation-circle-fill"></i>
                <span>{{ $errors->first() }}</span>
            </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="mb-4">
                    <label class="form-label">
                        <i class="bi bi-envelope me-1"></i> Correo Electrónico
                    </label>
                    <input
                        type="email"
                        name="correo"
                        class="form-control"
                        placeholder="tu@uptex.edu.mx"
                        value="{{ old('correo') }}"
                        required
                        autofocus
                    >
                </div>

                <button type="submit" class="btn btn-fp">
                    <i class="bi bi-send me-2"></i> Enviar enlace de recuperación
                </button>
            </form>

            <a href="{{ route('login') }}" class="back-link">
                <i class="bi bi-arrow-left"></i> Volver al inicio de sesión
            </a>

        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
