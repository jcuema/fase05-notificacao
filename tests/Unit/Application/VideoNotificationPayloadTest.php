<?php

use App\Application\Notification\DTOs\VideoNotificationPayload;

uses(Tests\TestCase::class);

it('cria payload a partir de array com status completed', function () {
    $data = [
        'video_id'   => 'vid-001',
        'user_id'    => '5',
        'status'     => 'completed',
        'result_url' => 'processed_videos/vid-001.zip',
        'timestamp'  => '2026-06-23T12:00:00+00:00',
    ];

    $payload = VideoNotificationPayload::fromArray($data);

    expect($payload->videoId)->toBe('vid-001')
        ->and($payload->userId)->toBe(5)
        ->and($payload->status)->toBe('completed')
        ->and($payload->resultUrl)->toBe('processed_videos/vid-001.zip');
});

it('cria payload a partir de array com status failed e result_url nulo', function () {
    $data = [
        'video_id'   => 'vid-002',
        'user_id'    => 3,
        'status'     => 'failed',
        'result_url' => null,
        'timestamp'  => '2026-06-23T12:00:00+00:00',
    ];

    $payload = VideoNotificationPayload::fromArray($data);

    expect($payload->status)->toBe('failed')
        ->and($payload->resultUrl)->toBeNull();
});

it('converte user_id para inteiro ao criar payload', function () {
    $payload = VideoNotificationPayload::fromArray([
        'video_id'   => 'vid-003',
        'user_id'    => '42',
        'status'     => 'completed',
        'result_url' => 'url',
        'timestamp'  => '2026-06-23T12:00:00+00:00',
    ]);

    expect($payload->userId)->toBeInt()->toBe(42);
});
