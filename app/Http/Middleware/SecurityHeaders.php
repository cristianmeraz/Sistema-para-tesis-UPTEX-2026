<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // ── Eliminar headers que revelan tecnología ──────────────────────────
        // header_remove() actúa a nivel SAPI (PHP nativo), necesario porque
        // PHP añade X-Powered-By antes de que Symfony pueda interceptarlo.
        if (function_exists('header_remove')) {
            header_remove('X-Powered-By');
            header_remove('Server');
        }
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        // ── Protección clickjacking ──────────────────────────────────────────
        $response->headers->set('X-Frame-Options', 'DENY');

        // ── MIME sniffing ────────────────────────────────────────────────────
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // ── XSS filter legacy (IE/Edge antiguos) ────────────────────────────
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // ── Referer enviado en navegación ────────────────────────────────────
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // ── HSTS: fuerza HTTPS por 1 año e incluye subdominios ───────────────
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');

        // ── Permissions-Policy: deshabilita APIs de hardware no usadas ───────
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=(), usb=()');

        // ── Content-Security-Policy ──────────────────────────────────────────
        // 'unsafe-inline' y 'unsafe-eval' son necesarios por los scripts Blade
        // inline existentes. Se puede endurecer más cuando se muevan a .js externo.
        $response->headers->set('Content-Security-Policy',
            "default-src 'self'; " .
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https:; " .
            "style-src 'self' 'unsafe-inline' https:; " .
            "img-src 'self' data: https:; " .
            "font-src 'self' https: data:; " .
            "connect-src 'self'; " .
            "object-src 'none'; " .
            "base-uri 'self'; " .
            "form-action 'self'; " .
            "frame-ancestors 'none';"
        );

        return $response;
    }
}
