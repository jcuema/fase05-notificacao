<?php

uses(Tests\TestCase::class);

it('boots the application successfully', function () {
    expect(app()->isBooted())->toBeTrue();
});
