<?php

namespace App\Models\DeploymentData\AppSource;

use App\Models\DeploymentData\Volume;
use Spatie\LaravelData\Attributes\Validation\DoesntStartWith;
use Spatie\LaravelData\Attributes\Validation\StartsWith;
use Spatie\LaravelData\Data;

class GitWithDockerfileSource extends Data
{
    public function __construct(
        #[StartsWith('git@')]
        public string $repo,
        public string $ref,
        #[DoesntStartWith('/')]
        public string $dockerfilePath,
        public ?Volume $volume,
    ) {}
}
