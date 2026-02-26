<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountLockedMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $nombreUsuario;
    public string $resetUrl;

    public function __construct(string $nombreUsuario, string $resetUrl)
    {
        $this->nombreUsuario = $nombreUsuario;
        $this->resetUrl      = $resetUrl;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Cuenta bloqueada — Recupera tu acceso | UPTEX Tickets',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.account-locked',
        );
    }
}
