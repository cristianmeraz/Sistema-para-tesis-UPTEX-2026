<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Usuario;
use Illuminate\Support\Facades\Cache;

class WebAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Verificar que exista token en sesión
        if (!session('token') || !session('usuario_id')) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión');
        }

        // ✅ FIX A-14: Si el admin desactivó al usuario, forzar re-verificación inmediata
        if (Cache::has('force_auth_check_' . session('usuario_id'))) {
            Cache::forget('force_auth_check_' . session('usuario_id'));
            session(['auth_last_check' => 0]);
        }

        // 2. Verificar que el usuario siga existiendo y activo en BD
        // Se cachea en sesión 5 min para no golpear BD en cada request
        $lastCheck = session('auth_last_check', 0);
        if ((time() - $lastCheck) > 300) {
            $usuario = Usuario::with('rol')
                ->where('id_usuario', session('usuario_id'))
                ->first();

            if (!$usuario || !$usuario->activo) {
                $request->session()->flush();
                return redirect()->route('login')->with('error', 'Tu sesión ha expirado o tu cuenta fue desactivada');
            }

            // Refrescar datos de sesión por si el admin cambió el rol
            session([
                'auth_last_check' => time(),
                'usuario_nombre'  => $usuario->nombre_completo,
                'usuario_rol'     => $usuario->rol->nombre,
            ]);
        }

        return $next($request);
    }
}