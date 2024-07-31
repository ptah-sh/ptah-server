<?php

namespace App\Models\NodeData;

use Spatie\LaravelData\Data;

class DockerPlatformData extends Data
{
    public function __construct(
        public string $name
    ) {}
}
