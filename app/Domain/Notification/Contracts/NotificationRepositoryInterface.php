<?php

namespace App\Domain\Notification\Contracts;

use App\Domain\Notification\Notification;
use DateTimeInterface;

interface NotificationRepositoryInterface
{
    public function create(array $data): Notification;

    public function findByVideoId(string $videoId): ?Notification;

    public function markAsSent(string $id, DateTimeInterface $sentAt): void;

    public function markAsDelivered(string $id, DateTimeInterface $deliveredAt): void;

    public function markAsFailed(string $id, DateTimeInterface $failedAt, string $reason): void;
}
