<?php

namespace App\Models\DeploymentData;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Between;
use Spatie\LaravelData\Attributes\Validation\In;


class Caddy extends Data
{
    public function __construct(
        #[In(['http', 'fastcgi'])]
        public string $targetProtocol,
        #[Between(1, 65535)]
        public int $targetPort,
        #[Between(1, 65535)]
        public int $publishedPort,
        public string $domain,
        public string $path
    )
    {

    }
}