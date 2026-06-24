<?php

namespace App\Providers;

use App\Domain\Notification\Contracts\EmailSenderInterface;
use App\Domain\Notification\Contracts\NotificationEventPublisherInterface;
use App\Domain\Notification\Contracts\NotificationRepositoryInterface;
use App\Domain\Notification\Contracts\UserEmailResolverInterface;
use App\Infrastructure\Mail\LaravelMailSender;
use App\Infrastructure\Messaging\RabbitMQEventPublisher;
use App\Infrastructure\Persistence\EloquentNotificationRepository;
use App\Infrastructure\UserResolver\PlaceholderUserEmailResolver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(NotificationRepositoryInterface::class, EloquentNotificationRepository::class);
        $this->app->bind(EmailSenderInterface::class, LaravelMailSender::class);
        $this->app->bind(NotificationEventPublisherInterface::class, RabbitMQEventPublisher::class);
        $this->app->bind(UserEmailResolverInterface::class, PlaceholderUserEmailResolver::class);
    }

    public function boot(): void {}
}
