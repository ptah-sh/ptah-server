<?php

namespace App\Models\DeploymentData;

use Spatie\LaravelData\Data;

class RewriteRule extends Data
{
    public function __construct(
        public string $id,
        public string $pathFrom,
        public string $pathTo
    ) {}
}
