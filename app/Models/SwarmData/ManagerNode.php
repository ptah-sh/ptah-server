<?php

namespace App\Models\SwarmData;

use Spatie\LaravelData\Data;

class ManagerNode extends Data
{
    public function __construct(
        public string $nodeId,
        public string $addr,
    ) {}
}
