<?php

namespace App\Models\PricingPlan;

use Closure;
use Illuminate\Validation\ValidationException;

class ItemQuota
{
    public function __construct(
        public string $name,
        public int $maxUsage,
        protected Closure $getCurrentUsage,
        public bool $isSoftQuota = false
    ) {}

    public function ensureQuota(): void
    {
        if ($this->quotaReached()) {
            throw ValidationException::withMessages([
                'quota' => "The maximum limit of {$this->maxUsage} {$this->name} has been reached.",
            ]);
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

    public function currentUsage(): int
    {
        return call_user_func($this->getCurrentUsage);
    }
}
