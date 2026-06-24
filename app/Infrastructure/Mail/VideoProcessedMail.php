<?php

namespace App\Infrastructure\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VideoProcessedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $videoId,
        public readonly string $resultUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Seu vídeo foi processado com sucesso!',
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlView: 'mail.video-processed',
            textView: 'mail.video-processed-text',
        );
    }
}
