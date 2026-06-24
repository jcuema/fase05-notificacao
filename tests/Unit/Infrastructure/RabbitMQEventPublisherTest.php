<?php

use App\Infrastructure\Messaging\RabbitMQEventPublisher;
use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;

uses(Tests\TestCase::class);

beforeEach(function () {
    Log::shouldReceive('debug', 'error', 'info');

    $this->mockChannel = Mockery::mock(AMQPChannel::class);
    $this->mockChannel->shouldReceive('queue_declare')->once()->with(
        Mockery::type('string'), false, true, false, false
    );
    $this->mockChannel->shouldReceive('basic_publish')->once();
    $this->mockChannel->shouldReceive('close')->once();

    $this->mockConnection = Mockery::mock(AMQPStreamConnection::class);
    $this->mockConnection->shouldReceive('channel')->once()->andReturn($this->mockChannel);
    $this->mockConnection->shouldReceive('close')->once();

    $this->publisher = Mockery::mock(RabbitMQEventPublisher::class)
        ->makePartial()
        ->shouldAllowMockingProtectedMethods();
    $this->publisher->shouldReceive('getConnection')->once()->andReturn($this->mockConnection);
});

it('publica evento notification_message_sent na fila correta', function () {
    $this->publisher->publishSent('vid-123', 1);
});

it('publica evento notification_message_delivered na fila correta', function () {
    $this->publisher->publishDelivered('vid-123', 1);
});

it('publica evento notification_message_failed na fila correta', function () {
    $this->publisher->publishFailed('vid-123', 1);
});
