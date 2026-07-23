<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Order $order
    ) {
        $this->mailer('orders');
    }

    public function build()
    {
        return $this
            ->from(
                config('mail.addresses.orders.address'),
                config('mail.addresses.orders.name'),
            )
            ->subject('Orderbevestiging #'.$this->order->id)
            ->view('emails.order-confirmation');
    }
}
