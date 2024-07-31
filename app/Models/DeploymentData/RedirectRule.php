<?php

namespace App\Models\DeploymentData;

use Spatie\LaravelData\Data;

class RedirectRule extends Data
{
    public function __construct(
        public string $id,
        public string $domainFrom,
        public string $domainTo,
        public string $pathFrom,
        public string $pathTo,
        public int $statusCode
    ) {}
}
