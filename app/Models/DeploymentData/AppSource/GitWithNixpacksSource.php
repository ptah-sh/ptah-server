<?php

namespace App\Models\DeploymentData\AppSource;

use App\Models\DeploymentData\Volume;
use Spatie\LaravelData\Data;

class GitWithNixpacksSource extends Data
{
    public function __construct(
        public string $repo,
        public string $ref,
        public string $nixpacksFilePath,
        public ?Volume $volume,
    ) {}
}
