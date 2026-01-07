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
    ],
];
