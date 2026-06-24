<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Notification\Contracts\NotificationRepositoryInterface;
use App\Domain\Notification\Enums\NotificationStatus;
use App\Domain\Notification\Notification;
use DateTimeInterface;

class EloquentNotificationRepository implements NotificationRepositoryInterface
{
    public function create(array $data): Notification
    {
        return Notification::create($data);
    }

    public function findByVideoId(string $videoId): ?Notification
    {
        return Notification::where('video_id', $videoId)->first();
    }

    public function markAsSent(string $id, DateTimeInterface $sentAt): void
    {
        Notification::where('id', $id)->update([
            'notification_status' => NotificationStatus::Sent,
            'sent_at'             => $sentAt,
        ]);
    }

    public function markAsDelivered(string $id, DateTimeInterface $deliveredAt): void
    {
        Notification::where('id', $id)->update([
            'notification_status' => NotificationStatus::Delivered,
            'delivered_at'        => $deliveredAt,
        ]);
    }

    public function markAsFailed(string $id, DateTimeInterface $failedAt, string $reason): void
    {
        Notification::where('id', $id)->update([
            'notification_status' => NotificationStatus::Failed,
            'failed_at'           => $failedAt,
            'failure_reason'      => $reason,
        ]);
    }
}
