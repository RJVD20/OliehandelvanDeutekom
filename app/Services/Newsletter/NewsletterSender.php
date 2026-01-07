<?php

namespace App\Services\Newsletter;

use App\Mail\NewsletterMailable;
use App\Models\Newsletter;
use App\Models\NewsletterSend;
use App\Models\NewsletterUnsubscribe;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NewsletterSender
{
    public function __construct(private readonly NewsletterRenderer $renderer)
    {
    }

    /**
     * Send a batch of recipients and update send logs.
     */
    public function sendBatch(Newsletter $newsletter, array $recipients, int $batchNumber = 0): void
    {
        foreach ($recipients as $recipient) {
            $email = $recipient['email'] ?? null;
            if (! $email) {
                continue;
            }

            if ($this->isUnsubscribed($email)) {
                NewsletterSend::where('newsletter_id', $newsletter->id)
                    ->where('recipient_email', $email)
                    ->update([
                        'status' => NewsletterSend::STATUS_FAILED,
                        'failed_at' => now(),
                        'failure_reason' => 'Unsubscribed',
                        'batch' => $batchNumber,
                    ]);
                continue;
            }

            $payload = $this->renderer->renderForRecipient($newsletter, $recipient);

            try {
                /** @var Mailer $mailer */
                $mailer = Mail::mailer();
                $mailer->to($email, $recipient['name'] ?? null)
                    ->send(new NewsletterMailable($newsletter, $payload['html'], $payload['text']));

                NewsletterSend::where('newsletter_id', $newsletter->id)
                    ->where('recipient_email', $email)
                    ->update([
                        'status' => NewsletterSend::STATUS_SENT,
                        'sent_at' => now(),
                        'batch' => $batchNumber,
                    ]);
            } catch (\Throwable $e) {
                Log::warning('Newsletter send failed', [
                    'newsletter_id' => $newsletter->id,
                    'email' => $email,
                    'error' => $e->getMessage(),
                ]);

                NewsletterSend::where('newsletter_id', $newsletter->id)
                    ->where('recipient_email', $email)
                    ->update([
                        'status' => NewsletterSend::STATUS_FAILED,
                        'failed_at' => now(),
                        'failure_reason' => $e->getMessage(),
                        'batch' => $batchNumber,
                    ]);
            }
        }
    }

    protected function isUnsubscribed(string $email): bool
    {
        return NewsletterUnsubscribe::where('email', $email)->exists();
    }
}
