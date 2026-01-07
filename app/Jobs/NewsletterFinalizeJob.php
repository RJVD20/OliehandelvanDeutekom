<?php

namespace App\Jobs;

use App\Models\Newsletter;
use App\Models\NewsletterSend;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NewsletterFinalizeJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public readonly int $newsletterId)
    {
    }

    public function handle(): void
    {
        $newsletter = Newsletter::find($this->newsletterId);
        if (! $newsletter) {
            return;
        }

        $queued = NewsletterSend::where('newsletter_id', $newsletter->id)
            ->where('status', NewsletterSend::STATUS_QUEUED)
            ->count();

        if ($queued > 0) {
            static::dispatch($newsletter->id)->delay(now()->addMinutes(1));
            return;
        }

        $failed = NewsletterSend::where('newsletter_id', $newsletter->id)
            ->where('status', NewsletterSend::STATUS_FAILED)
            ->count();

        $newsletter->sent_at = $newsletter->sent_at ?: now();
        $newsletter->send_lock_at = null;
        $newsletter->status = $failed > 0 ? Newsletter::STATUS_FAILED : Newsletter::STATUS_SENT;
        $newsletter->save();
    }
}
