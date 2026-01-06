<?php

namespace App\Services\Payments\Providers;

use App\Models\Payment;
use App\Services\Payments\PaymentProvider;
use Illuminate\Support\Str;

class MockPaymentProvider implements PaymentProvider
{
    public function createDeferredPayment(Payment $payment): Payment
    {
        $base = rtrim(config('payments.provider_options.mock.base_url', config('app.url')), '/');
        $payment->provider_payment_id = $payment->provider_payment_id ?: (string) Str::uuid();
        $payment->pay_link = $base.'/pay/mock/'.$payment->id.'?token='.$payment->provider_payment_id;
        $payment->provider = 'mock';

        return $payment;
    }
}
