<?php

namespace App\Models\PricingPlan;

use Spatie\LaravelData\Data;

class UsageQuotas extends Data
{
    public function __construct(
        public int $nodes,
    ) {}
}
