<?php

namespace App\Models\DeploymentData;

use Spatie\LaravelData\Data;

class BackupVolume extends Data
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $dockerName,
        public string $path,
    ) {}
}
