<?php

namespace App\Services\Payments;

use InvalidArgumentException;

class PaymentProviderFactory
{
    public function make(?string $provider): PaymentProvider
    {
        return match ($provider) {
            null, '', 'mock' => new Providers\MockPaymentProvider(),
            'mollie', 'stripe' => throw new InvalidArgumentException('Payment provider not implemented: '.$provider),
            default => throw new InvalidArgumentException('Unknown payment provider: '.$provider),
        };
    }
}
