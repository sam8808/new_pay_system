<?php


return [
    'providers' => [
        'test' => [
            'name' => 'Test Provider',
            'class' => \App\Services\Providers\TestProvider::class,
            'webhook_secret' => env('TEST_PROVIDER_WEBHOOK_SECRET'),
            'api_key' => env('TEST_PROVIDER_API_KEY'),
        ]
    ],

    'webhook' => [
        'secret' => env('WEBHOOK_SECRET'),
        'timeout' => 5,
        'retries' => 3
    ],

    'default_currency' => [
        'name' => 'RUB',
    ],

    'currencies' => [
        'fiat' => [
            'EUR',
            'RUB',
            'USD',
        ],
        'crypto' => [
            'BNB',
            'BTC',
            'DOGE',
            'LTC',
            'TRX',
            'USDT',
        ],
    ],


    'percent' => [
        'default' => 2.00,
    ],
];
