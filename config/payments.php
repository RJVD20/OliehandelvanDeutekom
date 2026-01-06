<?php

return [
    'provider' => env('PAYMENTS_PROVIDER', 'mock'),

    'reminders' => [
        'first_days'  => env('PAYMENTS_REMINDER_FIRST_DAYS', 7),
        'second_days' => env('PAYMENTS_REMINDER_SECOND_DAYS', 12),
        'cooldown_hours' => env('PAYMENTS_REMINDER_COOLDOWN_HOURS', 24),
        'max_reminders' => env('PAYMENTS_REMINDER_MAX', 2),
        'grace_days' => env('PAYMENTS_REMINDER_GRACE_DAYS', 0),
    ],

    'provider_options' => [
        'mock' => [
            'base_url' => env('APP_URL'),
        ],
        'mollie' => [
            'api_key' => env('MOLLIE_API_KEY'),
            'redirect_url' => env('MOLLIE_REDIRECT_URL'),
            'webhook_url'  => env('MOLLIE_WEBHOOK_URL'),
        ],
        'stripe' => [
            'api_key' => env('STRIPE_SECRET'),
            'redirect_url' => env('STRIPE_REDIRECT_URL'),
            'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        ],
    ],
];
