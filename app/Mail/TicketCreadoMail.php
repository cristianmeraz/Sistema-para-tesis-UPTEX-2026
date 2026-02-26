<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Ticket;

class TicketCreadoMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /** Reintentos si el SMTP falla */
    public int $tries = 3;
    /** Segundos entre reintentos */
    public int $backoff = 30;

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

        return new Envelope(
            subject: "[Ticket #{$id}] Nuevo ticket creado | UPTEX Soporte",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.ticket-creado',
        );
    }
}
