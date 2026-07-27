<?php

namespace App\Console\Commands;

use App\Enums\PaymentStatus;
use App\Enums\OrderStatus;
use App\Models\Payment;
use App\Models\PaymentEvent;
use Illuminate\Console\Command;

class ExpireOpenPaymentsCommand extends Command
{
    protected $signature = 'payments:expire-open';
    protected $description = 'Expire open payments past their due date';

    public function handle(): int
    {
        $count = 0;
        Payment::where('status', PaymentStatus::OPEN->value)
            ->whereNotNull('pay_link')
            ->whereDate('due_date', '<', now())
            ->chunkById(50, function ($payments) use (&$count) {
                foreach ($payments as $payment) {
                    $old = $payment->status;
                    $payment->status = PaymentStatus::EXPIRED;
                    $payment->save();
                    $count++;

                    PaymentEvent::create([
                        'payment_id' => $payment->id,
                        'type'       => 'expired',
                        'source'     => 'system',
                        'data'       => ['from' => $old->value ?? (string) $old, 'to' => PaymentStatus::EXPIRED->value],
                    ]);

                    if ($payment->order?->status === OrderStatus::AWAITING_PAYMENT) {
                        $payment->order->delete();
                    }
                }
            });

        $this->info("Expired {$count} payments");
        return self::SUCCESS;
    }
}
