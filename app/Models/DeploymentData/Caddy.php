<?php

namespace App\Models\DeploymentData;

use Spatie\LaravelData\Attributes\Validation\Between;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Data;

class Caddy extends Data
{
    public function __construct(
        public string $id,
        #[In(['http', 'fastcgi'])]
        public string $targetProtocol,
        #[Between(1, 65535)]
        public int $targetPort,
        #[In(80, 443)]
        public int $publishedPort,
        public string $domain,
        public string $path,
    ) {}
}
