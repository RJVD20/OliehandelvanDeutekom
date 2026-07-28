<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mapbox' => [
        'token' => env('MAPBOX_TOKEN'),
    ],

    'google_routes' => [
        'key' => env('GOOGLE_ROUTES_API_KEY'),
        'endpoint' => env('GOOGLE_ROUTES_ENDPOINT', 'https://routes.googleapis.com/directions/v2:computeRoutes'),
    ],

    'postcode_tech' => [
        'key' => env('POSTCODE_TECH_KEY'),
    ],

];
