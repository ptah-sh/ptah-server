<?php

namespace App\Models\NodeData;

use Spatie\LaravelData\Data;

class DockerData extends Data
{
    public function __construct(
        public DockerPlatformData $platform
    ) {}
}
