<?php

namespace App\Mail;

use App\Models\Newsletter;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewsletterMailable extends Mailable
{
    use Queueable;
    use SerializesModels;

    private string $renderedHtml;

    private string $renderedText;

    public function __construct(
        public Newsletter $newsletter,
        string $html,
        string $text
    ) {
        $this->renderedHtml = $html;
        $this->renderedText = $text;
        $this->mailer('info');
    }

    public function build(): self
    {
        return $this
            ->from(
                config('mail.addresses.info.address'),
                config('mail.addresses.info.name'),
            )
            ->subject($this->newsletter->subject)
            ->view('emails.newsletter')
            ->text('emails.newsletter-text')
            ->with([
                'html' => $this->renderedHtml,
                'text' => $this->renderedText,
                'newsletter' => $this->newsletter,
            ]);
    }
}
