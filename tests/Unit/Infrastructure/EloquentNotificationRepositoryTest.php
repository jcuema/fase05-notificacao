<?php

use App\Domain\Notification\Enums\NotificationStatus;
use App\Domain\Notification\Enums\VideoStatus;
use App\Domain\Notification\Notification;
use App\Infrastructure\Persistence\EloquentNotificationRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->repository = new EloquentNotificationRepository();
});

it('cria um registro de notificacao com status pending', function () {
    $notification = $this->repository->create([
        'video_id'            => 'vid-test-001',
        'user_id'             => 1,
        'email_address'       => 'user-1@fase05.local',
        'channel'             => 'email',
        'video_status'        => VideoStatus::Completed,
        'notification_status' => NotificationStatus::Pending,
        'result_url'          => 'processed_videos/vid-test-001.zip',
    ]);

    expect($notification->video_id)->toBe('vid-test-001')
        ->and($notification->notification_status)->toBe(NotificationStatus::Pending);
});

it('encontra notificacao por video_id', function () {
    Notification::create([
        'video_id'            => 'vid-busca',
        'user_id'             => 2,
        'email_address'       => 'user-2@fase05.local',
        'channel'             => 'email',
        'video_status'        => VideoStatus::Completed->value,
        'notification_status' => NotificationStatus::Pending->value,
    ]);

    $found = $this->repository->findByVideoId('vid-busca');

    expect($found)->not->toBeNull()
        ->and($found->video_id)->toBe('vid-busca');
});

it('retorna null quando video_id nao existe', function () {
    $found = $this->repository->findByVideoId('inexistente');

    expect($found)->toBeNull();
});

it('atualiza status para sent', function () {
    $notification = Notification::create([
        'video_id'            => 'vid-sent',
        'user_id'             => 1,
        'email_address'       => 'user-1@fase05.local',
        'channel'             => 'email',
        'video_status'        => VideoStatus::Completed->value,
        'notification_status' => NotificationStatus::Pending->value,
    ]);

    $this->repository->markAsSent($notification->id, now());

    expect(Notification::find($notification->id)->notification_status)
        ->toBe(NotificationStatus::Sent);
});

it('atualiza status para failed com motivo', function () {
    $notification = Notification::create([
        'video_id'            => 'vid-failed',
        'user_id'             => 1,
        'email_address'       => 'user-1@fase05.local',
        'channel'             => 'email',
        'video_status'        => VideoStatus::Completed->value,
        'notification_status' => NotificationStatus::Pending->value,
    ]);

    $this->repository->markAsFailed($notification->id, now(), 'SMTP timeout');

    $updated = Notification::find($notification->id);
    expect($updated->notification_status)->toBe(NotificationStatus::Failed)
        ->and($updated->failure_reason)->toBe('SMTP timeout');
});
