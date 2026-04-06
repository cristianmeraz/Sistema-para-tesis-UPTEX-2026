<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Ticket;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Carbon\Carbon;

class WebController extends Controller
{
    // ─────────────────────────────────────────────
    //  LOGIN / LOGOUT / REGISTRO
    // ─────────────────────────────────────────────

    public function showLogin()
    {
        if (session('token')) return redirect()->route('dashboard');
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'correo'   => [
                'required',
                'string',
                'max:150',
                // Exige TLD real — @gmail sin .com falla
                'regex:/^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/',
            ],
            'password' => 'required|string',
        ], [
            'correo.required'  => 'El correo es obligatorio.',
            'correo.regex'     => 'El formato del correo no es válido (ej: usuario@dominio.com).',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        // Rate limiting por IP+correo: 5 peticiones por minuto (capa de red)
        $throttleKey = 'login.' . $request->ip() . '|' . strtolower($request->correo);
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'correo' => "Demasiados intentos. Intenta de nuevo en {$seconds} segundos.",
            ])->withInput();
        }

        try {
            // Buscar el usuario sin filtrar activo todavía (para dar mensaje correcto si está bloqueado)
            $usuario = Usuario::with('rol')->where('correo', $request->correo)->first();

            // ── Cuenta bloqueada ──
            if ($usuario && $usuario->locked_at) {
                return back()->withErrors([
                    'correo' => 'Tu cuenta está bloqueada. Revisa tu correo para restablecerla, o contacta al administrador.',
                ])->withInput();
            }

            // ── Credenciales inválidas o usuario inactivo ──
            if (!$usuario || !$usuario->activo || !Hash::check($request->password, $usuario->password)) {
                RateLimiter::hit($throttleKey, 60);

                if ($usuario && $usuario->activo) {
                    // Incrementar contador de intentos fallidos
                    $nuevosIntentos = $usuario->login_attempts + 1;
                    $usuario->login_attempts = $nuevosIntentos;

                    if ($nuevosIntentos >= 5) {
                        // Bloquear cuenta y enviar email de desbloqueo
                        $usuario->locked_at = now();
                        $usuario->save();
                        $this->enviarEmailDesbloqueo($usuario);

                        return back()->withErrors([
                            'correo' => '¡Cuenta bloqueada! Ingresaste 5 contraseñas incorrectas. Te enviamos un correo para restablecerla.',
                        ])->withInput();
                    }

                    $restantes = 5 - $nuevosIntentos;
                    $usuario->save();

                    return back()->withErrors([
                        'correo' => "Contraseña incorrecta. Te quedan {$restantes} intento(s) antes de bloquear tu cuenta.",
                    ])->withInput();
                }

                return back()->withErrors(['correo' => 'Credenciales incorrectas'])->withInput();
            }

            // ── Login exitoso: limpiar contadores y crear sesión ──
            RateLimiter::clear($throttleKey);
            $usuario->update([
                'login_attempts' => 0,
                'locked_at'      => null,
                'last_login'     => Carbon::now('America/Mexico_City'),
            ]);
            // ✅ FIX: Regenerar ID de sesión para prevenir Session Fixation
            $request->session()->regenerate();
            session([
                'token'           => bin2hex(random_bytes(32)),
                'usuario_id'      => $usuario->id_usuario,
                'usuario_nombre'  => $usuario->nombre_completo,
                'usuario_rol'     => $usuario->rol->nombre,
                'usuario_area_id' => $usuario->area_id,
                'auth_last_check' => time(),
            ]);
            return redirect()->route('dashboard');

        } catch (\Exception $e) {
            \Log::error('Error en login: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al iniciar sesión. Intenta de nuevo.']);
        }
    }

    /** Envía email con enlace de desbloqueo/reset al usuario bloqueado */
    private function enviarEmailDesbloqueo(Usuario $usuario): void
    {
        $token    = Str::random(64);
        $resetUrl = url('/reset-password/' . $token . '?correo=' . urlencode($usuario->correo));

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $usuario->correo],
            ['token' => hash('sha256', $token), 'created_at' => now()]
        );

        try {
            Mail::to($usuario->correo)
                ->send(new \App\Mail\AccountLockedMail($usuario->nombre, $resetUrl));
        } catch (\Exception $e) {
            \Log::error('Error enviando email de bloqueo a ' . $usuario->correo . ': ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────
    //  RECUPERACIÓN DE CONTRASEÑA
    // ─────────────────────────────────────────────

    /** GET /forgot-password */
    public function forgotPassword()
    {
        if (session('token')) return redirect()->route('dashboard');
        return view('auth.forgot-password');
    }

    /** POST /forgot-password */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'correo' => [
                'required', 'string', 'max:150',
                'regex:/^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/',
            ],
        ], [
            'correo.regex' => 'Ingresa un correo válido con dominio completo (ej: usuario@gmail.com).',
        ]);

        $usuario = Usuario::where('correo', $request->correo)->first();

        // Siempre mostrar el mismo mensaje para no revelar si el correo existe
        if ($usuario && $usuario->activo) {
            // Si ya agotó los 3 resets permitidos, no enviar — la cuenta debería estar inactiva
            // pero por si acaso se reactivó sin resetear el contador
            if ($usuario->password_reset_count >= 3) {
                // Desactivar cuenta y no enviar enlace
                $usuario->activo = false;
                $usuario->save();
                Cache::put('force_auth_check_' . $usuario->id_usuario, true, 600);
            } else {
                $token    = Str::random(64);
                $resetUrl = url('/reset-password/' . $token . '?correo=' . urlencode($usuario->correo));

                DB::table('password_reset_tokens')->updateOrInsert(
                    ['email' => $usuario->correo],
                    ['token' => hash('sha256', $token), 'created_at' => now()]
                );

                try {
                    Mail::to($usuario->correo)
                        ->send(new \App\Mail\PasswordResetMail($usuario->nombre, $resetUrl));
                } catch (\Exception $e) {
                    \Log::error('Error enviando email de reset a ' . $usuario->correo . ': ' . $e->getMessage());
                }
            }
        }

        return back()->with('status', 'Si ese correo existe en nuestro sistema, recibirás el enlace en los próximos minutos.');
    }

    /** GET /reset-password/{token} */
    public function showResetPassword(string $token, Request $request)
    {
        return view('auth.reset-password', [
            'token'  => $token,
            'correo' => $request->query('correo', ''),
        ]);
    }

    /** POST /reset-password */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required|string',
            'correo'   => [
                'required', 'string', 'max:150',
                'regex:/^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/',
            ],
            'password' => [
                'required', 'confirmed',
                Password::min(8)->mixedCase()->numbers()->symbols(),
            ],
        ], [
            'correo.regex'              => 'Ingresa un correo válido con dominio completo.',
            'password.confirmed'        => 'Las contraseñas no coinciden.',
            'password.password.mixed'   => 'La contraseña debe tener al menos una letra mayúscula y una minúscula.',
            'password.password.numbers' => 'La contraseña debe contener al menos un número.',
            'password.password.symbols' => 'La contraseña debe contener al menos un símbolo (ej: #, @, !, $, %).',
            'password.min'              => 'La contraseña debe tener al menos 8 caracteres.',
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->correo)
            ->first();

        if (!$record || !hash_equals($record->token, hash('sha256', $request->token))) {
            return back()->withErrors(['correo' => 'El enlace no es válido o ya fue utilizado.']);
        }

        // Token expirado (60 minutos)
        if (Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->correo)->delete();
            return back()->withErrors(['correo' => 'El enlace ha expirado. Solicita uno nuevo.']);
        }

        $usuario = Usuario::where('correo', $request->correo)->firstOrFail();

        // Anti-repetición: no puede usar la misma contraseña actual
        if (Hash::check($request->password, $usuario->password)) {
            return back()->withErrors(['password' => 'La nueva contraseña no puede ser igual a la contraseña actual.']);
        }

        $usuario->password       = Hash::make($request->password);
        $usuario->login_attempts = 0;
        $usuario->locked_at      = null;
        $usuario->increment('password_reset_count');
        $usuario->save();

        // Invalidar token usado
        DB::table('password_reset_tokens')->where('email', $request->correo)->delete();

        // Al completar el 3er reset, desactivar cuenta por seguridad
        if ($usuario->password_reset_count >= 3) {
            $usuario->activo = false;
            $usuario->save();
            Cache::put('force_auth_check_' . $usuario->id_usuario, true, 600);
            return redirect()->route('login')
                ->with('warning', '¡Contraseña restablecida! Por seguridad, tu cuenta ha sido desactivada al agotar los 3 intentos de recuperación permitidos. Contacta al administrador para reactivarla.');
        }

        return redirect()->route('login')
            ->with('success', '¡Contraseña restablecida! Ya puedes iniciar sesión.');
    }

    // ─────────────────────────────────────────────
    //  DASHBOARD / PERFIL / LOGOUT
    // ─────────────────────────────────────────────

    public function dashboard() {
        $rol = session('usuario_rol');
        $usuarioId = session('usuario_id');
        if (!$rol) return redirect()->route('login');

        if ($rol === 'Administrador') return app(ReporteWebController::class)->panelAdmin();
        if ($rol === 'Técnico') return redirect()->route('tickets.asignados');

        $stats = [
            'total' => Ticket::where('usuario_id', $usuarioId)->count(),
            'en_proceso' => Ticket::where('usuario_id', $usuarioId)
                ->whereHas('estado', function($q) { $q->whereIn('tipo', ['abierto', 'en_proceso', 'pendiente']); })->count(),
            'resueltos' => Ticket::where('usuario_id', $usuarioId)
                ->whereHas('estado', function($q) { $q->where('tipo', 'resuelto'); })->count(),
        ];

        // ✅ FIX U-7: Re-verificar que el usuario siga activo en BD antes de mostrar dashboard
        $usuarioActivo = \App\Models\Usuario::find($usuarioId);
        if (!$usuarioActivo || !$usuarioActivo->activo) {
            $request = request();
            $request->session()->flush();
            return redirect()->route('login')->with('error', 'Tu cuenta ha sido desactivada.');
        }

        $tickets = Ticket::where('usuario_id', $usuarioId)->with(['estado', 'prioridad', 'area'])->orderBy('fecha_creacion', 'desc')->limit(5)->get();

        return view('usuarios.dashboard', compact('stats', 'tickets'));
    }

    public function logout(Request $request) { $request->session()->flush(); return redirect()->route('login'); }

    // ✅ FIX: Registro público deshabilitado — solo admin puede crear cuentas
    public function showRegister() {
        return redirect()->route('login')->with('error', 'El registro público está deshabilitado. Contacta al administrador para obtener una cuenta.');
    }

    // ✅ FIX: Registro público deshabilitado — solo admin crea cuentas desde el panel
    public function register(Request $request)
    {
        return redirect()->route('login')
            ->with('error', 'El registro público está deshabilitado. Contacta al administrador para obtener una cuenta.');
    }
    public function perfil() {
        // ✅ FIX: Null-check para evitar fatal error si el usuario fue eliminado
        $usuarioObj = Usuario::with('rol')->find(session('usuario_id'));
        if (!$usuarioObj) {
            return redirect()->route('login')->with('error', 'Sesión inválida. Por favor inicia sesión de nuevo.');
        }

        // Calcular cambios de contraseña restantes este mes (solo Técnico y Usuario Normal)
        $changesLeft = null;
        $rol = $usuarioObj->rol->nombre ?? '';
        if (in_array($rol, ['Técnico', 'Usuario Normal'])) {
            $inicioMes    = now()->startOfMonth()->toDateString();
            $cambiosUsados = 0;
            if ($usuarioObj->password_month_reset_at && $usuarioObj->password_month_reset_at >= $inicioMes) {
                $cambiosUsados = $usuarioObj->password_changes_this_month ?? 0;
            }
            $changesLeft = max(0, 3 - $cambiosUsados);
        }

        $usuario = $usuarioObj->toArray();
        return view('perfil', compact('usuario', 'changesLeft'));
    }

    public function updatePerfil(Request $request) {
        $usuario = Usuario::find(session('usuario_id'));

        $validated = $request->validate([
            'nombre'   => [
                'required', 'string', 'min:2', 'max:100',
                'regex:/^[\p{L}\s\-\']+$/u',
            ],
            'apellido' => [
                'required', 'string', 'min:2', 'max:100',
                'regex:/^[\p{L}\s\-\']+$/u',
            ],
            'correo'   => [
                'required', 'string', 'max:150',
                'regex:/^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/',
                'unique:usuarios,correo,' . $usuario->id_usuario . ',id_usuario',
            ],
            'password' => [
                'nullable', 'confirmed',
                Password::min(8)->mixedCase()->numbers()->symbols(),
            ],
        ], [
            'nombre.regex'              => 'El nombre solo puede contener letras y espacios.',
            'apellido.regex'            => 'El apellido solo puede contener letras y espacios.',
            'correo.regex'              => 'Ingresa un correo válido con dominio completo (ej: usuario@gmail.com).',
            'correo.unique'             => 'Ese correo ya está en uso por otra cuenta.',
            'password.password.mixed'   => 'La contraseña debe tener al menos una letra mayúscula y una minúscula.',
            'password.password.numbers' => 'La contraseña debe contener al menos un número.',
            'password.password.symbols' => 'La contraseña debe contener al menos un símbolo (ej: #, @, !, $, %).',
            'password.min'              => 'La contraseña debe tener al menos 8 caracteres.',
        ]);

        $usuario->update([
            'nombre'   => $validated['nombre'],
            'apellido' => $validated['apellido'],
            'correo'   => $validated['correo'],
        ]);

        if (!empty($validated['password'])) {
            // Anti-repetición: no puede ser igual a la contraseña actual
            if (Hash::check($validated['password'], $usuario->password)) {
                return back()->withErrors(['password' => 'La nueva contraseña no puede ser igual a la contraseña actual.'])->withInput();
            }

            // Límite mensual de 3 cambios (solo Técnico y Usuario Normal)
            $rolSesion = session('usuario_rol');
            if (in_array($rolSesion, ['Técnico', 'Usuario Normal'])) {
                $inicioMes = now()->startOfMonth()->toDateString();
                // Resetear contador si estamos en un nuevo mes
                if (!$usuario->password_month_reset_at || $usuario->password_month_reset_at < $inicioMes) {
                    $usuario->password_changes_this_month = 0;
                    $usuario->password_month_reset_at     = $inicioMes;
                    $usuario->save();
                }
                if ($usuario->password_changes_this_month >= 3) {
                    return back()->withErrors(['password' => 'Has alcanzado el límite de 3 cambios de contraseña por mes. Contacta al administrador si necesitas un cambio adicional.'])->withInput();
                }
                $usuario->increment('password_changes_this_month');
            }

            $usuario->update(['password' => Hash::make($validated['password'])]);
        }

        return back()->with('success', 'Perfil actualizado correctamente');
    }
}
