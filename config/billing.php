<?php

return [
    'paddle' => [
        'plans' => [
            [
                'name' => 'Hobby',
                'price' => 19,
                'description' => 'Perfect plan to try the service or host non-critical applications',
                'product_id' => env('PADDLE_PLAN_HOBBY_PRODUCT_ID'),
                'price_id' => env('PADDLE_PLAN_HOBBY_PRICE_ID'),
                'quotas' => [
                    'nodes' => 1,
                    'swarms' => 1,
                    'services' => 20,
                    'deployments' => 100,
                ],
            ],
            [
                'name' => 'Startup',
                'price' => 49,
                'description' => 'Need more power or an improved stability? This is your choice!',
                'product_id' => env('PADDLE_PLAN_STARTUP_PRODUCT_ID'),
                'price_id' => env('PADDLE_PLAN_STARTUP_PRICE_ID'),
                'quotas' => [
                    'nodes' => 9,
                    'swarms' => 2,
                    'services' => 20,
                    'deployments' => 100,
                ],
            ],
        ],
    ],
];
