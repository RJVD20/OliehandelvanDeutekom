<?php

namespace Tests\Unit;

use App\Models\Payment as LocalPayment;
use App\Services\Payments\Providers\MolliePaymentProvider;
use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\CreatePaymentRequest;
use Mollie\Api\Resources\Payment as MolliePayment;
use Tests\TestCase;

class MolliePaymentProviderTest extends TestCase
{
    public function test_it_creates_an_idempotent_mollie_payment_link(): void
    {
        $client = new MockMollieClient([
            CreatePaymentRequest::class => MockResponse::resource(MolliePayment::class)
                ->with([
                    'id' => 'tr_test_payment',
                    'mode' => 'test',
                    'status' => 'open',
                    'amount' => ['currency' => 'EUR', 'value' => '20.00'],
                ])
                ->create(),
        ], retainRequests: true);

        $payment = (new LocalPayment)->forceFill([
            'id' => 10,
            'order_id' => 123,
            'provider' => 'mollie',
            'amount' => '20.00',
            'currency' => 'EUR',
            'meta' => ['payment_method' => 'ideal'],
        ]);

        $provider = new MolliePaymentProvider($client);
        $provider->createDeferredPayment($payment);
        $provider->createDeferredPayment($payment);

        $this->assertSame('tr_test_payment', $payment->provider_payment_id);
        $this->assertNotEmpty($payment->pay_link);
        $this->assertSame('test', $payment->meta['mollie_mode']);
        $this->assertSame('ideal', $payment->meta['payment_method']);
        $client->assertSent(
            fn ($request) => $request->payload()?->all()['method'] === 'ideal'
        );
        $client->assertSentCount(1);
    }
}
