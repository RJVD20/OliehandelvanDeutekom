<?php

namespace App\Console\Commands;

use App\Enums\PaymentStatus;
use App\Jobs\SendPaymentReminderJob;
use App\Models\Payment;
use Illuminate\Console\Command;

class SendPaymentRemindersCommand extends Command
{
    protected $signature = 'payments:send-reminders';
    protected $description = 'Send payment reminders for open deferred payments';

    public function handle(): int
    {
        $firstDays = (int) config('payments.reminders.first_days', 7);
        $secondDays = (int) config('payments.reminders.second_days', 12);
        $cooldownHours = (int) config('payments.reminders.cooldown_hours', 24);

        $now = now();
        $soonFirst = $now->copy()->addDays($firstDays);
        $soonSecond = $now->copy()->addDays($secondDays);

        $query = Payment::query()
            ->where('status', PaymentStatus::OPEN->value)
            ->where(function ($q) use ($soonFirst, $soonSecond, $firstDays, $secondDays, $now) {
                $q->where(function ($q) use ($soonFirst, $firstDays, $now) {
                    $q->where('reminder_count', 0)
                        ->where(function ($q) use ($soonFirst, $firstDays, $now) {
                            $q->whereDate('due_date', '<=', $soonFirst)
                              ->orWhere(function ($q) use ($firstDays, $now) {
                                  $q->whereNull('due_date')
                                    ->whereDate('created_at', '<=', $now->copy()->subDays($firstDays));
                              });
                        });
                })
                ->orWhere(function ($q) use ($soonSecond, $secondDays, $now) {
                    $q->where('reminder_count', 1)
                        ->where(function ($q) use ($soonSecond, $secondDays, $now) {
                            $q->whereDate('due_date', '<=', $soonSecond)
                              ->orWhere(function ($q) use ($secondDays, $now) {
                                  $q->whereNull('due_date')
                                    ->whereDate('created_at', '<=', $now->copy()->subDays($secondDays));
                              });
                        });
                });
            });

        $query->chunkById(50, function ($payments) use ($cooldownHours) {
            foreach ($payments as $payment) {
                if ($payment->last_reminder_at && $payment->last_reminder_at->greaterThan(now()->subHours($cooldownHours))) {
                    continue;
                }
                SendPaymentReminderJob::dispatch($payment, 'email');
            }
        });

        $this->info('Reminder jobs dispatched');
        return self::SUCCESS;
    }
}
