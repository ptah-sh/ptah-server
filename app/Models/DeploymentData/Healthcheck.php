<?php

namespace App\Models\DeploymentData;

use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;

class Healthcheck extends Data
{
    public function __construct(
        public ?string $command = null,
        #[Min(1)]
        public int $interval = 10,
        #[Min(1)]
        public int $timeout = 5,
        #[Min(1)]
        public int $retries = 10,
        #[Min(0)]
        public int $startPeriod = 60,
        #[Min(1)]
        public int $startInterval = 10
    ) {}
}
