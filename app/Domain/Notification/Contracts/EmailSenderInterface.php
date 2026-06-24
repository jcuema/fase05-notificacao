<?php

namespace App\Domain\Notification\Contracts;

interface EmailSenderInterface
{
    public function sendVideoProcessed(string $to, string $videoId, string $resultUrl): void;

    public function sendVideoFailed(string $to, string $videoId): void;
}
