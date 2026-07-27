<?php

namespace App\Mail;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ManualPaymentRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Payment $payment)
    {
        $this->mailer('orders');
    }

    public function build()
    {
        return $this
            ->from(
                config('mail.addresses.orders.sender_address'),
                config('mail.addresses.orders.sender_name'),
            )
            ->replyTo(
                config('mail.addresses.orders.address'),
                config('mail.addresses.orders.name'),
            )
            ->subject('Betaalverzoek bestelling #'.$this->payment->order_id)
            ->view('emails.manual-payment-request');
    }
}
