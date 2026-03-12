<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\EncuestaSatisfaccion;

class EncuestaSatisfaccionMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public EncuestaSatisfaccion $encuesta
    ) {}

    public function envelope(): Envelope
    {
        $id = str_pad($this->encuesta->ticket->id_ticket, 5, '0', STR_PAD_LEFT);
        return new Envelope(
            subject: "[Ticket #{$id}] ¿Quedaste satisfecho? Cuéntanos tu experiencia | UPTEX Soporte"
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.encuesta-satisfaccion');
    }
}
