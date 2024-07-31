<?php

namespace App\Models\SwarmData;

use Spatie\LaravelData\Data;

class JoinTokens extends Data
{
    public function __construct(
        public string $worker,
        public string $manager
    ) {}
}
