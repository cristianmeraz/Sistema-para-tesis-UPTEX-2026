<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EncuestaSatisfaccion;

class EncuestaWebController extends Controller
{
    /**
     * Muestra la encuesta pública (sin autenticación).
     * También soporta respuesta rápida vía GET ?r=si|no (desde el email).
     */
    public function show(Request $request, string $token)
    {
        $encuesta = EncuestaSatisfaccion::with(['ticket', 'usuario'])
            ->where('token', $token)
            ->firstOrFail();

        // Respuesta rápida desde el email (link directo)
        $r = $request->query('r');
        if ($r && !$encuesta->estaRespondida()) {
            $satisfecho = ($r === 'si');
            $encuesta->update([
                'satisfecho'    => $satisfecho,
                'respondida_at' => now(),
            ]);
            return redirect()->route('encuesta.gracias')
                ->with('satisfecho', $satisfecho);
        }

        return view('encuesta.show', compact('encuesta'));
    }

    /**
     * Procesa el formulario POST de la encuesta.
     */
    public function responder(Request $request, string $token)
    {
        $encuesta = EncuestaSatisfaccion::with(['ticket', 'usuario'])
            ->where('token', $token)
            ->firstOrFail();

        if ($encuesta->estaRespondida()) {
            return redirect()->route('encuesta.show', $token)
                ->with('error', 'Esta encuesta ya fue respondida.');
        }

        $request->validate([
            'satisfecho' => 'required|in:0,1',
            'comentario' => 'nullable|string|max:500',
        ]);

        $satisfecho = (bool) $request->satisfecho;

        $encuesta->update([
            'satisfecho'    => $satisfecho,
            'comentario'    => $request->comentario,
            'respondida_at' => now(),
        ]);

        return redirect()->route('encuesta.gracias')
            ->with('satisfecho', $satisfecho);
    }

    /**
     * Página de agradecimiento.
     */
    public function gracias()
    {
        return view('encuesta.gracias');
    }
}
