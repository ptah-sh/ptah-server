<?php

namespace App\Models\PricingPlan;

use Closure;
use RuntimeException;

class ItemQuota
{
    public function __construct(
        public int $maxUsage,
        protected Closure $getCurrentUsage,
    ) {}

    public function ensureQuota(): void
    {
        if ($this->quotaReached()) {
            throw new RuntimeException('Invalid State - The team is at its node limit');
        }
    }

    public function almostQuotaReached(): bool
    {
        return $this->currentUsage() >= ($this->maxUsage * 0.8);
    }

    public function quotaReached(): bool
    {
        return $this->currentUsage() >= $this->maxUsage;
    }

    protected function currentUsage(): int
    {
        return call_user_func($this->getCurrentUsage);
    }
}
