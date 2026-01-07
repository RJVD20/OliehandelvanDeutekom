<?php

namespace App\Mail;

use App\Models\Newsletter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewsletterMailable extends Mailable implements ShouldQueue
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
    }

    public function build(): self
    {
        return $this
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
