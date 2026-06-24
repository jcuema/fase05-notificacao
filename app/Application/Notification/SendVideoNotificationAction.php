<?php

namespace App\Application\Notification;

use App\Application\Notification\DTOs\VideoNotificationPayload;
use App\Domain\Notification\Contracts\EmailSenderInterface;
use App\Domain\Notification\Contracts\NotificationEventPublisherInterface;
use App\Domain\Notification\Contracts\NotificationRepositoryInterface;
use App\Domain\Notification\Contracts\UserEmailResolverInterface;
use App\Domain\Notification\Enums\NotificationStatus;
use App\Domain\Notification\Enums\VideoStatus;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendVideoNotificationAction
{
    public function __construct(
        private readonly NotificationRepositoryInterface  $repository,
        private readonly EmailSenderInterface             $emailSender,
        private readonly NotificationEventPublisherInterface $publisher,
        private readonly UserEmailResolverInterface       $userEmailResolver,
    ) {}

    public function execute(VideoNotificationPayload $payload): void
    {
        if ($this->repository->findByVideoId($payload->videoId) !== null) {
            Log::info('Notificação já processada — evento ignorado.', ['video_id' => $payload->videoId]);
            return;
        }

        $email       = $this->userEmailResolver->resolve($payload->userId);
        $videoStatus = VideoStatus::from($payload->status);

        $notification = $this->repository->create([
            'video_id'            => $payload->videoId,
            'user_id'             => $payload->userId,
            'email_address'       => $email,
            'channel'             => 'email',
            'video_status'        => $videoStatus,
            'notification_status' => NotificationStatus::Pending,
            'result_url'          => $payload->resultUrl,
        ]);

        try {
            if ($videoStatus === VideoStatus::Completed) {
                $this->emailSender->sendVideoProcessed($email, $payload->videoId, (string) $payload->resultUrl);
            } else {
                $this->emailSender->sendVideoFailed($email, $payload->videoId);
            }

            $now = now();
            $this->repository->markAsSent($notification->id, $now);
            $this->repository->markAsDelivered($notification->id, $now);

            $this->publisher->publishSent($payload->videoId, $payload->userId);
            $this->publisher->publishDelivered($payload->videoId, $payload->userId);

            Log::info('Notificação enviada com sucesso.', [
                'video_id' => $payload->videoId,
                'email'    => $email,
                'status'   => $payload->status,
            ]);
        } catch (Throwable $e) {
            $this->repository->markAsFailed($notification->id, now(), $e->getMessage());
            $this->publisher->publishFailed($payload->videoId, $payload->userId);

            Log::error('Falha ao enviar notificação.', [
                'video_id' => $payload->videoId,
                'email'    => $email,
                'error'    => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
