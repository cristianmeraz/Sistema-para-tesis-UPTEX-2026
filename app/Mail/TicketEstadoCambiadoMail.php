<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Ticket;

class TicketEstadoCambiadoMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param Ticket $ticket              Ticket con relaciones cargadas
     * @param string $estadoAnterior      Nombre del estado anterior
     * @param string $estadoAnteriorTipo  Tipo del estado anterior (abierto|en_proceso|resuelto|pendiente|cerrado)
     * @param string $estadoNuevo         Nombre del nuevo estado
     * @param string $estadoNuevoTipo     Tipo del nuevo estado
     * @param string $comentario          Texto del comentario/avance registrado
     * @param string $operadorNombre      Nombre completo del operador que hizo el cambio
     * @param string $tipoDestinatario    'usuario' | 'tecnico' | 'admin'
     */
    public function __construct(
        public Ticket $ticket,
        public string $estadoAnterior,
        public string $estadoAnteriorTipo,
        public string $estadoNuevo,
        public string $estadoNuevoTipo,
        public string $comentario,
        public string $operadorNombre,
        public string $tipoDestinatario
    ) {}

    public function envelope(): Envelope
    {
        $id = str_pad($this->ticket->id_ticket, 5, '0', STR_PAD_LEFT);

        $esCierre = in_array(strtolower($this->estadoNuevoTipo), ['resuelto', 'cerrado']);

        $asunto = $esCierre
            ? "[Ticket #{$id}] ✅ Ticket resuelto | UPTEX Soporte"
            : "[Ticket #{$id}] 🔄 Actualización de estado | UPTEX Soporte";

        return new Envelope(subject: $asunto);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.ticket-estado-cambiado',
        );
    }
}
