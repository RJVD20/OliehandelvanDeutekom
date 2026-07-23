<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Mollie\Api\MollieApiClient;
use RuntimeException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(MollieApiClient::class, function (): MollieApiClient {
            $apiKey = config('payments.provider_options.mollie.api_key');

            if (! is_string($apiKey) || $apiKey === '') {
                throw new RuntimeException('MOLLIE_API_KEY is niet geconfigureerd.');
            }

            return (new MollieApiClient)->setApiKey($apiKey);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
