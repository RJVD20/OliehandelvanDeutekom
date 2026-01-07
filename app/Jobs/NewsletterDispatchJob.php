<?php

namespace App\Jobs;

use App\Models\Newsletter;
use App\Models\NewsletterSend;
use App\Models\NewsletterUnsubscribe;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NewsletterDispatchJob implements ShouldQueue
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
        /** @var Newsletter|null $newsletter */
        $newsletter = Newsletter::find($this->newsletterId);
        if (! $newsletter) {
            return;
        }

        if (! in_array($newsletter->status, [Newsletter::STATUS_SCHEDULED, Newsletter::STATUS_SENDING, Newsletter::STATUS_DRAFT], true)) {
            return;
        }

        // Prevent double dispatch
        if ($newsletter->send_lock_at && $newsletter->send_lock_at->gt(now()->subMinutes(5))) {
            return;
        }

        $newsletter->fill([
            'status' => Newsletter::STATUS_SENDING,
            'send_lock_at' => now(),
            'scheduled_at' => null,
        ])->save();

        $batchSize = (int) config('newsletter.batch_size', 50);
        $batchNumber = 1;

        DB::table('users')
            ->select('id', 'name', 'email')
            ->whereNotNull('email')
            ->orderBy('id')
            ->chunkById(500, function ($users) use ($newsletter, $batchSize, &$batchNumber) {
                $emails = collect($users)->pluck('email');
                $unsubscribed = NewsletterUnsubscribe::whereIn('email', $emails)->pluck('email')->all();

                $eligible = collect($users)->reject(fn ($u) => in_array($u->email, $unsubscribed, true));

                foreach ($eligible->chunk($batchSize) as $chunk) {
                    $recipients = [];

                    foreach ($chunk as $user) {
                        NewsletterSend::firstOrCreate(
                            [
                                'newsletter_id' => $newsletter->id,
                                'recipient_email' => $user->email,
                            ],
                            [
                                'user_id' => $user->id,
                                'recipient_name' => $user->name,
                                'status' => NewsletterSend::STATUS_QUEUED,
                                'batch' => $batchNumber,
                            ]
                        );

                        $recipients[] = [
                            'user_id' => $user->id,
                            'email' => $user->email,
                            'name' => $user->name,
                        ];
                    }

                    if (! empty($recipients)) {
                        NewsletterSendBatchJob::dispatch($newsletter->id, $recipients, $batchNumber);
                        $batchNumber++;
                    }
                }
            });

        NewsletterFinalizeJob::dispatch($newsletter->id)->delay(now()->addMinutes(1));
    }
}
