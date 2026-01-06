<?php

namespace App\Services\Payments;

use App\Models\Payment;

interface PaymentProvider
{
    /**
     * Create or refresh a deferred payment link at the provider.
     */
    public function createDeferredPayment(Payment $payment): Payment;
}
