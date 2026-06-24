<?php

namespace App\Domain\Notification;

use App\Domain\Notification\Enums\NotificationStatus;
use App\Domain\Notification\Enums\VideoStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasUuids;

    protected $table = 'notifications';

    protected $fillable = [
        'video_id',
        'user_id',
        'email_address',
        'channel',
        'video_status',
        'notification_status',
        'result_url',
        'sent_at',
        'delivered_at',
        'failed_at',
        'failure_reason',
    ];

    protected $casts = [
        'video_status'        => VideoStatus::class,
        'notification_status' => NotificationStatus::class,
        'sent_at'             => 'datetime',
        'delivered_at'        => 'datetime',
        'failed_at'           => 'datetime',
    ];
}
