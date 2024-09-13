<?php

namespace App\Models\DeploymentData;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;

class SecretVars extends Data
{
    public function __construct(
        #[DataCollectionOf(EnvVar::class)]
        public array $vars,
    ) {}
}
