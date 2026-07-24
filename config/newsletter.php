<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Planning timezone
    |--------------------------------------------------------------------------
    |
    | Administrators enter newsletter schedules in Dutch local time. Dates are
    | stored in UTC and converted back to this timezone in the admin screens.
    |
    */
    'timezone' => env('NEWSLETTER_TIMEZONE', 'Europe/Amsterdam'),

    'batch_size' => env('NEWSLETTER_BATCH_SIZE', 50),
    'test_recipient' => env('NEWSLETTER_TEST_RECIPIENT'),
];
