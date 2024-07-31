<?php

namespace App\Models\DeploymentData;

use Spatie\LaravelData\Data;

class EnvVar extends Data
{
    public function __construct(
        public string $name,
        public ?string $value
    ) {}
}
