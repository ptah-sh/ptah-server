<?php

namespace App\Models\DeploymentData;

use Spatie\LaravelData\Data;

class ReleaseCommand extends Data
{
    public function __construct(
        public ?string $dockerName,
        public ?string $command
    ) {
        //
    }
}
