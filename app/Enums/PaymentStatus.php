<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case OPEN = 'open';
    case PAID = 'paid';
    case EXPIRED = 'expired';
    case FAILED = 'failed';
    case CANCELLED = 'cancelled';

    public function isFinal(): bool
    {
        return in_array($this, [self::PAID, self::EXPIRED, self::FAILED, self::CANCELLED], true);
    }
}
