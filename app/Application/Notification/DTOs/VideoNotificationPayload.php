<?php

namespace App\Application\Notification\DTOs;

class VideoNotificationPayload
{
    public function __construct(
        public readonly string  $videoId,
        public readonly int     $userId,
        public readonly string  $status,
        public readonly ?string $resultUrl,
        public readonly string  $timestamp,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            videoId:   $data['video_id'],
            userId:    (int) $data['user_id'],
            status:    $data['status'],
            resultUrl: $data['result_url'] ?? null,
            timestamp: $data['timestamp'],
        );
    }
}
