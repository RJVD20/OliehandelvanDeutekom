<?php

namespace App\Services\Payments;

use Illuminate\Contracts\Container\Container;
use InvalidArgumentException;
use Mollie\Api\MollieApiClient;

class PaymentProviderFactory
{
    public function __construct(private readonly Container $container) {}

    public function make(?string $provider): PaymentProvider
    {
        return match ($provider) {
            null, '', 'mock' => new Providers\MockPaymentProvider,
            'mollie' => new Providers\MolliePaymentProvider($this->container->make(MollieApiClient::class)),
            'stripe' => throw new InvalidArgumentException('Payment provider not implemented: '.$provider),
            default => throw new InvalidArgumentException('Unknown payment provider: '.$provider),
        };
    }
}
