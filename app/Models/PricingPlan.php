<?php

namespace App\Models;

use App\Models\PricingPlan\UsageQuotas;
use Spatie\LaravelData\Data;

class PricingPlan extends Data
{
    public function __construct(
        public string $productId,
        public UsageQuotas $quotas,
    ) {}
}
