<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EncuestaSatisfaccion;

class EncuestaWebController extends Controller
{
    /**
     * Muestra la encuesta pública con las 5 preguntas (sin autenticación).
     */
    public function show(Request $request, string $token)
    {
        $encuesta = EncuestaSatisfaccion::with(['ticket', 'usuario'])
            ->where('token', $token)
            ->firstOrFail();

        return view('encuesta.show', compact('encuesta'));
    }

    /**
     * Procesa el formulario POST de la encuesta con las 5 preguntas.
     * Deriva el campo 'satisfecho' (boolean) del promedio de las respuestas.
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
            'pregunta_1' => 'required|integer|between:1,4',
            'pregunta_2' => 'required|integer|between:1,4',
            'pregunta_3' => 'required|integer|between:1,4',
            'pregunta_4' => 'required|integer|between:1,4',
            'pregunta_5' => 'required|integer|between:1,4',
            'comentario' => 'nullable|string|max:500',
        ], [
            'pregunta_1.required' => 'La pregunta 1 es obligatoria.',
            'pregunta_2.required' => 'La pregunta 2 es obligatoria.',
            'pregunta_3.required' => 'La pregunta 3 es obligatoria.',
            'pregunta_4.required' => 'La pregunta 4 es obligatoria.',
            'pregunta_5.required' => 'La pregunta 5 es obligatoria.',
        ]);

        // Derivar satisfecho: promedio >= 2.5 (Satisfecho o Muy Satisfecho) = true
        $promedio = (
            (int)$request->pregunta_1 +
            (int)$request->pregunta_2 +
            (int)$request->pregunta_3 +
            (int)$request->pregunta_4 +
            (int)$request->pregunta_5
        ) / 5;

        $satisfecho = $promedio >= 2.5;

        $encuesta->update([
            'pregunta_1'    => (int)$request->pregunta_1,
            'pregunta_2'    => (int)$request->pregunta_2,
            'pregunta_3'    => (int)$request->pregunta_3,
            'pregunta_4'    => (int)$request->pregunta_4,
            'pregunta_5'    => (int)$request->pregunta_5,
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
