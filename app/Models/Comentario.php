<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comentario extends Model
{
    protected $table = 'comentarios';
    protected $primaryKey = 'id_comentario';
    public $timestamps = true;
    
    protected $fillable = [
        'ticket_id',
        'usuario_id',
        'contenido',
        'es_actualizacion',
    ];

    protected $casts = [
        'es_actualizacion' => 'boolean',
    ];
    
    // Relaciones
    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id', 'id_ticket');
    }
    
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id', 'id_usuario');
    }
}