<?php

namespace App\Infrastructure\Mail;

use App\Domain\Notification\Contracts\EmailSenderInterface;
use Illuminate\Support\Facades\Mail;

class LaravelMailSender implements EmailSenderInterface
{
    public function sendVideoProcessed(string $to, string $videoId, string $resultUrl): void
    {
        Mail::to($to)->send(new VideoProcessedMail($videoId, $resultUrl));
    }

    public function sendVideoFailed(string $to, string $videoId): void
    {
        Mail::to($to)->send(new VideoFailedMail($videoId));
    }
}
