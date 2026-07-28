<?php

namespace App\Services\Newsletter;

use App\Enums\OrderStatus;
use App\Models\Newsletter;
use App\Models\NewsletterUnsubscribe;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class NewsletterRecipientResolver
{
    public function queryForNewsletter(Newsletter $newsletter): Builder
    {
        return $this->query(
            $newsletter->target_audience ?: 'all_users',
            $newsletter->filters ?? [],
        );
    }

    public function query(string $audience, array $filters = []): Builder
    {
        $audience = $audience === 'users' ? 'all_users' : $audience;
        $query = User::query()
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->whereNotIn('email', NewsletterUnsubscribe::query()->select('email'));

        if ($audience !== 'all_users') {
            $query->whereHas('orders', function ($orders) use ($audience, $filters) {
                $orders->where('status', '!=', OrderStatus::AWAITING_PAYMENT->value);

                if ($audience === 'province' && ! empty($filters['province'])) {
                    $orders->where('province', $filters['province']);
                }

                if ($audience === 'fulfillment' && ! empty($filters['fulfillment_method'])) {
                    $orders->where('fulfillment_method', $filters['fulfillment_method']);
                }

                if ($audience === 'recent_customers' && ! empty($filters['ordered_since'])) {
                    $orders->whereDate('created_at', '>=', $filters['ordered_since']);
                }
            });
        }

        return $query->orderBy('id');
    }

    public function count(string $audience, array $filters = []): int
    {
        return $this->query($audience, $filters)->count();
    }
}
