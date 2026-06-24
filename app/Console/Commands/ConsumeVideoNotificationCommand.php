<?php

namespace App\Console\Commands;

use App\Application\Notification\DTOs\VideoNotificationPayload;
use App\Application\Notification\SendVideoNotificationAction;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Throwable;

#[Signature('notification:consume-video')]
#[Description('Consome eventos do video_notification_exchange e envia notificações por e-mail.')]
class ConsumeVideoNotificationCommand extends Command
{
    public function handle(SendVideoNotificationAction $action): void
    {
        $exchange = env('NOTIFICATION_EXCHANGE', 'video_notification_exchange');
        $queue    = 'notificacao.' . $exchange;

        $this->info("Conectando ao RabbitMQ...");

        $connection = new AMQPStreamConnection(
            env('RABBITMQ_HOST', '127.0.0.1'),
            env('RABBITMQ_PORT', 5672),
            env('RABBITMQ_USER', 'guest'),
            env('RABBITMQ_PASSWORD', 'guest'),
            env('RABBITMQ_VHOST', '/')
        );

        $channel = $connection->channel();

        $channel->exchange_declare(
            exchange: $exchange,
            type: 'fanout',
            passive: false,
            durable: true,
            auto_delete: false
        );

        $channel->queue_declare(
            queue: $queue,
            passive: false,
            durable: true,
            exclusive: false,
            auto_delete: false
        );

        $channel->queue_bind($queue, $exchange);

        $this->info("Aguardando eventos em [{$exchange}]. Pressione CTRL+C para sair.");

        $channel->basic_consume(
            queue: $queue,
            consumer_tag: '',
            no_local: false,
            no_ack: false,
            exclusive: false,
            nowait: false,
            callback: function (AMQPMessage $message) use ($action): void {
                $this->processMessage($message, $action);
            }
        );

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }

    private function processMessage(AMQPMessage $message, SendVideoNotificationAction $action): void
    {
        $body = json_decode($message->body, true);

        $this->line("Evento recebido: video_id={$body['video_id']} status={$body['status']}");

        try {
            $payload = VideoNotificationPayload::fromArray($body);
            $action->execute($payload);
            $message->ack();
        } catch (Throwable $e) {
            $this->error("Falha ao processar evento: {$e->getMessage()}");
            // nack sem requeue para evitar loop infinito em falha permanente
            $message->nack(requeue: false);
        }
    }
}
