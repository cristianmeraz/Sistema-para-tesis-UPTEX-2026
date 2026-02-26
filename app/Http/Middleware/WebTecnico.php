<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WebTecnico
{
    public function handle(Request $request, Closure $next): Response
    {
        $rol = session('usuario_rol');
        
        // Administrador tiene acceso a todo, incluyendo rutas de técnico
        if ($rol !== 'Técnico' && $rol !== 'Administrador') {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para acceder a este módulo');
        }
        
        return $next($request);
    }
}
