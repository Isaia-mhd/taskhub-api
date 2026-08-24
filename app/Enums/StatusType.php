<?php

namespace App\Enums;

enum StatusType: string
{
    case OPEN = 'open';
    case CLOSED = 'closed';

    public static function values(): array
    {
        return array_map(fn (self $statusType) => $statusType->value, self::cases());
    }
}
