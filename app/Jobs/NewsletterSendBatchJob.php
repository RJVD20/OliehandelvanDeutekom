<?php

namespace App\Jobs;

use App\Models\Newsletter;
use App\Services\Newsletter\NewsletterSender;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NewsletterSendBatchJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /** @var array<int, array<string, mixed>> */
    public array $recipients;

    public function __construct(
        public readonly int $newsletterId,
        array $recipients,
        public readonly int $batchNumber
    ) {
        $this->recipients = $recipients;
    }

    public function handle(NewsletterSender $sender): void
    {
        $newsletter = Newsletter::find($this->newsletterId);
        if (! $newsletter) {
            return;
        }

        if ($newsletter->status !== Newsletter::STATUS_SENDING) {
            return;
        }

        $sender->sendBatch($newsletter, $this->recipients, $this->batchNumber);
    }
}
