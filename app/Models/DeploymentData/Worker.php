<?php

namespace App\Models\DeploymentData;

use Spatie\LaravelData\Data;

class Worker extends Data
{
    public function __construct(
        public string $name,
        public ?string $dockerName,
        public string $command,
        public int $replicas
    ) {
        //
    }
}
