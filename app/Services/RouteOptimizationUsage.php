<?php

namespace App\Services;

use App\Mail\RouteOptimizationLimitMail;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class RouteOptimizationUsage
{
    public const DEFAULT_LIMIT = 4750;

    public function summary(): array
    {
        $period = now()->format('Y-m');
        $limit = max(1, min(5000, (int) Setting::get('google_routes_monthly_limit', self::DEFAULT_LIMIT)));
        $used = max(0, (int) Setting::get($this->usageKey($period), 0));

        return [
            'period' => $period,
            'used' => $used,
            'limit' => $limit,
            'remaining' => max(0, $limit - $used),
            'google_available' => filled(config('services.google_routes.key')),
            'fallback_active' => $used >= $limit,
            'alert_email' => Setting::get(
                'google_routes_alert_email',
                config('mail.addresses.info.address')
            ),
        ];
    }

    public function claimGoogleRequest(): bool
    {
        if (! filled(config('services.google_routes.key'))) {
            return false;
        }

        $period = now()->format('Y-m');
        $limit = max(1, min(5000, (int) Setting::get('google_routes_monthly_limit', self::DEFAULT_LIMIT)));
        $usageKey = $this->usageKey($period);

        $used = DB::transaction(function () use ($usageKey, $limit) {
            $setting = Setting::query()->where('key', $usageKey)->lockForUpdate()->first();
            $current = max(0, (int) ($setting?->value ?? 0));

            if ($current >= $limit) {
                return null;
            }

            $next = $current + 1;
            Setting::query()->updateOrCreate(['key' => $usageKey], ['value' => (string) $next]);

            return $next;
        });

        Cache::forget('setting:'.$usageKey);

        if ($used === null) {
            return false;
        }

        if ($used >= $limit) {
            $this->sendLimitAlertOnce($period, $used, $limit);
        }

        return true;
    }

    private function sendLimitAlertOnce(string $period, int $used, int $limit): void
    {
        $alertKey = 'google_routes_alert_sent_'.$period;
        $shouldSend = DB::transaction(function () use ($alertKey) {
            $setting = Setting::query()->where('key', $alertKey)->lockForUpdate()->first();

            if ($setting?->value === '1') {
                return false;
            }

            Setting::query()->updateOrCreate(['key' => $alertKey], ['value' => '1']);

            return true;
        });

        Cache::forget('setting:'.$alertKey);

        if (! $shouldSend) {
            return;
        }

        $recipient = Setting::get(
            'google_routes_alert_email',
            config('mail.addresses.info.address')
        );

        if (! filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            Log::warning('Google Routes-limiet bereikt, maar het waarschuwingsadres is ongeldig.');

            return;
        }

        try {
            Mail::to($recipient)->send(new RouteOptimizationLimitMail($used, $limit, $period));
        } catch (Throwable $exception) {
            Log::error('Google Routes-limietmail kon niet worden verstuurd.', [
                'message' => $exception->getMessage(),
            ]);
        }
    }

    private function usageKey(string $period): string
    {
        return 'google_routes_usage_'.$period;
    }
}
