<?php

namespace App\Models\DeploymentData;

use Spatie\LaravelData\Data;

class ArchiveOptions extends Data
{
    public function __construct(
        public ArchiveFormat $format,
    ) {}
}
