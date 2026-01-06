<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly Payment $payment)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $payment = $this->payment;

        return (new MailMessage)
            ->subject('Herinnering betaling bestelling #'.$payment->order_id)
            ->greeting('Hallo '.$payment->order->name)
            ->line('We hebben nog geen betaling ontvangen voor je bestelling.')
            ->line('Vervaldatum: '.$payment->due_date->format('d-m-Y'))
            ->action('Betaal nu', $payment->pay_link ?? url('/'))
            ->line('Als je al betaald hebt, kun je deze e-mail negeren.');
    }
}
