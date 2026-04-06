<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Contraseña - UPTEX Tickets</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .card-box {
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            max-width: 450px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.25);
        }
        .icon-top { font-size: 3rem; text-align: center; margin-bottom: 1rem; }
        h2 { font-size: 1.5rem; font-weight: 700; color: #1E293B; text-align: center; }
        .sub { color: #64748B; text-align: center; font-size: 0.95rem; margin-bottom: 1.75rem; }
        .form-control:focus { border-color: #4F46E5; box-shadow: 0 0 0 3px rgba(79,70,229,0.15); }
        .btn-primary-custom {
            background: linear-gradient(135deg, #4F46E5, #7C3AED);
            border: none; color: white; width: 100%;
            padding: 0.75rem; border-radius: 10px;
            font-weight: 600; font-size: 1rem;
        }
        .btn-primary-custom:hover { opacity: 0.92; color: white; }
        .pass-toggle { position: relative; }
        .pass-toggle .toggle-btn {
            position: absolute; right: 12px; top: 50%;
            transform: translateY(-50%);
            background: none; border: none; color: #94A3B8; cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="card-box">
        <div class="icon-top">🔑</div>
        <h2>Nueva contraseña</h2>
        <p class="sub">Elige una contraseña segura para tu cuenta.</p>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            {{-- Token oculto para validar la solicitud --}}
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="correo" value="{{ $correo }}">

            <div class="mb-3">
                <label class="form-label fw-500 text-secondary">Correo</label>
                <input type="email" class="form-control" value="{{ $correo }}" disabled>
            </div>

                <div class="mb-3">
                <label class="form-label fw-500 text-secondary">Nueva contraseña</label>
                <div class="pass-toggle">
                    <input type="password" id="password" name="password"
                        class="form-control form-control-lg"
                        placeholder="Mínimo 8 caracteres"
                        required minlength="8">
                    <button type="button" class="toggle-btn" onclick="togglePass('password', this)">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                <small class="text-muted d-block mt-1">
                    <i class="bi bi-info-circle me-1"></i>
                    Mín. 8 caracteres &middot; Una mayúscula &middot; Un número &middot; Un símbolo (#, @, !, $, %)
                </small>
            </div>

            <div class="mb-4">
                <label class="form-label fw-500 text-secondary">Confirmar contraseña</label>
                <div class="pass-toggle">
                    <input type="password" id="password_confirmation" name="password_confirmation"
                        class="form-control form-control-lg"
                        placeholder="Repite la contraseña"
                        required minlength="8">
                    <button type="button" class="toggle-btn" onclick="togglePass('password_confirmation', this)">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary-custom">
                <i class="bi bi-shield-check me-2"></i>Guardar nueva contraseña
            </button>
        </form>
    </div>

    <script>
        function togglePass(id, btn) {
            const input = document.getElementById(id);
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        }
    </script>
</body>
</html>
