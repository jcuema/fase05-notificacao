<?php

namespace App\Domain\Notification\Contracts;

interface NotificationEventPublisherInterface
{
    public function publishSent(string $videoId, int $userId): void;

    public function publishDelivered(string $videoId, int $userId): void;

    public function publishFailed(string $videoId, int $userId): void;
}
