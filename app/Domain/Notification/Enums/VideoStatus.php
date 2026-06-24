<?php

namespace App\Domain\Notification\Enums;

enum VideoStatus: string
{
    case Completed = 'completed';
    case Failed    = 'failed';
}
