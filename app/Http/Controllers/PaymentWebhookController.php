<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\PaymentEvent;
use Illuminate\Http\Request;

class PaymentWebhookController extends Controller
{
    public function handle(string $provider, Request $request)
    {
        $providerPaymentId = $request->input('id') ?? $request->input('payment_id');
        if (!$providerPaymentId) {
            return response()->json(['message' => 'missing id'], 400);
        }

        $payment = Payment::where('provider_payment_id', $providerPaymentId)->first();
        if (!$payment) {
            return response()->json(['message' => 'ok'], 200);
        }

        $newStatus = $this->mapStatus($request->input('status'));
        if ($newStatus && $payment->status !== $newStatus) {
            $oldStatus = $payment->status;
            // allow webhook to move expired -> paid
            $payment->status = $newStatus;
            if ($newStatus === PaymentStatus::PAID) {
                $payment->paid_at = now();
            }
            $payment->save();

            PaymentEvent::create([
                'payment_id' => $payment->id,
                'type'      => 'webhook_status_update',
                'source'    => $provider,
                'data'      => ['from' => $oldStatus->value ?? (string) $oldStatus, 'to' => $newStatus->value],
            ]);
        }

        return response()->json(['message' => 'ok'], 200);
    }

    private function mapStatus(?string $status): ?PaymentStatus
    {
        return match ($status) {
            'paid', 'succeeded' => PaymentStatus::PAID,
            'expired'           => PaymentStatus::EXPIRED,
            'failed'            => PaymentStatus::FAILED,
            'canceled', 'cancelled' => PaymentStatus::CANCELLED,
            default             => null,
        };
    }
}
