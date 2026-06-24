<?php

use App\Application\Notification\DTOs\VideoNotificationPayload;
use App\Application\Notification\SendVideoNotificationAction;
use App\Domain\Notification\Contracts\EmailSenderInterface;
use App\Domain\Notification\Contracts\NotificationEventPublisherInterface;
use App\Domain\Notification\Contracts\NotificationRepositoryInterface;
use App\Domain\Notification\Contracts\UserEmailResolverInterface;
use App\Domain\Notification\Enums\NotificationStatus;
use App\Domain\Notification\Enums\VideoStatus;
use App\Domain\Notification\Notification;
use Illuminate\Support\Facades\Log;

uses(Tests\TestCase::class);

beforeEach(function () {
    Log::shouldReceive('info', 'error', 'debug', 'warning');

    $this->repository  = Mockery::mock(NotificationRepositoryInterface::class);
    $this->emailSender = Mockery::mock(EmailSenderInterface::class);
    $this->publisher   = Mockery::mock(NotificationEventPublisherInterface::class);
    $this->resolver    = Mockery::mock(UserEmailResolverInterface::class);

    $this->action = new SendVideoNotificationAction(
        $this->repository,
        $this->emailSender,
        $this->publisher,
        $this->resolver
    );

    $this->notificationModel = Mockery::mock(Notification::class)->makePartial();
    $this->notificationModel->id = 'uuid-notificacao-123';
});

it('envia email de sucesso e publica eventos sent e delivered quando video esta completed', function () {
    $payload = VideoNotificationPayload::fromArray([
        'video_id'   => 'vid-abc',
        'user_id'    => 1,
        'status'     => 'completed',
        'result_url' => 'processed_videos/vid-abc.zip',
        'timestamp'  => now()->toIso8601String(),
    ]);

    $this->repository->shouldReceive('findByVideoId')->with('vid-abc')->once()->andReturn(null);
    $this->resolver->shouldReceive('resolve')->with(1)->once()->andReturn('user-1@fase05.local');
    $this->repository->shouldReceive('create')->once()->andReturn($this->notificationModel);
    $this->emailSender->shouldReceive('sendVideoProcessed')
        ->once()
        ->with('user-1@fase05.local', 'vid-abc', 'processed_videos/vid-abc.zip');
    $this->repository->shouldReceive('markAsSent')->once();
    $this->repository->shouldReceive('markAsDelivered')->once();
    $this->publisher->shouldReceive('publishSent')->once()->with('vid-abc', 1);
    $this->publisher->shouldReceive('publishDelivered')->once()->with('vid-abc', 1);

    $this->action->execute($payload);
});

it('envia email de falha e publica evento failed quando video esta failed', function () {
    $payload = VideoNotificationPayload::fromArray([
        'video_id'   => 'vid-xyz',
        'user_id'    => 2,
        'status'     => 'failed',
        'result_url' => null,
        'timestamp'  => now()->toIso8601String(),
    ]);

    $this->repository->shouldReceive('findByVideoId')->with('vid-xyz')->once()->andReturn(null);
    $this->resolver->shouldReceive('resolve')->with(2)->once()->andReturn('user-2@fase05.local');
    $this->repository->shouldReceive('create')->once()->andReturn($this->notificationModel);
    $this->emailSender->shouldReceive('sendVideoFailed')
        ->once()
        ->with('user-2@fase05.local', 'vid-xyz');
    $this->repository->shouldReceive('markAsSent')->once();
    $this->repository->shouldReceive('markAsDelivered')->once();
    $this->publisher->shouldReceive('publishSent')->once()->with('vid-xyz', 2);
    $this->publisher->shouldReceive('publishDelivered')->once()->with('vid-xyz', 2);

    $this->action->execute($payload);
});

it('ignora evento duplicado quando video_id ja foi processado', function () {
    $payload = VideoNotificationPayload::fromArray([
        'video_id'   => 'vid-duplicado',
        'user_id'    => 1,
        'status'     => 'completed',
        'result_url' => 'url',
        'timestamp'  => now()->toIso8601String(),
    ]);

    $existingNotification = Mockery::mock(Notification::class);
    $this->repository->shouldReceive('findByVideoId')->with('vid-duplicado')->once()->andReturn($existingNotification);

    $this->resolver->shouldNotReceive('resolve');
    $this->emailSender->shouldNotReceive('sendVideoProcessed');
    $this->publisher->shouldNotReceive('publishSent');

    $this->action->execute($payload);
});

it('marca notificacao como failed e publica evento de falha quando o envio de email dispara excecao', function () {
    $payload = VideoNotificationPayload::fromArray([
        'video_id'   => 'vid-erro',
        'user_id'    => 3,
        'status'     => 'completed',
        'result_url' => 'url',
        'timestamp'  => now()->toIso8601String(),
    ]);

    $this->repository->shouldReceive('findByVideoId')->andReturn(null);
    $this->resolver->shouldReceive('resolve')->andReturn('user-3@fase05.local');
    $this->repository->shouldReceive('create')->andReturn($this->notificationModel);
    $this->emailSender->shouldReceive('sendVideoProcessed')
        ->andThrow(new RuntimeException('Conexão SMTP recusada'));
    $this->repository->shouldReceive('markAsFailed')->once();
    $this->publisher->shouldReceive('publishFailed')->once()->with('vid-erro', 3);

    expect(fn () => $this->action->execute($payload))
        ->toThrow(RuntimeException::class, 'Conexão SMTP recusada');
});
