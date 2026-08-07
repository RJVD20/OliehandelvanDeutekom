<?php

return [
    'provider' => env('PAYMENTS_PROVIDER', 'mock'),

    'methods' => [
        'ideal' => [
            'label' => 'iDEAL | Wero',
            'description' => 'Betaal direct en veilig via je eigen bank.',
        ],
        'creditcard' => [
            'label' => 'Creditcard',
            'description' => 'Betaal veilig met Visa, Mastercard of een andere ondersteunde kaart.',
        ],
        'cash' => [
            'label' => 'Contant betalen',
            'description' => 'Betaal het volledige bedrag contant bij bezorging of afhalen.',
            'offline' => true,
        ],
    ],

    'provider_options' => [
        'mock' => [
            'base_url' => env('APP_URL'),
        ],
        'mollie' => [
            'api_key' => env('MOLLIE_API_KEY'),
        ],
    ],
];
