<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PrereservaNotification extends Mailable
{
    use Queueable, SerializesModels;
    public $prereserva;
    public $title;
    public $message;

    /**
     * Create a new message instance.
     */
    public function __construct($prereserva, $title, $message)
    {
        $this->prereserva = $prereserva;
        $this->title = $title;
        $this->message = $message;
    }

    public function build()
    {
        return $this->markdown('emails.prereserva');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Notificação de Pré-Reserva',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.prereserva',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
