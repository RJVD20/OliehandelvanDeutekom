<?php

namespace App\Services\Payments;

use InvalidArgumentException;

class PaymentProviderFactory
{
    public function make(?string $provider): PaymentProvider
    {
        return match ($provider) {
            'mollie' => new Providers\MolliePaymentProvider(),
            'stripe' => new Providers\StripePaymentProvider(),
            default  => new Providers\MockPaymentProvider(),
        };
    }
}
