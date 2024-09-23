<?php

namespace App\Models;

use Spatie\LaravelData\Data;

class QuotasOverride extends Data
{
    public function __construct(
        public int $nodes = 0,
        public int $swarms = 0,
        public int $services = 0,
        public int $deployments = 0
    ) {}
}
