<?php

namespace App\Models\DeploymentData;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;

class FastCgi extends Data
{
    public function __construct(
        public string $root,
        #[DataCollectionOf(EnvVar::class)]
        /* @var EnvVar[] */
        public array $env
    ) {}
}
