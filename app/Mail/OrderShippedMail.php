<?php

namespace App\Mail;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderShippedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order
    ) {
        $this->mailer('orders');
    }

    /**
     * Mail onderwerp
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new \Illuminate\Mail\Mailables\Address(
                config('mail.addresses.orders.sender_address'),
                config('mail.addresses.orders.sender_name'),
            ),
            replyTo: [
                new \Illuminate\Mail\Mailables\Address(
                    config('mail.addresses.orders.address'),
                    config('mail.addresses.orders.name'),
                ),
            ],
            subject: 'Je bestelling wordt morgen geleverd'
        );
    }

    /**
     * Mail inhoud (view)
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.order-shipped',
            with: [
                'order' => $this->order,
            ]
        );
    }

    /**
     * Bijlagen
     */
    public function attachments(): array
    {
        $pdf = Pdf::loadView('pdfs.invoice', [
            'order' => $this->order,
        ]);

        return [
            Attachment::fromData(
                fn () => $pdf->output(),
                'factuur-'.$this->order->id.'.pdf'
            )->withMime('application/pdf'),
        ];
    }
}
