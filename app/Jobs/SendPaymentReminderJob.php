<?php

namespace App\Jobs;

use App\Models\Payment;
use App\Models\PaymentReminder;
use App\Notifications\PaymentReminderNotification;
use App\Enums\PaymentStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class SendPaymentReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly Payment $payment, private readonly string $channel = 'email')
    {
    }

    public function handle(): void
    {
        DB::transaction(function () {
            $payment = Payment::lockForUpdate()->find($this->payment->id);
            if (!$payment || $payment->status !== PaymentStatus::OPEN) {
                return;
            }

            $cooldownHours = (int) config('payments.reminders.cooldown_hours', 24);
            if ($payment->last_reminder_at && $payment->last_reminder_at->greaterThan(now()->subHours($cooldownHours))) {
                return;
            }

            $max = (int) config('payments.reminders.max_reminders', 2);
            if ($payment->reminder_count >= $max) {
                return;
            }

            Notification::route('mail', $payment->order->email)
                ->notify(new PaymentReminderNotification($payment));

            $payment->last_reminder_at = now();
            $payment->reminder_count = $payment->reminder_count + 1;
            $payment->save();

            PaymentReminder::create([
                'payment_id' => $payment->id,
                'channel'    => $this->channel,
                'status'     => 'sent',
                'payload'    => ['channel' => $this->channel],
                'sent_at'    => now(),
            ]);
        });
    }
}
