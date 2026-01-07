<?php

namespace App\Console\Commands;

use App\Jobs\NewsletterDispatchJob;
use App\Models\Newsletter;
use Illuminate\Console\Command;

class DispatchScheduledNewslettersCommand extends Command
{
    protected $signature = 'newsletter:dispatch-scheduled';

    protected $description = 'Dispatch scheduled newsletters for sending';

    public function handle(): int
    {
        $count = Newsletter::where('status', Newsletter::STATUS_SCHEDULED)
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', now())
            ->count();

        Newsletter::where('status', Newsletter::STATUS_SCHEDULED)
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', now())
            ->pluck('id')
            ->each(fn ($id) => NewsletterDispatchJob::dispatch($id));

        $this->info("Dispatched {$count} scheduled newsletters.");

        return self::SUCCESS;
    }
}
