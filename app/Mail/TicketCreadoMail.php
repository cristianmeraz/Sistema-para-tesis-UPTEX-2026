<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Ticket;

class TicketCreadoMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param Ticket $ticket          Ticket con relaciones cargadas (usuario, area, prioridad, estado)
     * @param string $tipoDestinatario 'usuario' | 'admin'
     */
    public function __construct(
        public Ticket $ticket,
        public string $tipoDestinatario
    ) {}

    public function envelope(): Envelope
    {
        $id = str_pad($this->ticket->id_ticket, 5, '0', STR_PAD_LEFT);

        $subject = $this->tipoDestinatario === 'tecnico'
            ? "[Ticket #{$id}] 🔧 Se te asignó un ticket | UPTEX Soporte"
            : "[Ticket #{$id}] Nuevo ticket creado | UPTEX Soporte";

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.ticket-creado',
        );
    }
}
