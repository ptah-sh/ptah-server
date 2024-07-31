<?php

namespace App\Models\DeploymentData;

use Spatie\LaravelData\Attributes\Validation\Between;
use Spatie\LaravelData\Data;

class NodePort extends Data
{
    public function __construct(
        #[Between(1, 65535)]
        public int $targetPort,
        #[Between(1, 65535)]
        public int $publishedPort
    ) {}
}
