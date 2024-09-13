<?php

namespace App\Models\PricingPlan;

use Spatie\LaravelData\Data;

class Plan extends Data
{
    public function __construct(
        public string $name,
        public int $price,
        public string $description,
        public ?string $product_id,
        public ?string $price_id,
        public array $quotas
    ) {}
}
