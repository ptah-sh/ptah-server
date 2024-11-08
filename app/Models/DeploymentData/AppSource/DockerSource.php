<?php

namespace App\Models\DeploymentData\AppSource;

use Spatie\LaravelData\Data;

class DockerSource extends Data
{
    public function __construct(
        public ?string $registryId,
        public string $image,
    ) {}
}
