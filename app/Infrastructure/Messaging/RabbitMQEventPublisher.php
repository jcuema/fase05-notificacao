<?php

namespace App\Infrastructure\Messaging;

use App\Domain\Notification\Contracts\NotificationEventPublisherInterface;
use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Throwable;

class RabbitMQEventPublisher implements NotificationEventPublisherInterface
{
    protected function getConnection(): AMQPStreamConnection
    {
        return new AMQPStreamConnection(
            env('RABBITMQ_HOST', '127.0.0.1'),
            env('RABBITMQ_PORT', 5672),
            env('RABBITMQ_USER', 'guest'),
            env('RABBITMQ_PASSWORD', 'guest'),
            env('RABBITMQ_VHOST', '/')
        );
    }

    public function publishSent(string $videoId, int $userId): void
    {
        $this->publish(
            queue: env('NOTIFICATION_QUEUE_SENT', 'notification_message_sent'),
            payload: $this->buildPayload($videoId, $userId, 'sent')
        );
    }

    public function publishDelivered(string $videoId, int $userId): void
    {
        $this->publish(
            queue: env('NOTIFICATION_QUEUE_DELIVERED', 'notification_message_delivered'),
            payload: $this->buildPayload($videoId, $userId, 'delivered')
        );
    }

    public function publishFailed(string $videoId, int $userId): void
    {
        $this->publish(
            queue: env('NOTIFICATION_QUEUE_FAILED', 'notification_message_failed'),
            payload: $this->buildPayload($videoId, $userId, 'failed')
        );
    }

    private function buildPayload(string $videoId, int $userId, string $status): string
    {
        return json_encode([
            'video_id'  => $videoId,
            'user_id'   => $userId,
            'channel'   => 'email',
            'status'    => $status,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    private function publish(string $queue, string $payload): void
    {
        try {
            $connection = $this->getConnection();
            $channel    = $connection->channel();

            $channel->queue_declare(
                queue: $queue,
                passive: false,
                durable: true,
                exclusive: false,
                auto_delete: false
            );

            $channel->basic_publish(
                msg: new AMQPMessage($payload, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]),
                exchange: '',
                routing_key: $queue
            );

            $channel->close();
            $connection->close();

            Log::debug('Evento de auditoria publicado.', ['queue' => $queue]);
        } catch (Throwable $e) {
            Log::error('Erro ao publicar evento no RabbitMQ.', ['queue' => $queue, 'error' => $e->getMessage()]);
        }
    }
}
