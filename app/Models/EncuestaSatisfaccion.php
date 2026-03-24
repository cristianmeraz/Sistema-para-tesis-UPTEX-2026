<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EncuestaSatisfaccion extends Model
{
    protected $table      = 'encuestas_satisfaccion';
    protected $primaryKey = 'id_encuesta';
    public    $timestamps = true;

    protected $fillable = [
        'ticket_id',
        'usuario_id',
        'token',
        'satisfecho',
        'comentario',
        'respondida_at',
        'pregunta_1',
        'pregunta_2',
        'pregunta_3',
        'pregunta_4',
        'pregunta_5',
    ];

    protected $casts = [
        'satisfecho'    => 'boolean',
        'respondida_at' => 'datetime',
    ];

    // ── Relaciones ──────────────────────────────────────────────────────────

    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id', 'id_ticket');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id', 'id_usuario');
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    public function estaRespondida(): bool
    {
        return $this->respondida_at !== null;
    }
}
