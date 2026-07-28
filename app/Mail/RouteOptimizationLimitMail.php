<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RouteOptimizationLimitMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public int $used,
        public int $limit,
        public string $period,
    ) {
        $this->mailer('info');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Google Routes-limiet bereikt – Mapbox fallback actief'
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.route-optimization-limit');
    }
}
