<?php

namespace App\Infrastructure\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VideoFailedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $videoId,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Falha no processamento do seu vídeo',
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlView: 'mail.video-failed',
            textView: 'mail.video-failed-text',
        );
    }
}
