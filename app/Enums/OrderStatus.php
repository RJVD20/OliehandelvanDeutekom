<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case SHIPPED = 'shipped';
    case CANCELLED = 'cancelled';
    case COMPLETED = 'completed';
}
