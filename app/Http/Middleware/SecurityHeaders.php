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

        // Evita que la app sea embebida en iframes (clickjacking)
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Evita que el navegador infiera el tipo MIME (MIME sniffing)
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Activa el filtro XSS del navegador
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Controla la información del referer enviada en navegación
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // ✅ FIX A-12: Content-Security-Policy — permisivo para compatibilidad con Blade inline scripts/styles
        $response->headers->set('Content-Security-Policy',
            "default-src 'self'; " .
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https:; " .
            "style-src 'self' 'unsafe-inline' https:; " .
            "img-src 'self' data: https:; " .
            "font-src 'self' https: data:; " .
            "connect-src 'self'; " .
            "frame-ancestors 'self';"
        );

        // Elimina la cabecera que revela que se usa PHP
        $response->headers->remove('X-Powered-By');

        return $response;
    }
}
