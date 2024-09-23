<?php

return [
    'enabled' => (bool) env('BILLING_ENABLED', true),

    'paddle' => [
        'plans' => [
            [
                'name' => 'Hobby',
                'price' => 57,
                'description' => 'Perfect plan to try the service or host non-critical applications',
                'product_id' => env('PADDLE_PLAN_HOBBY_PRODUCT_ID'),
                'price_id' => env('PADDLE_PLAN_HOBBY_PRICE_ID'),
                'billing_cycle' => 'yearly', // Added billing cycle
                'quotas' => [
                    'nodes' => ['limit' => 1, 'soft' => false],
                    'swarms' => ['limit' => 1, 'soft' => false],
                    'services' => ['limit' => 20, 'soft' => true],
                    'deployments' => ['limit' => 100, 'soft' => true],
                ],
            ],
            [
                'name' => 'Startup',
                'price' => 27,
                'description' => 'Need more power or an additional features? This is your choice!',
                'product_id' => env('PADDLE_PLAN_STARTUP_PRODUCT_ID'),
                'price_id' => env('PADDLE_PLAN_STARTUP_PRICE_ID'),
                'billing_cycle' => 'monthly', // Added billing cycle
                'quotas' => [
                    'nodes' => ['limit' => 5, 'soft' => true],
                    'swarms' => ['limit' => 1, 'soft' => false],
                    'services' => ['limit' => 10, 'soft' => true],
                    'deployments' => ['limit' => 100, 'soft' => true],
                ],
            ],
        ],
        'trialPlan' => [
            'name' => 'Trial',
            'price' => 0,
            'description' => 'Try our service for free for a limited time',
            'product_id' => null,
            'price_id' => null,
            'quotas' => [
                'nodes' => ['limit' => 1, 'soft' => false],
                'swarms' => ['limit' => 1, 'soft' => false],
                'services' => ['limit' => 3, 'soft' => false],
                'deployments' => ['limit' => 20, 'soft' => false],
            ],
        ],
        'selfHostedPlan' => [
            'name' => 'Self-Hosted',
            'price' => 0,
            'description' => 'For users running their own instance of the service',
            'product_id' => null,
            'price_id' => null,
            'quotas' => [
                'nodes' => ['limit' => 1000, 'soft' => true],
                'swarms' => ['limit' => 1, 'soft' => false],
                'services' => ['limit' => 5000, 'soft' => true],
                'deployments' => ['limit' => 100000, 'soft' => true],
            ],
        ],
    ],
];
